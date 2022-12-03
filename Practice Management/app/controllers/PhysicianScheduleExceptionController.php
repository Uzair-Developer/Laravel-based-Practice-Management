<?php

use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use core\hospital\HospitalRepository;
use core\physician\PhysicianRepository;
use Laracasts\Flash\Flash;

class PhysicianScheduleExceptionController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listPhysicianScheduleException()
    {
        if (!$this->user->hasAccess('physicianScheduleException.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();

        $inputs = Input::except('_token');
        $data['inputs'] = $inputs;
        if ($inputs) {
            $inputs['paginate'] = 50;
            $data['physicianScheduleExceptions'] = PhysicianScheduleException::getAll($inputs);
        }
        return View::make('physicianScheduleException/list', $data);
    }

    public function managePhysicianScheduleException()
    {
        if (!$this->user->hasAccess('physicianScheduleException.manage') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('physicianScheduleException/manage', $data);
    }

    public function getPhysicianScheduleExceptionsDateTime()
    {
        $inputs = Input::except('_token');
        if ($inputs['physician_id'] && $inputs['clinic_id'] && $inputs['physician_schedule_id']) {
            $data['success'] = 'yes';
            $physicianSchedule = PhysicianSchedule::getById($inputs['physician_schedule_id']);
            $data2['physicianSchedule'] = $physicianSchedule;
//            $data2['clinicSchedule'] = ClinicSchedule::getById($physicianSchedule['clinic_schedule_id']);
            $data['html'] = View::make('physicianScheduleException/physicianDateTime', $data2)->render();
        } else {
            $data['success'] = 'no';
            $data['message'] = 'Missing Data!';
        }
        return $data;
    }

    public function updatePhysicianScheduleException()
    {
        $inputs = Input::except('_token');
        $formArray = array();
        parse_str($inputs['form'], $formArray);
        if ($inputs['physician_schedule_id'] && $inputs['physician_id'] && $inputs['clinic_id']) {
            $data['success'] = 'yes';
            $scheduleException = PhysicianScheduleException::checkByClinic_Physician_Date($inputs['clinic_id'],
                $inputs['physician_id'], $formArray['date']);
            $array = array(
                'user_id' => $inputs['physician_id'],
                'clinic_id' => $inputs['clinic_id'],
                'slots' => $formArray['slots'],
                'date' => $formArray['date'],
                'num_of_shifts' => $formArray['num_of_shifts'],
                'shift1_dayoff' => isset($formArray['shift1_dayoff']) ? $formArray['shift1_dayoff'] : 1,
                'shift2_dayoff' => isset($formArray['shift2_dayoff']) ? $formArray['shift2_dayoff'] : 1,
                'shift3_dayoff' => isset($formArray['shift3_dayoff']) ? $formArray['shift3_dayoff'] : 1,
            );
            if (isset($formArray['shift1_dayoff'])) {
                if ($formArray['shift1_dayoff'] == 1) {
                    $array['shift1_time_from'] = null;
                } else {
                    $array['shift1_time_from'] = isset($formArray['shift1_time_from']) ? $formArray['shift1_time_from'] : null;
                }
            } else {
                $array['shift1_time_from'] = null;
            }
            if (isset($formArray['shift1_dayoff'])) {
                if ($formArray['shift1_dayoff'] == 1) {
                    $array['shift1_time_to'] = null;
                } else {
                    $array['shift1_time_to'] = isset($formArray['shift1_time_to']) ? $formArray['shift1_time_to'] : null;
                }
            } else {
                $array['shift1_time_to'] = null;
            }
            ////////////////////
            if (isset($formArray['shift2_dayoff'])) {
                if ($formArray['shift2_dayoff'] == 1) {
                    $array['shift2_time_from'] = null;
                } else {
                    $array['shift2_time_from'] = isset($formArray['shift2_time_from']) ? $formArray['shift2_time_from'] : null;
                }
            } else {
                $array['shift2_time_from'] = null;
            }
            if (isset($formArray['shift2_dayoff'])) {
                if ($formArray['shift2_dayoff'] == 1) {
                    $array['shift2_time_to'] = null;
                } else {
                    $array['shift2_time_to'] = isset($formArray['shift2_time_to']) ? $formArray['shift2_time_to'] : null;
                }
            } else {
                $array['shift2_time_to'] = null;
            }
            //////////////////////
            if (isset($formArray['shift3_dayoff'])) {
                if ($formArray['shift3_dayoff'] == 1) {
                    $array['shift3_time_from'] = null;
                } else {
                    $array['shift3_time_from'] = isset($formArray['shift3_time_from']) ? $formArray['shift3_time_from'] : null;
                }
            } else {
                $array['shift3_time_from'] = null;
            }
            if (isset($formArray['shift3_dayoff'])) {
                if ($formArray['shift3_dayoff'] == 1) {
                    $array['shift3_time_to'] = null;
                } else {
                    $array['shift3_time_to'] = isset($formArray['shift3_time_to']) ? $formArray['shift3_time_to'] : null;
                }
            } else {
                $array['shift3_time_to'] = null;
            }
            if ($scheduleException) {
                $array['updated_by'] = $this->user->id;
                PhysicianScheduleException::edit($array, $scheduleException['id']);
            } else {
                $array['created_by'] = $this->user->id;
                PhysicianScheduleException::add($array);
            }
            /////////////////reservations archived process/////////////////////
            $reservations = Reservation::getByPhysicianSchedule($inputs['clinic_id']
                , $inputs['physician_id'], $formArray['date'], $formArray['date']);
            if ($reservations) {
                foreach ($reservations as $key => $val) {
                    Reservation::edit(array(
                        'update_by' => $this->user->id,
                        'status' => ReservationStatus::archive,
                        'patient_status' => PatientStatus::archive,
                        'exception_reason' => 'Edit In Physician Schedule',
                        'show_reason' => 1,
                    ), $val['id']);
                    ReservationHistory::add([
                        'action' => 'Archive From Schedule Exception',
                        'action_by' => $this->user->id,
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
            return $data;
        } else {
            $data['success'] = 'no';
            $data['message'] = 'Missing Date!';
            return $data;
        }
    }

    public function getScheduleWithDate()
    {
        $inputs = Input::except('_token');
        if ($inputs['physician_schedule_id'] && $inputs['physician_id'] && $inputs['clinic_id']
            && $inputs['date']
        ) {
            $data['success'] = 'yes';
            $physicianSchedule = PhysicianSchedule::getById($inputs['physician_schedule_id']);
            $data2['clinicSchedule'] = ClinicSchedule::getById($physicianSchedule['clinic_schedule_id']);
            $checkException = PhysicianScheduleException::checkByClinic_Physician_Date($inputs['clinic_id'], $inputs['physician_id'], $inputs['date']);
            if ($checkException) {
                $data2['hasException'] = true;
                $data2['schedule'] = $checkException;
            } else {
                $physicianSchedule = PhysicianSchedule::getById($inputs['physician_schedule_id']);
                $data2['hasException'] = false;
                $data2['schedule'] = $physicianSchedule;
            }
            $data2['date'] = $inputs['date'];
            $data['html'] = View::make('physicianScheduleException/physicianScheduleDetails', $data2)->render();
            return $data;
        } else {
            $data['success'] = 'no';
            $data['message'] = 'Missing Date!';
            return $data;
        }
    }

    public function deletePhysicianScheduleException($id)
    {
        if (!$this->user->hasAccess('admin') && !$this->user->hasAccess('physicianScheduleException.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $phySchException = PhysicianScheduleException::getById($id);
        /////////////////reservations archived process/////////////////////
        $reservations = Reservation::getByPhysicianSchedule($phySchException['clinic_id']
            , $phySchException['user_id'], $phySchException['date'], $phySchException['date']);
        if ($reservations) {
            foreach ($reservations as $key => $val) {
                Reservation::edit(array(
                    'update_by' => $this->user->id,
                    'status' => ReservationStatus::archive,
                    'patient_status' => PatientStatus::archive,
                    'exception_reason' => 'Edit In Physician Schedule',
                    'show_reason' => 1,
                ), $val['id']);
                ReservationHistory::add([
                    'action' => 'Archive From Schedule Exception',
                    'action_by' => $this->user->id,
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
        PhysicianScheduleException::remove($id);
        Flash::success('Deleted Successfully');
        return Redirect::back();
    }
}