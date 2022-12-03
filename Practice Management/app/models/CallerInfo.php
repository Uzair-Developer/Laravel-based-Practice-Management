<?php

class CallerInfo extends Eloquent
{
    protected $table = 'caller_info';
    protected $guarded = array('');

    public static $rules = array(
        "name" => "required",
        "phone" => "required",
    );

    public static function add($inputs)
    {
        $inputs['create_timestamp'] = time();
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll()
    {
        return self::paginate(20);
    }

    public static function getAllWithFilter($inputs)
    {
        return self::where(function ($query) use ($inputs) {
            if (isset($inputs['name']) && $inputs['name'])
                $query->where('name', 'LIKE', "%" . $inputs['name'] . "%");
            if (isset($inputs['phone']) && $inputs['phone'])
                $query->where('phone', 'LIKE', "%" . $inputs['phone'] . "%");
        })->paginate(20);
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

    public static function getPhone($id)
    {
        return self::where('id', $id)->pluck('phone');
    }

    public static function getByPhone($phone, $hospital_id = '')
    {
        $data = self::where('phone', $phone);
        if($hospital_id){
            $data = $data->where('hospital_id', $hospital_id);
        }
        return $data->orderBy('id', 'desc')->first();
    }

}
