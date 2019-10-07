<?php

namespace App\Http\Requests;


trait Common {


    public $messages = [
        
        "text" => "bail|nullable|string|max:5000",
    ];

    public $messageFilter = [
        "text" => "trim|escape",
        "type" => "cast:integer",
    ];

    public $chats = [
        "desc" => "bail|nullable|string|max:500",
    ];

    public $chatFilter = [
        "title" => "trim|escape|capitalize",
        "type" => "cast:integer",
        "desc" => "trim|escape",
    ];

    public $users = [
        "phone" => "bail|nullable|numeric|unique:users|max:50|min:9",
        "desc" => "bail|nullable|string|max:500",
        "settings" => "bail|nullable|string|json|max:2000",
        "visibility" => "bail|integer"
    ];

    public $userFilter = [
        "name" => "trim|escape|capitalize",
        "email" => "trim|escape|lowercase",
        "desc" => "trim|escape",
        "visibility" => "cast:integer"
    ];

    public $participants = [
        "cid" => [
            "bail",
            "required",
            "integer",
        ],

        "uid" => [
            "bail",
            "required",
            "integer",
        ],

        
    ];

    public static function searchMessage($attribute, $value, $fail, $cid)
    {
        if(!( is_int($value) and message::search($cid, $value) ) ) {
            $fail("$attribute:$value does not reference an existing message id in chat $cid"); 
        }        
        
    }

    public static  function searchChat($attribute, $value, $fail)
    {
        if(!( is_int($value) and chat::search( $value) ) ) {
            $fail("$attribute:$value does not reference an existing chat id"); 
        }        
        
    }

    public static  function searchUser($attribute, $value, $fail)
    {
        if(!( is_int($value) and user::search( $value) ) ) {
            $fail("$attribute:$value does not reference an existing user id"); 
        }        
        
    }

}