<?php
namespace core\enums;


abstract class AttributeType
{
    public static $pms = array(
        '1' => 'exceptionReason',
        '2' => 'userExperience',
        '3' => 'specialty',
        '4' => 'clinicCategory',
        '5' => 'walkInType',
        '6' => 'relevantType',
        '7' => 'notReadyReason',
        '8' => 'department',
        '9' => 'company',
        '10' => 'cancelReservationReason',
        '11' => 'mainSystemAffected',
        '12' => 'referredTo',
    );

    public static $pmsReturn = array(
        'exceptionReason' => 1,
        'userExperience' => 2,
        'specialty' => 3,
        'clinicCategory' => 4,
        'walkInType' => 5,
        'relevantType' => 6,
        'notReadyReason' => 7,
        'department' => 8,
        'company' => 9,
        'cancelReservationReason' => 10,
        'mainSystemAffected' => 11,
        'referredTo' => 12,
    );
}