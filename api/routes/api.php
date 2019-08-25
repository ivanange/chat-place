<?php

use Illuminate\Http\Request;
use App\User;
use App\message;
use App\chat;
use App\Http\Resources\User as UserResource;
use App\Http\Resources\chat as chatResource;
use App\Http\Resources\message as messageResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// middleware(['auth:api'])->
Route::group([], function () {

    Route::prefix("/message")->group(function () {

        Route::get("/{id}", function (Request $request, int $cid) {
            return messageResource::collection( 
                chat::find($cid)->messages()->when($request->time, function ($q) use($request) {
                    $q->where('time', '>=', $request->time); 
                })->when($request->search, function ($q) {
                    $q->where('text', 'like', "%{$request->search}%"); 
                })->get());
        });

        Route::get("/{string}", function (Request $request, string $string) {
            return messageResource::collection( 
                chat::find($cid)->messages()->when($request->time, function ($q) {
                    $q->where('time', '>=', $request->time); 
                })->when($request->search, function ($q) {
                    $q->where('text', 'like', "%{$request->search}%"); 
                })->get());
        });

    });

    Route::prefix("/chat")->group(function () {

        Route::get("/{id?}", function (Request $request, int $uid = null ) {
            return  chatResource::collection( User::find($uid ?? Auth::getAuthIdentifier() )->chats );
        });

        Route::get("/{string?}", function (Request $request, string $string = null ) {
            return  chatResource::collection( User::find($uid ?? Auth::getAuthIdentifier() )->chats );
        });
/*
        ->when($request->list, function ($q) {
            $q->where() })
*/
        
    });

    Route::prefix("/user")->group(function () {

        Route::get("/{id?}", function (Request $request, int $cid = null) {
            return messageResource::collection( $cid ? chat::find($cid) : chat::all()  );
        });
    });

    Route::prefix("/participant")->group(function () {

        Route::get("/{id?}", function (Request $request, int $cid = null) {
            return messageResource::collection( $cid ? chat::find($cid) : chat::all() );
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


            /*

        messageResource::collection( chat::find($request->cid)->messages );
->load(['owners' => function ($q) use ($request) {
                $q->where('cid', $request->cid);
            }])
response()->json([ 
            "data"=>chat::find($request->cid)->messages
        ]);
        
        //message::find(1)->owner(11)->get()
        //message::find(55)->owner(11)->first()->pivot->uid
        //DB::select("select uid from messaging where mid=55 and cid=11")[0]->uid
                //chat::find($request->cid)->messages->load('owner:id') ] );
                        //messageResource::collection
        */


