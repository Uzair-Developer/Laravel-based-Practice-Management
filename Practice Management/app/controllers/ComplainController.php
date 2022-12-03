<?php

use core\clinicSchedule\ClinicScheduleRepository;
use core\enums\AgeType;
use core\enums\AttributeType;
use core\hospital\HospitalRepository;
use core\physician\PhysicianManager;
use Laracasts\Flash\Flash;

class ComplainController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listComplain()
    {
        if (!$this->user->hasAccess('complain.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $data['complains'] = Complain::getAll($inputs);

        $data['departments'] = AttributePms::getAll(AttributeType::$pmsReturn['department']);
        return View::make('complain/list', $data);
    }

    public function addComplain()
    {
        if (!$this->user->hasAccess('complain.add') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['complain'] = array(
            'patient_id' => '',
            'department_id' => '',
            'created_by' => '',
            'notes' => '',
            'read' => '',
        );
        $data['departments'] = AttributePms::getAll(AttributeType::$pmsReturn['department']);
        $data['relevant'] = AttributePms::getAll(AttributeType::$pmsReturn['relevantType']);
        return View::make('complain/add', $data);
    }

    public function createComplain()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, Complain::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                $patient_id = $inputs['patient_id'];
                if (!$patient_id) {
                    unset($inputs['patient_id']);
                    unset($inputs['id']);
                    if ($inputs['caller_id']) {
                        $caller_id = $inputs['caller_id'];
                    } else {
                        $caller = CallerInfo::add(array(
                            'name' => $inputs['caller_name'],
                            'phone' => $inputs['phone']
                        ));
                        $caller_id = $caller->id;
                    }
                    $interval = Functions::getAgeDetails($inputs['birthday']);
                    $age = '';
                    $age_type_id = '';
                    if ($interval->y) {
                        $age = $interval->y;
                        $age_type_id = AgeType::$ageReturn['Years'];
                    } else {
                        if ($interval->m) {
                            $age = $interval->m;
                            $age_type_id = AgeType::$ageReturn['Months'];
                        } else {
                            if ($interval->d) {
                                $age = $interval->d;
                                $age_type_id = AgeType::$ageReturn['Days'];
                            } else {
                                if ($interval->h) {
                                    $age = $interval->h;
                                    $age_type_id = AgeType::$ageReturn['Hours'];
                                }
                            }
                        }
                    }
                    $patient = Patient::add(array(
                        'phone' => $inputs['phone'],
                        'name' => $inputs['first_name'] . ' ' . $inputs['middle_name']
                            . ' ' . $inputs['last_name'] . ' ' . $inputs['family_name'],
                        'first_name' => $inputs['first_name'],
                        'middle_name' => $inputs['middle_name'],
                        'last_name' => $inputs['last_name'],
                        'family_name' => $inputs['family_name'] ? $inputs['family_name'] : null,
                        'relevant_type_id' => $inputs['relevant_id'],
                        'national_id' => $inputs['national_id'],
                        'phone2' => $inputs['phone2'],
                        'birthday' => $inputs['birthday'],
                        'age' => $age,
                        'age_type_id' => $age_type_id,
                        'age_year' => $interval->y,
                        'age_month' => $interval->m,
                        'age_day' => $interval->d,
                        'age_hour' => $interval->h,
                        'email' => $inputs['email'],
                        'gender' => $inputs['gender'],
                        'address' => $inputs['address'],
                        'caller_id' => $caller_id,
                        'by_complain' => 1, // 1 -> mean insert by complain
                        'sync_flag' => 0,
                    ));
                    $patient_id = $patient->id;
                }
                Complain::add(array(
                    'patient_id' => $patient_id,
                    'department_id' => $inputs['department_id'],
                    'created_by' => $this->user->id,
                    'notes' => $inputs['notes'],
                    'read' => 2,
                ));
                Flash::success('Added successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listComplain');
    }

    public function editComplain($id)
    {
        if (!$this->user->hasAccess('complain.edit') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['complain'] = Complain::getById($id);
        $data['patient'] = Patient::getById($data['complain']['patient_id']);
        $data['departments'] = AttributePms::getAll(AttributeType::$pmsReturn['department']);
        return View::make('complain/add', $data);
    }

    public function updateComplain($id)
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, Complain::$rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                Complain::edit($inputs, $id);
                Flash::success('Updated successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listComplain');
    }

    public function deleteComplain($id)
    {
        if (!$this->user->hasAccess('complain.delete') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        Complain::remove($id);
        return Redirect::route('listComplain');
    }

    public function readComplain($id)
    {
        if (!$this->user->hasAccess('complain.read') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        Complain::edit(array(
            'read' => 1
        ),$id);
        return Redirect::route('listComplain');
    }


}