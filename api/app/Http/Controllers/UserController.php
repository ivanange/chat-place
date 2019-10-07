<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User as UserResource;
use App\Http\Requests\CreateUser;
use App\Http\Requests\UpdateUser;


class UserController extends Controller
{

    public function __construct()
    {
        auth()->setDefaultDriver('api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, string $string )
    {
        $validated = (object) $request->validate([
            "deep" => "nullable|boolean"
        ]);

        return  UserResource::collection( 
            User::where( [ 
                ["visibility", "=", User::VISIBLE], 
                ["state", "=", User::AUTHENTICATED], 
                ["name", "like",  "%$string%" ]
            ])
            ->when( $validated->deep ?? false, function ($q) {
                $q->orWhere("desc", "like", "%$string%" );
            })->get()
        );
    }

    public function me()
    {
        return  new UserResource( Auth::user() );
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
    public function store(CreateUser $request )
    {
        $validated = (object) $request->validated();

        $user = new user((array) $validated );
        $user->state = user::AUTHENTICATED;
        $user->link = bcrypt("this");
        $user->save();
        $user->link = str_slug( config("appConfig.link-append").$validated->name." $user->id", "-" ) ;
        $user->save();
        $token = auth()->login($user);
        return $this->respondWithToken($token);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, int $uid )
    {
        return  new UserResource(User::searchOrFail($uid)) ;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUser $request )
    {
        $validated = (object) $request->validated();
        unset($validated->email);
        $user = Auth::user();
            $user->fill((array)$validated);
            $user->save();
     

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy () 
    {
        $user = Auth::user();
        $user->state = User::DELETED;
        $user->save();

    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
