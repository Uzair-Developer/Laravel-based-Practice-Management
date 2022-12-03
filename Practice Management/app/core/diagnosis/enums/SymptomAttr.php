<?php
namespace core\diagnosis\enums;


abstract class SymptomAttr {
    public static $type = array(
        '1' => 'primary',
        '2' => 'associated'
    );

    public static $typeReturn = array(
        'primary' => 1,
        'associated' => 2
    );

    public static $SS = array(
        '1' => 'symptom',
        '2' => 'sign'
    );

    public static $SSReturn = array(
        'symptom' => 1,
        'sign' => 2
    );
}