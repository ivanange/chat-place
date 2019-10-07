<?php

namespace App\Http\Requests;

use App\User;
use App\chat;
use App\message;

class UpdateChat extends BaseFormRequest 
{ 

    use Common;


    public function authorize()
    {
        return !false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge( $this->chats, [
            "cid"=>[ "bail","required",  "integer",],
            "title" => "bail|nullable|string|max:100|min:1",
            "type" => "bail|nullable|integer"
        ]);
    }

    public function filters () {
        return $this->chatFilter;
    }
}
