<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\clinic\ClinicManager;
use core\clinic\ClinicRepository;
use core\clinicSchedule\ClinicScheduleRepository;
use core\enums\AttributeType;
use core\hospital\HospitalRepository;
use core\physician\PhysicianManager;
use core\user\UserRepository;

class ClinicController extends BaseController
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
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinic_pms.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $clinicRepo = new ClinicRepository();
        $inputs = (Input::except('_token'));
        $inputs['details'] = true;
        $data['clinics'] = $clinicRepo->getAll($inputs);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('clinic/list', $data);
    }

    public function addClinic()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinic_pms.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();

        $data['category'] = AttributePms::getAll(AttributeType::$pmsReturn['clinicCategory']);
        return View::make('clinic/add', $data);
    }

    public function createClinic()
    {
        $clinicManager = new ClinicManager();
        $inputs = (Input::except('_token'));
        $data = $clinicManager->createClinic($inputs);
        if ($data['status']) {
            return Redirect::route('clinics');
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editClinic($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinic_pms.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $clinicRepo = new ClinicRepository();
        $data['clinic'] = $clinicRepo->getById($id);
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();

        $data['category'] = AttributePms::getAll(AttributeType::$pmsReturn['clinicCategory']);
        return View::make('clinic/edit', $data);
    }

    public function updateClinic($id)
    {
        $systemManager = new ClinicManager();
        $inputs = Input::except('_token');
        $data = $systemManager->updateClinic($inputs, $id);
        if ($data['status']) {
            return Redirect::route('clinics');
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deleteClinic($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinic_pms.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $clinicRepo = new ClinicRepository();
        $clinicRepo->delete($id);
        return Redirect::back();
    }

    public function getClinicsByHospitalId()
    {
        $hospitalId = Input::get('hospital_id');
        $clinicRepo = new ClinicRepository();
        $data['clinics'] = $clinicRepo->getByHospitalId($hospitalId);
        return View::make('clinic/clinicsByHospitalId', $data)->render();
    }

    public function getClinicAvailability()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinic_pms.times_availability')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['relevant'] = AttributePms::getAll(AttributeType::$pmsReturn['relevantType']);
        return View::make('clinic/times_availability', $data);
    }

    public function getAvailabilityByClinicId()
    {
        $clinic_id = Input::get('clinic_id');
        $physician_id = Input::get('physician_id');
        $selectedDate = Input::get('date');
        $physicianManager = new PhysicianManager();
        if($physician_id) {
            $physicians[0] = User::getById($physician_id);
        } else {
            $physicians = $physicianManager->getPhysicianByClinicId($clinic_id, true);
        }
        $userRepo = new UserRepository();
        $CSRepo = new ClinicScheduleRepository();
        $data = array();
        $data['selectedDate'] = $selectedDate;
        foreach ($physicians as $key => $val) {
            $availableTimes = array();
            $clinicSchedule = $CSRepo->getByClinicId($clinic_id, $selectedDate);
            $physician_selected = $userRepo->getByUserIdWithSchedule($val['id'], $clinicSchedule, $selectedDate);
            $physicianSchedule = isset($physician_selected['schedules'][0]) ? $physician_selected['schedules'][0] : array();
            if (!$physicianSchedule) {
                continue;
            }
            $physicianManager->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $selectedDate);
            $data['allData'][] = array(
                'physician_name' => $val['full_name'],
                'physician_id' => $val['id'],
                'slots' => $physicianSchedule['slots'],
                'clinic_schedule_id' => $physicianSchedule['clinic_schedule_id'],
                'times' => $availableTimes
            );
        }
        if(isset($data['allData'][0]) && isset($data['allData'][0]['physician_name']) && $data['allData'][0]['physician_name']) {
            return View::make('clinic/physicians_time', $data)->render();
        } else {
            return 'No Schedules For Selected Criteria!';
        }
    }

    public function printExcelClinics()
    {
        $inputs = Input::except('_token');
        $clinicRepo = new ClinicRepository();
        $clinics = $clinicRepo->getAll($inputs);
        Excel::create('clinics_' . date('Y-m-d H-i-s'), function ($excel) use ($clinics) {
            // Set the title
            $excel->setTitle('clinics');
            $excel->sheet('clinics', function ($sheet) use ($clinics) {
                $sheet->loadView('clinic/printExcel', array('clinics' => $clinics));
            });

        })->download('xlsx');
    }

}
