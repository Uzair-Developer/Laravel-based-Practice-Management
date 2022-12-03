<?php

namespace core\clinicSchedule;


use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Clinic;
use ClinicSchedule;
use core\clinic\ClinicRepository;
use core\hospital\HospitalRepository;
use core\userLocalization\UserLocalizationRepository;

class ClinicScheduleRepository
{

    public function save($inputs)
    {
        return ClinicSchedule::create($inputs)->toArray();
    }

    public function update($inputs, $id)
    {
        return ClinicSchedule::where('id', $id)->update($inputs);
    }

    public function getAll()
    {
        $user = Sentry::getUser();
        if ($user->user_type_id == 1) {
            $data = ClinicSchedule::orderBy('hospital_id')->get()->toArray();
        } else {
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($user->id);
            if ($hospitals) {
                $clinics = Clinic::whereIn('hospital_id', $hospitals)->lists('id');
            } else {
                $clinics = $ULRepo->getClinicsByUserId($user->id);
            }
            $data = ClinicSchedule::whereIn('clinic_id', $clinics)->orderBy('hospital_id')->get()->toArray();
        }
        $hospitalRepo = new HospitalRepository();
        $clinicRepo = new ClinicRepository();
        foreach ($data as $key => $val) {
            $data[$key]['hospital_name'] = $hospitalRepo->getName($val['hospital_id']);
            $data[$key]['clinic_name'] = $clinicRepo->getName($val['clinic_id']);
        }
        return $data;
    }

    public function getAllWithFilter($inputs)
    {
        $user = Sentry::getUser();
        if ($user->user_type_id == 1) {
            $data = ClinicSchedule::where(function ($q) use ($inputs) {
                if ($inputs['name']) {
                    $q->where('name', $inputs['name']);
                }
                if ($inputs['hospital_id']) {
                    $q->where('hospital_id', $inputs['hospital_id']);
                }
                if ($inputs['clinic_id']) {
                    $q->where('clinic_id', $inputs['clinic_id']);
                }
                if ($inputs['start_date']) {
                    $q->where('start_date', '>=', $inputs['start_date']);
                }
                if ($inputs['end_date']) {
                    $q->where('end_date', '<=', $inputs['end_date']);
                }
            })->orderBy('hospital_id')->get()->toArray();
        } else {
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($user->id);
            if ($hospitals) {
                $clinics = Clinic::whereIn('hospital_id', $hospitals)->lists('id');
            } else {
                $clinics = $ULRepo->getClinicsByUserId($user->id);
            }
            $data = ClinicSchedule::where(function ($q) use ($inputs) {
                if ($inputs['name']) {
                    $q->where('name', $inputs['name']);
                }
                if ($inputs['hospital_id']) {
                    $q->where('hospital_id', $inputs['hospital_id']);
                }
                if ($inputs['clinic_id']) {
                    $q->where('clinic_id', $inputs['clinic_id']);
                }
                if ($inputs['start_date']) {
                    $q->where('start_date', '>=', $inputs['start_date']);
                }
                if ($inputs['end_date']) {
                    $q->where('end_date', '<=', $inputs['end_date']);
                }
            })->whereIn('clinic_id', $clinics)->orderBy('hospital_id')->get()->toArray();
        }
        $hospitalRepo = new HospitalRepository();
        $clinicRepo = new ClinicRepository();
        foreach ($data as $key => $val) {
            $data[$key]['hospital_name'] = $hospitalRepo->getName($val['hospital_id']);
            $data[$key]['clinic_name'] = $clinicRepo->getName($val['clinic_id']);
        }
        return $data;
    }

    public function getById($id)
    {
        return ClinicSchedule::where('id', $id)->first();
    }

    public function delete($id)
    {
        return ClinicSchedule::where('id', $id)->delete();
    }

    public function checkDataIsAvailable($date, $exceptId = null, $clinic_id)
    {
        if ($exceptId) {
            return count(ClinicSchedule::where('id', '!=', $exceptId)
                ->where('clinic_id', $clinic_id)
                ->where('start_date', '<=', $date)
                ->where('end_date', '>=', $date)->get()->toArray());
        }
        return count(ClinicSchedule::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('clinic_id', $clinic_id)->get()->toArray());
    }

    public function checkActivation($id)
    {
        return count(ClinicSchedule::where('id', $id)
            ->where('start_date', '<=', date('Y-m-d'))
            ->where('end_date', '>=', date('Y-m-d'))->get()->toArray());
    }

    public function getByClinicId($id, $date)
    {
        return ClinicSchedule::where('clinic_id', $id)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)->first();
    }

    public function getAllByClinicId($id)
    {
        return ClinicSchedule::where('clinic_id', $id)->get()->toArray();
    }

    public function getName($id)
    {
        return ClinicSchedule::where('id', $id)->pluck('name');
    }

    public function getWithDates($start_date = '', $end_date = '')
    {
        return ClinicSchedule::where(function ($q) use ($start_date, $end_date) {
            if ($start_date) {
                $q->where('start_date', '>=', $start_date);
            }
            if ($end_date) {
                $q->where('end_date', '<=', $end_date);
            }
        })->lists('id');
    }


}