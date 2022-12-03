<?php

class PublicHoliday extends Eloquent  {
	protected $table = 'public_holidays';
    protected $guarded = array('');

    public static function add($inputs)
    {
        $inputs['create_timestamp']= time();
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

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function checkExist($hospital_id, $date)
    {
        return self::where('hospital_id', $hospital_id)->where('status', 1)
            ->where('from_date', '<=', $date)->where('to_date', '>=', $date)->first();
    }
}
