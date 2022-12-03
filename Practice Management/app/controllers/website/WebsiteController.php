<?php

class WebsiteController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login', array('except' => array('websiteOnlineSurvey', 'websiteInPatientSurvey',
            'websiteCheckInPatientDischarge', 'webSiteSaveInPatientSurvey')));
    }

    public function websiteSettings()
    {
        $data['settings'] = PatientInstruction::getById(1);
        return View::make('website/dynamic_fields', $data);
    }

    public function updateWebsiteSettings()
    {
        $inputs = Input::except('_token');
        $data = PatientInstruction::getById(1);
        if ($data) {
            PatientInstruction::edit($inputs, 1);
        } else {
            PatientInstruction::add($inputs);
        }
        Flash::success('Updated Successfully');
        return Redirect::back();
    }

    public function websiteOnlineSurvey($lang, $reservation_id)
    {
        $inputs = Input::except('_token');
        $inputs['lang'] = $lang;
        $data['inputs'] = $inputs;
        $data['lang'] = $lang;
        $data['reservation_id'] = $reservation_id;
        $data['reservation'] = Reservation::getById($reservation_id);
        if($data['reservation']) {
            $data['survey'] = Survey::getById(1);
            $data['surveyToGroups'] = GroupToSurvey::getAll(['survey_id' => 1, 'details' => true]);
            $data['html'] = View::make('api/websiteAPI/out_patient_survey', $data)->render();
        }
        return View::make('website/online_survey', $data);
    }

    public function websiteInPatientSurvey($lang)
    {
        $inputs = Input::except('_token');
        $inputs['lang'] = $lang;
        $data['inputs'] = $inputs;
        $data['lang'] = $lang;
        $data['survey'] = Survey::getById(2);
        $data['surveyToGroups'] = GroupToSurvey::getAll(['survey_id' => 2, 'details' => true]);
        return View::make('website/in_patient_survey', $data);
    }

    public function websiteCheckInPatientDischarge()
    {
        $inputs = Input::except('_token');
        $lang = $inputs['lang'];
        $patient = InPatient::getByRegistrationNo($inputs['patient_id']);
        if ($patient) {
            return json_encode(array('status' => '1', 'response' => ucwords(strtolower(Patient::getName($patient['patient_id'])))));
        } else {
            if ($lang == 'en') {
                $msg = 'Wrong PIN Number';
            } else {
                $msg = 'من فضلك أدخل الرقم التعريفي الصحيح';
            }
            return json_encode(array('status' => '0', 'response' => $msg));
        }
    }

    public function webSiteSaveInPatientSurvey()
    {
        $inputs = Input::except('_token');
        $lang = $inputs['lang'];
        $form = array();
        parse_str($inputs['form'], $form);
        $patient_reg_no = $form['id'];
        $answers = $form['answer'];
        $patient = Patient::getByRegistrationNo($patient_reg_no);

        $patientSurvey = PatientSurvey::add(array(
            'reservation_id' => null,
            'survey_id' => 2,
            'patient_id' => $patient['id'],
        ));
        foreach ($answers as $key => $val) {
            foreach ($val as $key2 => $val2) {
                $array = array(
                    'patient_survey_id' => $patientSurvey->id,
                    'group_id' => $key,
                    'question_id' => $key2,
                );
                if(is_array($val2)) {
                    $array['answer_key'] = implode(',', $val2);
                } else {
                    $array['answer_key'] = $val2;
                }
                PatientSurveyDetails::add($array);
            }
        }
        if ($lang == 'en') {
            $msg = 'Your survey has been submitted';
        } else {
            $msg = 'تم إرسال الإستبيان الخاص بك';
        }
        return json_encode(array('status' => '1', 'response' => $msg));
    }
}
