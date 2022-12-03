<?php

namespace core\diagnosis\symptom;

use Validator;

class SymptomValidator
{

    public function symptomValidate($inputs)
    {
        $rules = array(
            'name' => "required"
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}