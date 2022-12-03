<?php
namespace core\user;

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\clinic\ClinicRepository;
use core\hospital\HospitalRepository;
use core\userLocalization\UserLocalizationRepository;
use Physician;
use PhysicianSchedule;
use PhysicianScheduleException;
use User;
use UserGroup;
use UserTypes;

class UserRepository
{

    public function save($inputs)
    {
        return User::create($inputs);
    }

    public function update($inputs, $id)
    {
        return User::where('id', $id)->update($inputs);
    }

    public function getAll()
    {
        return User::where('id', '!=', 1)->where('activated', 1)->get()->toArray();
    }

    public function getAllPagination($inputs = '', $paginate = true)
    {
        $user = Sentry::getUser();
        if ($user->user_type_id == 1) {
            $users = User::where('id', '!=', 1);
        } else {
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($user->id);
            if ($hospitals) {
            } else {
                $hospitals = $ULRepo->getNotManagedHospitalsByUserId($user->id);
            }
            $userIds = $ULRepo->getUsersByHospitalIds($hospitals);
            $users = User::where('id', '!=', 1)->where('activated', 1)->whereIn('id', $userIds);
        }
        if ($inputs) {
            if (isset($inputs['name']) && $inputs['name']) {
                $users = $users->whereRaw('LOWER(full_name) LIKE LOWER("%' . $inputs['name'] . '%")');
            }
//        dd($users->get()->toArray());
            if (isset($inputs['user_type_id']) && $inputs['user_type_id']) {
                $users = $users->where('user_type_id', $inputs['user_type_id']);
            }
            if (isset($inputs['activated']) && ($inputs['activated'] === '0' || $inputs['activated'] == 1)) {
                $users = $users->where('activated', $inputs['activated']);
            }
            if (isset($inputs['group_id']) && $inputs['group_id']) {
                $userIds = UserGroup::getUsersByGroupId($inputs['group_id']);
                $users = $users->whereIn('id', $userIds);
            }
        }
        if ($paginate) {
            $users = $users->paginate(20);
        } else {
            $users = $users->get()->toArray();
        }
        foreach ($users as $index => $val) {
            $users[$index]['role_name'] = UserTypes::getName($val['user_type_id']);
            $oneUser = Sentry::findUserById($val['id']);
            $groups = $oneUser->getGroups();
            $count = count($groups);
            $users[$index]['group_name'] = '';
            foreach ($groups as $index2 => $val2) {
                if ($count == $index2 + 1) {
                    $users[$index]['group_name'] .= $val2['name'];
                } else {
                    $users[$index]['group_name'] .= $val2['name'] . ', ';
                }
            }
        }
        return $users;
    }

    public function getById($id)
    {
        return User::where('id', $id)->first();
    }

    public function getName($id)
    {
        return User::where('id', $id)->pluck('full_name');
    }

    public function getByUsersId($id)
    {
        return User::whereIn('id', $id)->where('activated', 1)->get()->toArray();
    }

    public function getByUsersIdWithSchedule($ids, $clinicSchedule = null, $date = null)
    {
        $data = User::whereIn('id', $ids)->where('activated', 1)->get()->toArray();
        foreach ($data as $key => $val) {
            $data[$key]['schedules'][0] = PhysicianSchedule::getByPhysicianId_Date($val['id'], $date, true, $clinicSchedule['clinic_id']);
        }
        return $data;
    }

    public function getByUserIdWithSchedule($id, $clinicSchedule = null, $date = null)
    {
        $scheduleException = PhysicianScheduleException::checkByClinic_Physician_Date($clinicSchedule['clinic_id']
            , $id, $date);
        if ($scheduleException) {
            $data = User::where('id', $id)->where('activated', 1)->first();
            $newScheduleArray = PhysicianSchedule::getByPhysicianId_Date($id, $date, true, $clinicSchedule['clinic_id']);
            $data['schedules'][0] = $newScheduleArray;
            return $data;
        } else {
            return User::with(array('schedules' => function ($q) use ($clinicSchedule, $date) {
                $q->where('clinic_schedule_id', $clinicSchedule['id']);
                $q->where('start_date', '<=', $date);
                $q->where('end_date', '>=', $date);
                $q->where('publish', 1);
            }))->where('id', $id)->where('activated', 1)->first();
        }
    }

    public function getUsersAndClinicsNameByUsersId($ids, $inputs = '', $all = false)
    {
        $data = User::whereIn('id', $ids);
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

    public function getUsers($id)
    {
        return User::whereIn('id', $id)->where('activated', 1)->get()->toArray();
    }

    public function delete($id)
    {
        UserGroup::removeByUserId($id);
        return User::where('id', $id)->delete();
    }

    public function getCurrentUser()
    {
        return Sentry::getUser();
    }
}