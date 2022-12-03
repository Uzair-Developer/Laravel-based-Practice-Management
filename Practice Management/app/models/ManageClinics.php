<?php

class ManageClinics extends Eloquent  {
	protected $table = 'manage_clinics';
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

}
