<?php

class Monitor extends Eloquent
{

    protected $table = 'monitor';
    protected $guarded = array('');

    public static $rules = array(
        "user_id" => "required",
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll()
    {
        $data = self::all()->toArray();
        foreach ($data as $key => $val) {
            $data[$key]['full_name'] = User::getName($val['user_id']);
            $data[$key]['not_ready_reason_name'] = '';
            if($val['not_ready_reason_id']){
                $data[$key]['not_ready_reason_name'] = AttributePms::getName($val['not_ready_reason_id']);
            }
        }
        return $data;
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
