<?php

namespace Tests;


trait Auth {

    public $id = 1;
    public function login ( array $credentials = null ) {
        return $this
            ->postJson("api/login", $credentials ?? ["email"=>"me@example.com", "password"=>"secret password"])->getData()
            ->access_token;
    }

    public function setToken( array $credentials  = null) {
        $_this = $_this ?? $this;
        $token = $this->login($credentials );

        return $_this
                ->withHeaders(["Accept"=>"application/json", "Authorization"=>"Bearer $token"]);
    }
}