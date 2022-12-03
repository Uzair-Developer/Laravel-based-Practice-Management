<?php

use core\clinic\ClinicRepository;
use core\enums\UserRules;
use core\userLocalization\UserLocalizationRepository;
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent
{

    protected $table = 'users';
    protected $guarded = array('');

    public function clinics()
    {
        return $this->belongsToMany('Clinic', 'user_localizations', 'user_id', 'clinic_id')->withPivot('hospital_id');
    }

    public function schedules()
    {
        return $this->hasMany('PhysicianSchedule', 'user_id');
    }

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
        return self::where('id', '!=', 1)->where('activated', 1)->get()->toArray();
    }

    public static function getAllSystem()
    {
        return self::where('activated', 1)->get()->toArray();
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getNameById($id)
    {
        return self::where('id', $id)->pluck('full_name');
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('full_name');
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getPhysicianIds($in_report = false)
    {
        $data = self::where('user_type_id', UserRules::physician)
            ->where('activated', 1);
        if ($in_report) {
            $data = $data->where('in_report', 1);
        }
        return $data->lists('id');
    }

    public static function getPhysicians($inputs = '', $all = false)
    {
        $data = self::where('user_type_id', UserRules::physician);
        if (!$all) {
            $data = $data->where('activated', 1);
        }
        $ULRepo = new UserLocalizationRepository();
        if ($inputs) {
            if (isset($inputs['name']) && $inputs['name']) {
                $data = $data->whereRaw('LOWER(full_name) LIKE LOWER("%' . $inputs['name'] . '%")');
            }
            if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                $data = $data->where('id', $inputs['physician_id']);
            }
            if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
                $users = $ULRepo->getUsersByHospitalIds(array($inputs['hospital_id']));
                $data = $data->whereIn('id', $users);
            }
            if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                $users = $ULRepo->getUsersByClinicIds(array($inputs['clinic_id']));
                $data = $data->whereIn('id', $users);
            }
            if (isset($inputs['current_status']) && ($inputs['current_status'] || $inputs['current_status'] === '0')) {
                $physicianIds = Physician::getPhysicianIdsByProfileStatus($inputs['current_status']);
                $data = $data->whereIn('id', $physicianIds);
            }
        }
        $data = $data->get()->toArray();
        $clinicRepo = new ClinicRepository();
        foreach ($data as $key => $val) {
            $data[$key]['clinic_name'] = '';
            $clinics = $ULRepo->getClinicsByUserId($val['id']);
            $countClinics = count($clinics);
            foreach ($clinics as $key2 => $val2) {
                if ($key2 + 1 == $countClinics)
                    $data[$key]['clinic_name'] .= $clinicRepo->getName($val2);
                else
                    $data[$key]['clinic_name'] .= $clinicRepo->getName($val2) . ', ';
            }
        }
        return $data;
    }

    public static function getDeactivatedPhysicians($hospital_id = '')
    {
        $userLocalization = new UsersLocalizationClinics();
        $deactivatePhysicians = $userLocalization->getActivePhysiciansByHospitalId($hospital_id, false, true);
        $data = self::where('user_type_id', UserRules::physician)->whereIn('id', $deactivatePhysicians)
            ->where('activated', 0)->get()->toArray();
        $ULRepo = new UserLocalizationRepository();
        $clinicRepo = new ClinicRepository();
        foreach ($data as $key => $val) {
            $data[$key]['clinic_name'] = '';
            $clinics = $ULRepo->getClinicsByUserId($val['id']);
            $countClinics = count($clinics);
            foreach ($clinics as $key2 => $val2) {
                if ($key2 + 1 == $countClinics)
                    $data[$key]['clinic_name'] .= $clinicRepo->getName($val2);
                else
                    $data[$key]['clinic_name'] .= $clinicRepo->getName($val2) . ', ';
            }
        }
        return $data;
    }

    public static function getPhysiciansId($all = false, $getHisId = false, $hospital = '')
    {
        $data = self::where('user_type_id', UserRules::physician);
        if (!$all) {
            $data = $data->where('activated', 1);
        }
        if ($getHisId) {
            if($hospital == 'riyadh') {
                $data = $data->whereNotNull('his_id_2')->lists('his_id_2');
            } else {
                $data = $data->whereNotNull('his_id')->lists('his_id');
            }
        } else {
            $data = $data->lists('id');
        }
        return $data;
    }

    public static function getByIds($ids, $user_experience_id = '', $user_specialty_id = '', $bookable = false, $activate = true)
    {
        return self::whereIn('id', $ids)->where(function ($q) use ($activate, $bookable, $user_experience_id, $user_specialty_id) {
            if ($activate) {
                $q->where('activated', 1);
            }
            if ($user_experience_id) {
                $q->where('user_experience_id', $user_experience_id);
            }
            if ($user_specialty_id) {
                $q->where('user_specialty_id', $user_specialty_id);
            }
            if ($bookable) {
                $q->where('bookable', 1);
            }
        })->get()->toArray();
    }

    public static function getPhysicianByClinicId($clinic_id)
    {
        $ULRepo = new UserLocalizationRepository();
        $allPhysiciansIds = User::getPhysiciansId();
        return $ULRepo->getPhysiciansByClinicId($allPhysiciansIds, $clinic_id);
    }

    public static function getPhysicianByClinicIds($clinic_ids)
    {
        $allPhysiciansIds = User::getPhysiciansId();
        $ULRepo = new UserLocalizationRepository();
        $physiciansIds = array();
        foreach ($clinic_ids as $key => $val) {
            $physiciansIds = array_merge($physiciansIds, $ULRepo->getPhysiciansByClinicId($allPhysiciansIds, $val));
        }
        return array_unique($physiciansIds);
    }

    public static function checkUsernameExist($user_name)
    {
        return self::where('user_name', $user_name)->first();
    }

    public static function getByRule($rule)
    {
        return self::where('user_type_id', $rule)->where('activated', 1)->get()->toArray();
    }

    public static function getAllHisId()
    {
        return self::where('user_type_id', 7)->where('activated', 1)->lists('his_id_2');
    }

    public static function checkHisExist($his_id, $activate = false)
    {
        $data = self::where('his_id', $his_id);
        if ($activate) {
            $data = $data->where('activated', 1);
        }
        return $data->first();
    }
}
