<?php

namespace core\physician;

use Validator;

class PhysicianValidator
{

    public function validatePhysician($inputs)
    {
        $rules = array(
            'first_name' => "required",
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}