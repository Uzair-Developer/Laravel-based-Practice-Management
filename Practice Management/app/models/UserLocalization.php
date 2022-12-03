<?php

class UserLocalization extends Eloquent  {
    protected $table = 'user_localizations';
    protected $guarded = array('');

    public static function add($inputs)
    {
        return self::create($inputs);
    }

}
