<?php

use core\clinicSchedule\ClinicScheduleRepository;
use core\hospital\HospitalRepository;
use core\physician\PhysicianManager;
use Laracasts\Flash\Flash;

class UnlockSlotController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listUnlockSlot()
    {
        if (!$this->user->hasAccess('unlockSlot.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $data['unlockSlots'] = UnlockSlot::getAll($inputs);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('unlockSlot/list', $data);
    }

    public function addUnlockSlot()
    {
        if (!$this->user->hasAccess('unlockSlot.add') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['unlockSlot'] = array(
            'name' => ''
        );
        return View::make('unlockSlot/add', $data);
    }

    public function createUnlockSlot()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, UnlockSlot::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                UnlockSlot::add($inputs);
                Flash::success('Added successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listUnlockSlot');
    }

    public function editUnlockSlot($id)
    {
        if (!$this->user->hasAccess('unlockSlot.edit') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['unlockSlot'] = UnlockSlot::getById($id);
        return View::make('unlockSlot/add', $data);
    }

    public function updateUnlockSlot($id)
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, UnlockSlot::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                UnlockSlot::edit($inputs, $id);
                Flash::success('Updated successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listUnlockSlot');
    }

    public function deleteUnlockSlot($id)
    {
        if (!$this->user->hasAccess('unlockSlot.delete') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        UnlockSlot::remove($id);
        return Redirect::route('listUnlockSlot');
    }

    public function unLockSlotReservation()
    {
        $inputs = Input::except('_token');
        $with_exception = isset($inputs['with_exception']) ? $inputs['with_exception'] : '';
        $physicianSchedule = PhysicianSchedule::getById($inputs['physician_schedule_id'], $with_exception, $inputs['date']);
        if ($inputs['times']) {
            $times = explode(',', $inputs['times']);
            foreach ($times as $key => $val) {
                $timeParts = explode('-', $val);
                $from_time = $timeParts[0];
                $to_time = $timeParts[1];
                if (!Reservation::checkExistRecord($physicianSchedule['clinic_id'], $physicianSchedule['user_id'], $inputs['date'], $from_time, $to_time)) {
                    UnlockSlot::add(array(
                        'user_id' => $inputs['physician_id'],
                        'date' => $inputs['date'],
                        'from_time' => $from_time,
                        'to_time' => $to_time,
                        'created_by' => $this->user->id,
                    ));
                    if (app('production')) {
                        ///////////// HIS Exception Integration //////////////
                        RdhDoctorException::lockOrUnlock([
                            'user_id' => $inputs['physician_id'],
                            'date' => $inputs['date'],
                            'from_time' => $from_time,
                            'to_time' => $to_time,
                        ], 1);
                    }
                }
            }
            $clinicSchedule = ClinicSchedule::getById($physicianSchedule['clinic_schedule_id']);
            $availableTimes = array();
            $physicianManager = new PhysicianManager();
            $physicianManager->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $inputs['date']);
            $data2['availableTimes'] = $availableTimes;
            $data2['selectDate'] = $inputs['date'];
            $data2['slots'] = $physicianSchedule['slots'];
            $data['physicianTimeHtml'] = View::make('physicianException/physician_time', $data2)->render();
            return $data;
        }
        return 0;
    }

    public function lockSlotReservation()
    {
        $inputs = Input::except('_token');
        $with_exception = isset($inputs['with_exception']) ? $inputs['with_exception'] : '';
        $physicianSchedule = PhysicianSchedule::getById($inputs['physician_schedule_id'], $with_exception, $inputs['date']);

        UnlockSlot::removeWithPhysicianDateTime($inputs['physician_id'], $inputs['date'], $inputs['time'], $inputs['to_time']);
        if (app('production')) {
            ///////////// HIS Exception Integration //////////////
            RdhDoctorException::lockOrUnlock([
                'user_id' => $inputs['physician_id'],
                'date' => $inputs['date'],
                'from_time' => $inputs['time'],
                'to_time' => $inputs['to_time'],
            ], 0);
        }

        $checkException = PhysicianException::checkExist($inputs['physician_id'], $inputs['date'],
            $inputs['time'], $physicianSchedule['slots'], $inputs['reason_id']);
        if (empty($checkException)) {
            PhysicianException::add(array(
                'user_id' => $inputs['physician_id'],
                'physician_schedule_id' => $inputs['physician_schedule_id'],
                'reason_id' => $inputs['reason_id'],
                'from_date' => $inputs['date'],
                'to_date' => $inputs['date'],
                'from_time' => $inputs['time'],
                'to_time' => $inputs['to_time'],
                'effect' => 1,
                'status' => 1,
                'created_by' => $this->user->id,
            ));
        }
        $clinicSchedule = ClinicSchedule::getById($physicianSchedule['clinic_schedule_id']);
        $availableTimes = array();
        $physicianManager = new PhysicianManager();
        $physicianManager->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $inputs['date']);
        $data2['availableTimes'] = $availableTimes;
        $data2['selectDate'] = $inputs['date'];
        $data2['slots'] = $physicianSchedule['slots'];
        $data['physicianTimeHtml'] = View::make('physicianException/physician_time', $data2)->render();
        return $data;
    }
}