<?php

class ReceptionDelegate extends Eloquent
{
    protected $table = 'reception_delegate';
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
        $data = self::whereRaw('1 = 1');
        if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
            $data = $data->where('hospital_id', $inputs['hospital_id']);
        }
        if (isset($inputs['reception_id']) && $inputs['reception_id']) {
            $data = $data->where('reception_id', $inputs['reception_id']);
        }
        if (isset($inputs['get_by_hospital_id'])) {
            $data = $data->where('hospital_id', $inputs['get_by_hospital_id']);
        }
        if (isset($inputs['getFirst']) && $inputs['getFirst']) {
            $data = $data->first();
        } else {
            $data = $data->get();
        }
        if (isset($inputs['details']) && $inputs['details']) {
            foreach ($data as $key => $val) {
                $data[$key]['hospital_name'] = Hospital::getName($val['hospital_id']);

                $reception = IpToScreen::getById($val['reception_id']);
                $data[$key]['reception_name'] = $reception['ip'] . ' [' . $reception['screen_name'] . ']';

                $reception_del1 = IpToScreen::getById($val['reception1_delegate_id']);
                $data[$key]['reception_del1'] = $reception_del1['ip'] . ' [' . $reception_del1['screen_name'] . ']';

                $reception_del2 = IpToScreen::getById($val['reception2_delegate_id']);
                $data[$key]['reception_del2'] = $reception_del2['ip'] . ' [' . $reception_del2['screen_name'] . ']';

                $reception_del3 = IpToScreen::getById($val['reception3_delegate_id']);
                $data[$key]['reception_del3'] = $reception_del3['ip'] . ' [' . $reception_del3['screen_name'] . ']';
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
