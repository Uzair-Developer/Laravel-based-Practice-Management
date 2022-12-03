<?php

class AnswerType extends Eloquent
{
    protected $table = 'answer_type';
    protected $guarded = array('');

    public static $rules = array(
        'title_ar' => "required",
        'title_en' => "required",
        'type' => "required",
        'answers_ar' => "required",
        'answers_en' => "required"
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
            if (isset($inputs['type']) && $inputs['type']) {
                $q->where('type', $inputs['type']);
            }
        });
        if(isset($inputs['paginate'])) {
            $data = $data->paginate($inputs['paginate']);
        }else{
            $data = $data->get();
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
}
