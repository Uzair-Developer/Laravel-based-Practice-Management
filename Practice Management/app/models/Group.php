<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;

class Group extends Eloquent
{

    protected $table = 'groups';
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
        $user = Sentry::getUser();
        $data = self::whereRaw('1=1');
        if ($user->user_type_id == 1) {
            $data = $data->where('system', 0);
        }
        if (isset($inputs['in_filter']) && $inputs['in_filter']) {
            $data = $data->where('in_filter', $inputs['in_filter']);
        }
        return $data->get()->toArray();
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
}
