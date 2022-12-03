<?php

use core\enums\ReservationStatus;
use core\physician\PhysicianManager;

class Clinic extends Eloquent
{
    protected $table = 'clinics';
    protected $guarded = array('');

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function editByHisId($inputs, $id)
    {
        return self::where('his_id', $id)->update($inputs);
    }

    public static function getAll()
    {
        return self::all()->toArray();
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getByHisId($id)
    {
        return self::where('his_id', $id)->first();
    }

    public static function getByIds($ids)
    {
        return self::whereIn('id', $ids)->get()->toArray();
    }

    public static function getNameById($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function getSmsCode($id)
    {
        return self::where('id', $id)->pluck('sms_code');
    }

    public static function getByHospitalId($hospital_id)
    {
        return self::where('hospital_id', $hospital_id)->lists('id');
    }

    public static function getAllByHospitalId($hospital_id)
    {
        return self::where('hospital_id', $hospital_id)->get()->toArray();
    }

    public static function getAllOpened()
    {
        return self::where('status', 1)->get()->toArray();
    }

    public static function closeAllOpened()
    {
        return self::where('status', 1)->update(array(
            'status' => 0
        ));
    }

    public function users()
    {
        return $this->belongsToMany('User', 'user_localizations', 'clinic_id', 'user_id')->withPivot('hospital_id');
    }


    public static function getReport($inputs)
    {
        ini_set('max_execution_time', 0);
        if ($inputs) {
            $clinicArray = array();
            if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                $clinicArray[0] = self::getById($inputs['clinic_id'])->toArray();
            } elseif (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
                $clinicArray = Clinic::getAllByHospitalId($inputs['hospital_id']);
            }
            $daysName = array(
                'saturday' => 'sat',
                'sunday' => 'sun',
                'monday' => 'mon',
                'tuesday' => 'tues',
                'wednesday' => 'wed',
                'thursday' => 'thurs',
                'friday' => 'fri',
            );
            foreach ($clinicArray as $index => $value) {
                $physicianArray = UsersLocalizationClinics::getActivePhysiciansByClinicId($value['id'], true, true);
                if (empty($physicianArray)) {
                    unset($clinicArray[$index]);
                    continue;
                }
                foreach ($physicianArray as $key => $val) {
                    $from_date = $inputs['from_date'];
                    $to_date = date('Y-m-d', strtotime($inputs['to_date']));
                    $clinicArray[$index]['physicians'][$key]['schedule_time'] = 0;
                    $clinicArray[$index]['physicians'][$key]['exception_time'] = 0;
                    $clinicArray[$index]['physicians'][$key]['work_time'] = 0;
                    $clinicArray[$index]['physicians'][$key]['estimate_visits'] = 0;
                    $clinicArray[$index]['physicians'][$key]['patientVisits'] = 0;
                    $clinicArray[$index]['physicians'][$key]['patientPaid'] = 0;
                    $clinicArray[$index]['physicians'][$key]['PTSeenPerHour'] = 0;
                    $clinicArray[$index]['physicians'][$key]['allVisits'] = 0;
                    $clinicArray[$index]['physicians'][$key]['noShow'] = 0;
                    $clinicArray[$index]['physicians'][$key]['noShowRate'] = 0;
                    while (1 == 1) {
                        $physicianData = User::getById($val);
                        if ($physicianData['activated'] == 0 && $physicianData['deactivate_date']
                            && $physicianData['deactivate_date'] < $from_date
                        ) {
                            if ($from_date == $to_date) {
                                unset($clinicArray[$index]['physicians'][$key]);
                                break;
                            } else {
                                $from_date = date('Y-m-d', strtotime("+1 day", strtotime($from_date)));
                            }
                            continue;
                        }
                        $phySchedule = PhysicianSchedule::getByPhysicianId_Date($val, $from_date);
                        if ($phySchedule) {
                            $clinicArray[$index]['physicians'][$key]['physicianData'] = $physicianData->toArray();

                            $schedule_time = 0;
                            $inputDayName = lcfirst(date('l', strtotime($from_date)));
                            if ($phySchedule['num_of_shifts'] == 1 && strpos($phySchedule['dayoff_1'], $inputDayName) === false) {
                                $from_time = Functions::hoursToSeconds($phySchedule[$daysName[$inputDayName] . '_start_time_1']);
                                $to_time = Functions::hoursToSeconds($phySchedule[$daysName[$inputDayName] . '_end_time_1']);
                                $schedule_time += round(abs($to_time - $from_time) / 60);
                            } elseif ($phySchedule['num_of_shifts'] == 2 || $phySchedule['num_of_shifts'] == 3) {
                                if (strpos($phySchedule['dayoff_1'], $inputDayName) === false) {
                                    $from_time = Functions::hoursToSeconds($phySchedule[$daysName[$inputDayName] . '_start_time_1']);
                                    $to_time = Functions::hoursToSeconds($phySchedule[$daysName[$inputDayName] . '_end_time_1']);
                                    $schedule_time += round(abs($to_time - $from_time) / 60);
                                }
                                if (strpos($phySchedule['dayoff_2'], $inputDayName) === false) {
                                    $from_time = Functions::hoursToSeconds($phySchedule[$daysName[$inputDayName] . '_start_time_2']);
                                    $to_time = Functions::hoursToSeconds($phySchedule[$daysName[$inputDayName] . '_end_time_2']);
                                    $schedule_time += round(abs($to_time - $from_time) / 60);
                                }
                                if ($phySchedule['num_of_shifts'] == 3) {
                                    if (strpos($phySchedule['dayoff_3'], $inputDayName) === false) {
                                        $from_time = Functions::hoursToSeconds($phySchedule[$daysName[$inputDayName] . '_start_time_3']);
                                        $to_time = Functions::hoursToSeconds($phySchedule[$daysName[$inputDayName] . '_end_time_3']);
                                        $schedule_time += round(abs($to_time - $from_time) / 60);
                                    }
                                }
                            }
                            if (!isset($clinicArray[$index]['physicians'][$key]['schedule_time'])) {
                                $clinicArray[$index]['physicians'][$key]['schedule_time'] = 0;
                            }
                            $clinicArray[$index]['physicians'][$key]['schedule_time'] += $schedule_time;

                            if (!isset($clinicArray[$index]['physicians'][$key]['exception_time'])) {
                                $clinicArray[$index]['physicians'][$key]['exception_time'] = 0;
                            }
                            $clinicSchedule = ClinicSchedule::getById($phySchedule['clinic_schedule_id']);
                            $physicianManager = new PhysicianManager();
                            $availableTimes = array();
                            $physicianManager->getAvailableTimeOfPhysician($availableTimes, $phySchedule, $clinicSchedule, $from_date);
                            if ($availableTimes) {
                                $dailyMinutesException = 0;
                                foreach ($availableTimes as $key2 => $val2) {
                                    if ((isset($val2['reserved']) && isset($val2['effect']) && $val2['effect'] == 1)
                                        && ($val2['status'] == ReservationStatus::not_available
                                            || $val2['status'] == ReservationStatus::pending)
                                    ) {
                                        $seconds = Functions::hoursToSeconds($val2['time']);
                                        $newSeconds = $seconds + ($phySchedule['slots'] * 60);

                                        $dailyMinutesException += abs(($newSeconds) - $seconds) / 60;
                                    }
                                }
                                $exception_time = round($dailyMinutesException, 1);
                                $clinicArray[$index]['physicians'][$key]['exception_time'] += $exception_time;
                            } else {
                                $exception_time = 0;
                                $clinicArray[$index]['physicians'][$key]['exception_time'] += $exception_time;
                            }
                            if ($schedule_time) {
                                $work_time = $schedule_time - $exception_time;
                            } else {
                                $work_time = 0;
                            }
                            if (!isset($clinicArray[$index]['physicians'][$key]['work_time'])) {
                                $clinicArray[$index]['physicians'][$key]['work_time'] = 0;
                            }
                            $clinicArray[$index]['physicians'][$key]['work_time'] += $work_time;
                            $estimateVisits = round(($work_time) / $phySchedule['slots']);
                            if (!isset($clinicArray[$index]['physicians'][$key]['estimate_visits'])) {
                                $clinicArray[$index]['physicians'][$key]['estimate_visits'] = 0;
                            }
                            $clinicArray[$index]['physicians'][$key]['estimate_visits'] += $estimateVisits;
                            $patientVisits = Reservation::countByPhysicianAndDate($val, $from_date, true, true);
                            if (!isset($clinicArray[$index]['physicians'][$key]['patientVisits'])) {
                                $clinicArray[$index]['physicians'][$key]['patientVisits'] = 0;
                            }
                            $clinicArray[$index]['physicians'][$key]['patientVisits'] += $patientVisits;

                            if (app('production')) {
                                $patientPaid = HisBillDetail::getCount(array(
                                    'date_from' => $from_date,
                                    'date_to' => $from_date,
                                    'physician_id' => $val,
                                ));
                                if (!isset($clinicArray[$index]['physicians'][$key]['patientPaid'])) {
                                    $clinicArray[$index]['physicians'][$key]['patientPaid'] = 0;
                                }
                                $clinicArray[$index]['physicians'][$key]['patientPaid'] += $patientPaid;
                                if (!isset($clinicArray[$index]['physicians'][$key]['PTSeenPerHour'])) {
                                    $clinicArray[$index]['physicians'][$key]['PTSeenPerHour'] = 0;
                                }
                                if ($work_time) {
                                    $clinicArray[$index]['physicians'][$key]['PTSeenPerHour'] += round($patientPaid / $work_time, 2);
                                } else {
                                    $clinicArray[$index]['physicians'][$key]['PTSeenPerHour'] += 0;
                                }
                                $allVisits = Reservation::countByPhysicianAndDate($val, $from_date, false, true, false);
                                $allVisitsCount = count($allVisits);
                                if (!isset($clinicArray[$index]['physicians'][$key]['allVisits'])) {
                                    $clinicArray[$index]['physicians'][$key]['allVisits'] = 0;
                                }
                                $clinicArray[$index]['physicians'][$key]['allVisits'] += $allVisitsCount;
                                $noShow = $allVisitsCount - $patientPaid;
                                if (!isset($clinicArray[$index]['physicians'][$key]['noShow'])) {
                                    $clinicArray[$index]['physicians'][$key]['noShow'] = 0;
                                }
                                $clinicArray[$index]['physicians'][$key]['noShow'] += $noShow;
                            }
                        } else {
                            unset($clinicArray[$index]['physicians'][$key]);
                        }
                        if ($from_date == $to_date) {
                            break;
                        } else {
                            $from_date = date('Y-m-d', strtotime("+1 day", strtotime($from_date)));
                        }
                    }
                }
                if (empty($clinicArray[$index]['physicians'])) {
                    unset($clinicArray[$index]);
                }
            }
            return $clinicArray;
        } else {
            return array();
        }
    }

}
