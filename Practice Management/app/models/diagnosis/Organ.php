<?php


class Organ extends Eloquent
{
    protected $table = 'diagnosis_organs';
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

    public static function getByRefId($ref_id)
    {
        return self::where('ref_id', $ref_id)->first();
    }

    public static function getIdByRefId($id)
    {
        return self::where('ref_id', $id)->pluck('id');
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
