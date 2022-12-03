<?php

class IpToScreen extends Eloquent
{
    protected $table = 'ip_to_screen';
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
        if (isset($inputs['ip']) && $inputs['ip']) {
            $data = $data->where('ip', 'LIKE', '%' . $inputs['ip'] . '%');
        }
        if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
            $data = $data->where('hospital_id', $inputs['hospital_id']);
        }
        if (isset($inputs['get_by_hospital_id'])) {
            $data = $data->where('hospital_id', $inputs['get_by_hospital_id']);
        }
        if (isset($inputs['screen_name']) && $inputs['screen_name']) {
            $data = $data->whereRaw('LOWER(screen_name) LIKE LOWER("' . $inputs['screen_name'] . '%")');
        }
        if (isset($inputs['wait_area_name']) && $inputs['wait_area_name']) {
            $data = $data->whereRaw('LOWER(wait_area_name) LIKE LOWER("' . $inputs['wait_area_name'] . '%")');
        }
        $data = $data->get();
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

    public static function getByIp($ip)
    {
        return self::where('ip', $ip)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
