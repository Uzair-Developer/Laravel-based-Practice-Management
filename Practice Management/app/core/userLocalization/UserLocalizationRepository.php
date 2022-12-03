<?php

namespace core\userLocalization;


use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Clinic;
use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use Reservation;
use ReservationHistory;
use UserLocalization;

class UserLocalizationRepository
{

    public function save($inputs)
    {
        return UserLocalization::create($inputs)->toArray();
    }

    public function update($inputs, $id)
    {
        return UserLocalization::where('id', $id)->update($inputs);
    }

    public function deleteWithUserAndClinic($userId, $clinicId)
    {
        $user = Sentry::getUser();
        $reservations = Reservation::getByPhysicianSchedule($clinicId, $userId, date('Y-m-d'));
        if ($reservations) {
            foreach ($reservations as $key => $val) {
                Reservation::edit(array(
                    'update_by' => $user->id,
                    'status' => ReservationStatus::archive,
                    'patient_status' => PatientStatus::archive,
                    'exception_reason' => 'Delete The Localization In This Clinic',
                    'show_reason' => 2,
                ), $val['id']);
                ReservationHistory::add([
                    'action' => 'Archive From Delete The Localization In This Clinic',
                    'action_by' => $user->id,
                    'reservation_id' => $val['id'],
                    'code' => $val['code'],
                    'physician_id' => $val['physician_id'],
                    'clinic_id' => $val['clinic_id'],
                    'patient_id' => $val['patient_id'],
                    'date' => $val['date'],
                    'time_from' => $val['time_from'],
                    'time_to' => $val['time_to'],
                    'status' => ReservationStatus::archive,
                    'patient_status' => PatientStatus::archive,
                    'exception_reason' => 'Delete The Localization In This Clinic',
                ]);
            }
        }
        return UserLocalization::where('user_id', $userId)->where('clinic_id', $clinicId)->delete();
    }

    public function deleteWithUserAndHospital($userId, $hospitalId)
    {
        $user = Sentry::getUser();
        $clinics = Clinic::getByHospitalId($hospitalId);
        if ($clinics) {
            $reservations = Reservation::getByPhysicianSchedule('', $userId, date('Y-m-d'), '', $clinics);
            if ($reservations) {
                foreach ($reservations as $key => $val) {
                    Reservation::edit(array(
                        'update_by' => $user->id,
                        'status' => ReservationStatus::archive,
                        'patient_status' => PatientStatus::archive,
                        'exception_reason' => 'Delete The Localization In This Hospital',
                        'show_reason' => 2,
                    ), $val['id']);
                    ReservationHistory::add([
                        'action' => 'Archive From Delete The Localization In This Hospital',
                        'action_by' => $user->id,
                        'reservation_id' => $val['id'],
                        'code' => $val['code'],
                        'physician_id' => $val['physician_id'],
                        'clinic_id' => $val['clinic_id'],
                        'patient_id' => $val['patient_id'],
                        'date' => $val['date'],
                        'time_from' => $val['time_from'],
                        'time_to' => $val['time_to'],
                        'status' => ReservationStatus::archive,
                        'patient_status' => PatientStatus::archive,
                        'exception_reason' => 'Delete The Localization In This Hospital',
                    ]);
                }
            }
        }
        return UserLocalization::where('user_id', $userId)->where('hospital_id', $hospitalId)->where('clinic_id', 0)->delete();
    }

    public function getAll()
    {
        return UserLocalization::all()->toArray();
    }

    public function getById($id)
    {
        return UserLocalization::where('id', $id)->first();
    }

    public function delete($id)
    {
        return UserLocalization::where('id', $id)->delete();
    }

    public function isHospitalExistForUser($userId, $hospitalId)
    {
        return UserLocalization::where('user_id', $userId)->where('hospital_id', $hospitalId)->where('clinic_id', 0)->first();
    }

    public function isClinicExistForUser($userId, $clinicId)
    {
        return UserLocalization::where('user_id', $userId)->where('clinic_id', $clinicId)->first();
    }

    public function getClinicsByUserId($userId)
    {
        return UserLocalization::where('user_id', $userId)->where('clinic_id', '!=', 0)->lists('clinic_id');
    }

    public function getNotManagedHospitalsByUserId($userId)
    {
        return UserLocalization::where('user_id', $userId)->where('clinic_id', '!=', 0)->lists('hospital_id');
    }

    public function getUsersByUsersIdAndClinicsId($usersId, $clinicsId)
    {
        return UserLocalization::whereIn('user_id', $usersId)->whereIn('clinic_id', $clinicsId)->lists('user_id');
    }

    public function getUsersByUsersIdAndClinicId($usersId, $clinicId)
    {
        return UserLocalization::whereIn('user_id', $usersId)->where('clinic_id', $clinicId)->lists('user_id');
    }

    public function getPhysiciansByClinicId($physiciansIds, $clinicId)
    {
        return UserLocalization::whereIn('user_id', $physiciansIds)
            ->where('clinic_id', $clinicId)
            ->where('clinic_id', '!=', 0)
            ->lists('user_id');
    }

    public function getUsersByUsersIdAndHospitalsId($usersId, $hospitalsId)
    {
        return UserLocalization::whereIn('user_id', $usersId)->whereIn('hospital_id', $hospitalsId)->lists('user_id');
    }

    public function getUsersByUsersIdAndHospitalId($usersId, $hospitalId)
    {
        return UserLocalization::whereIn('user_id', $usersId)->where('hospital_id', $hospitalId)->lists('user_id');
    }

    public function getHospitalsByUserId($userId)
    {
        return UserLocalization::where('user_id', $userId)->groupBy('hospital_id')->lists('hospital_id');
    }

    public function getUsersByHospitalIds($hospitalIds)
    {
        return UserLocalization::whereIn('hospital_id', $hospitalIds)->groupBy('user_id')->lists('user_id');
    }

    public function getUsersByClinicIds($clinicIds)
    {
        return UserLocalization::whereIn('clinic_id', $clinicIds)->groupBy('user_id')->lists('user_id');
    }

    public function getManageHospitalsByUserId($userId)
    {
        return UserLocalization::where('user_id', $userId)->where('clinic_id', 0)->lists('hospital_id');
    }

    public function getHospitalsManageByUserId($userId)
    {
        return UserLocalization::where('user_id', $userId)->where('clinic_id', 0)->lists('hospital_id');
    }

    public function getFirstClinicByUserId($userId)
    {
        return UserLocalization::where('user_id', $userId)->where('clinic_id', '!=', 0)->first();
    }

}