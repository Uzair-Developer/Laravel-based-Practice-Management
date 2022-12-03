<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\enums\UserRules;

class HisBillDetail extends Eloquent
{
    protected $table = 'dbo.OPCompanyBillDetail_V';
    protected $guarded = array('');
    protected $connection = 'sqlsrv3';
    public $timestamps = false;

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll()
    {
        return self::all()->toArray();
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function getCount($inputs)
    {
        $user = Sentry::getUser();
        $data = self::where(function ($q) use ($inputs, $user) {
            $physiciansHisArray = User::getAllHisId();
            $q->whereIn('DoctorId', $physiciansHisArray);
            if ($user->user_type_id == UserRules::physician) {
                $physicianData = User::getById($user->id);
                $q->where('DoctorId', $physicianData['his_id']);
            }
            if (isset($inputs['date_from']) && $inputs['date_from']) {
                $q->where('Billdatetime', '>=', $inputs['date_from'] . ' 00:00:00.000');
            }
            if (isset($inputs['date_to']) && $inputs['date_to']) {
                $q->where('Billdatetime', '<=', $inputs['date_to'] . ' 23:59:59.000');
            }
            if (isset($inputs['clinic_id']) && $inputs['clinic_id'] && empty($inputs['physician_id'])) {
                $physicianHISIds = UsersLocalizationClinics::getHISActivePhysiciansByClinicId($inputs['clinic_id']);
                $q->whereIn('DoctorId', $physicianHISIds);
            }
            if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                $physicianData = User::getById($inputs['physician_id']);
                $q->where('DoctorId', $physicianData['his_id']);
            }
        })->count();
        return $data;
    }

    public static function checkPatientFees($patient_reg_no, $date)
    {
        return self::where('RegistrationNo', $patient_reg_no)
            ->where('Billdatetime' , '>=', $date . ' 00:00:00.000')
            ->where('Billdatetime' , '<=', $date . ' 23:59:59.000')
            ->first();
    }

}
