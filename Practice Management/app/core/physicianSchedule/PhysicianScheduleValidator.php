<?php

namespace core\physicianSchedule;

use Validator;

class PhysicianScheduleValidator
{

    public function validatePhysicianSchedule($inputs)
    {
        $rules = array(
            'user_id' => "required",
            'clinic_id' => "required",
            'clinic_schedule_id' => "required",
            'start_date' => "required",
            'end_date' => "required",
        );

        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}