<?php

class GroupQuestion extends Eloquent
{
    protected $table = 'group_question';
    protected $guarded = array('');

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = [])
    {
        $data = self::where(function ($q) use ($inputs) {
            if (isset($inputs['group_id']) && $inputs['group_id']) {
                $q->where('group_id', $inputs['group_id']);
            }
            if (isset($inputs['groups_id']) && $inputs['groups_id']) {
                $q->whereIn('group_id', $inputs['groups_id']);
            }
            if (isset($inputs['survey_id']) && $inputs['survey_id']) {
                $q->where('survey_id', $inputs['survey_id']);
            }
            if (isset($inputs['question_id']) && $inputs['question_id']) {
                $q->where('question_id', $inputs['question_id']);
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
        }

        if (isset($inputs['details']) && $inputs['details']) {
            foreach ($data as &$val) {
                $val['question'] = Question::getById($val['question_id']);
            }
        }

        return $data;
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function removeBySurvey($survey_id)
    {
        return self::where('survey_id', $survey_id)->delete();
    }
}
