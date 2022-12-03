<?php
namespace core\diagnosis\enums;


abstract class DiseaseAttr
{
    public static $type = array(
        '1' => 'mostCommon',
        '2' => 'lesCommon',
        '3' => 'rare',
    );

    public static $typeReturn = array(
        'mostCommon' => 1,
        'lesCommon' => 2,
        'rare' => 3,
    );

}