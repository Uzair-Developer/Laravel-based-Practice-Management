<?php

namespace core\clinicSchedule;

use Validator;

class ClinicScheduleValidator
{

    public function validateClinicSchedule($inputs, $isEdit = false)
    {
        $rules = array(
//            'name' => "required",
            "hospital_id" => "required",
            "clinic_id" => "required",
        );
        if ($isEdit) {
            $rules = array(
//                'name' => "required",
            );
        }
        $validation = Validator::make($inputs, $rules);
        return $validation;
    }

}