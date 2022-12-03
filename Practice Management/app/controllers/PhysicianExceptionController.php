<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\enums\AttributeType;
use core\enums\PhysicianExceptionStatus;
use core\hospital\HospitalRepository;
use core\physician\PhysicianManager;
use core\physician\PhysicianRepository;

class PhysicianExceptionController extends BaseController
{
    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function index()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianException.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $data['physicianExceptions'] = PhysicianException::getAll($inputs, false);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();

        $data['reasons'] = AttributePms::getAll(AttributeType::$pmsReturn['exceptionReason']);

        return View::make('physicianException/list', $data);
    }

    public function addPhysicianException()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianException.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['reasons'] = AttributePms::getAll(AttributeType::$pmsReturn['exceptionReason']);
        $data['physicianException'] = array(
            'hospital_id' => '',
            'clinic_id' => '',
            'effect' => '',
            'user_id' => '',
            'reason_id' => '',
            'from_date' => '',
            'to_date' => '',
            'all_day' => '',
            'from_time' => '',
            'to_time' => '',
            'notes' => '',
        );
        $data['physician'] = '';
        $data['physician_schedule'] = '';
        $data['schedule_times'] = '';
        return View::make('physicianException/add', $data);
    }

    public function createPhysicianException()
    {
        $inputs = Input::except('_token');
        if ($inputs['effect'] == 1) {
            unset($inputs['reason_id2']);
        } else {
            $inputs['reason_id'] = $inputs['reason_id2'];
            unset($inputs['reason_id2']);
        }
        $validator = Validator::make($inputs, PhysicianException::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            if ($inputs['from_date'] > $inputs['to_date']) {
                Flash::error("Make sure the end date is greater than start date");
                return Redirect::back()->withInput(Input::all());
            }
            $physicianSch = PhysicianSchedule::getById($inputs['physician_schedule_id']);
            if ($inputs['from_date'] < $physicianSch['start_date']) {
                Flash::error("Make sure the start date is greater than start date of this physician schedule");
                return Redirect::back()->withInput(Input::all());
            }
            if ($inputs['to_date'] > $physicianSch['end_date']) {
                Flash::error("Make sure the end date is less than end date of this physician schedule");
                return Redirect::back()->withInput(Input::all());
            }
            $hospital_id = $inputs['hospital_id'];
            $clinic_id = $inputs['clinic_id'];
            unset($inputs['hospital_id']);
            unset($inputs['clinic_id']);
            $inputs['created_by'] = $this->user->id;

            if (isset($inputs['all_day']) && $inputs['all_day']) {
                $clinicSchedule = ClinicSchedule::getById($physicianSch['clinic_schedule_id']);
                $num_of_shifts = $clinicSchedule['num_of_shifts'];
                $inputs['all_day'] = null;
                $inputs['is_all_day'] = 1;
                $inputs['from_time'] = $clinicSchedule['shift1_start_time'];
                if ($num_of_shifts == 1) {
                    $inputs['to_time'] = $clinicSchedule['shift1_end_time'];
                }
                if ($num_of_shifts == 2) {
                    $inputs['to_time'] = $clinicSchedule['shift2_end_time'];
                }
                if ($num_of_shifts == 3) {
                    $inputs['to_time'] = $clinicSchedule['shift3_end_time'];
                }
            }
            if (isset($inputs['schedule_times']) && $inputs['schedule_times']) {
                $inputs['schedule_times'] = implode(',', $inputs['schedule_times']);
            }
            $reason = AttributePms::getById($inputs['reason_id']);
            if (($this->user->user_type_id == 1) || ($this->user->user_type_id != 7)) {
                Reservation::pendingWithPeriodByPhysician($inputs['user_id'], $inputs['from_date'], $inputs['to_date'],
                    $reason['name'], $inputs['from_time'], $inputs['to_time'], $inputs['effect'], isset($inputs['schedule_times']) ? $inputs['schedule_times'] : null, $inputs['physician_schedule_id']);
                $inputs['status'] = PhysicianExceptionStatus::approved;
                $inputs['change_status_by'] = $this->user->id;
                $inputs['change_status_at'] = date('Y-m-d H:i:s');
                UnlockSlot::removeWithPeriod($inputs['user_id'], $inputs['from_date'], $inputs['to_date'], $inputs['from_time'], $inputs['to_time']);
                RdhDoctorException::updateUnlockWithPeriod($inputs['user_id'], $inputs['from_date'], $inputs['to_date'], $inputs['from_time'], $inputs['to_time']);
            }
            $physicianException = PhysicianException::add($inputs);
            if (app('production')) {
                if (($this->user->user_type_id == 1) || ($this->user->user_type_id != 7)) {
                    ///////////// Add To HIS Exception Integration //////////////
                    $physicianException->reason_name = $reason['name'];
                    RdhDoctorException::addFromPMS($physicianException);
                }
            }
            $physicianException['hospital_id'] = $hospital_id;
            $physicianException['clinic_id'] = $clinic_id;
            Flash::success('Added Successfully');
            return Redirect::back()->with('physicianException', $physicianException);
        }
    }

    public function editPhysicianException($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianException.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['physicianException'] = PhysicianException::getById($id);
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['reasons'] = AttributePms::getAll(AttributeType::$pmsReturn['exceptionReason']);

        $physician = User::getById($data['physicianException']['user_id']);
        $data['physician'] = '<option selected value=' . $physician['id'] . '>' . $physician['full_name'] . '</option>';

        $schedule = PhysicianSchedule::getById($data['physicianException']['physician_schedule_id']);
        $data['physician_schedule'] = '<option selected value=' . $data['physicianException']['physician_schedule_id'] . '>'
            . $schedule['start_date'] . ' / ' . $schedule['end_date'] . '</option>';

        $schedule_times = PhysicianSchedule::scheduleTimeInSelect($schedule);
        $data['schedule_times'] = $schedule_times;
        return View::make('physicianException/add', $data);
    }

    public function updatePhysicianException($id)
    {
        $inputs = Input::except('_token');
        if ($inputs['effect'] == 1) {
            unset($inputs['reason_id2']);
        } else {
            $inputs['reason_id'] = $inputs['reason_id2'];
            unset($inputs['reason_id2']);
        }
        $validator = Validator::make($inputs, array(
            "reason_id" => "required",
        ));
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {

            $oldException = PhysicianException::getById($id);

            if ($inputs['from_date'] > $inputs['to_date']) {
                Flash::error("Make sure the end date is greater than start date");
                return Redirect::back()->withInput(Input::all());
            }
            $physicianSch = PhysicianSchedule::getById($oldException['physician_schedule_id']);
            if ($inputs['from_date'] < $physicianSch['start_date']) {
                Flash::error("Make sure the start date is greater than start date of this physician schedule");
                return Redirect::back()->withInput(Input::all());
            }
            if ($inputs['to_date'] > $physicianSch['end_date']) {
                Flash::error("Make sure the end date is less than end date of this physician schedule");
                return Redirect::back()->withInput(Input::all());
            }

            if (isset($inputs['all_day']) && $inputs['all_day']) {
                $clinicSchedule = ClinicSchedule::getById($physicianSch['clinic_schedule_id']);
                $inputs['all_day'] = null;
                $inputs['is_all_day'] = 1;
                $inputs['from_time'] = $clinicSchedule['shift1_start_time'];
                $inputs['to_time'] = $clinicSchedule['shift1_end_time'];
            }

            if (isset($inputs['schedule_times']) && $inputs['schedule_times']) {
                $inputs['schedule_times'] = implode(',', $inputs['schedule_times']);
            } else {
                $inputs['schedule_times'] = '';
            }

            $reason = AttributePms::getById($inputs['reason_id']);
            if (($this->user->user_type_id == 1) || ($this->user->user_type_id != 7 && $this->user->hasAccess('physicianException.changeStatus'))) {
                if ($oldException['from_date'] != $inputs['from_date'] || $oldException['to_date'] != $inputs['to_date']
                    || $oldException['from_time'] != $inputs['from_time'] || $oldException['to_time'] != $inputs['to_time']
                    || $oldException['schedule_times'] != $inputs['schedule_times'] || $oldException['reason_id'] != $inputs['reason_id']
                ) {

                    Reservation::reservedWithPeriodByPhysician($oldException['user_id'], $oldException['from_date'], $oldException['to_date'],
                        $oldException['from_time'], $oldException['to_time'], $oldException['schedule_times'], $oldException['physician_schedule_id']);
                    Reservation::pendingWithPeriodByPhysician($oldException['user_id'], $inputs['from_date'], $inputs['to_date'],
                        $reason['name'], $inputs['from_time'], $inputs['to_time'], $inputs['effect'], $inputs['schedule_times'], $oldException['physician_schedule_id']);

                } else {
                    $reason = AttributePms::getById($inputs['reason_id']);
                    Reservation::pendingWithPeriodByPhysician($oldException['user_id'], $inputs['from_date'], $inputs['to_date'],
                        $reason['name'], $inputs['from_time'], $inputs['to_time'], $inputs['effect'], $inputs['schedule_times'], $oldException['physician_schedule_id']);
                }
                $inputs['status'] = PhysicianExceptionStatus::approved;
                UnlockSlot::removeWithPeriod($oldException['user_id'], $inputs['from_date'], $inputs['to_date'], $inputs['from_time'], $inputs['to_time']);
            }
            $inputs['change_status_by'] = $this->user->id;
            $inputs['change_status_at'] = date('Y-m-d H:i:s');
            $inputs['updated_by'] = $this->user->id;
            PhysicianException::edit($inputs, $id);
            if (app('production')) {
                ///////////// HIS Exception Integration //////////////
                if (($this->user->user_type_id == 1) || ($this->user->user_type_id != 7 &&
                        $this->user->hasAccess('physicianException.changeStatus'))
                ) {
                    // delete flag first for old exceptions
                    RdhDoctorException::deleteFlagByPMSId($id);
                    $physicianException = PhysicianException::getById($id);
                    $physicianException['reason_name'] = $reason['name'];
                    // add new records after editing
                    RdhDoctorException::addFromPMS($physicianException);
                }
            }
            Flash::success('Updated Successfully');
            return Redirect::route('physicianExceptions');
        }
    }

    public function deletePhysicianException($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianException.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $oldException = PhysicianException::getById($id);
        PhysicianException::remove($id);
        Reservation::reservedWithPeriodByPhysician($oldException['user_id'], $oldException['from_date'], $oldException['to_date'],
            $oldException['from_time'], $oldException['to_time'], $oldException['schedule_times'], $oldException['physician_schedule_id']);
        if (app('production')) {
            ///////////// HIS Exception Integration //////////////
            // delete flag for exceptions
            RdhDoctorException::deleteFlagByPMSId($id);
        }
        Flash::success('Deleted Successfully');
        return Redirect::back();
    }

    public function approvedPhysicianException($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianException.changeStatus')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        PhysicianException::edit(array(
            'status' => PhysicianExceptionStatus::approved,
            'change_status_by' => $this->user->id,
            'change_status_at' => date('Y-m-d H:i:s')
        ), $id);
        $inputs = PhysicianException::getById($id);
        $reason = AttributePms::getById($inputs['reason_id']);
        Reservation::pendingWithPeriodByPhysician($inputs['user_id'], $inputs['from_date'], $inputs['to_date'],
            $reason['name'], $inputs['from_time'], $inputs['to_time'], $inputs['effect'], $inputs['schedule_times'], $inputs['physician_schedule_id']);
        UnlockSlot::removeWithPeriod($inputs['user_id'], $inputs['from_date'], $inputs['to_date'], $inputs['from_time'], $inputs['to_time']);

        if (app('production')) {
            ///////////// HIS Exception Integration //////////////
            $physicianException = $inputs;
            $physicianException['reason_name'] = $reason['name'];
            RdhDoctorException::addFromPMS($physicianException);
        }
        Flash::success('Updated Successfully');
        return Redirect::back();
    }

    public function notApprovedPhysicianException($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianException.changeStatus')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        PhysicianException::edit(array(
            'status' => PhysicianExceptionStatus::not_approved,
            'change_status_by' => $this->user->id,
            'change_status_at' => date('Y-m-d H:i:s')
        ), $id);
        Flash::success('Updated Successfully');
        return Redirect::back();
    }

    public function getByPhysicianSchedule()
    {
        $physician_schedule_id = Input::get('physician_schedule_id');
        $data2['exceptions'] = PhysicianException::getByPhysicianSchedule($physician_schedule_id);
        $data['exceptionHtml'] = View::make('physicianException/listExceptions', $data2)->render();
        return $data;
    }

    public function getPhysicianAvailableTime()
    {
        $inputs = Input::except('_token');
        $with_exception = isset($inputs['with_exception']) ? $inputs['with_exception'] : '';
        if (isset($inputs['without_physician_schedule']) && $inputs['without_physician_schedule']) {
            $physicianSchedule = PhysicianSchedule::getByPhysicianId_Date($inputs['physician_id'], $inputs['date'], true, $inputs['clinic_id']);

        } else {
            $physicianSchedule = PhysicianSchedule::getById($inputs['physician_schedule_id'], $with_exception, $inputs['date']);
        }
        $clinicSchedule = ClinicSchedule::getById($physicianSchedule['clinic_schedule_id']);
        $availableTimes = array();
        $physicianManager = new PhysicianManager();
        $physicianManager->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $inputs['date']);
        $data2['availableTimes'] = $availableTimes;
        $data2['selectDate'] = $inputs['date'];
        $data2['slots'] = isset($physicianSchedule['slots']) ? $physicianSchedule['slots'] : '';
        if (isset($inputs['without_physician_schedule']) && $inputs['without_physician_schedule']) {
            $data['physicianTimeHtml'] = View::make('clinic/discover_physician_time', $data2)->render();
        } else {
            $data['physicianTimeHtml'] = View::make('physicianException/physician_time', $data2)->render();
        }
        return $data;
    }
}
