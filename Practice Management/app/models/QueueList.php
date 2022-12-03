<?php

class QueueList extends Eloquent
{
    protected $table = 'queue';
    protected $guarded = array('');

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
        return self::all()->toArray();
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function checkHospitalExist($hospital_id)
    {
        return self::where('hospital_id', $hospital_id)->first();
    }

    public static function getByHospital($hospital_id)
    {
        return self::where('hospital_id', $hospital_id)->first();
    }
}
