<?php

class DefaultPrinter extends Eloquent
{
    protected $table = 'default_printer';
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
        if (isset($inputs['user_id']) && $inputs['user_id']) {
            $data = $data->where('user_id', $inputs['user_id']);
        }
        if (isset($inputs['printer_id']) && $inputs['printer_id']) {
            $data = $data->where('printer_id', $inputs['printer_id']);
        }
        if (isset($inputs['getFirst']) && $inputs['getFirst']) {
            $data = $data->first();
        } else {
            $data = $data->get();
        }
        if (isset($inputs['details']) && $inputs['details']) {
            if (isset($inputs['getFirst']) && $inputs['getFirst']) {
                $data['printer'] = IpToPrinter::getById($data['printer_id']);
            }
//            foreach ($data as $key => $val) {
//                $data[$key]['hospital_name'] = Hospital::getName($val['hospital_id']);
//            }
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
}
