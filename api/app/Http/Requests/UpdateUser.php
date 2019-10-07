<?php

namespace App\Http\Requests;


class UpdateUser extends BaseFormRequest 
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
        return array_merge( $this->users, [
            "name" => "bail|string|max:100",
            "password" => "bail|string|max:50",
        ]);
    }

    public function filters () {
        return $this->userFilter;
    }
}
