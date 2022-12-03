<?php

class UserGroup extends Eloquent
{

    protected $table = 'users_groups';
    protected $guarded = array('');


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
        return self::all()->toArray();
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function removeByUserId($user_id)
    {
        return self::where('user_id', $user_id)->delete();
    }

    public static function removeByGroupId($group_id)
    {
        return self::where('group_id', $group_id)->delete();
    }

    public static function getGroupByUserId($user_id)
    {
        return self::where('user_id', $user_id)->lists('group_id');
    }

    public static function getUsersByGroupId($group_id)
    {
        return self::where('group_id', $group_id)->lists('user_id');
    }
}
