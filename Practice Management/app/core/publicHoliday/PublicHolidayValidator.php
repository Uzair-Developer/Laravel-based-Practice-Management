<?php

namespace core\publicHoliday;

use Validator;

class PublicHolidayValidator
{

    public function validatePublicHoliday($inputs)
    {
        $rules = array(
            'name' => "required",
            'from_date' => "required",
            'to_date' => "required"
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}