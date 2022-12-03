<?php

use core\enums\AttributeType;

class AttributePms extends Eloquent
{
    protected $table = 'attribute_pms';
    protected $guarded = array('');

    public static $rules = array(
        'name' => "required",
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($type = '')
    {
        if (!$type) {
            $data = self::paginate(50);
        } else {
            $data = self::where('type_id', $type)->paginate(50);
        }
        foreach ($data as $key => $val) {
            $data[$key]['type_name'] = AttributeType::$pms[$val['type_id']];
            $data[$key]['parent_name'] = '';
            if ($val['parent_id']) {
                $data[$key]['parent_name'] = self::getName($val['parent_id']);
            }
        }
        return $data;
    }

    public static function getAllWithOutPaginate($type = '', $inputs = '')
    {
        $data = self::whereRaw('1=1');
        if ($type) {
            $data = $data->where('type_id', $type);
        }
        if (isset($inputs['effect']) && $inputs['effect']) {
            $data = $data->where('effect', $inputs['effect']);
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

    public static function getPatentReferredTo()
    {
        return self::where('type_id', AttributeType::$pmsReturn['referredTo'])
            ->where('parent_id', 0)->get()->toArray();
    }

    public static function getChildReferredTo()
    {
        return self::where('type_id', AttributeType::$pmsReturn['referredTo'])
            ->where('parent_id', '!=', 0)->get()->toArray();
    }

    public static function getByPatentReferredTo($parent_id)
    {
        return self::where('type_id', AttributeType::$pmsReturn['referredTo'])
            ->where('parent_id', $parent_id)->get()->toArray();
    }
}
