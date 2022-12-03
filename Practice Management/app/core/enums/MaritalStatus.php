<?php
namespace core\enums;


abstract class MaritalStatus
{
    public static $status = array(
        '1' => 'Divorced',
        '2' => 'Married',
        '3' => 'Separated',
        '4' => 'Single',
        '5' => 'Widowed',
    );

    public static $statusReturn = array(
        'Divorced' => 1,
        'Married' => 2,
        'Separated' => 3,
        'Single' => 4,
        'Widowed' => 5,
    );
}