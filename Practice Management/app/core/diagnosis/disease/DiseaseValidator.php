<?php

namespace core\diagnosis\disease;

use Validator;

class DiseaseValidator
{

    public function diseaseValidate($inputs)
    {
        $rules = array(
            'name' => "required"
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}