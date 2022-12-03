<?php

use core\diagnosis\enums\CommentsType;

class Comment extends Eloquent
{
    protected $table = 'diagnosis_comments';
    protected $guarded = array('');

    public static $rules = array(
        "comment" => "required",
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($type)
    {
        if($type == 'symptom') {
            $data = self::where('type', CommentsType::symptom)->paginate(20);
        } else {
            $data = self::paginate(20);
        }
        foreach ($data as $key => $val) {
            $data[$key]['user_name'] = User::getName($val['user_id']);
            $data[$key]['symptom_name'] = Symptom::getName($val['ref_id']);
        }
        return $data;
    }

    public static function getAllWithFilters($q, $type)
    {
        $data = self::where(function ($query) use ($q, $type) {
            if ($q['user']) {
                $query->where('user_id', $q['user']);
            }
            if ($q['symptom']) {
                $symptomsId = Symptom::getIdsByName($q['symptom']);
                $query->whereIn('ref_id', $symptomsId);
            }
            if($type == 'symptom'){
                $query->where('type', CommentsType::symptom);
            }
        })->paginate(20);
        foreach ($data as $key => $val) {
            $data[$key]['user_name'] = User::getName($val['user_id']);
            $data[$key]['symptom_name'] = Symptom::getName($val['ref_id']);
        }
        return $data;
    }

    public static function getAllByUser($type, $user_id)
    {
        $data = self::where('user_id', $user_id)->where('type', $type)->paginate(10);
        foreach ($data as $key => $val) {
            $data[$key]['user_name'] = User::getName($val['user_id']);
            $data[$key]['symptom_name'] = Symptom::getName($val['ref_id']);
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

    public static function getParents()
    {
        return self::where('parent_id', 0)->get()->toArray();
    }

}
