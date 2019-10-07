<?php

namespace App\Http\Requests;


class CreateUser extends BaseFormRequest 
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
            "name" => "bail|required|string|max:100",
            "email" => "bail|required|unique:users|email|max:100",
            "password" => "bail|required|string|max:50",
        ]);
    }

    public function filters () {
        return $this->userFilter;
    }
}
