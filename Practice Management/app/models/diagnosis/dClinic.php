<?php

class dClinic extends Eloquent  {
	protected $table = 'diagnosis_clinics';
    protected $guarded = array('');

    public static function getAll()
    {
        return self::all()->toArray();
    }

    public static function getNameById($id)
    {
        return self::where('id', $id)->pluck('name');
    }

}
