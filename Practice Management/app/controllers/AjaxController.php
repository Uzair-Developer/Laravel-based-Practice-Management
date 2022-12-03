<?php

use core\diagnosis\disease\DiseaseRepository;
use core\diagnosis\symptom\SymptomRepository;

class AjaxController extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function autoCompleteDisease()
    {
        $results = array();
        $diseaseRepo = new DiseaseRepository();
        $data = $diseaseRepo->getAutoComplete(Input::get('q'));
        foreach ($data as $key => $val) {
            $results[] = array($val, $key);
        }
        echo json_encode($results);
    }

    public function autoCompleteSymptom()
    {
        $results = array();
        $symptomRepo = new SymptomRepository();
        $data = $symptomRepo->getAutoComplete(Input::get('q'));
        foreach ($data as $key => $val) {
            $results[] = array($val, $key);
        }
        echo json_encode($results);
    }

    public function autoCompleteCountry()
    {
        $results = array();
        $data = Country::getAutoComplete(Input::get('q'));
        foreach ($data as $key => $val) {
            $results[] = array($val, $key);
        }
        echo json_encode($results);
    }

    public function autoCompletePatient()
    {
        $results = array();
        if (Input::get('q')) {
            $term = Input::get('q');
        } else {
            $term = Input::get('term');
        }
        $data = Patient::getPatientIdAutoComplete($term, Input::get('hospital_id'));
        foreach ($data as $key => $val) {
            if ($key) {
                $results[] = array('label' => $key . ' ' . $val, 'value' => $key);
            }
        }
        echo json_encode($results);
    }

    public function autoCompletePatient2()
    {
        $results = array();
        $data = Patient::getPatientIdAutoComplete(Input::get('q'), Input::get('hospital_id'));
        foreach ($data as $key => $val) {
            $results[] = array($key . ' ' . $val, $key);
        }
        echo json_encode($results);
    }

    public function autoCompletePatientByPhone()
    {
        $results = array();
        $data = Patient::getPatientIdByPhone(Input::get('q'), Input::get('hospital_id'));
        foreach ($data as $key => $val) {
            $patient = Patient::getById($key);
            $results[] = array($patient['phone'] . ' ' . $patient['name'], $patient['phone'], $key);
        }
        echo json_encode($results);
    }

    public function autoCompletePatientByPhone2()
    {
        $results = array();
        if (Input::get('q')) {
            $term = Input::get('q');
        } else {
            $term = Input::get('term');
        }
        $data = Patient::getPatientIdByPhone($term, Input::get('hospital_id'));
        foreach ($data as $key => $val) {
            if ($key) {
                $patient = Patient::getById($key);
                $results[] = array('label' => $patient['phone'] . ' ' . $patient['name'],
                    'value' => $patient['phone'], 'row_id' => $patient['id']);
            }
        }
        echo json_encode($results);
    }

    public function autoCompletePatientByNationalId()
    {
        $results = array();
        $data = Patient::getPatientIdByNationalId(Input::get('q'), Input::get('hospital_id'));
        foreach ($data as $key => $val) {
            $patient = Patient::getById($key);
            $results[] = array($patient['national_id'] . ' ' . $patient['name'], $patient['national_id'], $key);
        }
        echo json_encode($results);
    }

    public function autoCompletePatientByNationalId2()
    {
        $results = array();
        if (Input::get('q')) {
            $term = Input::get('q');
        } else {
            $term = Input::get('term');
        }
        $data = Patient::getPatientIdByNationalId($term, Input::get('hospital_id'));
        foreach ($data as $key => $val) {
            if ($key) {
                $patient = Patient::getById($key);
                $results[] = array('label' => $patient['national_id'] . ' ' . $patient['name'],
                    'value' => $patient['national_id'], 'row_id' => $patient['id']);
            }
        }
        echo json_encode($results);
    }

    public function autoCompletePatientShowName()
    {
        $results = array();
        $data = Patient::getPatientIdByIdsAutoComplete(Input::get('q'), Input::get('hospital_id'));
        foreach ($data as $key => $val) {
            $results[] = array($key . ' ' . $val, $val);
        }
        echo json_encode($results);
    }

    public function autoCompletePatientAll()
    {
        $results = array();
        $data = Patient::getPatientIdAutoComplete(Input::get('q'), Input::get('hospital_id'));
        foreach ($data as $key => $val) {
            $results[] = array($val, $key);
        }
        echo json_encode($results);
    }

}
