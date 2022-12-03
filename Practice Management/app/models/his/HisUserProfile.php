<?php

class HisUserProfile extends Eloquent
{
    protected $table = 'dbo.UserProfile';
    protected $guarded = array('');
    protected $connection = 'sqlsrv2';
    public $timestamps = false;

    public static function getInitialPasswordByPatient($reg_no)
    {
        return self::where('PatientRegistrationNo', $reg_no)->pluck('InitialPassword');
    }
}
