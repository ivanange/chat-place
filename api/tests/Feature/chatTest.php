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
    public function testChats() {

        $response = $this->setToken($this)
                    ->getJson("api/chat");
        
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
                    ->getJson("api/chat/$cid");
        
        $this->assertJsonStringEqualsJsonString( 
            json_encode( $response->getData() ), 
            json_encode( new chatResource( chat::find($cid) ) ) 
        );
    }

}
