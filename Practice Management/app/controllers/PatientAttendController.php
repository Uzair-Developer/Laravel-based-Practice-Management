<?php

use core\hospital\HospitalRepository;
use Laracasts\Flash\Flash;

class PatientAttendController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listPatientAttend()
    {
        if (!$this->user->hasAccess('patient_attend.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $data['patients'] = PatientAttend::getAll($inputs);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('patient_attend/list', $data);
    }

}