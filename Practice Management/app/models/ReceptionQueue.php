<?php

class ReceptionQueue extends Eloquent
{
    protected $table = 'reception_queue';
    protected $guarded = array('');

    public static function add($inputs)
    {
        $inputs['create_timestamp'] = time();
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = '')
    {
        $data = self::whereRaw(' 1 = 1 ');
        if (isset($inputs['ip_to_reception_id']) && $inputs['ip_to_reception_id']) {
            $data = $data->where('ip_to_reception_id', $inputs['ip_to_reception_id']);
        }
        if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
            $data = $data->where('hospital_id', $inputs['hospital_id']);
        }
        if (isset($inputs['date']) && $inputs['date']) {
            $data = $data->where('date', $inputs['date']);
        }
        if (isset($inputs['orderByDesc']) && $inputs['orderByDesc']) {
            $data = $data->orderBy($inputs['orderByDesc'], 'desc');
        }
        if (isset($inputs['getFirst']) && $inputs['getFirst']) {
            $data = $data->first();
        } else {
            $data = $data->get();
        }
        if (isset($inputs['details']) && $inputs['details']) {
            foreach ($data as $key => $val) {
                $data[$key]['hospital_name'] = Hospital::getName($val['hospital_id']);
            }
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

    public static function getNameById($id)
    {
        return self::where('id', $id)->pluck('room_name');
    }
}
