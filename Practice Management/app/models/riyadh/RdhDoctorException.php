<?php

class RdhDoctorException extends Eloquent
{
    protected $table = 'dbo.doctor_exceptions';
    protected $guarded = array('');
    protected $connection = 'sqlsrv3';
    public $timestamps = false;

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
        self::all();
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function deleteFlagByPMSId($pms_id)
    {
        return self::where('pms_id', $pms_id)->update(['deleted' => 1]);
    }

    public static function addFromPMS($inputs)
    {
        // must be effect to integration
        if($inputs['effect'] != 1) {
            return;
        }
        $user = Sentry::getUser();
        $physician = User::getById($inputs['user_id']);
        $from_time = explode(':', $inputs['from_time']);
        if (!isset($from_time[1])) {
            $inputs['from_time'] = $inputs['from_time'] . ':00';
        }
        $to_time = explode(':', $inputs['to_time']);
        if (!isset($to_time[1])) {
            $inputs['to_time'] = $inputs['to_time'] . ':00';
        }
        if (isset($inputs['schedule_times']) && $inputs['schedule_times']) {
            $from_date = $inputs['from_date'];
            $to_date = $inputs['to_date'];
            $daysName = array(
                'saturday' => 'sat',
                'sunday' => 'sun',
                'monday' => 'mon',
                'tuesday' => 'tues',
                'wednesday' => 'wed',
                'thursday' => 'thurs',
                'friday' => 'fri',
            );
            while ($from_date <= $to_date) {
                $inputDayName = lcfirst(date('l', strtotime($from_date)));
                $physicianSchedule = PhysicianSchedule::getById($inputs['physician_schedule_id']);
                if ($physicianSchedule) {
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
                        ($physicianSchedule['num_of_shifts'] == 1 && strpos($inputs['schedule_times'], $startTime_1 . ' ') !== false
                            && strpos($inputs['schedule_times'], $endTime_1) !== false)
                        ||
                        ($physicianSchedule['num_of_shifts'] == 2 && strpos($inputs['schedule_times'], $startTime_1 . ' ') !== false
                            && strpos($inputs['schedule_times'], $endTime_1) !== false && strpos($inputs['schedule_times'], $startTime_2 . ' ') !== false
                            && strpos($inputs['schedule_times'], $endTime_2) !== false)
                        ||
                        ($physicianSchedule['num_of_shifts'] == 3 && strpos($inputs['schedule_times'], $startTime_1 . ' ') !== false
                            && strpos($inputs['schedule_times'], $endTime_1) !== false && strpos($inputs['schedule_times'], $startTime_2 . ' ') !== false
                            && strpos($inputs['schedule_times'], $endTime_2) !== false && strpos($inputs['schedule_times'], $startTime_3 . ' ') !== false
                            && strpos($inputs['schedule_times'], $endTime_3) !== false)

                    ) {
                        self::add([
                            'pms_id' => $inputs['id'],
                            'pms_doctor_id' => $inputs['user_id'],
                            'his_doctor_id' => $physician['his_id_2'],
                            'reason' => $inputs['reason_name'],
                            'from_date' => date('Y-m-d', strtotime($from_date)),
                            'to_date' => date('Y-m-d', strtotime($from_date)),
                            'from_time' => date('H:i:s', strtotime($inputs['from_time'])),
                            'to_time' => date('H:i:s', strtotime($inputs['to_time'])),
                            'created_by' => isset($user['full_name']) ? $user['full_name'] : '',
                        ]);
                    }
                }

                $from_date = date("Y-m-d", strtotime("+1 day", strtotime($from_date)));
            }
        } else {
            self::add([
                'pms_id' => $inputs['id'],
                'pms_doctor_id' => $inputs['user_id'],
                'his_doctor_id' => $physician['his_id_2'],
                'reason' => $inputs['reason_name'],
                'from_date' => date('Y-m-d', strtotime($inputs['from_date'])),
                'to_date' => date('Y-m-d', strtotime($inputs['to_date'])),
                'from_time' => date('H:i:s', strtotime($inputs['from_time'])),
                'to_time' => date('H:i:s', strtotime($inputs['to_time'])),
                'created_by' => isset($user['full_name']) ? $user['full_name'] : '',
            ]);
        }
    }

    public static function lockOrUnlock($inputs, $deleted = 0)
    {
        $user = Sentry::getUser();
        $physician = User::getById($inputs['user_id']);
        $from_time = explode(':', $inputs['from_time']);
        if (!isset($from_time[1])) {
            $inputs['from_time'] = $inputs['from_time'] . ':00';
        }
        $to_time = explode(':', $inputs['to_time']);
        if (!isset($to_time[1])) {
            $inputs['to_time'] = $inputs['to_time'] . ':00';
        }
        $exist = self::where('pms_doctor_id', $inputs['user_id'])
            ->where('from_date', date('Y-m-d', strtotime($inputs['date'])))
            ->where('to_date', date('Y-m-d', strtotime($inputs['date'])))
            ->where('from_time', date('H:i:s', strtotime($inputs['from_time'])))
            ->where('to_time', date('H:i:s', strtotime($inputs['from_to'])))->first();
        if($exist) {
            $exist->update(['deleted' => $deleted]);
        } else {
            self::add([
                'pms_doctor_id' => $inputs['user_id'],
                'his_doctor_id' => $physician['his_id_2'],
                'reason' => 'Lock Slot',
                'from_date' => date('Y-m-d', strtotime($inputs['date'])),
                'to_date' => date('Y-m-d', strtotime($inputs['date'])),
                'from_time' => date('H:i:s', strtotime($inputs['from_time'])),
                'to_time' => date('H:i:s', strtotime($inputs['to_time'])),
                'created_by' => isset($user['full_name']) ? $user['full_name'] : '',
                'deleted' => $deleted
            ]);
        }
    }

    public static function updateUnlockWithPeriod($user_id, $from_date, $to_date, $from_time, $to_time)
    {
        if ($user_id && $from_date && $to_date && $from_time && $to_time) {
            while (1 == 1) {
                $exist = self::where('pms_doctor_id', $user_id)
                    ->where('from_date', date('Y-m-d', strtotime($from_date)))
                    ->where('to_date', date('Y-m-d', strtotime($from_date)))
                    ->where('reason', 'Lock Slot')
                    ->where(function ($q) use ($from_time, $to_time) {
                        if ($from_time && $to_time) {
                            $q->where(function ($q2) use ($from_time, $to_time) {
                                $q2->where(function ($q3) use ($from_time, $to_time) {
                                    $q3->where('from_time', '>=', date('H:i:s', strtotime($from_time)))->where('from_time', '<', date('H:i:s', strtotime($to_time)));
                                });
                                $q2->orWhere(function ($q3) use ($from_time, $to_time) {
                                    $q3->where('to_time', '>', date('H:i:s', strtotime($from_time)))->where('to_time', '<=', date('H:i:s', strtotime($to_time)));
                                });
                                $q2->orWhere(function ($q3) use ($from_time, $to_time) {
                                    $q3->where('from_time', '<=', date('H:i:s', strtotime($from_time)))->where('to_time', '>=', date('H:i:s', strtotime($to_time)));
                                });
                            });
                        }
                    })->first();
                if($exist) {
                    $exist->update(['deleted' => 0]);
                }
                if ($from_date == $to_date) {
                    break;
                } else {
                    $from_date = date('Y-m-d', strtotime("+1 day", strtotime($from_date)));
                }
            }
        }
    }
}
