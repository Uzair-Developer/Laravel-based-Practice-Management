<?php

namespace core\physician;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\authorized\Authorized;
use core\user\UserRepository;
use core\userLocalization\UserLocalizationRepository;
use Physician;
use User;

class PhysicianRepository
{

    public function save($inputs)
    {
        return Physician::create($inputs)->toArray();
    }

    public function update($inputs, $id)
    {
        return Physician::where('user_id', $id)->update($inputs);
    }

    public function getAll($inputs = '', $all = true)
    {
        $auth = new Authorized();
        if ($auth->isSupperAdmin()) {
            if($all) {
                return User::getPhysicians($inputs, true);
            } else {
                return User::getPhysicians($inputs, false);
            }
        } else {
            $user = Sentry::getUser();
            if ($user->user_type_id == 7 && !$user->hasAccess('head_dept.access')) {
                $physiciansIds = array($user->id);
            } else {
                $physiciansIds = User::getPhysiciansId();
            }
            $userRepo = new UserRepository();
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($userRepo->getCurrentUser()->id);
            if ($hospitals) {
                $usersId = $ULRepo->getUsersByUsersIdAndHospitalsId($physiciansIds, $hospitals);
            } else {
                $clinics = $ULRepo->getClinicsByUserId($userRepo->getCurrentUser()->id);
                $usersId = $ULRepo->getUsersByUsersIdAndClinicsId($physiciansIds, $clinics);
            }
            if($all) {
                return $userRepo->getUsersAndClinicsNameByUsersId($usersId, $inputs, true);
            } else {
                return $userRepo->getUsersAndClinicsNameByUsersId($usersId, $inputs, false);
            }
        }
    }

    public function getById($id)
    {
        return Physician::where('id', $id)->first();
    }

    public function getByUserId($id)
    {
        return Physician::where('user_id', $id)->first();
    }

    public function delete($id)
    {
        return Physician::where('id', $id)->delete();
    }

}