<?php
namespace core\enums;


abstract class PatientGender
{
    public static $gender = array(
        '1' => 'Female',
        '2' => 'Male',
    );

    public static $genderReturn = array(
        'Female' => 1,
        'Male' => 2
    );
}