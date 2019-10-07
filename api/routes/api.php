<?php

use Illuminate\Http\Request;
use App\User;
use App\message;
use App\chat;
use App\participants;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\chat as chatResource;
use App\Http\Resources\message as messageResource;
use App\Http\Requests\CreateMessage;
use App\Http\Requests\GetMessage;
use App\Http\Requests\UpdateMessage;
use App\Http\Requests\CreateChat;
use App\Http\Requests\UpdateChat;
use App\Http\Requests\CreateUser;
use App\Http\Requests\UpdateUser;
use App\Http\Requests\CreateParticipant;
use App\Http\Requests\UpdateParticipant;


// middleware(['auth:api'])->
Route::middleware(['auth:api'])->group( function () {

    Route::prefix("/chats")->group(function () {

        Route::post("", "ChatController@store" );
        Route::post("/", "ChatController@store" );
        Route::put("", "ChatController@update");
        Route::put("/", "ChatController@update");
        Route::get("", "ChatController@show");
        Route::get("/{cid?}", "ChatController@show");
        Route::get("/{string}", "ChatController@search");
        Route::delete("/{cid}", "ChatController@destroy");

        Route::prefix("/{cid}/participants")->group(function () {
            Route::post("", "ChatController@addParticipant");
            Route::post("/", "ChatController@addParticipant");
            Route::put("", "ChatController@updateParticipant");
            Route::put("/", "ChatController@updateParticipant");
            Route::delete("/{uid}", "ChatController@deleteParticipant");
            Route::get("", "ChatController@viewParticipant");
            Route::get("/{uid?}", "ChatController@viewParticipant");
        });
        
    });

    Route::prefix("/messages")->group(function () {

        Route::post("", "MessageController@store");
        Route::post("/", "MessageController@store");
        Route::put("", "MessageController@update");
        Route::put("/", "MessageController@update");
        Route::get("/{cid}", "MessageController@index");
        Route::get("/{cid}/{mid}", "MessageController@show");
        Route::delete("/{mid}", "MessageController@destroyAll");
        Route::delete("/{cid}/{mid}", "MessageController@destroy");

    });

    Route::prefix("/users")->group(function () {
        Route::put("", "UserController@update");
        Route::put("/", "UserController@update");
        Route::get("", "UserController@me");
        Route::get("/{id}", "UserController@show");
        Route::get("/{string}", "UserController@index");
        Route::delete("", "UserController@destroy");

    });

});

Route::post('/register', "UserController@store");
Route::post('/user', "UserController@store");
Route::post('/login', 'AuthController@login')->name('login');
Route::get('/login', 'AuthController@login')->name('login');
Route::post('/logout', 'AuthController@logout');

Route::fallback(function () {
    return response()->json(["status"=>404, "description"=> "Resource not found"], 404);
});

Route::get("/test", function (Request $request) {
    $user = user::first();
    $user->chats()->first()->pivot->permissions = 200;
    $user->save();
    return response()->json($user->chats()->get(), 200);
});


