<?php

namespace core\clinic;


use Clinic;
use core\authorized\Authorized;
use core\user\UserRepository;
use core\userLocalization\UserLocalizationRepository;
use Hospital;

class ClinicRepository
{

    public function save($inputs)
    {
        return Clinic::create($inputs)->toArray();
    }

    public function update($inputs, $id)
    {
        return Clinic::where('id', $id)->update($inputs);
    }

    public function getAll($inputs = '')
    {
        $auth = new Authorized();
        if ($auth->isSupperAdmin()) {
            $clinicsData = Clinic::whereRaw('1 = 1');
        } else {
            $userRepo = new UserRepository();
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($userRepo->getCurrentUser()->id);
            if ($hospitals) {
                $clinicsData = Clinic::whereIn('hospital_id', $hospitals);
            } else {
                $clinics = $ULRepo->getClinicsByUserId($userRepo->getCurrentUser()->id);
                $clinicsData = Clinic::whereIn('id', $clinics);
            }
        }
        if ($inputs) {
            if (isset($inputs['name']) && $inputs['name']) {
                $clinicsData = $clinicsData->where('name', 'LIKE', '%' . $inputs['name'] . '%');
            }
            if (isset($inputs['code']) && $inputs['code']) {
                $clinicsData = $clinicsData->where('code', 'LIKE', '%' . $inputs['code'] . '%');
            }
            if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
                $clinicsData = $clinicsData->where('hospital_id', $inputs['hospital_id']);
            }
        }
        if (isset($inputs['paginate']) && $inputs['paginate']) {
            $clinicsData = $clinicsData->paginate(25);
        } else {
            $clinicsData = $clinicsData->get()->toArray();
        }
        if (isset($inputs['details']) && $inputs['details']) {
            foreach ($clinicsData as $key => $val) {
                $clinicsData[$key]['hospital_name'] = Hospital::getName($val['hospital_id']);
            }
        }
        return $clinicsData;
    }

    public function getById($id)
    {
        return Clinic::where('id', $id)->first();
    }

    public function getHospitalId($id)
    {
        return Clinic::where('id', $id)->pluck('hospital_id');
    }

    public function getByHospitalId($id)
    {
        $auth = new Authorized();
        if ($auth->isSupperAdmin()) {
            return Clinic::where('hospital_id', $id)->get()->toArray();
        } else {
            $userRepo = new UserRepository();
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($userRepo->getCurrentUser()->id);
            if ($hospitals) {
                return Clinic::whereIn('hospital_id', $hospitals)->where('hospital_id', $id)->get()->toArray();
            } else {
                $clinics = $ULRepo->getClinicsByUserId($userRepo->getCurrentUser()->id);
                return Clinic::whereIn('id', $clinics)->where('hospital_id', $id)->get()->toArray();
            }
        }
    }

    public function delete($id)
    {
        return Clinic::where('id', $id)->delete();
    }

    public function getName($id)
    {
        return Clinic::where('id', $id)->pluck('name');
    }

}