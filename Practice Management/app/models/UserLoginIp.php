<?php

class UserLoginIp extends Eloquent
{
    public static $rules = array(
        "user_id" => "required",
    );
    protected $table = 'user_login_ip';
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

    public static function getAll()
    {
        return self::paginate(20);
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function check($user_id = '', $date = '', $ip = '', $user_type_id, $getFirst = true)
    {
        $data = self::whereRaw(' 1 = 1 ');
        if ($ip) {
            $data = $data->where('ip', $ip);
        }
        if ($user_id) {
            $data = $data->where('user_id', $user_id);
        }
        if ($date) {
            $data = $data->where('date', $date);
        }
        if ($user_type_id) {
            $data = $data->where('user_type_id', $user_type_id);
        }
        $data = $data->orderBy('id', 'desc');
        if ($getFirst) {
            return $data->first();
        } else {
            return $data->get();
        }
    }
}
