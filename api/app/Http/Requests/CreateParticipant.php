<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateParticipant extends BaseFormRequest
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
        return array_merge( $this->participants, [
            "permissions" => "bail|nullable|integer"
        ]);
    }
}
