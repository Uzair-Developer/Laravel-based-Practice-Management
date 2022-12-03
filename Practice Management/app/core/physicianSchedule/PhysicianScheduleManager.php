<?php
namespace core\physicianSchedule;


use Cartalyst\Sentry\Facades\Laravel\Sentry;
use ClinicSchedule;
use core\BaseManager;
use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use core\enums\ResponseTypes;
use Input;
use PhysicianSchedule;
use Reservation;
use ReservationHistory;

class PhysicianScheduleManager extends BaseManager
{
    function __construct()
    {
        $this->PhysicianScheduleValidator = new PhysicianScheduleValidator();
    }

    public function createPhysicianSchedule($inputs)
    {
        $validator = $this->PhysicianScheduleValidator->validatePhysicianSchedule($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            if ($inputs['start_date'] > $inputs['end_date']) {
                return $this->response()->ResponseObject(ResponseTypes::error, "Make sure the end date is greater than start date");
            }
            $clinicSch = ClinicSchedule::getById($inputs['clinic_schedule_id']);
            if ($inputs['start_date'] < $clinicSch['start_date']) {
                return $this->response()->ResponseObject(ResponseTypes::error, "Make sure the start date is greater than start date of this clinic schedule");
            }
            if ($inputs['end_date'] > $clinicSch['end_date']) {
                return $this->response()->ResponseObject(ResponseTypes::error, "Make sure the end date is less than end date of this clinic schedule");
            }
            if (PhysicianSchedule::checkExist($inputs['user_id'], $inputs['clinic_schedule_id'], $inputs['start_date'])) {
                return $this->response()->ResponseObject(ResponseTypes::error, "Start date is used in another schedule");
            }
            if (PhysicianSchedule::checkExist($inputs['user_id'], $inputs['clinic_schedule_id'], $inputs['end_date'])) {
                return $this->response()->ResponseObject(ResponseTypes::error, "End date is used in another schedule");
            }
            $start_date = $inputs['start_date'];
            $end_date = $inputs['end_date'];
            $count = 0;
            $dayoff_1 = isset($inputs['dayoff_1']) ? $inputs['dayoff_1'] : array();
            $dayoff_2 = array();
            $dayoff_3 = array();
            if ($inputs['num_of_shifts'] == 2 || $inputs['num_of_shifts'] == 3) {
                $dayoff_2 = isset($inputs['dayoff_2']) ? $inputs['dayoff_2'] : array();
                if ($inputs['num_of_shifts'] == 3) {
                    $dayoff_3 = isset($inputs['dayoff_3']) ? $inputs['dayoff_3'] : array();
                }
            }
            $Repo = new PhysicianScheduleRepository();

            if (isset($inputs['split']) && $inputs['split'] == 1) {
                while (date("Y-m-t", strtotime($start_date)) <= date("Y-m-t", strtotime($end_date))) {
                    if (date("Y-m-t", strtotime($start_date)) == date("Y-m-t", strtotime($end_date))) {
                        if ($count == 0) {
                            $inputs['start_date'] = $start_date;
                            $inputs['end_date'] = $end_date;
                        } else {
                            $inputs['start_date'] = date("Y-m-01", strtotime($start_date));
                            $inputs['end_date'] = $end_date;
                        }
                    } else {
                        if ($count == 0) {
                            $inputs['start_date'] = $start_date;
                            $inputs['end_date'] = date("Y-m-t", strtotime($start_date));
                        } else {
                            $inputs['start_date'] = date("Y-m-01", strtotime($start_date));
                            $inputs['end_date'] = date("Y-m-t", strtotime($start_date));
                        }
                    }
                    $count++;
                    $start_date = date("Y-m-d", strtotime("+1 month", strtotime($start_date)));
                    $inputs['publish'] = 2;
                    $inputs['create_timestamp'] = time();
                    if ($inputs['num_of_shifts'] == 1) {
                        if($dayoff_1) {
                            $inputs['dayoff_1'] = implode(',', $dayoff_1);
                        } else {
                            $inputs['dayoff_1'] = null;
                        }
                        $inputs['dayoff_2'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                        $inputs['dayoff_3'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                        $inputs['sat_start_time_2'] = null;
                        $inputs['sat_end_time_2'] = null;
                        $inputs['sun_start_time_2'] = null;
                        $inputs['sun_end_time_2'] = null;
                        $inputs['mon_start_time_2'] = null;
                        $inputs['mon_end_time_2'] = null;
                        $inputs['tues_start_time_2'] = null;
                        $inputs['tues_end_time_2'] = null;
                        $inputs['wed_start_time_2'] = null;
                        $inputs['wed_end_time_2'] = null;
                        $inputs['thurs_start_time_2'] = null;
                        $inputs['thurs_end_time_2'] = null;
                        $inputs['fri_start_time_2'] = null;
                        $inputs['fri_end_time_2'] = null;
                        $inputs['sat_start_time_3'] = null;
                        $inputs['sat_end_time_3'] = null;
                        $inputs['sun_start_time_3'] = null;
                        $inputs['sun_end_time_3'] = null;
                        $inputs['mon_start_time_3'] = null;
                        $inputs['mon_end_time_3'] = null;
                        $inputs['tues_start_time_3'] = null;
                        $inputs['tues_end_time_3'] = null;
                        $inputs['wed_start_time_3'] = null;
                        $inputs['wed_end_time_3'] = null;
                        $inputs['thurs_start_time_3'] = null;
                        $inputs['thurs_end_time_3'] = null;
                        $inputs['fri_start_time_3'] = null;
                        $inputs['fri_end_time_3'] = null;
                    } elseif ($inputs['num_of_shifts'] == 2) {
                        if($dayoff_1) {
                            $inputs['dayoff_1'] = implode(',', $dayoff_1);
                        } else {
                            $inputs['dayoff_1'] = null;
                        }
                        if($dayoff_2) {
                            $inputs['dayoff_2'] = implode(',', $dayoff_2);
                        } else {
                            $inputs['dayoff_2'] = null;
                        }
                        $inputs['dayoff_3'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                        $inputs['sat_start_time_3'] = null;
                        $inputs['sat_end_time_3'] = null;
                        $inputs['sun_start_time_3'] = null;
                        $inputs['sun_end_time_3'] = null;
                        $inputs['mon_start_time_3'] = null;
                        $inputs['mon_end_time_3'] = null;
                        $inputs['tues_start_time_3'] = null;
                        $inputs['tues_end_time_3'] = null;
                        $inputs['wed_start_time_3'] = null;
                        $inputs['wed_end_time_3'] = null;
                        $inputs['thurs_start_time_3'] = null;
                        $inputs['thurs_end_time_3'] = null;
                        $inputs['fri_start_time_3'] = null;
                        $inputs['fri_end_time_3'] = null;
                    } elseif ($inputs['num_of_shifts'] == 3) {
                        if($dayoff_1) {
                            $inputs['dayoff_1'] = implode(',', $dayoff_1);
                        } else {
                            $inputs['dayoff_1'] = null;
                        }
                        if($dayoff_2) {
                            $inputs['dayoff_2'] = implode(',', $dayoff_2);
                        } else {
                            $inputs['dayoff_2'] = null;
                        }
                        if($dayoff_3) {
                            $inputs['dayoff_3'] = implode(',', $dayoff_3);
                        } else {
                            $inputs['dayoff_3'] = null;
                        }
                    }
                    unset($inputs['split']);
                    $Repo->save($inputs);
                }
            } elseif (isset($inputs['split']) && $inputs['split'] == 2) {
                $inputs['publish'] = 2;
                $inputs['create_timestamp'] = time();
                if ($inputs['num_of_shifts'] == 1) {
                    $inputs['dayoff_1'] = implode(',', $dayoff_1);
                    $inputs['dayoff_2'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                    $inputs['dayoff_3'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                    $inputs['sat_start_time_2'] = null;
                    $inputs['sat_end_time_2'] = null;
                    $inputs['sun_start_time_2'] = null;
                    $inputs['sun_end_time_2'] = null;
                    $inputs['mon_start_time_2'] = null;
                    $inputs['mon_end_time_2'] = null;
                    $inputs['tues_start_time_2'] = null;
                    $inputs['tues_end_time_2'] = null;
                    $inputs['wed_start_time_2'] = null;
                    $inputs['wed_end_time_2'] = null;
                    $inputs['thurs_start_time_2'] = null;
                    $inputs['thurs_end_time_2'] = null;
                    $inputs['fri_start_time_2'] = null;
                    $inputs['fri_end_time_2'] = null;
                    $inputs['sat_start_time_3'] = null;
                    $inputs['sat_end_time_3'] = null;
                    $inputs['sun_start_time_3'] = null;
                    $inputs['sun_end_time_3'] = null;
                    $inputs['mon_start_time_3'] = null;
                    $inputs['mon_end_time_3'] = null;
                    $inputs['tues_start_time_3'] = null;
                    $inputs['tues_end_time_3'] = null;
                    $inputs['wed_start_time_3'] = null;
                    $inputs['wed_end_time_3'] = null;
                    $inputs['thurs_start_time_3'] = null;
                    $inputs['thurs_end_time_3'] = null;
                    $inputs['fri_start_time_3'] = null;
                    $inputs['fri_end_time_3'] = null;
                } elseif ($inputs['num_of_shifts'] == 2) {
                    $inputs['dayoff_1'] = implode(',', $dayoff_1);
                    $inputs['dayoff_2'] = implode(',', $dayoff_2);
                    $inputs['dayoff_3'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                    $inputs['sat_start_time_3'] = null;
                    $inputs['sat_end_time_3'] = null;
                    $inputs['sun_start_time_3'] = null;
                    $inputs['sun_end_time_3'] = null;
                    $inputs['mon_start_time_3'] = null;
                    $inputs['mon_end_time_3'] = null;
                    $inputs['tues_start_time_3'] = null;
                    $inputs['tues_end_time_3'] = null;
                    $inputs['wed_start_time_3'] = null;
                    $inputs['wed_end_time_3'] = null;
                    $inputs['thurs_start_time_3'] = null;
                    $inputs['thurs_end_time_3'] = null;
                    $inputs['fri_start_time_3'] = null;
                    $inputs['fri_end_time_3'] = null;
                } elseif ($inputs['num_of_shifts'] == 3) {
                    $inputs['dayoff_1'] = implode(',', $dayoff_1);
                    $inputs['dayoff_2'] = implode(',', $dayoff_2);
                    $inputs['dayoff_3'] = implode(',', $dayoff_3);
                }
                unset($inputs['split']);
                $Repo->save($inputs);
            }
            return $this->response()->ResponseObject(ResponseTypes::success, 'Added Successfully');
        } catch (\Exception $e) {
            dd($e->getMessage(), $e->getFile(), $e->getLine());

            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, There is error please try again");
        }

    }

    public function updatePhysicianSchedule($inputs, $id)
    {
        $user = Sentry::getUser();
        $physicianSchedule = PhysicianSchedule::getById($id);
        try {
            $Repo = new PhysicianScheduleRepository();
            if ($Repo->checkExist($inputs['user_id'], $physicianSchedule['clinic_schedule_id'], $id, $physicianSchedule['start_date'])) {
                return $this->response()->ResponseObject(ResponseTypes::error, "This physician take this schedule before");
            }
            $is_edit = $inputs['is_edit'];
            unset($inputs['is_edit']);
            if ($is_edit == 0) {
                return $this->response()->ResponseObject(ResponseTypes::success, 'No Changes Found');
            } else {
                if ($physicianSchedule['start_date'] > date('Y-m-d')) {
                    $start_date = $physicianSchedule['start_date'];
                } else {
                    $start_date = date('Y-m-d');
                }
                $reservations = Reservation::getByPhysicianSchedule($physicianSchedule['clinic_id']
                    , $physicianSchedule['user_id'], $start_date, $physicianSchedule['end_date']);
                if ($reservations) {
                    foreach ($reservations as $key => $val) {
                        Reservation::edit(array(
                            'update_by' => $user->id,
                            'status' => ReservationStatus::archive,
                            'patient_status' => PatientStatus::archive,
                            'exception_reason' => 'Edit In Physician Schedule',
                            'show_reason' => 1,
                        ), $val['id']);
                        ReservationHistory::add([
                            'action' => 'Archive From Edit Schedule',
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
                            'exception_reason' => 'Edit In Physician Schedule',
                        ]);
                    }
                }
            }
            $inputs['create_timestamp'] = time();
            $inputs['sat_start_time_1'] = isset($inputs['sat_start_time_1']) ? $inputs['sat_start_time_1'] : null;
            $inputs['sat_start_time_2'] = isset($inputs['sat_start_time_2']) ? $inputs['sat_start_time_2'] : null;
            $inputs['sat_start_time_3'] = isset($inputs['sat_start_time_3']) ? $inputs['sat_start_time_3'] : null;

            $inputs['sun_start_time_1'] = isset($inputs['sun_start_time_1']) ? $inputs['sun_start_time_1'] : null;
            $inputs['sun_start_time_2'] = isset($inputs['sun_start_time_2']) ? $inputs['sun_start_time_2'] : null;
            $inputs['sun_start_time_3'] = isset($inputs['sun_start_time_3']) ? $inputs['sun_start_time_3'] : null;

            $inputs['mon_start_time_1'] = isset($inputs['mon_start_time_1']) ? $inputs['mon_start_time_1'] : null;
            $inputs['mon_start_time_2'] = isset($inputs['mon_start_time_2']) ? $inputs['mon_start_time_2'] : null;
            $inputs['mon_start_time_3'] = isset($inputs['mon_start_time_3']) ? $inputs['mon_start_time_3'] : null;

            $inputs['tues_start_time_1'] = isset($inputs['tues_start_time_1']) ? $inputs['tues_start_time_1'] : null;
            $inputs['tues_start_time_2'] = isset($inputs['tues_start_time_2']) ? $inputs['tues_start_time_2'] : null;
            $inputs['tues_start_time_3'] = isset($inputs['tues_start_time_3']) ? $inputs['tues_start_time_3'] : null;

            $inputs['wed_start_time_1'] = isset($inputs['wed_start_time_1']) ? $inputs['wed_start_time_1'] : null;
            $inputs['wed_start_time_2'] = isset($inputs['wed_start_time_2']) ? $inputs['wed_start_time_2'] : null;
            $inputs['wed_start_time_3'] = isset($inputs['wed_start_time_3']) ? $inputs['wed_start_time_3'] : null;

            $inputs['thurs_start_time_1'] = isset($inputs['thurs_start_time_1']) ? $inputs['thurs_start_time_1'] : null;
            $inputs['thurs_start_time_2'] = isset($inputs['thurs_start_time_2']) ? $inputs['thurs_start_time_2'] : null;
            $inputs['thurs_start_time_3'] = isset($inputs['thurs_start_time_3']) ? $inputs['thurs_start_time_3'] : null;

            $inputs['fri_start_time_1'] = isset($inputs['fri_start_time_1']) ? $inputs['fri_start_time_1'] : null;
            $inputs['fri_start_time_2'] = isset($inputs['fri_start_time_2']) ? $inputs['fri_start_time_2'] : null;
            $inputs['fri_start_time_3'] = isset($inputs['fri_start_time_3']) ? $inputs['fri_start_time_3'] : null;
///////////////////////////////////////
            $inputs['sat_end_time_1'] = isset($inputs['sat_end_time_1']) ? $inputs['sat_end_time_1'] : null;
            $inputs['sat_end_time_2'] = isset($inputs['sat_end_time_2']) ? $inputs['sat_end_time_2'] : null;
            $inputs['sat_end_time_3'] = isset($inputs['sat_end_time_3']) ? $inputs['sat_end_time_3'] : null;

            $inputs['sun_end_time_1'] = isset($inputs['sun_end_time_1']) ? $inputs['sun_end_time_1'] : null;
            $inputs['sun_end_time_2'] = isset($inputs['sun_end_time_2']) ? $inputs['sun_end_time_2'] : null;
            $inputs['sun_end_time_3'] = isset($inputs['sun_end_time_3']) ? $inputs['sun_end_time_3'] : null;

            $inputs['mon_end_time_1'] = isset($inputs['mon_end_time_1']) ? $inputs['mon_end_time_1'] : null;
            $inputs['mon_end_time_2'] = isset($inputs['mon_end_time_2']) ? $inputs['mon_end_time_2'] : null;
            $inputs['mon_end_time_3'] = isset($inputs['mon_end_time_3']) ? $inputs['mon_end_time_3'] : null;

            $inputs['tues_end_time_1'] = isset($inputs['tues_end_time_1']) ? $inputs['tues_end_time_1'] : null;
            $inputs['tues_end_time_2'] = isset($inputs['tues_end_time_2']) ? $inputs['tues_end_time_2'] : null;
            $inputs['tues_end_time_3'] = isset($inputs['tues_end_time_3']) ? $inputs['tues_end_time_3'] : null;

            $inputs['wed_end_time_1'] = isset($inputs['wed_end_time_1']) ? $inputs['wed_end_time_1'] : null;
            $inputs['wed_end_time_2'] = isset($inputs['wed_end_time_2']) ? $inputs['wed_end_time_2'] : null;
            $inputs['wed_end_time_3'] = isset($inputs['wed_end_time_3']) ? $inputs['wed_end_time_3'] : null;

            $inputs['thurs_end_time_1'] = isset($inputs['thurs_end_time_1']) ? $inputs['thurs_end_time_1'] : null;
            $inputs['thurs_end_time_2'] = isset($inputs['thurs_end_time_2']) ? $inputs['thurs_end_time_2'] : null;
            $inputs['thurs_end_time_3'] = isset($inputs['thurs_end_time_3']) ? $inputs['thurs_end_time_3'] : null;

            $inputs['fri_end_time_1'] = isset($inputs['fri_end_time_1']) ? $inputs['fri_end_time_1'] : null;
            $inputs['fri_end_time_2'] = isset($inputs['fri_end_time_2']) ? $inputs['fri_end_time_2'] : null;
            $inputs['fri_end_time_3'] = isset($inputs['fri_end_time_3']) ? $inputs['fri_end_time_3'] : null;
            if ($inputs['num_of_shifts'] == 1) {
                $inputs['dayoff_1'] = implode(',', $inputs['dayoff_1']);
                $inputs['dayoff_2'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                $inputs['dayoff_3'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                $inputs['sat_start_time_2'] = null;
                $inputs['sat_end_time_2'] = null;
                $inputs['sun_start_time_2'] = null;
                $inputs['sun_end_time_2'] = null;
                $inputs['mon_start_time_2'] = null;
                $inputs['mon_end_time_2'] = null;
                $inputs['tues_start_time_2'] = null;
                $inputs['tues_end_time_2'] = null;
                $inputs['wed_start_time_2'] = null;
                $inputs['wed_end_time_2'] = null;
                $inputs['thurs_start_time_2'] = null;
                $inputs['thurs_end_time_2'] = null;
                $inputs['fri_start_time_2'] = null;
                $inputs['fri_end_time_2'] = null;
                $inputs['sat_start_time_3'] = null;
                $inputs['sat_end_time_3'] = null;
                $inputs['sun_start_time_3'] = null;
                $inputs['sun_end_time_3'] = null;
                $inputs['mon_start_time_3'] = null;
                $inputs['mon_end_time_3'] = null;
                $inputs['tues_start_time_3'] = null;
                $inputs['tues_end_time_3'] = null;
                $inputs['wed_start_time_3'] = null;
                $inputs['wed_end_time_3'] = null;
                $inputs['thurs_start_time_3'] = null;
                $inputs['thurs_end_time_3'] = null;
                $inputs['fri_start_time_3'] = null;
                $inputs['fri_end_time_3'] = null;
            } elseif ($inputs['num_of_shifts'] == 2) {
                $inputs['dayoff_1'] = implode(',', $inputs['dayoff_1']);
                $inputs['dayoff_2'] = implode(',', $inputs['dayoff_2']);
                $inputs['dayoff_3'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                $inputs['sat_start_time_3'] = null;
                $inputs['sat_end_time_3'] = null;
                $inputs['sun_start_time_3'] = null;
                $inputs['sun_end_time_3'] = null;
                $inputs['mon_start_time_3'] = null;
                $inputs['mon_end_time_3'] = null;
                $inputs['tues_start_time_3'] = null;
                $inputs['tues_end_time_3'] = null;
                $inputs['wed_start_time_3'] = null;
                $inputs['wed_end_time_3'] = null;
                $inputs['thurs_start_time_3'] = null;
                $inputs['thurs_end_time_3'] = null;
                $inputs['fri_start_time_3'] = null;
                $inputs['fri_end_time_3'] = null;
            } elseif ($inputs['num_of_shifts'] == 3) {
                $inputs['dayoff_1'] = implode(',', $inputs['dayoff_1']);
                $inputs['dayoff_2'] = implode(',', $inputs['dayoff_2']);
                $inputs['dayoff_3'] = implode(',', $inputs['dayoff_3']);
            }
            PhysicianSchedule::edit($inputs, $id);
            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, There is error please try again");
        }
    }


}