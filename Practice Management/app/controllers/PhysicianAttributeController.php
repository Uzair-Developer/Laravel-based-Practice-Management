<?php

use core\hospital\HospitalRepository;
use Laracasts\Flash\Flash;

class PhysicianAttributeController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listPhysicianAttribute()
    {
        if (!$this->user->hasAccess('physician_attribute.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['clinic_services'] = PhysicianAttribute::getAll(array('type' => 1));
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('physician_attribute/list', $data);
    }

    public function createPhysicianAttribute()
    {
        $inputs = Input::except('_token');
        $inputs = $inputs['form'];
        unset($inputs['_token']);
        $validator = Validator::make($inputs, PhysicianAttribute::$rules);
        if ($validator->fails()) {
            $data['message'] = $validator->messages();
            $data['success'] = 'no';
        } else {
            try {
                PhysicianAttribute::add($inputs);
                $data['message'] = 'Added successfully';
                $data['success'] = 'yes';
            } catch (Exception $e) {
                $data['message'] = 'Ops, try again later!';
                $data['success'] = 'no';
            }
        }
        return $data;
    }

    public function updatePhysicianAttribute()
    {
        $inputs = Input::except('_token');
        $inputs = $inputs['form'];
        $id = $inputs['id'];
        unset($inputs['id']);
        unset($inputs['_token']);
        $validator = Validator::make($inputs, AttributePms::$rules);
        if ($validator->fails()) {
            $data['message'] = $validator->messages();
            $data['success'] = 'no';
        } else {
            try {
                PhysicianAttribute::edit($inputs, $id);
                $data['message'] = 'Updated successfully';
                $data['success'] = 'yes';
            } catch (Exception $e) {
                $data['message'] = 'Ops, try again later!';
                $data['success'] = 'no';
            }
        }
        return $data;
    }

    public function deletePhysicianAttribute()
    {
        $id = Input::get('id');
        PhysicianAttribute::remove($id);
        return 'Deleted Successfully';
    }

    public function getPhysicianAttribute()
    {
        $id = Input::get('id');
        return PhysicianAttribute::getById($id);
    }

    public function getPhysicianAttributeByType()
    {
        $type = Input::get('type');
        $data2['physicianAttribute'] = PhysicianAttribute::getAll(array('type' => $type));
        return View::make('physician_attribute.custom_list', $data2)->render();
    }
}