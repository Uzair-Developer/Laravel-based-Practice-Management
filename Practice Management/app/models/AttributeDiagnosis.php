<?php

class AttributeDiagnosis extends Eloquent  {
	protected $table = 'attribute_diagnosis';
    protected $guarded = array('');

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($type = '')
    {
        if($type){
            return self::all()->toArray();
        } else {
            return self::where('type_id', $type)->get()->toArray();
        }
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }
}
