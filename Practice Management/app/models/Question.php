<?php

class Question extends Eloquent
{
    protected $table = 'question';
    protected $guarded = array('');

    public static $rules = array(
        'title_ar' => "required",
        'title_en' => "required",
        'answer_type' => "required"
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = '')
    {
        $data = self::where(function ($q) use ($inputs) {
            if (isset($inputs['answer_type']) && $inputs['answer_type']) {
                $q->where('answer_type', $inputs['answer_type']);
            }
            if (isset($inputs['ids']) && $inputs['ids']) {
                $q->whereIn('id', $inputs['ids']);
            }
        });
        if (isset($inputs['getFirst'])) {
            $data = $data->first();
        } elseif (isset($inputs['getIds'])) {
            $data = $data->lists($inputs['getIds']);
        } else {
            if (isset($inputs['paginate'])) {
                $data = $data->paginate($inputs['paginate']);
            } else {
                $data = $data->get();
            }
            foreach ($data as $k => $v) {
                $answer_type = AnswerType::getById($v['answer_type']);
                $data[$k]["answer_type_name"] = $answer_type['title_en'];
            }
        }

        return $data;
    }

    public static function getById($id)
    {
        $data = self::where('id', $id)->first();
        $data['answerType'] = AnswerType::getById($data['answer_type']);
        return $data;
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getBySurvey($survey_id)
    {
        $questionIds = GroupQuestion::getAll(['survey_id' => $survey_id, 'getIds' => 'question_id']);
        return Question::getAll(['ids' => $questionIds])->toArray();
    }

    public static function getReportCounts($survey_id, $questions, $inputs = '')
    {
        $array = array();
        if ($survey_id == 1 && ((isset($inputs['hospital_id']) && $inputs['hospital_id']) || (isset($inputs['clinic_id']) && $inputs['clinic_id'])
                || (isset($inputs['physician_id']) && $inputs['physician_id']) || (isset($inputs['res_date_from']) && $inputs['res_date_from'])
                || (isset($inputs['res_date_to']) && $inputs['res_date_to'])
            )
        ) {
            $inputs['reservation_ids'] = true;
            $inputs['getIds'] = 'id';
            $patientSurveyIds = PatientSurvey::getAll($inputs);
            $array['patient_survey_ids'] = $patientSurveyIds;
        }
        foreach ($questions as $key => &$val) {
            $answer_type_data = AnswerType::getById($val['answer_type']);
            $val['answer_type_data'] = $answer_type_data;

            $array['survey_id'] = $survey_id;
            $array['question_id'] = $val['id'];
            $array['getCount'] = true;

            $answers = explode(',', $answer_type_data['answers_en']);
            foreach ($answers as $k => $v) {
                $array['answer_key'] = $k;
                $val['patientCount'][$k] = PatientSurveyDetails::getAll($array);
            }

        }
        return $questions;
    }
}
