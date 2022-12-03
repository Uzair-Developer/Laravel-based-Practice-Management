<?php

class PhysicianSchedule extends Eloquent
{
    protected $table = 'physician_schedules';
    protected $guarded = array('');


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
        return self::all()->toArray();
    }

    public static function getById($id, $with_exception = '', $date = '')
    {
        $array = self::where('id', $id)->first();
        if ($with_exception) {
            $scheduleException = PhysicianScheduleException::checkByClinic_Physician_Date($array['clinic_id']
                , $array['user_id'], $date);
            if ($scheduleException) {
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
                $array['slots'] = $scheduleException['slots'];
                $array[$daysName[$inputDayName] . '_start_time_1'] = $scheduleException['shift1_time_from'];
                $array[$daysName[$inputDayName] . '_end_time_1'] = $scheduleException['shift1_time_to'];
                $array[$daysName[$inputDayName] . '_start_time_2'] = $scheduleException['shift2_time_from'];
                $array[$daysName[$inputDayName] . '_end_time_2'] = $scheduleException['shift2_time_to'];
                $array[$daysName[$inputDayName] . '_start_time_3'] = $scheduleException['shift3_time_from'];
                $array[$daysName[$inputDayName] . '_end_time_3'] = $scheduleException['shift3_time_to'];
                $dayoff_1 = explode(',', $array['dayoff_1']);
                $dayoff_2 = explode(',', $array['dayoff_2']);
                $dayoff_3 = explode(',', $array['dayoff_3']);

                if ($scheduleException['shift1_dayoff'] == 1) {
                    if (!in_array($inputDayName, $dayoff_1)) {
                        if (empty($array['dayoff_1'])) {
                            $array['dayoff_1'] = $inputDayName;
                        } else {
                            $array['dayoff_1'] = $array['dayoff_1'] . ',' . $inputDayName;
                        }
                    }
                } else {
                    $key = array_search($inputDayName, $dayoff_1);
                    if ($key !== false) {
                        unset($dayoff_1[$key]);
                        $array['dayoff_1'] = implode(',', $dayoff_1);
                    }
                }
                if ($scheduleException['shift2_dayoff'] == 1) {
                    if (!in_array($inputDayName, $dayoff_2)) {
                        if (empty($array['dayoff_2'])) {
                            $array['dayoff_2'] = $inputDayName;
                        } else {
                            $array['dayoff_2'] = $array['dayoff_2'] . ',' . $inputDayName;
                        }
                    }
                } else {
                    $key = array_search($inputDayName, $dayoff_2);
                    if ($key !== false) {
                        unset($dayoff_2[$key]);
                        $array['dayoff_2'] = implode(',', $dayoff_2);
                    }
                }
                if ($scheduleException['shift3_dayoff'] == 1) {
                    if (!in_array($inputDayName, $dayoff_3)) {
                        if (empty($array['dayoff_3'])) {
                            $array['dayoff_3'] = $inputDayName;
                        } else {
                            $array['dayoff_3'] = $array['dayoff_3'] . ',' . $inputDayName;
                        }
                    }
                } else {
                    $key = array_search($inputDayName, $dayoff_3);
                    if ($key !== false) {
                        unset($dayoff_3[$key]);
                        $array['dayoff_3'] = implode(',', $dayoff_3);
                    }
                }
            }
            return $array;
        } else {
            return $array;
        }
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function removeArray($ids)
    {
        return self::whereIn('id', $ids)->delete();
    }

    public static function getClinicScheduleId($clinic_schedule_id)
    {
        return self::where('clinic_schedule_id', $clinic_schedule_id)->get()->toArray();
    }

    public static function getClinicScheduleIdWithPhysicianId($clinic_schedule_id, $physician_id, $date = null)
    {
        $array = self::where('clinic_schedule_id', $clinic_schedule_id)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('user_id', $physician_id)->first();
        if ($array) {
            $scheduleException = PhysicianScheduleException::checkByClinic_Physician_Date($array['clinic_id']
                , $physician_id, $date);
            if ($scheduleException) {
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
                $array['slots'] = $scheduleException['slots'];
                $array[$daysName[$inputDayName] . '_start_time_1'] = $scheduleException['shift1_time_from'];
                $array[$daysName[$inputDayName] . '_end_time_1'] = $scheduleException['shift1_time_to'];
                $array[$daysName[$inputDayName] . '_start_time_2'] = $scheduleException['shift2_time_from'];
                $array[$daysName[$inputDayName] . '_end_time_2'] = $scheduleException['shift2_time_to'];
                $array[$daysName[$inputDayName] . '_start_time_3'] = $scheduleException['shift3_time_from'];
                $array[$daysName[$inputDayName] . '_end_time_3'] = $scheduleException['shift3_time_to'];
                $dayoff_1 = explode(',', $array['dayoff_1']);
                $dayoff_2 = explode(',', $array['dayoff_2']);
                $dayoff_3 = explode(',', $array['dayoff_3']);

                if ($scheduleException['shift1_dayoff'] == 1) {
                    if (!in_array($inputDayName, $dayoff_1)) {
                        if (empty($array['dayoff_1'])) {
                            $array['dayoff_1'] = $inputDayName;
                        } else {
                            $array['dayoff_1'] = $array['dayoff_1'] . ',' . $inputDayName;
                        }
                    }
                } else {
                    $key = array_search($inputDayName, $dayoff_1);
                    if ($key !== false) {
                        unset($dayoff_1[$key]);
                        $array['dayoff_1'] = implode(',', $dayoff_1);
                    }
                }
                if ($scheduleException['shift2_dayoff'] == 1) {
                    if (!in_array($inputDayName, $dayoff_2)) {
                        if (empty($array['dayoff_2'])) {
                            $array['dayoff_2'] = $inputDayName;
                        } else {
                            $array['dayoff_2'] = $array['dayoff_2'] . ',' . $inputDayName;
                        }
                    }
                } else {
                    $key = array_search($inputDayName, $dayoff_2);
                    if ($key !== false) {
                        unset($dayoff_2[$key]);
                        $array['dayoff_2'] = implode(',', $dayoff_2);
                    }
                }
                if ($scheduleException['shift3_dayoff'] == 1) {
                    if (!in_array($inputDayName, $dayoff_3)) {
                        if (empty($array['dayoff_3'])) {
                            $array['dayoff_3'] = $inputDayName;
                        } else {
                            $array['dayoff_3'] = $array['dayoff_3'] . ',' . $inputDayName;
                        }
                    }
                } else {
                    $key = array_search($inputDayName, $dayoff_3);
                    if ($key !== false) {
                        unset($dayoff_3[$key]);
                        $array['dayoff_3'] = implode(',', $dayoff_3);
                    }
                }
            }
            return $array;
        } else {
            return $array;
        }
    }

    public static function getByPhysicianId_Date($physician_id, $date, $published = true, $clinic_id = '')
    {
        $data = self::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date);
        if ($published) {
            $data = $data->where('publish', 1);
        }
        if ($clinic_id) {
            $data = $data->where('clinic_id', $clinic_id);
        }
        $array = $data->where('user_id', $physician_id)->first();

        if ($array) {
            $scheduleException = PhysicianScheduleException::checkByClinic_Physician_Date($clinic_id
                , $physician_id, $date);
            if ($scheduleException) {
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
                $array['slots'] = $scheduleException['slots'];
                $array[$daysName[$inputDayName] . '_start_time_1'] = $scheduleException['shift1_time_from'];
                $array[$daysName[$inputDayName] . '_end_time_1'] = $scheduleException['shift1_time_to'];
                $array[$daysName[$inputDayName] . '_start_time_2'] = $scheduleException['shift2_time_from'];
                $array[$daysName[$inputDayName] . '_end_time_2'] = $scheduleException['shift2_time_to'];
                $array[$daysName[$inputDayName] . '_start_time_3'] = $scheduleException['shift3_time_from'];
                $array[$daysName[$inputDayName] . '_end_time_3'] = $scheduleException['shift3_time_to'];
                $dayoff_1 = explode(',', $array['dayoff_1']);
                $dayoff_2 = explode(',', $array['dayoff_2']);
                $dayoff_3 = explode(',', $array['dayoff_3']);

                if ($scheduleException['shift1_dayoff'] == 1) {
                    if (!in_array($inputDayName, $dayoff_1)) {
                        if (empty($array['dayoff_1'])) {
                            $array['dayoff_1'] = $inputDayName;
                        } else {
                            $array['dayoff_1'] = $array['dayoff_1'] . ',' . $inputDayName;
                        }
                    }
                } else {
                    $key = array_search($inputDayName, $dayoff_1);
                    if ($key !== false) {
                        unset($dayoff_1[$key]);
                        $array['dayoff_1'] = implode(',', $dayoff_1);
                    }
                }
                if ($scheduleException['shift2_dayoff'] == 1) {
                    if (!in_array($inputDayName, $dayoff_2)) {
                        if (empty($array['dayoff_2'])) {
                            $array['dayoff_2'] = $inputDayName;
                        } else {
                            $array['dayoff_2'] = $array['dayoff_2'] . ',' . $inputDayName;
                        }
                    }
                } else {
                    $key = array_search($inputDayName, $dayoff_2);
                    if ($key !== false) {
                        unset($dayoff_2[$key]);
                        $array['dayoff_2'] = implode(',', $dayoff_2);
                    }
                }
                if ($scheduleException['shift3_dayoff'] == 1) {
                    if (!in_array($inputDayName, $dayoff_3)) {
                        if (empty($array['dayoff_3'])) {
                            $array['dayoff_3'] = $inputDayName;
                        } else {
                            $array['dayoff_3'] = $array['dayoff_3'] . ',' . $inputDayName;
                        }
                    }
                } else {
                    $key = array_search($inputDayName, $dayoff_3);
                    if ($key !== false) {
                        unset($dayoff_3[$key]);
                        $array['dayoff_3'] = implode(',', $dayoff_3);
                    }
                }
            }
            return $array;
        } else {
            return $array;
        }
    }

    public static function getPhysicianScheduleByClinicSchedule($clinic_schedule_id, $physician_id)
    {
        return self::where('clinic_schedule_id', $clinic_schedule_id)
            ->where('user_id', $physician_id)->orderBy('start_date')->get()->toArray();
    }

    public static function checkExist($user_id, $clinic_schedule_id = '', $date)
    {
        $data = self::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('user_id', $user_id);
        if ($clinic_schedule_id) {
            $data = $data->where('clinic_schedule_id', $clinic_schedule_id);
        }
        return count($data->get()->toArray());
    }

    public static function getByPhysicianId($user_id, $clinic_id = '')
    {
        $data = self::where('user_id', $user_id);
        if (isset($clinic_id) && $clinic_id) {
            $data = $data->where('clinic_id', $clinic_id);
        }
        $data = $data->orderBy('start_date')->get()->toArray();
        return $data;
    }

    public static function checkExistDate($user_id, $date, $id = null)
    {
        $data = self::where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            ->where('user_id', $user_id);
        if ($id) {
            $data = $data->where('id', '!=', $id);
        }
        return $data->first();
    }

    public static function getNextWithDate($user_id, $date, $id = null)
    {
        $data = self::where('start_date', '>', $date)
            ->where('user_id', $user_id);
        if ($id) {
            $data = $data->where('id', '!=', $id);
        }
        return $data->first();
    }

    public static function scheduleTimeInSelect($schedule)
    {
        $schedule_times = array();
        ///////////// Saturday ///////////////
        if ($schedule['sat_start_time_1'] || $schedule['sat_start_time_2'] || $schedule['sat_start_time_3']) {
            if(empty($schedule['sat_start_time_1'])) {
                $schedule['sat_start_time_1'] = 'null';
                $schedule['sat_end_time_1'] = 'null';
            }
            $times = $schedule['sat_start_time_1'] . ' ' . $schedule['sat_end_time_1'];
            if ($schedule['num_of_shifts'] == 2 || $schedule['num_of_shifts'] == 3) {
                if(empty($schedule['sat_start_time_2'])) {
                    $schedule['sat_start_time_2'] = 'null';
                    $schedule['sat_end_time_2'] = 'null';
                }
                $times = $schedule['sat_start_time_1'] . ' ' . $schedule['sat_end_time_1']
                    . ' - ' . $schedule['sat_start_time_2'] . ' ' . $schedule['sat_end_time_2'];
                if ($schedule['num_of_shifts'] == 3) {
                    if(empty($schedule['sat_start_time_2'])) {
                        $schedule['sat_start_time_2'] = 'null';
                        $schedule['sat_end_time_2'] = 'null';
                    }
                    if(empty($schedule['sat_start_time_3'])) {
                        $schedule['sat_start_time_3'] = 'null';
                        $schedule['sat_end_time_3'] = 'null';
                    }
                    $times = $schedule['sat_start_time_1'] . ' ' . $schedule['sat_end_time_1']
                        . ' - ' . $schedule['sat_start_time_2'] . ' ' . $schedule['sat_end_time_2']
                        . ' - ' . $schedule['sat_start_time_3'] . ' ' . $schedule['sat_end_time_3'];
                }
            }
            $schedule_times[] = $times;
        }
        /////////// Sunday /////////////////
        if ($schedule['sun_start_time_1'] || $schedule['sun_start_time_2'] || $schedule['sun_start_time_3']) {
            if(empty($schedule['sun_start_time_1'])) {
                $schedule['sun_start_time_1'] = 'null';
                $schedule['sun_end_time_1'] = 'null';
            }
            $times = $schedule['sun_start_time_1'] . ' ' . $schedule['sun_end_time_1'];
            if ($schedule['num_of_shifts'] == 2 || $schedule['num_of_shifts'] == 3) {
                if(empty($schedule['sun_start_time_2'])) {
                    $schedule['sun_start_time_2'] = 'null';
                    $schedule['sun_end_time_2'] = 'null';
                }
                $times = $schedule['sun_start_time_1'] . ' ' . $schedule['sun_end_time_1']
                    . ' - ' . $schedule['sun_start_time_2'] . ' ' . $schedule['sun_end_time_2'];
                if ($schedule['num_of_shifts'] == 3 && $schedule['sun_start_time_3']) {
                    if(empty($schedule['sun_start_time_2'])) {
                        $schedule['sun_start_time_2'] = 'null';
                        $schedule['sun_end_time_2'] = 'null';
                    }
                    if(empty($schedule['sun_start_time_3'])) {
                        $schedule['sun_start_time_3'] = 'null';
                        $schedule['sun_end_time_3'] = 'null';
                    }
                    $times = $schedule['sun_start_time_1'] . ' ' . $schedule['sun_end_time_1']
                        . ' - ' . $schedule['sun_start_time_2'] . ' ' . $schedule['sun_end_time_2']
                        . ' - ' . $schedule['sun_start_time_3'] . ' ' . $schedule['sun_end_time_3'];
                }
            }
            if(!in_array($times, $schedule_times)){
                $schedule_times[] = $times;
            }
        }
        /////////// Monday /////////////////
        if ($schedule['mon_start_time_1'] || $schedule['mon_start_time_2'] || $schedule['mon_start_time_3']) {
            if(empty($schedule['mon_start_time_1'])) {
                $schedule['mon_start_time_1'] = 'null';
                $schedule['mon_end_time_1'] = 'null';
            }
            $times = $schedule['mon_start_time_1'] . ' ' . $schedule['mon_end_time_1'];
            if ($schedule['num_of_shifts'] == 2 || $schedule['num_of_shifts'] == 3) {
                if(empty($schedule['mon_start_time_2'])) {
                    $schedule['mon_start_time_2'] = 'null';
                    $schedule['mon_end_time_2'] = 'null';
                }
                $times = $schedule['mon_start_time_1'] . ' ' . $schedule['mon_end_time_1']
                    . ' - ' . $schedule['mon_start_time_2'] . ' ' . $schedule['mon_end_time_2'];
                if ($schedule['num_of_shifts'] == 3 && $schedule['mon_start_time_3']) {
                    if(empty($schedule['mon_start_time_2'])) {
                        $schedule['mon_start_time_2'] = 'null';
                        $schedule['mon_end_time_2'] = 'null';
                    }
                    if(empty($schedule['mon_start_time_3'])) {
                        $schedule['mon_start_time_3'] = 'null';
                        $schedule['mon_end_time_3'] = 'null';
                    }
                    $times = $schedule['mon_start_time_1'] . ' ' . $schedule['mon_end_time_1']
                        . ' - ' . $schedule['mon_start_time_2'] . ' ' . $schedule['mon_end_time_2']
                        . ' - ' . $schedule['mon_start_time_3'] . ' ' . $schedule['mon_end_time_3'];
                }
            }
            if(!in_array($times, $schedule_times)){
                $schedule_times[] = $times;
            }
        }
        /////////// Tuesday /////////////////
        if ($schedule['tues_start_time_1'] || $schedule['tues_start_time_2'] || $schedule['tues_start_time_3']) {
            if(empty($schedule['tues_start_time_1'])) {
                $schedule['tues_start_time_1'] = 'null';
                $schedule['tues_end_time_1'] = 'null';
            }
            $times = $schedule['tues_start_time_1'] . ' ' . $schedule['tues_end_time_1'];
            if ($schedule['num_of_shifts'] == 2 || $schedule['num_of_shifts'] == 3) {
                if(empty($schedule['tues_start_time_2'])) {
                    $schedule['tues_start_time_2'] = 'null';
                    $schedule['tues_end_time_2'] = 'null';
                }

                $times = $schedule['tues_start_time_1'] . ' ' . $schedule['tues_end_time_1']
                    . ' - ' . $schedule['tues_start_time_2'] . ' ' . $schedule['tues_end_time_2'];
                if ($schedule['num_of_shifts'] == 3 && $schedule['tues_start_time_3']) {
                    if(empty($schedule['tues_start_time_2'])) {
                        $schedule['tues_start_time_2'] = 'null';
                        $schedule['tues_end_time_2'] = 'null';
                    }
                    if(empty($schedule['tues_start_time_3'])) {
                        $schedule['tues_start_time_3'] = 'null';
                        $schedule['tues_end_time_3'] = 'null';
                    }
                    $times = $schedule['tues_start_time_1'] . ' ' . $schedule['tues_end_time_1']
                        . ' - ' . $schedule['tues_start_time_2'] . ' ' . $schedule['tues_end_time_2']
                        . ' - ' . $schedule['tues_start_time_3'] . ' ' . $schedule['tues_end_time_3'];
                }
            }
            if(!in_array($times, $schedule_times)){
                $schedule_times[] = $times;
            }
        }
        /////////// Wednesday /////////////////
        if ($schedule['wed_start_time_1'] || $schedule['wed_start_time_2'] || $schedule['wed_start_time_3']) {
            if(empty($schedule['wed_start_time_1'])) {
                $schedule['wed_start_time_1'] = 'null';
                $schedule['wed_end_time_1'] = 'null';
            }
            $times = $schedule['wed_start_time_1'] . ' ' . $schedule['wed_end_time_1'];
            if ($schedule['num_of_shifts'] == 2 || $schedule['num_of_shifts'] == 3) {
                if(empty($schedule['wed_start_time_2'])) {
                    $schedule['wed_start_time_2'] = 'null';
                    $schedule['wed_end_time_2'] = 'null';
                }
                $times = $schedule['wed_start_time_1'] . ' ' . $schedule['wed_end_time_1']
                    . ' - ' . $schedule['wed_start_time_2'] . ' ' . $schedule['wed_end_time_2'];
                if ($schedule['num_of_shifts'] == 3 && $schedule['wed_start_time_3']) {
                    if(empty($schedule['wed_start_time_2'])) {
                        $schedule['wed_start_time_2'] = 'null';
                        $schedule['wed_end_time_2'] = 'null';
                    }
                    if(empty($schedule['wed_start_time_3'])) {
                        $schedule['wed_start_time_3'] = 'null';
                        $schedule['wed_end_time_3'] = 'null';
                    }
                    $times = $schedule['wed_start_time_1'] . ' ' . $schedule['wed_end_time_1']
                        . ' - ' . $schedule['wed_start_time_2'] . ' ' . $schedule['wed_end_time_2']
                        . ' - ' . $schedule['wed_start_time_3'] . ' ' . $schedule['wed_end_time_3'];
                }
            }
            if(!in_array($times, $schedule_times)){
                $schedule_times[] = $times;
            }
        }
        /////////// Thursday /////////////////
        if ($schedule['thurs_start_time_1'] || $schedule['thurs_start_time_2'] || $schedule['thurs_start_time_3']) {
            if(empty($schedule['thurs_start_time_1'])) {
                $schedule['thurs_start_time_1'] = 'null';
                $schedule['thurs_end_time_1'] = 'null';
            }
            $times = $schedule['thurs_start_time_1'] . ' ' . $schedule['thurs_end_time_1'];
            if ($schedule['num_of_shifts'] == 2 || $schedule['num_of_shifts'] == 3) {
                if(empty($schedule['thurs_start_time_2'])) {
                    $schedule['thurs_start_time_2'] = 'null';
                    $schedule['thurs_end_time_2'] = 'null';
                }
                $times = $schedule['thurs_start_time_1'] . ' ' . $schedule['thurs_end_time_1']
                    . ' - ' . $schedule['thurs_start_time_2'] . ' ' . $schedule['thurs_end_time_2'];
                if ($schedule['num_of_shifts'] == 3 && $schedule['thurs_start_time_3']) {
                    if(empty($schedule['thurs_start_time_2'])) {
                        $schedule['thurs_start_time_2'] = 'null';
                        $schedule['thurs_end_time_2'] = 'null';
                    }
                    if(empty($schedule['thurs_start_time_3'])) {
                        $schedule['thurs_start_time_3'] = 'null';
                        $schedule['thurs_end_time_3'] = 'null';
                    }
                    $times = $schedule['thurs_start_time_1'] . ' ' . $schedule['thurs_end_time_1']
                        . ' - ' . $schedule['thurs_start_time_2'] . ' ' . $schedule['thurs_end_time_2']
                        . ' - ' . $schedule['thurs_start_time_3'] . ' ' . $schedule['thurs_end_time_3'];
                }
            }
            if(!in_array($times, $schedule_times)){
                $schedule_times[] = $times;
            }
        }
        /////////// Friday /////////////////
        if ($schedule['fri_start_time_1'] || $schedule['fri_start_time_2'] || $schedule['fri_start_time_3']) {
            if(empty($schedule['fri_start_time_1'])) {
                $schedule['fri_start_time_1'] = 'null';
                $schedule['fri_end_time_1'] = 'null';
            }
            $times = $schedule['fri_start_time_1'] . ' ' . $schedule['fri_end_time_1'];
            if ($schedule['num_of_shifts'] == 2 || $schedule['num_of_shifts'] == 3) {
                if(empty($schedule['fri_start_time_2'])) {
                    $schedule['fri_start_time_2'] = 'null';
                    $schedule['fri_end_time_2'] = 'null';
                }
                $times = $schedule['fri_start_time_1'] . ' ' . $schedule['fri_end_time_1']
                    . ' - ' . $schedule['fri_start_time_2'] . ' ' . $schedule['fri_end_time_2'];
                if ($schedule['num_of_shifts'] == 3 && $schedule['fri_start_time_3']) {
                    if(empty($schedule['fri_start_time_2'])) {
                        $schedule['fri_start_time_2'] = 'null';
                        $schedule['fri_end_time_2'] = 'null';
                    }
                    if(empty($schedule['fri_start_time_3'])) {
                        $schedule['fri_start_time_3'] = 'null';
                        $schedule['fri_end_time_3'] = 'null';
                    }
                    $times = $schedule['fri_start_time_1'] . ' ' . $schedule['fri_end_time_1']
                        . ' - ' . $schedule['fri_start_time_2'] . ' ' . $schedule['fri_end_time_2']
                        . ' - ' . $schedule['fri_start_time_3'] . ' ' . $schedule['fri_end_time_3'];
                }
            }
            if(!in_array($times, $schedule_times)){
                $schedule_times[] = $times;
            }
        }
        $physicianScheduleException = PhysicianScheduleException::getAll([
            'withoutDetails' => true,
            'clinic_id' => $schedule['clinic_id'],
            'user_id' => $schedule['user_id'],
            'start_date' => $schedule['start_date'],
            'end_date' => $schedule['end_date'],
        ]);
        if($physicianScheduleException) {
            foreach ($physicianScheduleException as $key => $val) {
                if($val['shift1_dayoff'] == 2 || $val['shift2_dayoff'] == 2 || $val['shift3_dayoff'] == 2){
                    if(empty($val['shift1_time_from'])) {
                        $val['shift1_time_from'] = 'null';
                        $val['shift1_time_to'] = 'null';
                    }
                    $times = $val['shift1_time_from'] . ' ' . $val['shift1_time_to'];
                    if($val['num_of_shifts'] == 2 || $val['num_of_shifts'] == 3) {
                        if(empty($val['shift2_time_from'])) {
                            $val['shift2_time_from'] = 'null';
                            $val['shift2_time_to'] = 'null';
                        }
                        $times = $val['shift1_time_from'] . ' ' . $val['shift1_time_to']
                            . ' - ' . $val['shift2_time_from'] . ' ' . $val['shift2_time_to'];
                        if($val['num_of_shifts'] == 3) {
                            if(empty($val['shift3_time_from'])) {
                                $val['shift3_time_from'] = 'null';
                                $val['shift3_time_to'] = 'null';
                            }
                            $times = $val['shift1_time_from'] . ' ' . $val['shift1_time_to']
                                . ' - ' . $val['shift2_time_from'] . ' ' . $val['shift2_time_to']
                                . ' - ' . $val['shift3_time_from'] . ' ' . $val['shift3_time_to'];
                        }
                    }
                    if(!in_array($times, $schedule_times)){
                        $schedule_times[] = $times;
                    }
                }
            }
        }
        return $schedule_times;
    }
}
