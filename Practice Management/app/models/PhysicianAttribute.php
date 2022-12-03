<?php

class PhysicianAttribute extends Eloquent
{
    protected $table = 'physician_attribute';
    protected $guarded = array('');

    public static $rules = array(
        "name" => "required",
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

    public static function getAll($inputs)
    {
        $data = self::where(function ($q) use ($inputs) {
            if ($inputs) {
                if (isset($inputs['type']) && $inputs['type']) {
                    $q->where('type', $inputs['type']);
                }
                if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
                    $q->where('hospital_id', $inputs['hospital_id']);
                }
                if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                    $q->where('clinic_id', $inputs['clinic_id']);
                }
                if (isset($inputs['clinic_ids']) && $inputs['clinic_ids']) {
                    $q->whereIn('clinic_id', $inputs['clinic_ids']);
                }
            }
        })->orderBy('id', 'desc')->get()->toArray();
        foreach ($data as $key => $val) {
            $data[$key]['hospital_name'] = Hospital::getName($val['hospital_id']);
            $data[$key]['clinic_name'] = Clinic::getNameById($val['clinic_id']);
        }
        return $data;
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
