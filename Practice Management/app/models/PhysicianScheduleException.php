<?php

class PhysicianScheduleException extends Eloquent
{
    protected $table = 'physician_schedule_exception';
    protected $guarded = array('');

    public static $rules = array(
        "physician_id" => "required",
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = '')
    {
        $data = self::where(function ($q) use ($inputs) {
            if (isset($inputs['hospital_id']) && $inputs['hospital_id'] && empty($inputs['clinic_id'])) {
                $physicianArray = UsersLocalizationClinics::getActivePhysiciansByHospitalId($inputs['hospital_id']);
                $q->whereIn('user_id', $physicianArray);
            }
            if (isset($inputs['clinic_id']) && $inputs['clinic_id'] && empty($inputs['user_id'])) {
                $physicianArray = UsersLocalizationClinics::getActivePhysiciansByClinicId($inputs['clinic_id']);
                $q->whereIn('user_id', $physicianArray);
            }
            if (isset($inputs['user_id']) && $inputs['user_id']) {
                $q->where('user_id', $inputs['user_id']);
            }
            if (isset($inputs['start_date']) && $inputs['start_date']) {
                $q->where('date', '>=', $inputs['start_date']);
            }
            if (isset($inputs['end_date']) && $inputs['end_date']) {
                $q->where('date', '<=', $inputs['end_date']);
            }
        });
        if (isset($inputs['paginate']) && $inputs['paginate']) {
            $data = $data->paginate($inputs['paginate']);
        } else {
            $data = $data->get();
        }

        if (!isset($inputs['withoutDetails'])) {
            foreach ($data as $key => $val) {
                $data[$key]['physician_name'] = User::getName($val['user_id']);
                $data[$key]['clinic_name'] = Clinic::getNameById($val['clinic_id']);
            }
        }
        return $data;
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function checkByClinic_Physician_Date($clinic_id = '', $user_id, $date)
    {
        $data = self::where('user_id', $user_id);
        if ($clinic_id) {
            $data = $data->where('clinic_id', $clinic_id);
        }
        return $data->where('date', $date)->first();
    }

}
