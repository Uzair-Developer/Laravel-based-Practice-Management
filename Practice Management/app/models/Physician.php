<?php

use core\enums\ReservationStatus;
use core\physician\PhysicianManager;
use core\userLocalization\UserLocalizationRepository;

class Physician extends Eloquent
{
    protected $table = 'physicians';
    protected $guarded = array('');

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function checkHisExist($his_id)
    {
        return self::where('his_id', $his_id)->first();
    }

    public static function getByPhysicianId($user_id)
    {
        return self::where('user_id', $user_id)->first();
    }

    public static function getReport($inputs)
    {
        ini_set('max_execution_time', 0);
        if ($inputs) {
            $from_date = $inputs['from_date'];
            $to_date = date('Y-m-d', strtotime($inputs['to_date']));
            $count = 0;
            $totalScheduleTime = 0;
            $totalExceptionTime = 0;
            $totalWorkTime = 0;
            $totalVisits = 0; // patient
            $totalRevisitAttend = 0; // patient
            $totalRevisitNoShow = 0; // patient
            $totalEstimate = 0;
            $totalAllVisits = 0;
            $totalRevisits = 0;
            $totalWaitingList = 0;
            $totalBooked = 0;
            $totalClinics = 0;
            $totalHospitals = 0;
            $totalPhysicians = 0;
            $totalNoShow = 0;
            $totalNoShowRate = 0;
            $totalPatientPaid = 0;
            $totalAttendSubPaid = 0;

            $diff = abs(strtotime($to_date) - strtotime($from_date));
            $years = floor($diff / (365 * 60 * 60 * 24));
            $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
            $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            $data['days_count'] = $days;

            if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                $physicianArray = array($inputs['physician_id']);
            } elseif (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                $physicianArray = UsersLocalizationClinics::getActivePhysiciansByClinicId($inputs['clinic_id'], true);
            } elseif (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
                $physicianArray = UsersLocalizationClinics::getActivePhysiciansByHospitalId($inputs['hospital_id'], true);
            } else {
                $physicianArray = User::getPhysicianIds(true);
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
            $data['data'] = array();
            $clinicArray = array();
            $hospitalArray = array();
            $physicianNameArray = array();
            while (1 == 1) {
                foreach ($physicianArray as $key => $val) {
                    $phySchedule = PhysicianSchedule::getByPhysicianId_Date($val, $from_date);
                    if ($phySchedule) {
                        $count++;
                        $clinicData = Clinic::getById($phySchedule['clinic_id']);
                        $data['data'][$count]['clinic_name'] = $clinicData['name'];
                        if (!in_array($clinicData['name'], $clinicArray)) {
                            $clinicArray[] = $clinicData['name'];
                            $totalClinics++;
                        }
                        $hospitalName = Hospital::getName($clinicData['hospital_id']);
                        $data['data'][$count]['hospital_name'] = $hospitalName;
                        if (!in_array($hospitalName, $hospitalArray)) {
                            $hospitalArray[] = $hospitalName;
                            $totalHospitals++;
                        }
                        $data['data'][$count]['date'] = $from_date;
                        $physicianName = User::getName($val);
                        $data['data'][$count]['physician_name'] = $physicianName;
                        if (!in_array($physicianName, $physicianNameArray)) {
                            $physicianNameArray[] = $physicianName;
                            $totalPhysicians++;
                        }
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
                        $totalScheduleTime += $schedule_time;
                        $data['data'][$count]['schedule_time'] = $schedule_time;
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
                            $data['data'][$count]['exception_time'] = $exception_time;
                        } else {
                            $exception_time = 0;
                            $data['data'][$count]['exception_time'] = $exception_time;
                        }
                        $totalExceptionTime += $exception_time;
                        if ($schedule_time) {
                            $work_time = $schedule_time - $exception_time;
                        } else {
                            $work_time = 0;
                        }
                        $totalWorkTime += $work_time;
                        $data['data'][$count]['work_time'] = $work_time;
                        $estimateVisits = round(($work_time) / $phySchedule['slots']);
                        $data['data'][$count]['estimate_visits'] = $estimateVisits;
                        $totalEstimate += $estimateVisits;
                        $patientVisits = Reservation::countByPhysicianAndDate($val, $from_date, true, true);
                        $totalVisits += $patientVisits;
                        $data['data'][$count]['patientVisits'] = $patientVisits;

                        if (app('production')) {
                            $patientPaid = HisBillDetail::getCount(array(
                                'date_from' => $from_date,
                                'date_to' => $from_date,
                                'physician_id' => $val,
                            ));
                            $totalPatientPaid += $patientPaid;
                            $data['data'][$count]['patientPaid'] = $patientPaid;
                            $attendSubPaid = $patientPaid - $patientVisits;
                            $data['data'][$count]['attendSubPaid'] = $attendSubPaid;
                            $totalAttendSubPaid += $attendSubPaid;

                            if ($work_time) {
                                $work_time_inTimeFormat = Functions::convertToHoursMins($work_time);
                                $hours = '';
                                $minutes = '';
                                sscanf($work_time_inTimeFormat, "%d:%d", $hours, $minutes);
                                $time_hours = $hours + ($minutes / 60);
//                                dd($patientPaid , $time_hours);
                                if ($time_hours) {
                                    $data['data'][$count]['PTSeenPerHour'] = round($patientPaid / $time_hours, 2);
                                } else {
                                    $data['data'][$count]['PTSeenPerHour'] = 0;
                                }
                            } else {
                                $data['data'][$count]['PTSeenPerHour'] = 0;
                            }
                        }

                        $allVisits = Reservation::countByPhysicianAndDate($val, $from_date, false, true, false);
                        $countBooking = 0;
                        $countWaitingList = 0;
                        foreach ($allVisits as $key2 => $val2) {
                            if ($val2['type'] == 1) {
                                $countBooking++;
                            } elseif ($val2['type'] == 2) {
                                $countWaitingList++;
                            }
                        }
                        $data['data'][$count]['countBooking'] = $countBooking;
                        $data['data'][$count]['countWaitingList'] = $countWaitingList;
                        $totalBooked += $countBooking;
                        $totalWaitingList += $countWaitingList;

                        $allVisitsCount = count($allVisits);
                        $totalAllVisits += $allVisitsCount;
                        $data['data'][$count]['allVisits'] = $allVisitsCount;
                        $noShow = $allVisitsCount - $patientVisits;
                        $data['data'][$count]['noShow'] = $noShow;
                        $totalNoShow += $noShow;

                        if ($allVisitsCount) {
                            $noShowRate = round(($noShow / $allVisitsCount) * 100, 2);
                        } else {
                            $noShowRate = 0;
                        }
                        $data['data'][$count]['noShowRate'] = $noShowRate;
                        $totalNoShowRate += $noShowRate;

                        $allRevisit = Reservation::countByPhysicianAndDate($val, $from_date, false, false, true, true);
                        $data['data'][$count]['allRevisit'] = $allRevisit;
                        $totalRevisits += $allRevisit;
                        $revisitAttend = Reservation::countByPhysicianAndDate($val, $from_date, true, false, true, true);
                        $data['data'][$count]['revisitAttend'] = $revisitAttend;
                        $totalRevisitAttend += $revisitAttend;

                        $revisitNoShow = $allRevisit - $revisitAttend;
                        $data['data'][$count]['revisitNoShow'] = $revisitNoShow;
                        $totalRevisitNoShow += $revisitNoShow;
                    } else {
                        continue;
                    }
                }
                if ($from_date == $to_date) {
                    break;
                } else {
                    $from_date = date('Y-m-d', strtotime("+1 day", strtotime($from_date)));
                    $count++;
                }
            }
            $data['total_schedule_time'] = $totalScheduleTime;
            $data['total_exception_time'] = $totalExceptionTime;
            $data['total_work_time'] = $totalWorkTime;
            $work_time_inTimeFormat = Functions::convertToHoursMins($totalWorkTime);
            $hours = '';
            $minutes = '';
            sscanf($work_time_inTimeFormat, "%d:%d", $hours, $minutes);
            $time_hours = $hours + ($minutes / 60);
            if ($time_hours <= 0) {
                $data['total_PTSeenPerHour'] = 0;
            } else {
                $data['total_PTSeenPerHour'] = round($totalPatientPaid / $time_hours, 2);
            }
            $data['total_visits'] = $totalVisits;
            $data['total_all_visits'] = $totalAllVisits;
            $data['total_estimate'] = $totalEstimate;
            $data['total_hospitals'] = $totalHospitals;
            $data['total_clinics'] = $totalClinics;
            $data['total_physicians'] = $totalPhysicians;
            $data['total_no_show'] = $totalNoShow;
            $data['total_no_show_rate'] = round($totalNoShowRate / $count, 2);
            $data['total_patient_paid'] = $totalPatientPaid;
            $data['total_attend_sub_paid'] = $totalAttendSubPaid;
            $data['totalBooked'] = $totalBooked;
            $data['totalWaitingList'] = $totalWaitingList;
            $data['totalRevisits'] = $totalRevisits;
            $data['totalRevisitAttend'] = $totalRevisitAttend;
            $data['totalRevisitNoShow'] = $totalRevisitNoShow;
            return $data;
        } else {
            return array();
        }
    }

    public static function getExceptionReport($inputs)
    {
        ini_set('max_execution_time', 0);
        if ($inputs) {
            $clinicArray = array();
            if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                $clinicArray[0] = Clinic::getById($inputs['clinic_id'])->toArray();
            } elseif (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
                $clinicArray = Clinic::getAllByHospitalId($inputs['hospital_id']);
            }
            if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                $type = 1;
            } else {
                $type = 2;
            }
            foreach ($clinicArray as $index => $value) {
                if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                    $physicianArray[0] = User::getById($inputs['physician_id'])->toArray();
                } else {
                    $physicianArray = UsersLocalizationClinics::getActivePhysiciansByClinicId($value['id'], true, true, true);
                }
                if (empty($physicianArray)) {
                    unset($clinicArray[$index]);
                    continue;
                }
                foreach ($physicianArray as $key => $val) {
                    if($type == 1) {
                        $val['user_id'] = $val['id'];
                    }
                    $from_date = $inputs['from_date'];
                    $to_date = date('Y-m-d', strtotime($inputs['to_date']));
                    $exceptions = AttributePms::getAllWithOutPaginate(1, ['effect' => 1]);
                    foreach ($exceptions as $key3 => $exception) {
                        $clinicArray[$index]['physicians'][$key]['exceptions'][$exception['id']] = 0;
                    }
                    while (1 == 1) {
                        $physicianData = User::getById($val['user_id']);
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
                        $phySchedule = PhysicianSchedule::getByPhysicianId_Date($val['user_id'], $from_date);
                        if ($phySchedule) {
                            $clinicArray[$index]['physicians'][$key]['physicianData'] = $physicianData->toArray();
                            $clinicSchedule = ClinicSchedule::getById($phySchedule['clinic_schedule_id']);
                            $physicianManager = new PhysicianManager();
                            $availableTimes = array();
                            $physicianManager->getAvailableTimeOfPhysician($availableTimes, $phySchedule, $clinicSchedule, $from_date);
                            if ($availableTimes) {
                                foreach ($availableTimes as $key2 => $val2) {
                                    if ((isset($val2['exception_reason_id']) && isset($val2['reserved']) &&
                                            isset($val2['effect']) && $val2['effect'] == 1)
                                        && ($val2['status'] == ReservationStatus::not_available
                                            || $val2['status'] == ReservationStatus::pending)
                                    ) {
                                        if (!isset($clinicArray[$index]['physicians'][$key]['exceptions'][$val2['exception_reason_id']])) {
                                            $clinicArray[$index]['physicians'][$key]['exceptions'][$val2['exception_reason_id']] = 0;
                                        }
                                        $seconds = Functions::hoursToSeconds($val2['time']);
                                        $newSeconds = $seconds + ($phySchedule['slots'] * 60);
                                        $clinicArray[$index]['physicians'][$key]['exceptions'][$val2['exception_reason_id']]
                                            += abs(($newSeconds) - $seconds) / 60;
                                    }
                                }
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

    public static function getPhysicianIdsByProfileStatus($status)
    {
        return self::where('current_status', $status)->lists('user_id');
    }
}
