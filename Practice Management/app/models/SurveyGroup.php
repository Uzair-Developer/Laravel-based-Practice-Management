<?php

class SurveyGroup extends Eloquent
{
    protected $table = 'survey_group';
    protected $guarded = array('');

    public static $rules = [
      'title_en' => "required",
        'title_ar' => "required"
    ];

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
            
        });
        if(isset($inputs['getFirst'])) {
            $data = $data->first();
        }else {
            if (isset($inputs['paginate'])) {
                $data = $data->paginate($inputs['paginate']);
            } else {
                $data = $data->get();
            }
        }

        return $data;
    }

    public static function getById($id, $inputs = '')
    {
        $data = self::where('id', $id)->first();
        if (isset($inputs['details']) && $inputs['details']) {
            $inputs['details'] = true;
            $inputs['group_id'] = $data['id'];
            $data['questions'] = GroupQuestion::getAll($inputs);
        }
        return $data;
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
