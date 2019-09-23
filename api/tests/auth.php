<?php

namespace Tests;


trait Auth {

    public $id = 1;
    public function login () {
        return $this
            ->postJson("api/login", ["email"=>"me@example.com", "password"=>"secret password"])->getData()
            ->access_token;
    }

    public function setToken( $_this = null ) {
        $_this = $_this ?? $this;
        $token = $this->login();

        return $_this
                ->withHeaders(["Accept"=>"application/json", "Authorization"=>"Bearer $token"]);
    }
}