<?php

namespace core\physicianSchedule;


use Clinic;
use core\clinicSchedule\ClinicScheduleRepository;
use PhysicianSchedule;
use core\authorized\Authorized;
use core\user\UserRepository;
use core\userLocalization\UserLocalizationRepository;
use PhysiciansScheduleClinics;
use UsersLocalizationClinics;

class PhysicianScheduleRepository
{

    public function save($inputs)
    {
        return PhysicianSchedule::create($inputs)->toArray();
    }

    public function update($inputs, $id)
    {
        return PhysicianSchedule::where('id', $id)->update($inputs);
    }

    public function getAll()
    {
        if (Authorized::isSupperAdmin()) {
            $data = PhysiciansScheduleClinics::paginate(20);
        } else {
            $userRepo = new UserRepository();
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($userRepo->getCurrentUser()->id);
            if ($hospitals) {
                $clinics = Clinic::whereIn('hospital_id', $hospitals)->lists('id');
            } else {
                $clinics = $ULRepo->getClinicsByUserId($userRepo->getCurrentUser()->id);
            }
            $data = PhysiciansScheduleClinics::whereIn('clinic_id', $clinics)->paginate(20);
        }
        $csRepo = new ClinicScheduleRepository();
        foreach ($data as $key => $val) {
            $schedule = $csRepo->getById($val['clinic_schedule_id']);
            $data[$key]['clinic_schedule_name'] = $schedule['name'];
        }
        return $data;
    }

    public function getAllWithFilter($inputs)
    {
        if (Authorized::isSupperAdmin()) {
            $data = PhysiciansScheduleClinics::where(function ($q) use ($inputs) {
                if (isset($inputs['hospital_id']) && $inputs['hospital_id'] && empty($inputs['clinic_id'])) {
                    $physicianArray = UsersLocalizationClinics::getActivePhysiciansByHospitalId($inputs['hospital_id']);
                    $q->whereIn('user_id', $physicianArray);
                }
                if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                    $q->where('clinic_id', $inputs['clinic_id']);
                }
                if (isset($inputs['user_id']) && $inputs['user_id']) {
                    $q->where('user_id', $inputs['user_id']);
                }
                if (isset($inputs['publish']) && $inputs['publish']) {
                    $q->where('publish', $inputs['publish']);
                }
                if (isset($inputs['start_date']) && $inputs['start_date']) {
                    $q->where('start_date', '>=', $inputs['start_date']);
                }
                if (isset($inputs['end_date']) && $inputs['end_date']) {
                    $q->where('end_date', '<=', $inputs['end_date']);
                }
            })->paginate(20);
        } else {
            $userRepo = new UserRepository();
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($userRepo->getCurrentUser()->id);
            if ($hospitals) {
                $clinics = Clinic::whereIn('hospital_id', $hospitals)->lists('id');
            } else {
                $clinics = $ULRepo->getClinicsByUserId($userRepo->getCurrentUser()->id);
            }
            $data = PhysiciansScheduleClinics::where(function ($q) use ($inputs) {
                if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                    $q->where('clinic_id', $inputs['clinic_id']);
                }
                if (isset($inputs['user_id']) && $inputs['user_id']) {
                    $q->where('user_id', $inputs['user_id']);
                }
                if (isset($inputs['start_date']) && $inputs['start_date']) {
                    $q->where('start_date', '>=', $inputs['start_date']);
                }
                if (isset($inputs['end_date']) && $inputs['end_date']) {
                    $q->where('end_date', '<=', $inputs['end_date']);
                }
            })->whereIn('clinic_id', $clinics)->paginate(20);
        }
        $csRepo = new ClinicScheduleRepository();
        foreach ($data as $key => $val) {
            $schedule = $csRepo->getById($val['clinic_schedule_id']);
            $data[$key]['clinic_schedule_name'] = $schedule['name'];
        }
        return $data;
    }

    public function getById($id)
    {
        return PhysicianSchedule::where('id', $id)->first();
    }

    public function getHospitalId($id)
    {
        return PhysicianSchedule::where('id', $id)->pluck('hospital_id');
    }

    public function getByHospitalId($id)
    {
        return PhysicianSchedule::where('hospital_id', $id)->get()->toArray();
    }

    public function delete($id)
    {
        return PhysicianSchedule::where('id', $id)->delete();
    }

    public function checkExist($physicianId, $clinicScheduleId, $id = null, $date = null)
    {
        if ($id) {
            $data = PhysicianSchedule::where('id', '!=', $id)
                ->where('clinic_schedule_id', $clinicScheduleId);
            if ($date) {
                $data = $data->where('start_date', '<=', $date)->where('end_date', '>=', $date);
            }
            return $data->where('user_id', $physicianId)->get()->toArray();
        }
        $data = PhysicianSchedule::where('clinic_schedule_id', $clinicScheduleId);
        if ($date) {
            $data = $data->where('start_date', '<=', $date)->where('end_date', '>=', $date);
        }
        return $data->where('user_id', $physicianId)->get()->toArray();
    }

}