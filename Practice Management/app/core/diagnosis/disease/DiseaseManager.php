<?php
namespace core\diagnosis\disease;


use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\BaseManager;
use core\diagnosis\enums\DiseaseSymptomsStatus;
use core\enums\ResponseTypes;
use DiseaseQuestions;
use DiseaseSymptoms;

class DiseaseManager extends BaseManager
{
    function __construct()
    {
        $this->DiseaseValidator = new DiseaseValidator();
    }

    public function addDisease($inputs)
    {
        $validator = $this->DiseaseValidator->diseaseValidate($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $diseaseRepo = new DiseaseRepository();

            $symptom_id = array();
            $symptomRate = array();
            $questionText = array();
            $questionScore = array();
            $questionRate = array();
            if (isset($inputs['symptom_id'])) {
                $symptom_id = $inputs['symptom_id'];
                $symptomRate = $inputs['symptomRate'];
                unset($inputs['symptom_id']);
                unset($inputs['symptomRate']);
            }
            if (isset($inputs['questionText'])) {
                $questionText = $inputs['questionText'];
                $questionScore = $inputs['questionScore'];
                unset($inputs['questionText']);
                unset($inputs['questionScore']);
                $count = 0;
                foreach ($questionScore as $key => $val) {
                    $count += $val;
                }
                $score = 100 / $count;
                foreach ($questionScore as $key => $val) {
                    $questionRate[] = $score * $val;
                }
            }
            $inputs['create_timestamp'] = time();
            $disease = $diseaseRepo->save($inputs);

            if (!empty($symptom_id[0])) {
                foreach ($symptom_id as $key => $val) {
                    $array = array(
                        'disease_id' => $disease->id,
                        'symptom_id' => $val,
                        'rate' => $symptomRate[$key],
                        'create_timestamp' => time(),
                    );
                    if (!Sentry::getUser()->hasAccess('admin')) {
                        $array['status'] = DiseaseSymptomsStatus::pending;
                        $array['enter_direct'] = 0;
                        $array['user_id'] = Sentry::getUser()->id;
                    }
                    DiseaseSymptoms::add($array);
                }
            }
            if (!empty($questionText[0])) {
                foreach ($questionText as $key => $val) {
                    $array = array(
                        'disease_id' => $disease->id,
                        'text' => $val,
                        'score' => $questionScore[$key],
                        'rate' => $questionRate[$key],
                        'create_timestamp' => time()
                    );
                    DiseaseQuestions::add($array);
                }
            }
            return $this->response()->ResponseObject(ResponseTypes::success, 'Added Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Error Occurred");
        }
    }


    public function updateDisease($inputs, $id)
    {
        $validator = $this->DiseaseValidator->diseaseValidate($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $diseaseRepo = new DiseaseRepository();

            $symptom_id = array();
            $symptomRate = array();
            $questionText = array();
            $questionScore = array();
            $questionRate = array();
            $diseaseSymptomIds = array();
            $diseaseQuestionIds = array();
            if (isset($inputs['symptom_id'])) {
                $symptom_id = $inputs['symptom_id'];
                $symptomRate = $inputs['symptomRate'];
                unset($inputs['symptom_id']);
                unset($inputs['symptomRate']);
            }
            if (isset($inputs['questionText'])) {
                $questionText = $inputs['questionText'];
                $questionScore = $inputs['questionScore'];
                unset($inputs['questionText']);
                unset($inputs['questionScore']);
                $count = 0;
                foreach ($questionScore as $key => $val) {
                    $count += $val;
                }
                $score = 100 / $count;
                foreach ($questionScore as $key => $val) {
                    $questionRate[] = $score * $val;
                }
            }
            if (isset($inputs['diseaseSymptomIds'])) {
                $diseaseSymptomIds = $inputs['diseaseSymptomIds'];
                unset($inputs['diseaseSymptomIds']);
            }
            if (isset($inputs['diseaseQuestionIds'])) {
                $diseaseQuestionIds = $inputs['diseaseQuestionIds'];
                unset($inputs['diseaseQuestionIds']);
            }
            $diseaseRepo->update($inputs, $id);
            if (count($diseaseSymptomIds)) {
                foreach ($diseaseSymptomIds as $key => $val) {
                    if (DiseaseSymptoms::getById($val)) {
                        $array = array(
                            'disease_id' => $id,
                            'symptom_id' => $symptom_id[$key],
                            'rate' => $symptomRate[$key]
                        );
                        DiseaseSymptoms::edit($array, $val);
                        unset($symptom_id[$key]);
                        unset($symptomRate[$key]);
                    }
                }
            }
            if (count($diseaseQuestionIds)) {
                foreach ($diseaseQuestionIds as $key => $val) {
                    if (DiseaseQuestions::getById($val)) {
                        $array = array(
                            'disease_id' => $id,
                            'text' => $questionText[$key],
                            'score' => $questionScore[$key],
                            'rate' => $questionRate[$key]
                        );
                        DiseaseQuestions::edit($array, $val);
                        unset($questionText[$key]);
                        unset($questionScore[$key]);
                        unset($questionRate[$key]);
                    }
                }
            }
            if (count($symptom_id)) {
                foreach ($symptom_id as $key => $val) {
                    $array = array(
                        'disease_id' => $id,
                        'symptom_id' => $val,
                        'rate' => $symptomRate[$key],
                        'create_timestamp' => time()
                    );
                    DiseaseSymptoms::add($array);
                }
            }
            if (count($questionText)) {
                foreach ($questionText as $key => $val) {
                    $array = array(
                        'disease_id' => $id,
                        'text' => $val,
                        'score' => $questionScore[$key],
                        'rate' => $questionRate[$key],
                        'create_timestamp' => time()
                    );
                    DiseaseQuestions::add($array);
                }
            }

            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return $this->response()->ResponseObject(ResponseTypes::error, "Error Occurred");
        }

    }

    public function delete($id)
    {
        $ORepo = new DiseaseRepository();
        $ORepo->delete($id);
        return $this->response()->ResponseObject(ResponseTypes::success, 'Deleted Successfully');
    }


}