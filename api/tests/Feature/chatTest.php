<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\auth as Auth;
use App\User;
use App\chat;
use App\Http\Resources\chat as chatResource;


class chatTest extends TestCase
{
    use Auth;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    public $baseUrl = "api/chat";

    public function testChatGetAuth () {
        $cid = user::where("id", "<>", $this->id)
                ->get()
                ->random()
                ->chats()
                ->first()->id;

       $response =  $this
        ->setToken($this)
        ->getJSon($this->baseUrl."/$cid")->assertStatus(401);
    }

    public function testChats() {

        $response = $this->setToken($this)
                    ->getJson($this->baseUrl);
        
        $this->assertEquals( 
            collect( 
                $response
                ->getData()
            )->count(),
            
            user::find($this->id)
                ->chats()
                ->get()
                ->count()
        );
    }

    public function testChat() {

        $cid = user::find($this->id)->chats()->pluck("id")->random();

        $response = $this->setToken($this)
                    ->getJson($this->baseUrl."/$cid");
        
        $this->assertJsonStringEqualsJsonString( 
            json_encode( $response->getData() ), 
            json_encode( new chatResource( chat::find($cid) ) ) 
        );
    }

    public function testDeleteChat() {
        $cid = user::find($this->id)
                ->chats()
                ->get()
                ->pluck("id")
                ->random();
        $this
        ->setToken($this)
        ->json("delete", $this->baseUrl."/$cid")
        ->assertOk();

        $this->assertNull( chat::search($cid)->id );
    }

}
