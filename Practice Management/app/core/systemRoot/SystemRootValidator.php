<?php

namespace core\systemRoot;

use Validator;

class SystemRootValidator
{

    public function systemRootValidate($inputs)
    {
        $rules = array(
            'system_name' => "required",
            "logo" => "image"
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}