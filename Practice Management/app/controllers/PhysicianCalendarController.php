<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\enums\AttributeType;
use core\enums\PatientStatus;
use core\enums\PhysicianExceptionStatus;
use core\hospital\HospitalRepository;

class PhysicianCalendarController extends BaseController
{
    public $user = '';

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function viewCalendar()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianCalendar.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['reasons'] = AttributePms::getAll(AttributeType::$pmsReturn['exceptionReason']);
        return View::make('physicianCalendar/calendar', $data);
    }

    public function physicianCalendarGetEvents()
    {
        $physician_id = Input::get('physician_id');
        $clinic_id = Input::get('clinic_id');
        $data = Reservation::getByClinicAndPhysician($clinic_id, $physician_id);
        $return = array();
        foreach ($data as $key => $val) {
            $return[$key]['id'] = $key;
            $return[$key]['start'] = $val['date'] . 'T' . $val['time_from'];
            $return[$key]['end'] = $val['date'] . 'T' . $val['time_to'];
            $return[$key]['title'] = $val['time_from'] . " - " . $val['time_to'];
            if (PatientStatus::patient_in == $val['patient_status']) {
                $return[$key]['color'] = '#FFA200'; // orange
            } elseif (PatientStatus::patient_out == $val['patient_status']) {
                $return[$key]['color'] = '#9C9B9A'; // gray
            } elseif (PatientStatus::no_show == $val['patient_status']) {
                $return[$key]['color'] = '#FF0000'; // red
            }
        }
        echo(json_encode($return));
    }

    public function addPhysicianExceptionPopUp()
    {
        $inputs = Input::except('_token')['physicianException'];
        if ($inputs['effect'] == 1) {
            unset($inputs['reason_id2']);
        } else {
            $inputs['reason_id'] = $inputs['reason_id2'];
            unset($inputs['reason_id2']);
        }
        unset($inputs['_token']);
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianException.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return array('return' => false, 'message' => 'You don\'t have a permission to do this action');
        }
        if ($inputs['from_date'] > $inputs['to_date']) {
            Flash::error("Make sure the end date is greater than start date");
            return array('return' => false, 'message' => 'Make sure the end date is greater than start date');
        }
        $physicianSch = PhysicianSchedule::getById($inputs['physician_schedule_id']);
        if ($inputs['from_date'] < $physicianSch['start_date']) {
            Flash::error("Make sure the start date is greater than start date of this physician schedule");
            return array('return' => false, 'message' => 'Make sure the start date is greater than start date of this physician schedule');
        }
        if ($inputs['to_date'] > $physicianSch['end_date']) {
            Flash::error("Make sure the end date is less than end date of this physician schedule");
            return array('return' => false, 'message' => 'Make sure the end date is less than end date of this physician schedule');
        }
        unset($inputs['hospital_id']);
        unset($inputs['clinic_id']);
        $inputs['created_by'] = $this->user->id;
        if (isset($inputs['all_day'])) {
            $inputs['all_day'] = 1;
            $inputs['from_time'] = '';
            $inputs['to_time'] = '';
        }
        if (isset($inputs['schedule_times']) && $inputs['schedule_times']) {
            $inputs['schedule_times'] = implode(',', $inputs['schedule_times']);
        }
        if ($this->user->user_type_id != 7) {
            $reason = AttributePms::getById($inputs['reason_id']);
            Reservation::pendingWithPeriodByPhysician($inputs['user_id'], $inputs['from_date'], $inputs['to_date'],
                $reason['name'], $inputs['from_time'], $inputs['to_time'], $inputs['effect'], isset($inputs['schedule_times']) ? $inputs['schedule_times'] : null, $inputs['physician_schedule_id']);
            $inputs['status'] = PhysicianExceptionStatus::approved;
        }
        PhysicianException::add($inputs);
        return array('return' => true, 'message' => 'Added Successfully');
    }
}
