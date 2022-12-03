<?php

class HisLookUp extends Eloquent
{
    protected $table = 'dbo.LOOKUP_MST';
    protected $guarded = array('');
    protected $connection = 'sqlsrv';
    public $timestamps = false;

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($type)
    {
        return self::where('LOOKUP_TYPE', $type)->get()->toArray();
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }
}
