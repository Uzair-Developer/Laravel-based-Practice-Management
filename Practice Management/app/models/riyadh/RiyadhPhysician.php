<?php

class RiyadhPhysician extends Eloquent
{
    protected $table = 'dbo.BAS_OPDDoctors_TB';
    protected $guarded = array('');
    protected $connection = 'sqlsrv3';
    public $timestamps = false;

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
        return self::where('MERGE_FLAG', 1)->get()->toArray();
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

    public static function getAllExceptIds($ids)
    {
        return self::whereNotIn('HIS_Id', $ids)->get()->toArray();
    }

    public static function getByIds($ids)
    {
        return self::whereIn('HIS_Id', $ids)->get()->toArray();
    }
}
