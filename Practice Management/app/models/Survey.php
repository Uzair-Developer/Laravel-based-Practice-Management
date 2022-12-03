<?php

class Survey extends Eloquent
{
    protected $table = 'survey';
    protected $guarded = array('');

    public static $rules = array(
        'header_en' => "required",
        'header_ar' => "required"
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs=[])
    {
        $data = self::where(function ($q) use ($inputs) {
            if (isset($inputs['header_en']) && $inputs['header_en']) {
                $q->where('header_en', 'LIKE', "'%{$inputs['header_en']}'");
            }
            if (isset($inputs['header_ar']) && $inputs['header_ar']) {
                $q->where('header_ar', 'LIKE', "'%{$inputs['header_ar']}'");
            }
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

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
