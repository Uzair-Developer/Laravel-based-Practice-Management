<?php


class Symptom extends Eloquent
{
    protected $table = 'diagnosis_symptoms';
    protected $guarded = array('');

    public function diseases()
    {
        return $this->belongsToMany('Disease');
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function getLastRow()
    {
        return self::all()->last()->toArray();
    }

    public static function getIdsByName($name)
    {
        return self::where('name', 'LIKE', "%" . $name . "%")->lists('id');
    }

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

    public static function getIdByIdRef($id)
    {
        return self::where('id_ref', $id)->pluck('id');
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
