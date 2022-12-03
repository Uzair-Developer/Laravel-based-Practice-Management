<?php

class PatientMarketingSMS extends Eloquent
{
    protected $table = 'patient_marketing_sms';
    protected $guarded = array('');

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = '')
    {
        $data = self::where(function ($q) use ($inputs) {
            if (isset($inputs['type']) && $inputs['type']) {
                $q->where('type', $inputs['type']);
            }
        });
        if (isset($inputs['paginate'])) {
            $data = $data->paginate($inputs['paginate']);
        } else {
            $data = $data->get();
        }

        return $data;
    }

    public static function getById($patient_id)
    {
        return self::where('patient_id', $patient_id)->first();
    }

    public static function getByPhone($phone)
    {
        return self::where('phone', $phone)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
