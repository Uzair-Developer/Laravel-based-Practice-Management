<?php


use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\diagnosis\disease\DiseaseRepository;
use core\diagnosis\enums\CommentsType;
use core\diagnosis\enums\DiseaseSymptomsStatus;
use core\diagnosis\enums\EventDetailsTypes;
use core\diagnosis\enums\EventStatus;
use core\diagnosis\enums\SymptomAttr;
use core\diagnosis\organ\OrganRepository;
use core\diagnosis\symptom\SymptomRepository;
use Laracasts\Flash\Flash;

class DiagnosisController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function startDiagnosis1()
    {
        if (!$this->user->hasAccess('diagnosis.list') && $this->user->user_type_id != 1) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        if (Request::ajax()) {
            $comments = Comment::getAllByUser(CommentsType::symptom, Sentry::getUser()['id']);
            $diseaseSymptom = DiseaseSymptoms::getAllByUser(Sentry::getUser()['id']);
            $data['comments'] = View::make('diagnosis/symptom/comment_list', array('comments' => $comments))->render();
            $data['diseaseSymptom'] = View::make('diagnosis/diagnosis/disease_symptom',
                array('diseaseSymptom' => $diseaseSymptom))->render();
            return $data;
        } else {
            $data['countries'] = Country::getParents();

            $syRepo = new SymptomRepository();
            $data['symptoms'] = $syRepo->getAll();

            $organRepo = new OrganRepository();
            $data['organs'] = $organRepo->getAll();

            $diseaseRepo = new DiseaseRepository();
            $data['diseases'] = $diseaseRepo->getAll();
//            $data['comments'] = Comment::getAllByUser(CommentsType::symptom, Sentry::getUser()['id']);
//            $data['diseaseSymptom'] = DiseaseSymptoms::getAllByUser(Sentry::getUser()['id']);
            return View::make('diagnosis/diagnosis/newPatient', $data);
        }
    }

    public function postStartDiagnosis1()
    {
        $inputs = Input::except('_token');
        try {
            $patient_id = $inputs['patient_id'];
            unset($inputs['patient_id']);
            if ($patient_id) {
                Patient::edit($inputs, $patient_id);
                $event = PatientEvents::add(array(
                    'create_by' => Sentry::getUser()->id,
                    'patient_id' => $patient_id,
                    'status' => EventStatus::pending,
                    'create_timestamp' => time()
                ));
                $date = explode('-', $inputs['birthday']);
                Session::put('patient', array(
                    'id' => $patient_id,
                    'event_id' => $event->id,
                    'age' => Functions::getAge($date[0]),
                    'gender' => $inputs['gender'],
                    'phone' => $inputs['phone'],
                    'name' => $inputs['name']
                ));
            } else {
                $inputs['create_timestamp'] = time();
                $patient = Patient::add($inputs);
                $event = PatientEvents::add(array(
                    'patient_id' => $patient->id,
                    'status' => EventStatus::pending,
                    'create_timestamp' => time()
                ));
                Session::put('patient', array(
                    'id' => $patient->id,
                    'event_id' => $event->id,
                    'age' => Functions::getAge($inputs['birthday']),
                    'gender' => $inputs['gender'],
                    'phone' => $inputs['phone'],
                    'name' => $inputs['name']
                ));
            }
            return (1);
        } catch (Exception $e) {
            return (2);
        }
    }

    public function postStartDiagnosis2()
    {
        if (!Session::has('patient')) {
            return (0);
        }
        $patientInfo = Session::get('patient');
        $inputs = Input::except('_token');
        if (empty($inputs['symptom_id'])) {
            return (2);
        }
        foreach ($inputs['symptom_id'] as $key => $val) {
            if (!EventDetails::checkExist($patientInfo['event_id'], EventDetailsTypes::symptoms, $val)) {
                $array = array(
                    'event_id' => $patientInfo['event_id'],
                    'event_type' => EventDetailsTypes::symptoms,
                    'reference_id' => $val,
                    'create_timestamp' => time()
                );
                EventDetails::add($array);
            }
        }
        $this->getDiseaseOfSymptoms($inputs['symptom_id']);
        return (1);
    }

    public function startDiagnosis3()
    {
        $diseaseIds = array();
        foreach (Session::get('diseases') as $key => $val) {
            $diseaseIds[] = $val['id'];
        }

        $data['diseases'] = Disease::getWithSymptoms($diseaseIds, Session::get('symptoms'));
        $diseasesDiv = '';
        $this->viewDisease($diseasesDiv, Session::get('diseases'));
        $data['diseasesResult'] = $diseasesDiv;
        return View::make('diagnosis/diagnosis/additionalSymptoms', $data);
    }

    public function postStartDiagnosis3()
    {
        if (!Session::has('diseases') && !Session::has('patient')) {
            return (0);
        }
        $inputs = Input::except('_token');
        $patientInfo = Session::get('patient');
        $allSymptoms = Session::get('symptoms');
        // disease loop in form
        foreach ($inputs as $key => $val) {
            // symptoms loop in form
            foreach ($val as $key2 => $val2) {
                if ($val2 != 0) {
                    if (!EventDetails::checkExist($patientInfo['event_id'], EventDetailsTypes::symptoms, $key2)) {
                        $array = array(
                            'event_id' => $patientInfo['event_id'],
                            'event_type' => EventDetailsTypes::symptoms,
                            'reference_id' => $key2,
                            'create_timestamp' => time()
                        );
                        EventDetails::add($array);
                    }
                    if (!in_array($key2, $allSymptoms)) {
                        $allSymptoms[] = $key2;
                    }
                }
            }
        }
        $this->getDiseaseOfSymptoms($allSymptoms);
        return 1;
    }

    public function startDiagnosis4()
    {
        $diseaseIds = array();
        $patientInfo = Session::get('patient');
        foreach (Session::get('diseases') as $key => $val) {
            $diseaseIds[] = $val['id'];
        }

        $data['diseases'] = Disease::getWithQuestions($diseaseIds, $patientInfo['age'], $patientInfo['gender']);
        $diseasesDiv = '';
        $this->viewDisease($diseasesDiv, Session::get('diseases'));
        $data['diseasesResult'] = $diseasesDiv;
        return View::make('diagnosis/diagnosis/diseaseQuestions', $data);
    }

    public function postStartDiagnosis4()
    {
        if (!Session::has('diseases') && !Session::has('patient')) {
            return (0);
        }
        $inputs = Input::except('_token');
        $patientInfo = Session::get('patient');
        $diseases = Session::get('diseases');
        $allSymptoms = Session::get('symptoms');
        $this->getDiseaseOfSymptoms($allSymptoms);
        foreach ($diseases as $key => $val) {
            if (!EventDetails::checkExist($patientInfo['event_id'], EventDetailsTypes::diseases, $val['id'])) {
                $array = array(
                    'event_id' => $patientInfo['event_id'],
                    'event_type' => EventDetailsTypes::diseases,
                    'reference_id' => $val['id'],
                    'response' => $val['rate'],
                    'create_timestamp' => time()
                );
                EventDetails::add($array);
            }
        }
        foreach ($inputs as $key => $val) {
            $countQuestionsFailScore = 0;
            $countQuestionsFail = 0;
            foreach ($val as $key2 => $val2) {
                $array = array(
                    'event_id' => $patientInfo['event_id'],
                    'event_type' => EventDetailsTypes::questions,
                    'reference_id' => $key2,
                    'create_timestamp' => time()
                );
                if ($val2) {
                    $array['response'] = 0;
                    $countQuestionsFailScore += $val2;
                    $countQuestionsFail++;
                } else {
                    $array['response'] = 1;
                }
                if (!EventDetails::checkExist($patientInfo['event_id'], EventDetailsTypes::questions, $key2, $array['response'])) {
                    EventDetails::add($array);
                }
            }
            if ($countQuestionsFail != 0) {
                $decrementPercentage = (($countQuestionsFailScore / $countQuestionsFail) * 100) / 5;
                $newRate = $diseases[$key]['rate'] - (($diseases[$key]['rate'] * $decrementPercentage) / 100);
                $diseases[$key]['rate'] = number_format((float)$newRate, 2, '.', '');
            }
        }
        PatientEvents::edit(array('status' => EventStatus::success), $patientInfo['event_id']);
//        Session::forget('symptoms');
//        Session::forget('diseases');
//        Session::forget('patient');
        $data['patientInfo'] = $patientInfo;
        arsort($diseases);
        $diseasesDiv = '';
        $this->viewDisease($diseasesDiv, $diseases);
        $data['diseases'] = $diseasesDiv;
        return View::make('diagnosis/diagnosis/finalDiagnosis', $data);
    }

    public function diagnosisStartAgain()
    {
        $agent_comment = Input::get('agent_comment');
        $patientInfo = Session::get('patient');
        PatientEvents::edit(array('agent_comment' => $agent_comment), $patientInfo['event_id']);
        Session::forget('symptoms');
        Session::forget('diseases');
        Session::forget('patient');
        return 1;
    }

    public function getDiseaseOfSymptoms($symps = null)
    {
        if ($symps) {
            $symptoms = $symps;
        } else {
            $symptoms = Input::get('symptoms');
        }
        $countSymptoms = count($symptoms);
        $all = array();
        $allDisease = array();
        $allSymptoms = array();
        $associatedSymptom = array();
        foreach ($symptoms as $key => $val) {
            $symptom = Symptom::getById($val);
            if ($symptom['type'] != SymptomAttr::$typeReturn['primary']) {
                $associatedSymptom[] = $val;
                continue;
            }
            $disease = ViewDiseaseSymptoms::getDiseasesBySymptom($val);
            if ($disease) {
                foreach ($disease as $val2) {
                    $name = str_replace(' ', '_', $val2['disease_name']);
                    $all[$name]['rate'][] = $val2['rate'];
                    $all[$name]['id'] = $val2['disease_id'];
                    $all[$name]['type'] = $val2['type'];
                    $all[$name]['symptoms'][] = $val;
                    $allSymptoms[] = $val;
                    $allDisease[] = $val2['disease_id'];
                }
            }
        }
        $associatedSymptomDisease = array();
        if ($associatedSymptom) {
            foreach ($associatedSymptom as $key => $val) {
                $disease = ViewDiseaseSymptoms::getDiseasesBySymptom($val);
                if ($disease) {
                    foreach ($disease as $val2) {
                        $associatedSymptomDisease[$key]['disease_id'][] = $val2['disease_id'];
                        $associatedSymptomDisease[$key]['symptom_id'] = $val;
                        if (in_array($val2['disease_id'], $allDisease)) {
                            $name = str_replace(' ', '_', $val2['disease_name']);
                            if (!in_array($val, $all[$name]['symptoms'])) {
                                $all[$name]['rate'][] = $val2['rate'];
                                $all[$name]['symptoms'][] = $val;
                                if(!in_array($val, $allSymptoms)){
                                    $allSymptoms[] = $val;
                                }
                            }
                        }
                    }
                }
            }
        }
        if ($associatedSymptomDisease) {
            foreach ($associatedSymptomDisease as $key => $val) {
                foreach ($associatedSymptomDisease as $key2 => $val2) {
                    if ($key2 > $key) {
                        foreach ($val2['disease_id'] as $key3 => $val3) {
                            if (in_array($val3, $val['disease_id'])) {
                                $inner = ViewDiseaseSymptoms::getByDiseasesAndSymptom($val3, $val2['symptom_id']);
                                $outer = ViewDiseaseSymptoms::getByDiseasesAndSymptom($val3, $val['symptom_id']);
                                if ($inner) {
                                    $name = str_replace(' ', '_', $inner['disease_name']);
                                    if (in_array($val3, $allDisease)) {
                                        if (!in_array($val2['symptom_id'], $all[$name]['symptoms'])) {
                                            $all[$name]['rate'][] = $inner['rate'];
                                            $all[$name]['symptoms'][] = $val2['symptom_id'];
                                            if(!in_array($val2['symptom_id'], $allSymptoms)){
                                                $allSymptoms[] = $val2['symptom_id'];
                                            }
                                        }
                                    } else {
                                        $all[$name]['rate'][] = $inner['rate'];
                                        $all[$name]['id'] = $val3;
                                        $all[$name]['type'] = $inner['type'];
                                        $all[$name]['symptoms'][] = $val2['symptom_id'];
                                        $allDisease[] = $val3;
                                        if(!in_array($val2['symptom_id'], $allSymptoms)){
                                            $allSymptoms[] = $val2['symptom_id'];
                                        }
                                    }
                                }
                                if ($outer) {
                                    $name = str_replace(' ', '_', $outer['disease_name']);
                                    if (in_array($val3, $allDisease)) {
                                        if (!in_array($val['symptom_id'], $all[$name]['symptoms'])) {
                                            $all[$name]['rate'][] = $outer['rate'];
                                            $all[$name]['symptoms'][] = $val['symptom_id'];
                                            if(!in_array($val['symptom_id'], $allSymptoms)){
                                                $allSymptoms[] = $val['symptom_id'];
                                            }
                                        }
                                    } else {
                                        $all[$name]['rate'][] = $outer['rate'];
                                        $all[$name]['id'] = $val3;
                                        $all[$name]['type'] = $outer['type'];
                                        $all[$name]['symptoms'][] = $val['symptom_id'];
                                        $allDisease[] = $val3;
                                        if(!in_array($val['symptom_id'], $allSymptoms)){
                                            $allSymptoms[] = $val['symptom_id'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $countSymptoms = count($allSymptoms);
        foreach ($all as $key => $val) {
            $count = 0;
            if ($val['rate']) {
                foreach ($val['rate'] as $key2 => $val2) {
                    $count += $val2;
                }
            }
            $newRate = $count / $countSymptoms;
            $all[$key]['rate'] = number_format((float)$newRate, 2, '.', '');
        }
        arsort($all);
        Session::put('diseases', $all);
        Session::put('symptoms', $symptoms);
        $diseasesDiv = '';
        $this->viewDisease($diseasesDiv, $all);
        return $diseasesDiv;
    }

    private function viewDisease(&$diseasesDiv, $all)
    {
        $data['all'] = $all;
        $diseasesDiv = View::make('diagnosis/diagnosis/possible_diseases', $data)->render();
    }

    public function checkPatientExist()
    {
        $search = Input::get('search');
        $searchById = Input::get('this_id');
        $searchByNationalId = Input::get('this_national_id');
        $hospital_id = Input::get('hospital_id');
        $data = Patient::getRefId($search, $searchById, $hospital_id, $searchByNationalId);
        if ($data) {
//            $countries = Country::getParents();
//            $cities = Country::getChildOfCountry($data['country_id']);
//            $data['countries'] = 0;
//            $data['cities'] = 0;
//            if ($data) {
//                $htmlCountries = "<option value=''>Choose</option>";
//                $htmlCities = "<option value=''>Choose</option>";
//                foreach ($countries as $val) {
//                    if ($val['id'] == $data['country_id'])
//                        $htmlCountries .= "<option selected value='" . $val['id'] . "'>" . $val['name'] . "</option>";
//                    else
//                        $htmlCountries .= "<option value='" . $val['id'] . "'>" . $val['name'] . "</option>";
//                }
//
//                foreach ($cities as $val) {
//                    if ($val['id'] == $data['city_id'])
//                        $htmlCities .= "<option selected value='" . $val['id'] . "'>" . $val['name'] . "</option>";
//                    else
//                        $htmlCities .= "<option value='" . $val['id'] . "'>" . $val['name'] . "</option>";
//                }
//                $data['countries'] = $htmlCountries;
//                $data['cities'] = $htmlCities;
//            }
//            $data['eventsCount'] = count(PatientEvents::getEvents($data['id']));
//            $view['events'] = PatientEvents::getEvents($data['id']);
//            $data['patientEvents'] = View::make('diagnosis/diagnosis/patientEvents', $view)->render();
            return $data;
        } else {
            return array();
        }
    }

    public function createSymptomInDiagnosis()
    {
        $inputs = Input::except('_token');
        try {
            $symptomRepo = new SymptomRepository();
            $disease_id = array();
            $diseaseRate = array();
            if (isset($inputs['disease_id'])) {
                $disease_id = $inputs['disease_id'];
                $diseaseRate = $inputs['diseaseRate'];
                unset($inputs['disease_id']);
                unset($inputs['diseaseRate']);
            }
            $inputs['create_timestamp'] = time();
            $symptom = $symptomRepo->save($inputs);

            if (!empty($disease_id[0])) {
                foreach ($disease_id as $key => $val) {
                    $array = array(
                        'disease_id' => $val,
                        'symptom_id' => $symptom->id,
                        'rate' => $diseaseRate[$key],
                        'create_timestamp' => time(),
                        'status' => DiseaseSymptomsStatus::pending,
                        'enter_direct' => 0,
                        'user_id' => $this->user->id
                    );
                    if($this->user->user_type_id == 1) {
                        $array['status'] = DiseaseSymptomsStatus::approval;
                        $array['enter_direct'] = 1;
                    }
                    DiseaseSymptoms::add($array);
                }
            }
            return 'Added Successfully';
        } catch (Exception $e) {
            return '0';
        }
    }

    public function addDiseaseToSymptom()
    {
        $inputs = Input::except('_token');
        try {
            $disease_id = array();
            $diseaseRate = array();
            if (isset($inputs['disease_id'])) {
                $disease_id = $inputs['disease_id'];
                $diseaseRate = $inputs['diseaseRate'];
            }
            if (!empty($disease_id[0])) {
                foreach ($disease_id as $key => $val) {
                    if (!DiseaseSymptoms::checkExist($val, $inputs['symptom_id'])) {
                        $array = array(
                            'disease_id' => $val,
                            'symptom_id' => $inputs['symptom_id'],
                            'rate' => $diseaseRate[$key],
                            'create_timestamp' => time(),
                            'status' => DiseaseSymptomsStatus::pending,
                            'enter_direct' => 0,
                            'user_id' => Sentry::getUser()->id
                        );
                        DiseaseSymptoms::add($array);
                    }
                }
            }
            return 'Added Successfully';
        } catch (Exception $e) {
            return '0';
        }
    }

    public function addCommentToSymptom()
    {
        $inputs = (Input::except('_token'));
        try {
            Comment::add(array(
                'type' => CommentsType::symptom,
                'ref_id' => $inputs['symptom_id'],
                'comment' => $inputs['comment'],
                'user_id' => Sentry::getUser()->id
            ));
            return 'Added Successfully';
        } catch (Exception $e) {
            return '0';
        }
    }

    public function diagnosisDeleteComment()
    {
        $id = Input::get('id');
        Comment::remove($id);
        return 1;
    }

    public function diagnosisDeleteDiseaseSymptom()
    {
        $id = Input::get('id');
        DiseaseSymptoms::remove($id);
        return 1;
    }
}
