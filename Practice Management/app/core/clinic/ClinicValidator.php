<?php

namespace core\clinic;

use Validator;

class ClinicValidator
{

    public function validateClinic($inputs)
    {
        $rules = array(
            'name' => "required",
            "hospital_id" => "required"
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}