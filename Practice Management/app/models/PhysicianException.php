<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\authorized\Authorized;
use core\enums\PhysicianExceptionStatus;
use core\userLocalization\UserLocalizationRepository;

class PhysicianException extends Eloquent
{

    protected $table = 'physician_exception';
    protected $guarded = array('');

    public static $rules = array(
        "user_id" => "required",
//        "schedule_times" => "required",
        "reason_id" => "required",
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = '', $all = true)
    {
        $user = Sentry::getUser();
        if ($user->user_type_id == 1) {
            if ($inputs) {
                $data = self::where(function ($q) use ($inputs) {
                    if (isset($inputs['hospital_id']) && $inputs['hospital_id'] && empty($inputs['clinic_id'])) {
                        $physicianArray = UsersLocalizationClinics::getActivePhysiciansByHospitalId($inputs['hospital_id']);
                        $q->whereIn('user_id', $physicianArray);
                    }
                    if (isset($inputs['clinic_id']) && $inputs['clinic_id'] && empty($inputs['user_id'])) {
                        $physicianArray = UsersLocalizationClinics::getActivePhysiciansByClinicId($inputs['clinic_id']);
                        $q->where('user_id', $physicianArray);
                    }
                    if (isset($inputs['user_id']) && $inputs['user_id']) {
                        $q->where('user_id', $inputs['user_id']);
                    }
                    if (isset($inputs['reason_id']) && $inputs['reason_id']) {
                        $q->where('reason_id', $inputs['reason_id']);
                    }
                    if (isset($inputs['start_date']) && $inputs['start_date']) {
                        $q->where('from_date', '>=', $inputs['start_date']);
                    }
                    if (isset($inputs['end_date']) && $inputs['end_date']) {
                        $q->where('to_date', '<=', $inputs['end_date']);
                    }
                    if (isset($inputs['start_created_at']) && $inputs['start_created_at']) {
                        $q->where('created_at', '>=', $inputs['start_created_at']);
                    }
                    if (isset($inputs['end_created_at']) && $inputs['end_created_at']) {
                        $q->where('created_at', '<=', $inputs['end_created_at']);
                    }
                    if (isset($inputs['status']) && $inputs['status']) {
                        $q->where('status', $inputs['status']);
                    }
                    if (isset($inputs['status']) && $inputs['status'] === '0') {
                        $q->where('status', 0);
                    }
                });
                if (!$all) {
                    $data = $data->paginate(50);
                } else {
                    $data = $data->get()->toArray();
                }
            } else {
                if (!$all) {
                    $data = self::paginate(50);
                } else {
                    $data = self::all()->toArray();
                }
            }
        } else {
            if ($user->user_type_id == 7) {
                $physiciansIds = array($user->id);
            } else {
                $physiciansIds = User::getPhysiciansId();
            }
            $ULRepo = new UserLocalizationRepository();
            $hospitals = $ULRepo->getManageHospitalsByUserId($user->id);
            if (!$hospitals) {
                $hospitals = $ULRepo->getNotManagedHospitalsByUserId($user->id);
            }
            $usersId = $ULRepo->getUsersByUsersIdAndHospitalsId($physiciansIds, $hospitals);
            if ($inputs) {
                $data = self::where(function ($q) use ($inputs) {
                    if (isset($inputs['user_id']) && $inputs['user_id']) {
                        $q->where('user_id', $inputs['user_id']);
                    }
                    if (isset($inputs['reason_id']) && $inputs['reason_id']) {
                        $q->where('reason_id', $inputs['reason_id']);
                    }
                    if (isset($inputs['start_date']) && $inputs['start_date']) {
                        $q->where('from_date', '>=', $inputs['start_date']);
                    }
                    if (isset($inputs['end_date']) && $inputs['end_date']) {
                        $q->where('to_date', '<=', $inputs['end_date']);
                    }
                    if (isset($inputs['start_created_at']) && $inputs['start_created_at']) {
                        $q->where('created_at', '>=', $inputs['start_created_at']);
                    }
                    if (isset($inputs['end_created_at']) && $inputs['end_created_at']) {
                        $q->where('created_at', '<=', $inputs['end_created_at']);
                    }
                    if (isset($inputs['status']) && $inputs['status']) {
                        $q->where('status', $inputs['status']);
                    }
                    if (isset($inputs['status']) && $inputs['status'] === '0') {
                        $q->where('status', 0);
                    }
                })->whereIn('user_id', $usersId);
                if (!$all) {
                    $data = $data->paginate(50);
                } else {
                    $data = $data->get()->toArray();
                }
            } else {
                $data = self::whereIn('user_id', $usersId);
                if (!$all) {
                    $data = $data->paginate(50);
                } else {
                    $data = $data->get()->toArray();
                }
            }
        }
        foreach ($data as $key => $val) {
            $data[$key]['physician_name'] = User::getName($val['user_id']);
            $data[$key]['change_by_name'] = User::getName($val['change_status_by']);
            $data[$key]['clinic_name'] = Clinic::getNameById(PhysicianSchedule::getById($val['physician_schedule_id'])['clinic_id']);
            $data[$key]['create_name'] = User::getName($val['created_by']);
            $data[$key]['reason_name'] = AttributePms::getName($val['reason_id']);
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

    public static function removeByPhysicianSchedule($schedule_id)
    {
        return self::where('physician_schedule_id', $schedule_id)->delete();
    }

    public static function checkExist($physician_id, $date, $time_from = '', $slots = '', $reason_id = '', $physicianSchedule = [])
    {
        $seconds = Functions::hoursToSeconds($time_from);
        $newSeconds = $seconds + ($slots * 60);
        $time_to = Functions::timeFromSeconds($newSeconds);
        if ($time_from) {
            // check unlock slots
            if (UnlockSlot::checkByPhysicianDateTime($physician_id, $date, $time_from, $time_to)) {
                return array();
            }
        }
        $data = self::where('user_id', $physician_id)->where('status', PhysicianExceptionStatus::approved)
            ->where('from_date', '<=', $date)->where('to_date', '>=', $date)
            ->where(function ($q) use ($time_from, $time_to, $reason_id) {
                if (!empty($time_from) && !empty($time_to)) {
                    $q->where('from_time', '<=', $time_from)->where('to_time', '>', $time_from);
                    $q->orWhere(function ($q2) use ($time_to) {
                        $q2->where('from_time', '<', $time_to)->where('to_time', '>', $time_to);
                    });
                }

            });
        if ($reason_id) {
            $data = $data->where('reason_id', $reason_id);
        }
        return $data->orderBy('id', 'desc')->get()->toArray();
    }

    public static function checkReasonExist($reason_id)
    {
        return self::where('reason_id', $reason_id)->first();
    }

    public static function getByPhysicianSchedule($physician_schedule_id)
    {
        $data = self::where('physician_schedule_id', $physician_schedule_id)->get()->toArray();
        foreach ($data as $key => $val) {
            $data[$key]['physician_name'] = User::getName($val['user_id']);
            $data[$key]['clinic_name'] = Clinic::getNameById(PhysicianSchedule::getById($val['physician_schedule_id'])['clinic_id']);
            $data[$key]['create_name'] = User::getName($val['created_by']);
            $data[$key]['reason_name'] = AttributePms::getName($val['reason_id']);
        }
        return $data;
    }

    public static function checkSlotClosedByException($physician_id, $date, $time_from = '', $slots = '', $reason_id = '', $physicianSchedule = [])
    {
        $physicianException = self::checkExist($physician_id, $date, $time_from, $slots, null, $physicianSchedule);
        $daysName = array(
            'saturday' => 'sat',
            'sunday' => 'sun',
            'monday' => 'mon',
            'tuesday' => 'tues',
            'wednesday' => 'wed',
            'thursday' => 'thurs',
            'friday' => 'fri',
        );
        $inputDayName = lcfirst(date('l', strtotime($date)));

        if (!empty($physicianException)) {
            foreach ($physicianException as $key => $val) {
                if ($val['schedule_times']) {
                    $startTime_1 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'] ?
                        $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'] : 'null';
                    $endTime_1 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'] ?
                        $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'] : 'null';
                    if ($physicianSchedule['num_of_shifts'] == 2 || $physicianSchedule['num_of_shifts'] == 3) {
                        $startTime_2 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'] ?
                            $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'] : 'null';
                        $endTime_2 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'] ?
                            $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'] : 'null';
                        if ($physicianSchedule['num_of_shifts'] == 3) {
                            $startTime_3 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'] ?
                                $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'] : 'null';
                            $endTime_3 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'] ?
                                $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'] : 'null';
                        }
                    }
                    if (
                        ($physicianSchedule['num_of_shifts'] == 1 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                            && strpos($val['schedule_times'], $endTime_1) !== false)
                        ||
                        ($physicianSchedule['num_of_shifts'] == 2 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                            && strpos($val['schedule_times'], $endTime_1) !== false && strpos($val['schedule_times'], $startTime_2 . ' ') !== false
                            && strpos($val['schedule_times'], $endTime_2) !== false)
                        ||
                        ($physicianSchedule['num_of_shifts'] == 3 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                            && strpos($val['schedule_times'], $endTime_1) !== false && strpos($val['schedule_times'], $startTime_2 . ' ') !== false
                            && strpos($val['schedule_times'], $endTime_2) !== false && strpos($val['schedule_times'], $startTime_3 . ' ') !== false
                            && strpos($val['schedule_times'], $endTime_3) !== false)

                    ) {
                        $reason = AttributePms::getById($val['reason_id']);
                        if ($val['effect'] == 1) {
                            return [
                                'close' => true,
                                'exception' => $val,
                                'effect' => true,
                                'reason' => $reason['name'],
                            ];
                        } else {
                            return [
                                'close' => true,
                                'exception' => $val,
                                'effect' => false,
                                'reason' => $reason['name'],
                            ];
                        }
                    }
                } else {
                    $reason = AttributePms::getById($val['reason_id']);
                    if ($val['effect'] == 1) {
                        return [
                            'close' => true,
                            'exception' => $val,
                            'effect' => true,
                            'reason' => $reason['name'],
                        ];
                    } else {
                        return [
                            'close' => true,
                            'exception' => $val,
                            'effect' => false,
                            'reason' => $reason['name'],
                        ];
                    }
                }
            }
        } else {
            return [
                'close' => false
            ];
        }
        return [
            'close' => false
        ];
    }

}
