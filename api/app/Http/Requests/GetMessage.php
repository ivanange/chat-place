<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetMessage extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
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
        return [
            "before" => "bail|nullable|date",
            "time" => "bail|nullable|date",
            "search" => "bail|nullable|string|max:500",
            "edited" => "bail|nullable|boolean",
            "read" => "bail|nullable|boolean",
            "limit" => "bail|nullable|integer",
            "interval" => [
                "bail",
                "nullable",
                "string",
                function ($attr, $val, $fail) {
                    foreach(explode( ", ", $val)  as  $date ) {
                        if( new DateTime($date) === false ) {
                            $fail("$attr contains date $date which is not a valid date");
                        }
                    }
                }
            ]
        ];
    }
}
