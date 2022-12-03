<?php
namespace core\user;

use Validator;

class UserValidator
{
    public function validateUser($inputs, $edit = false, $id = '')
    {
        $rules = array(
//            "full_name" => "required",
            "user_name" => "required|min:5|unique:users",
            "email" => "email|unique:users",
            "image_url" => "image",
        );
        if($edit) {
            $rules['user_name'] = "required|min:5|unique:users,user_name,$id";
            $rules['email'] = "min:5|unique:users,email,$id";
            $rules['password'] = "confirmed";
        } else {
            $rules['password'] = "required|confirmed";
        }

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

    public function validateLogin($inputs)
    {
        $rules = array(
            "user_name" => "required",
            "password" => "required"
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }
}