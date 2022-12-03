<?php

namespace core\hospital;

use Validator;

class HospitalValidator
{

    public function validateHospital($inputs)
    {
        $rules = array(
            'name' => "required",
            "logo" => "image"
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}