<?php

namespace core\diagnosis\organ;

use Validator;

class OrganValidator
{

    public function organValidate($inputs)
    {
        $rules = array(
            'name' => "required"
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}