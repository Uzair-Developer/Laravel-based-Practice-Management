<?php

class Complain extends Eloquent
{
    protected $table = 'complains';
    protected $guarded = array('');

    public static $rules = array(
        "department_id" => "required",
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs)
    {
        $data = self::where(function ($q) use ($inputs) {
            if (isset($inputs['department_id']) && $inputs['department_id']) {
                $q->where('department_id', $inputs['department_id']);
            }
            if (isset($inputs['read']) && $inputs['read']) {
                $q->where('read', $inputs['read']);
            }
        })->orderBy('id', 'desc');

        $data = $data->get()->toArray();

        foreach ($data as $key => $val) {
            $data[$key]['create_by'] = User::getName($val['created_by']);
            $data[$key]['department_name'] = AttributePms::getById($val['department_id'])['name'];
            $data[$key]['patient_name'] = Patient::getName($val['patient_id']);
        }
        return $data;
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

    public static function checkByPhysicianDateTime($user_id, $date, $time_from, $time_to)
    {
        return self::where('user_id', $user_id)
            ->where('date', $date)
            ->where('from_time', $time_from)
            ->where('to_time', $time_to)
            ->first();
    }

}
