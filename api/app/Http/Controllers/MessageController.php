<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\message as messageResource;
use App\User;
use App\message;
use App\chat;
use App\Http\Requests\CreateMessage;
use App\Http\Requests\GetMessage;
use App\Http\Requests\UpdateMessage;
use Illuminate\Support\Facades\Auth;


class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(GetMessage $request, int $cid)
    {

        $chat = chat::searchOrFail($cid);
        if ( $this->authorize("view", [message::class, $chat] ) ) {
                return messageResource::collection( 
                    $chat->messages()->orderBy('time')
                    ->whereRAW("state & ".message::DELETED." = 0 ")
                    ->when($request->time, function ($q) use($request) {
                        $q->where('time', '>=', $request->time); 
                    })->when($request->search, function ($q) use($request) {
                        $q->where('text', 'like', "%{$request->search}%"); 
                    })->when($request->interval, function ($q) use($request) {
                        $q->whereBetween('time', explode(",", $request->interval) ); 
                    })->when($request->before, function ($q) use($request) {
                        $q->where('time', '<=', $request->before ); 
                    })->when($request->edited, function ($q) use($request) {
                        $q->where('state', "&", message::EDITED ); 
                    })->when($request->read, function ($q) use($request) {
                        $q->where('state', "&", message::READ ); 
                    })->when($request->limit, function ($q) use($request) {
                        $q->limit( $request->limit ); 
                    })->get()
                );
            }

        else {
            return abort(403, "You are not allowed to view this message");
        }
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
    public function store(CreateMessage $request )
    {
        $validated = (object) $request->validated();
        $chat = chat::searchOrFail($validated->cid);
        if ( $this->authorize("create", [message::class, $chat]) ) {
            $message = new Message( (array) $validated);
            $message->save();
            $message->chats()->attach($chat->id, [
                "uid"=> Auth::id(),
                "state"=>message::UNREAD,
                "time"=> gmdate("Y-m-d H:i:s"),
            ]);            
            return new messageResource( $message );
        }
        else {
            return abort(403, "You are not alllowed to post in this chat");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $cid, int $mid)
    {
        $chat = chat::searchOrFail($cid);
        $message = message::searchOrFail($chat, $mid);

        return $this->authorize("view", [$message, $chat]) ?   
                new messageResource( $message ) :
                abort(403, "You are not allowed to view this message");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, int $cid)
    {


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateMessage $request )
    {
        $validated = (object) $request->validated();
        $chat = chat::searchOrFail($validated->cid);
        $message = message::searchOrFail($chat, $validated->mid);

        if( $this->authorize("update", [$message, $chat] ) ){
            $message->fill( (array) $validated );
            $message-save();
            $chat->messages()
                 ->updateExistingPivot($message->id, [
                     "state" =>  $state  & message::EDITED ? 0 : message::EDITED,
                 ]);
            
            return response()->json([], 200);
        }

        return abort(403, "You are not allowed to update this message");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $cid, int $mid )
    {
        $message  = message::searchOrFail($mid);
        $chat = chat::searchOrFail($cid);

        if( $this->authorize("delete", [$message, $chat] ) ) {
            DB::table("messaging")
            ->where([ ["mid", "=", $mid], ["cid", "=", $cid]])
            ->whereRAW("state & ".message::DELETED." = 0 ")
            ->increment( "state", message::DELETED );

            return response()->json([], 200);
        }

        return abort(403, "You are not allowed to delete this message");
    }

    public function destroyAll(Request $request, int $mid) 
    {
        DB::table("messaging")
        ->where([ ["mid", "=", $mid],  ["uid", "=", Auth::id() ] ] )
        ->whereRAW("state & ".message::DELETED." = 0 ")
        ->increment( "state", message::DELETED );
    }

}
