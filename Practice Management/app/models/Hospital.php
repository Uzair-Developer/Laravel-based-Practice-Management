<?php

class Hospital extends Eloquent  {
	protected $table = 'hospitals';
    protected $guarded = array('');

    public function clinics()
    {
        return $this->hasMany('Clinic', 'hospital_id', 'id');
    }

    public static function getAll($inputs = [])
    {
        return self::all()->toArray();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }
}
