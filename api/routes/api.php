<?php

use Illuminate\Http\Request;
use App\User;
use App\message;
use App\chat;
use App\participants;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\chat as chatResource;
use App\Http\Resources\message as messageResource;


// middleware(['auth:api'])->
Route::middleware(['auth:api'])->group( function () {

    Route::prefix("/message")->group(function () {

        Route::get("/{cid}", function (Request $request, int $cid) {

            $chat = chat::search($cid);
            if ( $chat and $chat->hasParticipant(Auth::id())  ) {
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
                    return response()->json(["error" =>$chat ? "UNAUTHORIZED ACESS TO CHAT " : "Not found"], $chat ? 401 : 404);
                }
        });

        Route::get("/{cid}/{mid}", function (Request $request, int $cid, int $mid) {

            $chat = chat::search($cid);

            return ( $chat and $chat->hasParticipant(Auth::id())  ) ?   
                    messageResource( message::whereRAW(" mid = $mid and ( state & ".message::DELETED."= 0 ) ") ) :
                    response()->json(["error" =>  "Not found"], 404);
        });

        Route::delete("/{mid}", function (Request $request, int $mid) {
            $id = Auth::id();
                // update state = state + state::deleted  from messaging where uid = $id and mid = $mid
                DB::table("messaging")
                ->where([ ["mid", "=", $mid],  ["uid", "=", $uid]] )
                ->increment( "state", message::DELETED );
        });

        Route::delete("/{cid}/{mid}", function (Request $request, int $cid, int $mid ) {
            $id = Auth::id();
            $message  = message::find($mid);
            $chat = chat::find($cid);
            $participant = $chat->participant($id)->first();

            if( $chat and $chat->hasParticipant($id)  and   (   $participant->pivot
                                                                ->hasPermission(participants::DELETE_USER_MESSAGE) 
                                                                or  $message
                                                                    ->owner()
                                                                    ->first()
                                                                    ->id == $id )
                ) {
                    DB::table("messaging")
                    ->where([ ["mid", "=", $mid], ["cid", "=", $cid],  ["uid", "=", $id]])
                    ->increment( "state", message::DELETED );

                    return response()->json(["sucess" => "done" ]);
                }

                return response()->json(["error" => "Not found \n hasparticipant: ".$chat->hasParticipant($id)."  chatId : $cid messageId : $mid message : $message participant : $participant chat : $chat id: $id " ]);

            

        });


    });

    Route::prefix("/chat")->group(function () {

        Route::get("/{id?}", function (Request $request, int $cid = null ) {
            if($cid) {
                $chat = chat::search( $cid );
                return !$chat->hasParticipant(Auth::id()) ?   response()->json(["error" => "UNAUTHORIZED ACESS TO CHAT"], 401) 
                                                                                :   new chatResource(chat::search($cid)) ;
            }
            else {
                return  chatResource::collection( Auth::user()
                                                        ->chats()
                                                        ->where("status", "<>", chat::DELETED)
                                                        ->get()
                                                        );
            }
            
        });

        Route::get("/{string}", function (Request $request, string $string ) {
            
            return  chatResource::collection( 
                chat::where("status", "<>", chat::DELETED)->where(function ($q) {
                    $q->whereIn("id", DB::table("participants")->where("uid", "=", Auth::id())->pluck("cid")->toArray() )
                    ->orWhere("type", "&", chat::OPEN_G);
                })->where(function ($q) use ($request, $string) {
                    $q->where("title", "like",  "%$string%" )
                    ->when( $request->deep, function ($q) {
                        $q->orWhere("desc", "like", "%$string%" );
                    });
                })->get()
            );
        });

        Route::delete("/{id}", function (Request $request, int $cid) {
            $chat =  chat::find($cid);
            $id = Auth::id();
            if( $chat and $chat->hasParticipant($id)  and   $chat
                                                        ->participant($id)->first()
                                                        ->pivot
                                                        ->hasPermission(participants::DELETE_CHAT) 
            ) {
                $chat->status = chat::DELETED; //  soft delete
                $chat->save();
                return  response()
                        ->json(["sucess" => "CHAT DELETED SUCCESFULLY"] ) ;
            }
            return  response()->json(["error" => "UNAUTHORIZED ACESS TO CHAT"], 401);
        });

        
    });


    Route::prefix("/user")->group(function () {

        // overwrite soft delete method and find method not to search for deleted users

        Route::get("/{id}", function (Request $request, int $id ) {
            return  new UserResource(User::find($id)) ;
        });

        Route::get("/{string}", function (Request $request, string $string ) {
            return  UserResource::collection( 
                User::where( [ ["visibility", "=", User::VISIBLE], ["name", "like",  "%$string%" ] ])
                ->when( $request->deep, function ($q) {
                    $q->orWhere("desc", "like", "%$string%" );
                })->get()
            );
        });

        Route::delete("", function (Request $request) {
            $id = Auth::id();
            $user = User::where([["id", "=", $id],["state", "<>", User::DELETED]])->first();

            if ( $user->id ) {
                $user->state = User::DELETED;
                $user->save();
                return  response()
                        ->json(["sucess" => "Account DELETED SUCCESFULLY"] ) ;
            }
            return  response()->json(["error" => "User not found"], 404);
        });

    });

    Route::prefix("/participant")->group(function () {

        Route::get("/{id}", function (Request $request, int $cid ) {
            $chat =  chat::find($cid);
            if ( $chat->hasParticipant(Auth::id())  ) {
                return UserResource::collection($chat->participants()
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
                );
            }
            else {
                return response()->json(["error" => "UNAUTHORIZED ACESS TO CHAT"], 401) ;
            }
        });


    });

});

Route::post('/register', 'AuthController@register');
Route::post('/login', 'AuthController@login');
Route::get('/login', 'AuthController@login')->name('login');
Route::post('/logout', 'AuthController@logout');

Route::fallback(function () {
    return response()->json(["status"=>404, "description"=> "Resource not found"], 404);
});






