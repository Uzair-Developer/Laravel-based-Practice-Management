<?php

use core\hospital\HospitalRepository;

class PatientSurveyController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listPatientSurvey()
    {
        if (!$this->user->hasAccess('patientSurvey.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['paginate'] = 20;
        $inputs['details'] = true;
        $data['surveys'] = Survey::getAll();
        $data['patientSurvey'] = PatientSurvey::getAll($inputs);
        return View::make('patientSurvey/list', $data);
    }

    public function viewPatientSurvey()
    {
        $inputs = Input::except('_token');
        $id = $inputs['id'];
        $patientSurvey = PatientSurvey::getById($id);
        $survey = Survey::getById($patientSurvey['survey_id']);
        $data['survey'] = $survey;
        $data['patientSurveys'] = PatientSurveyDetails::getAllDetailsArray(['patient_survey_id' => $id, 'details' => true]);
        return View::make('patientSurvey/view', $data)->render();
    }

    public function getPatientSurvey()
    {
        $id = Input::get('id');
        return PatientSurvey::getById($id);
    }

    public function reportCountsPatientSurvey()
    {
        if (!$this->user->hasAccess('patientSurvey.report') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $data['surveys'] = Survey::getAll();
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        $data['inputs'] = $inputs;
        if ($inputs) {
            $questions = array();
            if (isset($inputs['question_id']) && $inputs['question_id']) {
                $questions[] = Question::getById($inputs['question_id'])->toArray();
            } elseif (isset($inputs['survey_id']) && $inputs['survey_id']) {
                $questions = Question::getBySurvey($inputs['survey_id']);
            }
            $data['survey'] = Survey::getById($inputs['survey_id']);
            $data['report'] = Question::getReportCounts($inputs['survey_id'], $questions, $inputs);
        }
        return View::make('patientSurvey/reportCounts', $data)->render();
    }

    public function printExcelPatientSurveyReportCounts()
    {
        if (!$this->user->hasAccess('patientSurvey.print_excel') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        Excel::create('survey_' . date('Y-m-d H-i-s'), function ($excel) use ($inputs) {
            // Set the title
            $excel->setTitle('survey_' . date('Y-m-d H-i-s'));
            $excel->sheet('survey', function ($sheet) use ($inputs) {
                $questions = array();
                if (isset($inputs['question_id']) && $inputs['question_id']) {
                    $questions[] = Question::getById($inputs['question_id'])->toArray();
                } elseif (isset($inputs['survey_id']) && $inputs['survey_id']) {
                    $questions = Question::getBySurvey($inputs['survey_id']);
                }
                $survey = Survey::getById($inputs['survey_id']);
                $report = Question::getReportCounts($inputs['survey_id'], $questions, $inputs);
                $row1 = array('Survey Name: ' . $survey['header_en']);
                $sheet->row(1, $row1);

                if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                    $clinic = Clinic::getById($inputs['clinic_id']);
                    $row1 = array('Clinic Name: ' . $clinic['name']);
                } else {
                    $row1 = array('Clinic Name: All');
                }
                $sheet->row(2, $row1);

                if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                    $physician = User::getById($inputs['physician_id']);
                    $row1 = array('Doctor Name: ' . $physician['full_name']);
                } else {
                    $row1 = array('Doctor Name: All');
                }
                $sheet->row(3, $row1);

                foreach ($report as $key => $val) {
                    $rowNum = $key + 4;
                    $row = array($val['title_en']);
                    $answer_type_data = $val['answer_type_data'];
                    $answers = explode(',', $answer_type_data['answers_en']);
                    foreach ($answers as $k => $v) {
                        $row[] = $v ;
                        $row[] =  $val['patientCount'][$k];
                    }
                    $sheet->row($rowNum, $row);
                }
                $sheet->setAutoSize(true);
                $sheet->cell('A1:A3', function ($cell) {
                    $cell->setBackground('#878D84');
                });
            });
        })->download('xlsx');
    }
}
