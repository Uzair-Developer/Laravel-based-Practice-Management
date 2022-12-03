<?php

class ReservationHistory extends Eloquent
{
    protected $table = 'reservation_history';
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

    public static function getAll($inputs = [])
    {
        $data = self::whereRaw('1 = 1');
        if (isset($inputs['action_by']) && $inputs['action_by']) {
            $data = $data->where('action_by', $inputs['action_by']);
        }
        if (isset($inputs['reservation_id'])) {
            $data = $data->where('reservation_id', $inputs['reservation_id']);
        }
        return $data->get();
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
