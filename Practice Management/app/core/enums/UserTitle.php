<?php
namespace core\enums;


abstract class UserTitle
{
    public static $title = array(
        '1' => 'Mr',
        '2' => 'Mrs',
        '3' => 'Prof',
        '4' => 'Maj',
        '5' => 'Sis',
        '6' => 'Lt',
        '7' => 'Fr',
        '8' => 'Ms',
        '9' => 'Flt Lt',
        '10' => 'Jus',
        '11' => 'Dr',
        '12' => 'B/B Of',
        '13' => 'B/G Of',
        '14' => 'RYAN',
    );

    public static $titleReturn = array(
        'Mr' => 1,
        'Mrs' => 2,
        'Prof' => 2,
        'Maj' => 2,
        'Sis' => 2,
        'Lt' => 2,
        'Fr' => 2,
        'Ms' => 2,
        'Flt Lt' => 2,
        'Jus' => 2,
        'Dr' => 2,
        'B/B Of' => 2,
        'B/G Of' => 2,
        'RYAN' => 2,

    );
}