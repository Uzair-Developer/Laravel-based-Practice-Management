<?php
namespace core\enums;


abstract class ReservationHistoryActions
{
    public static $actions = array(
        'Patient Attend' => 'Patient Attend',
        'Archive From Edit Schedule' => 'Archive From Edit Schedule',
        'Archive From Schedule Exception' => 'Archive From Schedule Exception',
        'Add Reservation' => 'Add',
        'Cancel Reservation' => 'Cancel',
        'Add Notes' => 'Add Notes',
        'Update' => 'Update',
        'Pending From Un Archive' => 'Pending From Un Archive',
        'Un Archive' => 'Un Archive',
        'Patient Out' => 'Patient Out',
        'Patient In' => 'Patient In',
        'Pending' => 'Pending',
        'Resume' => 'Resume',
        'Patient Not Attend' => 'Patient Not Attend',
        'Walk In Approved' => 'Walk In Approved',
        'Patient In Service' => 'Patient In Service',
        'Service Done' => 'Service Done',
        'Archive From Delete The Localization In This Clinic' => 'Archive From Delete The Localization In This Clinic',
        'Archive From Delete The Localization In This Hospital' => 'Archive From Delete The Localization In This Hospital',
        'Add Exception By Holiday' => 'Add Exception By Holiday',
        'Add Exception' => 'Add Exception',
        'Resume From Exception' => 'Resume From Exception',
        'No Show' => 'No Show',
        'Doctor Resigned' => 'Doctor Resigned',
    );
}