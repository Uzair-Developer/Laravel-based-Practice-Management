<?php

namespace core\hospital;


use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\authorized\Authorized;
use core\user\UserRepository;
use core\userLocalization\UserLocalizationRepository;
use Hospital;

class HospitalRepository
{

    public function save($inputs)
    {
        return Hospital::create($inputs)->toArray();
    }

    public function update($inputs, $id)
    {
        return Hospital::where('id',$id)->update($inputs);
    }

    public function getAll()
    {
        $auth = new Authorized();
        if($auth->isSupperAdmin()){
            return Hospital::all()->toArray();
        } else {
            $userRepo = new UserRepository();
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($userRepo->getCurrentUser()->id);
            if ($hospitals) {
                return Hospital::whereIn('id', $hospitals)->get()->toArray();
            } else {
                $hospitals = $ULRepo->getNotManagedHospitalsByUserId($userRepo->getCurrentUser()->id);
                return Hospital::whereIn('id', $hospitals)->get()->toArray();
            }

        }
    }

    public function getHospitalsLocalization()
    {
        if(Authorized::isSupperAdmin()){
            return Hospital::all()->toArray();
        } else {
            $userRepo = new UserRepository();
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($userRepo->getCurrentUser()->id);
            if ($hospitals) {
                return Hospital::whereIn('id', $hospitals)->get()->toArray();
            } else {
                $hospitals = $ULRepo->getNotManagedHospitalsByUserId($userRepo->getCurrentUser()->id);
                return Hospital::whereIn('id', $hospitals)->get()->toArray();
            }
        }
    }

    public function getAllWithClinics()
    {
        $user = Sentry::getUser();
        if($user->user_type_id == 1){
            return Hospital::with('clinics')->get()->toArray();
        } else {
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($user->id);
            if ($hospitals) {
                return Hospital::with('clinics')->whereIn('id', $hospitals)->get()->toArray();
            } else {
                $hospitals = $ULRepo->getNotManagedHospitalsByUserId($user->id);
                return Hospital::with('clinics')->whereIn('id', $hospitals)->get()->toArray();
            }
        }
    }

    public function getById($id)
    {
        return Hospital::where('id',$id)->first();
    }

    public function delete($id)
    {
        return Hospital::where('id',$id)->delete();
    }

    public function getName($id)
    {
        return Hospital::where('id',$id)->pluck('name');
    }

}