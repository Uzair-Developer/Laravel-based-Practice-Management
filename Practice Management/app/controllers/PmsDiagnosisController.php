<?php


use core\enums\AttributeType;
use core\hospital\HospitalRepository;

class PmsDiagnosisController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function pmsDiagnosis()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('pmsDiagnosis.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['details'] = true;
        $inputs['paginate'] = true;
        $data['pmsDiagnosis'] = PmsDiagnosis::getAll($inputs);

        $data['main_system_affected'] = AttributePms::getAll(AttributeType::$pmsReturn['mainSystemAffected']);
        $data['referred_to_parent'] = AttributePms::getPatentReferredTo();
        $data['organs'] = Organ::getAll();
        return View::make('pmsDiagnosis/list', $data);
    }

    public function addPmsDiagnosis()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('pmsDiagnosis.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['pmsDiagnosis'] = array(
            'patient_id' => '',
            'complain' => '',
            'main_system_affected_id' => '',
            'organ_id' => '',
            'primary_diagnosis' => '',
            'referred_to_parent_id' => '',
            'referred_to_child_id' => '',
        );

        $data['relevant'] = AttributePms::getAll(AttributeType::$pmsReturn['relevantType']);
        $data['main_system_affected'] = AttributePms::getAll(AttributeType::$pmsReturn['mainSystemAffected']);
        $data['referred_to_parent'] = AttributePms::getPatentReferredTo();
//        $data['referred_to_child'] = AttributePms::getChildReferredTo();
        $data['organs'] = Organ::getAll();
        return View::make('pmsDiagnosis/add', $data);
    }

    public function createPmsDiagnosis()
    {
        try {
            $inputs = (Input::except('_token'));
            $patient_id = $inputs['patient_id'];
            $fName = $inputs['first_name'] ? $inputs['first_name'] : '';
            $mName = $inputs['middle_name'] ? ' ' . $inputs['middle_name'] : '';
            $lName = $inputs['last_name'] ? ' ' . $inputs['last_name'] : '';
            $fmName = $inputs['family_name'] ? ' ' . $inputs['family_name'] : '';
            $name = $fName . $mName . $lName . $fmName;
            if (!$patient_id) {
                unset($inputs['patient_id']);
                unset($inputs['id']);
                $patient = Patient::add(array(
                    'hospital_id' => $inputs['hospital_id'],
                    'phone' => $inputs['phone'],
                    'name' => $name,
                    'first_name' => $fName,
                    'middle_name' => $mName,
                    'last_name' => $lName,
                    'family_name' => $fmName,
                    'relevant_type_id' => $inputs['relevant_id'],
                    'national_id' => $inputs['national_id'],
                    'phone2' => $inputs['phone2'],
                    'age' => $inputs['age'],
                    'email' => $inputs['email'],
                    'gender' => $inputs['gender'],
                    'marital_status_id' => $inputs['marital_status_id'],
                    'address' => $inputs['address'],
                    'sync_flag' => 0,
                ));
                $patient_id = $patient->id;
            } else {
                if (isset($inputs['phone2']) && $inputs['phone2'] && $inputs['phone'] != $inputs['phone2']) {
                    $patientArray['phone'] = $inputs['phone2'];
                    Patient::edit($patientArray, $patient_id);
                }
            }
            $array = array(
                'created_by' => $this->user->id,
                'patient_id' => $patient_id,
                'complain' => $inputs['complain'],
                'organ_id' => implode(',', $inputs['organ_id']),
                'main_system_affected_id' => $inputs['main_system_affected_id'],
                'primary_diagnosis' => $inputs['primary_diagnosis'],
                'referred_to_parent_id' => $inputs['referred_to_parent_id'],
                'referred_to_child_id' => isset($inputs['referred_to_child_id']) ? $inputs['referred_to_child_id'] : null,
            );
            PmsDiagnosis::add($array);
            Flash::success('Added Successfully');
            return Redirect::route('pmsDiagnosis');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editPmsDiagnosis($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('pmsDiagnosis.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();

        $data['relevant'] = AttributePms::getAll(AttributeType::$pmsReturn['relevantType']);
        $data['main_system_affected'] = AttributePms::getAll(AttributeType::$pmsReturn['mainSystemAffected']);
        $data['referred_to_parent'] = AttributePms::getPatentReferredTo();
        $data['organs'] = Organ::getAll();

        $pmsDiagnosis = PmsDiagnosis::getById($id);
        $data['pmsDiagnosis'] = $pmsDiagnosis;
        $data['patient'] = Patient::getById($pmsDiagnosis['patient_id']);
        return View::make('pmsDiagnosis/add', $data);
    }

    public function updatePmsDiagnosis($id)
    {
        $inputs = (Input::except('_token'));
        try {
            $inputs['organ_id'] = implode(',', $inputs['organ_id']);
            $inputs['updated_by'] = $this->user->id;
            PmsDiagnosis::edit($inputs, $id);
            Flash::success('Updated Successfully');
            return Redirect::route('pmsDiagnosis');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deletePmsDiagnosis($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('pmsDiagnosis.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        PmsDiagnosis::remove($id);
        Flash::success('Delete Successfully');
        return Redirect::route('pmsDiagnosis');
    }

    public function getDiseaseByName()
    {
        $term = Input::get('term');
        $disease = Disease::getAll(array(
            'name' => $term,
            'namesOnly' => true,
            'limit' => 10
        ));
        return json_encode($disease);
    }
}
