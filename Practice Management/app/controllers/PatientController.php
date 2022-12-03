<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\enums\LoggingAction;
use core\hospital\HospitalRepository;

class PatientController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listPatient()
    {
        if (!$this->user->hasAccess('patient.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['patients'] = Patient::getAllWithFilter(Input::all());
        $data['countries'] = Country::getParents();
//        $data['cities'] = Country::getChild();
        $data['links'] = $data['patients']->appends(Input::all())->links();

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('diagnosis/patient/list', $data);

    }

    public function listEvents()
    {
        $id = Input::get('patient_id');
        $view['events'] = PatientEvents::getEvents($id);
        $data['patient'] = Patient::getById($id);
        $data['patientEvents'] = View::make('diagnosis/diagnosis/patientEvents', $view)->render();
        return $data;
    }

    public function deletePatient($id)
    {
//        if (!$this->user->hasAccess('patient.edit') && !$this->user->hasAccess('admin')) {
//            Flash::error('You don\'t have a permission to do this action');
//            return Redirect::back();
//        }
//        Patient::remove($id);
//        Flash::success('Deleted Successfully');
//        return Redirect::back();
    }

    public function editPatient($id)
    {
        if (!$this->user->hasAccess('patient.edit') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['patient'] = Patient::getById($id);
//        $data['countries'] = Country::getParents();
        return View::make('diagnosis/patient/template', $data);
    }

    public function updatePatient($id)
    {
        $inputs = Input::except('_token');
        $validation = Validator::make($inputs, Patient::$rules);
        if ($validation->fails()) {
            Flash::error($validation->messages());
            return Redirect::back();
        }
        $fName = $inputs['first_name'] ? $inputs['first_name'] : '';
        $mName = $inputs['middle_name'] ? ' ' . $inputs['middle_name'] : '';
        $lName = $inputs['last_name'] ? ' ' . $inputs['last_name'] : '';
        $fmName = $inputs['family_name'] ? ' ' . $inputs['family_name'] : '';
        $name = $fName . $mName . $lName . $fmName;

        $inputs['name'] = $name;
        if(isset($inputs['sms_lang']) && $inputs['sms_lang']) {
            Reservation::editByPatient(array(
                'sms_lang' => $inputs['sms_lang']
            ), $id);
        }
        unset($inputs['sms_lang']);
        Patient::edit($inputs, $id);
        Flash::success('Updated Successfully');
        return Redirect::route('listPatient');
    }

    public function showReserveBtn()
    {
        $reservation_id = Input::get('reservation_id');
        $reservation = Reservation::getById($reservation_id);
        $patient = Patient::getById($reservation['patient_id']);
        $patient['reservation_code'] = $reservation['code'];
        return $patient;
    }

    public function searchCallerPhone()
    {
        $phone = Input::get('phone');
        $hospital_id = Input::get('hospital_id');
        $caller = CallerInfo::getByPhone($phone, $hospital_id);
        $data['patients'] = Patient::getByCallerId($caller['id']);
        $data['caller_id'] = $caller['id'];
        $data['caller_name'] = $caller['name'];
        $data['caller_phone'] = $caller['phone'];
        $data['patient'] = array();
        return $data;
    }

    public function searchCallerPhoneWithPatientId()
    {
        $patient_id = Input::get('id');
        $hospital_id = Input::get('hospital_id');
        $patient = Patient::getByRegistrationNo($patient_id, $hospital_id);
        $data['patient'] = $patient;
        $caller = CallerInfo::getById($patient['caller_id']);
        $data['patients'] = Patient::getByCallerId($caller['id']);
        $data['caller_id'] = $caller['id'];
        $data['caller_name'] = $caller['name'];
        $data['caller_phone'] = $caller['phone'];
        return $data;
    }

    public function updatePatientData()
    {
        $inputs = Input::except('_token');
        $patientData = $inputs['patientData'];
        if (isset($patientData['patient_id'])&& $patientData['patient_id']
        ) {
            $fName = $patientData['first_name'] ? $patientData['first_name'] : '';
            $mName = $patientData['middle_name'] ? ' ' . $patientData['middle_name'] : '';
            $lName = $patientData['last_name'] ? ' ' . $patientData['last_name'] : '';
            $fmName = $patientData['family_name'] ? ' ' . $patientData['family_name'] : '';
            $name = $fName . $mName . $lName . $fmName;
            if (isset($patientData['phone2']) && $patientData['phone2']) {
                $patientData['phone'] = $patientData['phone2'];
            }
            $patientArray = array(
                'name' => $name,
                'first_name' => $fName,
                'middle_name' => $mName,
                'last_name' => $lName,
                'family_name' => $fmName,
                'phone' => $patientData['phone'],
                'national_id' => $patientData['national_id'],
                'birthday' => $patientData['birthday'],
                'preferred_contact' => $patientData['preferred_contact'],
                'email' => $patientData['email'],
                'gender' => $patientData['gender'],
                'address' => $patientData['address']
            );
            Patient::edit($patientArray, $patientData['patient_id']);
            Logging::add([
                'action' => LoggingAction::update_patient,
                'table' => 'patients',
                'ref_id' => $patientData['patient_id'],
                'user_id' => $this->user->id,
            ]);
            $data['response'] = 'yes';
        } else {
            $data['response'] = 'no';
        }
        return $data;
    }

    public function getPatientData()
    {
        $inputs = Input::except('_token');
        return Patient::getById($inputs['patient_id']);
    }
}
