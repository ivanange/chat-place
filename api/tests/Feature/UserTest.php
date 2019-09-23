<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use Tests\auth as Auth;

class UserTest extends TestCase
{
    use Auth;

    public function testAuth()
    {
        $response = $this->withHeaders(["Accept"=>"application/json"])->get('api/chat/');

        $response->assertStatus(401);

        $response = $this->post("api/login", ["email"=>"me@example.com", "password"=>"secret password"]);

        $response->assertOk()->assertJsonStructure(["access_token","expires_in","token_type"]);
        
    }




}
