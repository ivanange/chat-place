<?php

namespace App\Http\Requests;

use App\User;
use App\chat;
use App\message;

class CreateMessage extends BaseFormRequest 
{

    use Common;

    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *        if(!is_int($this->cid)) return false;
     *  $chat = chat::search($this->cid);
     *  return $chat ? $chat->hasParticipant(Auth::id()) : false;
     * @return array
     */
    public function rules()
    {
        return array_merge( $this->messages, [
            "cid" => [  
                        "required",
                        "integer", 
                    ],
            "type" => "bail|required|integer",
        ]);
    }

    public function filters () {
        return $this->messageFilter;
    }
}
