<?php

namespace core\diagnosis\clinic;

use Validator;

class ClinicValidator
{

    public function clinicValidate($inputs)
    {
        $rules = array(
            'name' => "required"
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}