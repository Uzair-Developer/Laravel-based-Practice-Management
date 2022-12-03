<?php

class Lookup extends Eloquent
{

    protected $table = 'lookup';
    protected $guarded = array('');


    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function editByHis($inputs,$type, $id)
    {
        return self::where('his_id', $id)->where('type', $type)->update($inputs);
    }

    public static function getAll($type)
    {
        return self::where('type', $type)->toArray();
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

    public static function checkExist($type, $hisId)
    {
        return self::where('type', $type)->where('his_id', $hisId)->first();
    }
}
