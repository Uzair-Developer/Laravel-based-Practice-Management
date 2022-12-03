<?php

class Country extends Eloquent  {
	protected $table = 'diagnosis_country';
    protected $guarded = array('');

    public static  $rules = array(
        "name" => "required",
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id',$id)->update($inputs);
    }

    public static function getAll()
    {
        $data = self::orderBy('order', 'desc')->paginate(20);
        foreach ($data as $key => $val) {
            if($val['parent_id'] != 0){
                $data[$key]['parent_name'] = self::getNameById($val['parent_id']);
            } else {
                $data[$key]['parent_name'] = '';
            }
        }
        return $data;
    }

    public static function getAllPaginateWithFilter($q)
    {
        $data = self::where(function ($query) use ($q) {
            $query->where('name', 'LIKE', "%" . $q . "%");
        })->orderBy('order', 'desc')->paginate(20);
        foreach ($data as $key => $val) {
            if($val['parent_id'] != 0){
                $data[$key]['parent_name'] = self::getNameById($val['parent_id']);
            } else {
                $data[$key]['parent_name'] = '';
            }
        }
        return $data;
    }

    public static function getAutoComplete($q)
    {
        return self::where(function ($query) use ($q) {
            $query->where('name', 'LIKE', "%" . $q . "%");
        })->lists('name', 'id');
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getNameById($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getParents()
    {
        return self::where('parent_id', 0)->orderBy('order', 'desc')->get()->toArray();
    }

    public static function getChild()
    {
        return self::where('parent_id', '!=', 0)->get()->toArray();
    }

    public static function getChildOfCountry($id)
    {
        return self::where('parent_id', $id)->get()->toArray();
    }

    public static function getIdsByName($name)
    {
        return self::where('name', 'LIKE', "%" . $name . "%")->lists('id');
    }

}
