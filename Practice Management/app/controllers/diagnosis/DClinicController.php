<?php

use core\diagnosis\clinic\ClinicManager;
use core\diagnosis\clinic\ClinicRepository;


class DClinicController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->clinicRepo = new ClinicRepository();
        $this->clinicManager = new ClinicManager();
        $this->user = Sentry::getUser();
//        dd($this->user->hasAccess('clinic.list'));
    }

    public function dListClinic()
    {
        if(!$this->user->hasAccess('clinic.list') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['clinics'] = $this->clinicRepo->getAll();
        return View::make('diagnosis/clinic.list', $data);

    }

    public function dAddClinic()
    {
        if(!$this->user->hasAccess('clinic.add') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        return View::make('diagnosis/clinic.add');
    }

    public function dCreateClinic()
    {
        $inputs = Input::except('_token');
        $clinic = $this->clinicManager->addClinic($inputs);
        if ($clinic['status']) {
            return Redirect::route('dListClinic');
        } else {
            return Redirect::back()->withInput(Input::except('_token'));
        }
    }

    public function dEditClinic($id)
    {
        if(!$this->user->hasAccess('clinic.edit') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['clinic'] = $this->clinicRepo->getById($id);
        return View::make('diagnosis/clinic.edit', $data);


    }

    public function dUpdateClinic($id)
    {
        $this->clinicManager->updateClinic(Input::except('_token'), $id);
        return Redirect::route('dListClinic');
    }

    public function dDeleteClinic($id)
    {
        if(!$this->user->hasAccess('clinic.delete') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $this->clinicManager->delete($id);
        return Redirect::route('dListClinic');

    }
}
