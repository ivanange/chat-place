<?php

namespace App\Http\Requests;

use App\User;
use App\chat;
use App\message;

class CreateChat extends BaseFormRequest 
{

    use Common;

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge( $this->chats, [
            "type" => "bail|required|integer",
            "title" => "bail|required_unless:type,".chat::ONE2ONE."|string|max:100",
            "uid" => "bail|nullable|integer",
            "users.*" => [ "bail", "integer", $this->searchUser]
        ]);
    }

    public function filters () {
        return $this->chatFilter;
    }
}
