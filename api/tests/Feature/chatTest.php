<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\auth as Auth;
use App\User;
use App\chat;
use App\participants as permissions;
use App\Http\Resources\chat as chatResource;
use Faker\Generator as Faker;


class chatTest extends TestCase
{
    use Auth;
    /**
     * A basic feature test example.
     *
     * @return void
     * *
     */

    public $baseUrl = "api/chats";

    public function testChatGetAuth () {
        $cid = DB::table("participants")->where("uid", "<>", $this->id)
                ->get()
                ->random()
                ->cid;

       $response =  $this
        ->setToken()
        ->getJSon($this->baseUrl."/$cid")->assertStatus(200);
    }

    public function testChats() {

        $response = $this->setToken()
                    ->getJson($this->baseUrl);

        
        $this->assertEquals( 
            collect( 
                $response
                ->getData()
            )->count(),
            
            user::find($this->id)
                ->chats()
                ->where("status", "<>", chat::DELETED)
                ->get()
                ->count()
        );
    }

    public function testChat() {

        $cid = user::find($this->id)
                    ->chats()
                    ->where("status", "<>", chat::DELETED)
                    ->pluck("id")
                    ->random();

        $response = $this->setToken()
                    ->getJson($this->baseUrl."/$cid");
        
        $this->assertJsonStringEqualsJsonString( 
            json_encode( $response->getData() ), 
            json_encode( new chatResource( chat::find($cid) ) ) 
        );
    }

    public function testDeleteChat() {

        // change this and insert test data remove it later
        $credentials = [
            "email" => "echo@delete.dev",
            "password" => "secret password"
        ];

        $cid = chat::where("status", "<>", chat::DELETED)
                    ->get()
                    ->pluck("id")
                    ->random();

        $user = user::firstOrNew( [
                        "name"=>"Arthur", 
                        "state"=> user::AUTHENTICATED,
                        "link"=> "abcdefghijklmnopqrstuvwxyz"
                    ] );
        $user->fill( $credentials)->save();

        $user->chats()
            ->attach(  $cid, [
                "permissions" => permissions::DELETE_CHAT,
                "time" => "1976-07-02 09:07:26",
            ]);

        $this
        ->setToken($credentials)
        ->json("delete", $this->baseUrl."/$cid")
        ->assertOk();

        $this->assertNull( chat::search($cid) );
    }

}
