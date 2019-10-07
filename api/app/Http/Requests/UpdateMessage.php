<?php

namespace App\Http\Requests;

use App\User;
use App\chat;
use App\message;

class UpdateMessage extends BaseFormRequest 
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
        return array_merge( $this->messages, [
            "cid" => [  "bail",
                        "required",
                        "integer", 
            ],
            "mid" => [  "bail",
                        "required",
                        "integer", 
                    ]
        ]);
    }

    public function filters () {
        return $this->messageFilter;
    }
}
