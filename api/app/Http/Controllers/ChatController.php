<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\chat as chatResource;
use App\Http\Requests\CreateChat;
use App\Http\Requests\UpdateChat;
use App\User;
use App\chat;
use App\participants;
use App\Http\Requests\CreateParticipant;
use App\Http\Requests\UpdateParticipant;
use App\Http\Resources\User as UserResource;
use App\Http\Requests\GetMessage;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  chatResource::collection( Auth::user()
                ->chats()
                ->where("status", "<>", chat::DELETED)
                ->get() );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateChat $request )
    {
        
        $validated =  (object) $request->validated() ;
        $chat = new Chat( (array) $validated);
        $chat->save();
        $chat->participants()->attach( 
            array_merge(
                [Auth::id()],
                isset($validated->uid) ? [$validated->uid] :
                ( ( isset($validated->users) and $validated->type !== chat::ONE2ONE) ? $validated->users : [] )
            ), 
            [
                "permissions" => $chat->getDefaultPermission(),
                "time" => gmdate("Y-m-d H:i:s")
            ]
        );

        
        $chat->link  =  $validated->type == chat::OPEN_G ? 
        str_slug( config("appConfig.link-append").$validated->title." $chat->id", "-" ) :
        NULL;
        $chat->save();
        return new chatResource( $chat );

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $cid = null ) 
    {

        $validated = $request->validate([
            'cid' => 'nullable|integer',
        ]);

        if($cid) {
            return new chatResource( chat::searchOrFail($cid)) ;
        }
        else {
            return $this->index();
        }

    }

    public function search(Request $request,  $string ) 
    {

        $validated = (object) $request->validate([
            "string" => "required|string|max:500",
            "deep" => "nullable|boolean"
        ]);

        return  chatResource::collection( 
            chat::where("status", "<>", chat::DELETED)->where(function ($q) {
                $q->whereIn("id", DB::table("participants")->where("uid", "=", Auth::id())->pluck("cid")->toArray() )
                ->orWhere("type", "&", chat::OPEN_G);
            })->where(function ($q) use ($validated, $string) {
                $q->where("title", "like",  "%$string%" )
                ->when( $validated->deep, function ($q) {
                    $q->orWhere("desc", "like", "%$string%" );
                });
            })->get()
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateChat $request )
    {
        
        $validated =  (object) $request->validated() ;
        $chat = chat::searchOrFail($validated->cid);
        if ( $chat->type == chat::ONE2ONE and ( isset($validated->type) and $validated->type !== chat::ONE2ONE and isset($validated->title) or isset($validated->desc)) ) {
            abort( 422, "one to one chats don't have title and description fields");
        }


        
        if ( $this->authorize("update", $chat) ) {
            $chat->fill( (array) $validated );
            $chat->save();
            return response()->json((array)$validated);
        }

        else {
            return abort(403, "You are not allowed to update this chat");
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $cid )
    {
        $chat =  chat::searchOrFail($cid);
        if( $this->authorize("delete", $chat) ) {
            $chat->status = chat::DELETED; //  soft delete
            $chat->save();
            return  response()->json([], 200 ) ;
        }
        return  abort(403, "You are not allowed to delete this chat");
    }

    public function addParticipant(CreateParticipant $request, int $cid )
    {
        $validated = (object) $request->valdated();

        $chat = chat::searchOrFail($cid);
        $user = user::searchOrFail($validated->uid);
        if( $this->authorize("addUser", $chat)) {
            $chat->type = $chat->type === chat::ONE2ONE ? chat::CLOSE_G : $chat->type;
            $chat->participants()->attach($user->id, [
                "permissions" => $validated->permissions ?? $chat->getDeafultPermission(),
                "time" => gmdate("Y-m-d H:i:s")
            ]);
            $chat->save();
        }

        else {
            return abort(403, "You are not allowed to add a user to this chat");
        }
    }

    public function updateParticipant(UpdateParticipant $request, int $cid  ) 
    {
        $validated = (object) $request->valdated();

        $chat = chat::searchOrFail($cid);
        $user = user::searchOrFail($validated->uid);
        if( $this->authorize("updateUser", [$chat, $user]) ) {
            $user->chats()
                 ->updateExistingPivot($chat->id, [
                      'permissions' => $validated->permissions, 
                ]);
        }
        else {
            return abort(403, "You are not allowed to edit a user in this chat");
        }
    }

    public function deleteParticipant(Request $request, int $cid, int $uid ) 
    {

        $chat = chat::searchOrFail($cid);
        $user = user::searchOrFail($uid);
        if( $this->authorize("removeUser", [$chat, $user]) ) {
            $user->chats()->detach($chat->id);
        }
        else {
            return abort(403, "You are not allowed to remove a user in this chat");
        }
    }


    public function viewParticipant(GetMessage $request, int $cid, int $uid = null )
    {

        $chat = chat::searchOrFail($cid);
        if( $this->authorize("viewUser", $chat) ) {
            return is_null($uid) ? 
                    (UserResource::collection($chat->participants()
                    ->when($request->search, function ($q) use($request) {
                        $q->where('name', 'like', "%{$request->search}%")
                        ->when( $request->deep, function ($q) {
                            $q->orWhere("desc", "like", "%{$request->search}%" );
                        }); 
                    })->when($request->interval, function ($q) use($request) {
                        $q->whereBetween('time', explode(",", $request->interval) ); 
                    })->when($request->before, function ($q) use($request) {
                        $q->where('time', '<=', $request->before ); 
                    })->when($request->limit, function ($q) use($request) {
                        $q->limit( $request->limit ); 
                    })->get()
                    )) :
                    new UserResource( $chat->getParticipant($uid) );
        }
        else {
            return abort(403, "cant view this perticipant");
        }
    }





}
