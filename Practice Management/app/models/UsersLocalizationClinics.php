<?php

use core\enums\UserRules;

class UsersLocalizationClinics extends Eloquent
{
    protected $table = 'users_localization_clinics';
    protected $guarded = array('');

    public static function getActivePhysiciansByClinicId($clinicId, $in_report = false, $withDeactivate = false, $getAll = false)
    {
        $data = self::where('clinic_id', $clinicId);
        if (!$withDeactivate) {
            $data = $data->where('activated', 1);
        }
        $data = $data->where('user_type_id', UserRules::physician);
        if ($in_report) {
            $data = $data->where('in_report', 1);
        }
        if ($getAll) {
            return $data->get();
        } else {
            return $data->lists('user_id');
        }
    }

    public static function getActivePhysiciansByHospitalId($hospitalId, $in_report = false, $getDeactivate = false, $getActivateAndDeactivate = false)
    {
        $data = self::where('hospital_id', $hospitalId);
        if (!$getActivateAndDeactivate) {
            if ($getDeactivate) {
                $data = $data->where('activated', 0);
            } else {
                $data = $data->where('activated', 1);
            }
        }
        $data = $data->where('user_type_id', UserRules::physician);
        if ($in_report) {
            $data = $data->where('in_report', 1);
        }
        return $data->lists('user_id');
    }

    public static function getHISActivePhysiciansByClinicId($clinicId, $in_report = false)
    {
        $data = self::where('clinic_id', $clinicId)
            ->where('activated', 1)
            ->where('user_type_id', UserRules::physician);
        if ($in_report) {
            $data = $data->where('in_report', 1);
        }
        return $data->lists('his_id');
    }

    public static function getHISActivePhysiciansByHospitalId($hospitalId, $in_report = false, $getDeactivate = false)
    {
        $data = self::where('hospital_id', $hospitalId);
        if ($getDeactivate) {
            $data = $data->where('activated', 0);
        } else {
            $data = $data->where('activated', 1);
        }
        $data = $data->where('user_type_id', UserRules::physician);
        if ($in_report) {
            $data = $data->where('in_report', 1);
        }
        return $data->lists('his_id');
    }
}
