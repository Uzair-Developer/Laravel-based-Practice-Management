<?php

class PatientSurveyDetails extends Eloquent
{
    protected $table = 'patient_survey_details';
    protected $guarded = array('');

    public static $rules = array(
        'patient_survey_id' => "required",
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
            if (isset($inputs['patient_survey_id']) && $inputs['patient_survey_id']) {
                $q->where('patient_survey_id', $inputs['patient_survey_id']);
            }
            if (isset($inputs['patient_survey_ids'])) {
                $q->whereIn('patient_survey_id', $inputs['patient_survey_ids']);
            }
            if (isset($inputs['survey_id']) && $inputs['survey_id']) {
                $patientSurvey = PatientSurvey::getAll(['getIds' => 'id', 'survey_id' => $inputs['survey_id']]);
                $q->whereIn('patient_survey_id', $patientSurvey);
            }
            if (isset($inputs['group_id']) && $inputs['group_id']) {
                $q->where('group_id', $inputs['group_id']);
            }
            if (isset($inputs['question_id']) && $inputs['question_id']) {
                $q->where('question_id', $inputs['question_id']);
            }
            if (isset($inputs['answer_key'])) {
                $q->where('answer_key', $inputs['answer_key']);
            }
            if (isset($inputs['answer_type']) && $inputs['answer_type']) {
                $questions = Question::getAll(['answer_type' => $inputs['answer_type'], 'getIds' => 'id']);
                $q->whereIn('question_id', $questions);
            }
        });
        if (isset($inputs['paginate'])) {
            $data = $data->paginate($inputs['paginate']);
        } elseif (isset($inputs['getFirst']) && $inputs['getFirst']) {
            $data = $data->first();
        } elseif (isset($inputs['getCount']) && $inputs['getCount']) {
            $data = $data->count('id');
        } else {
            $data = $data->get();
        }

        if (isset($inputs['details']) && $inputs['details']) {
            foreach ($data as $key => &$val) {
                $val['group'] = SurveyGroup::getById($val['group_id']);
                $val['question'] = Question::getById($val['question_id']);
            }
        }
        return $data;
    }

    public static function getAllDetailsArray($inputs = '')
    {
        $data = self::where(function ($q) use ($inputs) {
            if (isset($inputs['patient_survey_id']) && $inputs['patient_survey_id']) {
                $q->where('patient_survey_id', $inputs['patient_survey_id']);
            }
            if (isset($inputs['group_id']) && $inputs['group_id']) {
                $q->where('group_id', $inputs['group_id']);
            }
            if (isset($inputs['question_id']) && $inputs['question_id']) {
                $q->where('question_id', $inputs['question_id']);
            }
        });
        $data = $data->get();

        $arr = [];
        if (isset($inputs['details'])) {
            foreach ($data as $k => $v) {
                $arr[$v['group_id']] = self::getByGroupId($v['group_id'], $inputs['patient_survey_id']);
            }
        }
        $det['groups'] = $arr;
        return $det;
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getByGroupId($id, $patient_survey_id = '')
    {
        $data = self::where('group_id', $id)->where('patient_survey_id', $patient_survey_id)->get();
        $group = SurveyGroup::getById($id);
        $arr2 = [];
        $arr = ['title' => $group['title_en']];
        foreach ($data as $k => $val) {
            $arr2[$val['question_id']] = self::getByQuestionId($val['question_id'], $patient_survey_id);
        }
        $arr['questions'] = $arr2;
        return $arr;
    }

    public static function getByQuestionId($id, $patient_survey_id = '')
    {
        $data = self::where('question_id', $id)->where('patient_survey_id', $patient_survey_id)->first();
        $question = Question::getById($id);
        $answer_type = AnswerType::getById($question['answer_type']);
        $answers = explode(",", $answer_type['answers_en']);
        $arr = ['title' => $question['title_en'], 'answer' => $answers[$data['answer_key']]];
        return $arr;
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
