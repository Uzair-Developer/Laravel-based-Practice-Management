<?php
namespace core\enums;


abstract class AgeType
{
    public static $age = array(
        '1' => 'Years',
        '2' => 'Months',
        '3' => 'Days',
        '4' => 'Hours',
    );

    public static $ageReturn = array(
        'Years' => 1,
        'Months' => 2,
        'Days' => 3,
        'Hours' => 4,
    );
}