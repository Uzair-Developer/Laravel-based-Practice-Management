<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;

class AgentComments extends Eloquent
{
    protected $table = 'diagnosis_agent_comments';
    protected $guarded = array('');

    public static $rules = array(
        "notes" => "required",
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll()
    {
        $user = Sentry::getUser();
        if ($user->user_type_id == 1) {
            $data = self::orderBy('created_at', 'desc')->paginate(20);
        } else {
            $data = self::where(function ($q) use ($user) {
                $q->where('create_by', $user->id);
                $q->orWhere('to_all', 1);
                $q->orWhere('user_id', $user->id);
                $q->orWhere('user_id', 'LIKE', $user->id . ',%');
                $q->orWhere('user_id', 'LIKE', '%,' . $user->id . ',%');
                $q->orWhere('user_id', 'LIKE', '%,' . $user->id);
                $user_groups = UserGroup::getGroupByUserId($user->id);
                if ($user_groups) {
                    foreach ($user_groups as $key => $val) {
                        $q->orWhere('group_id', $val);
                        $q->orWhere('group_id', 'LIKE', $val . ',%');
                        $q->orWhere('group_id', 'LIKE', '%,' . $val . ',%');
                        $q->orWhere('group_id', 'LIKE', '%,' . $val);
                    }
                }
            })->orderBy('created_at', 'desc')->paginate(20);
        }
        foreach ($data as $key => $val) {
            $data[$key]['create_name'] = User::getName($val['create_by']);
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

    public static function getUnSeen()
    {
        $user = Sentry::getUser();
        if ($user->user_type_id == 1) {
            $data = self::where('seen', 0)->orderBy('created_at', 'desc')->get()->toArray();
        } else {
            $data = self::where('seen', 0)->where(function ($q) use ($user) {
                $q->where('create_by', $user->id);
                $q->orWhere('to_all', 1);
                $q->orWhere('user_id', $user->id);
                $q->orWhere('user_id', 'LIKE', $user->id . ',%');
                $q->orWhere('user_id', 'LIKE', '%,' . $user->id . ',%');
                $q->orWhere('user_id', 'LIKE', '%,' . $user->id);
                $user_groups = UserGroup::getGroupByUserId($user->id);
                if ($user_groups) {
                    foreach ($user_groups as $key => $val) {
                        $q->orWhere('group_id', $val);
                        $q->orWhere('group_id', 'LIKE', $val . ',%');
                        $q->orWhere('group_id', 'LIKE', '%,' . $val . ',%');
                        $q->orWhere('group_id', 'LIKE', '%,' . $val);
                    }
                }
            })->orderBy('created_at', 'desc')->get()->toArray();
        }
        return $data;
    }
}
