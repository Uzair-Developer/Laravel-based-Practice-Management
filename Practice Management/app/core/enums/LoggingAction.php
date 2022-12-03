<?php
namespace core\enums;


abstract class LoggingAction
{
    const  add_patient = "1",
        add_reservation = "2",
        cancel_reservation = "3",
        update_reservation = "4",
        add_revisit_reservation = "5",
        unarchive_reservation = "6",
        update_revisit_reservation = "7",
        add_stand_alone_reservation = "8",
        resend_sms_reservation = "9",
        patient_out_reservation = "10",
        patient_in_reservation = "11",
        pending_reservation = "12",
        waiting_reservation = "13",
        patient_in_service_reservation = "14",
        patient_service_done_reservation = "15",
        patient_attend_reservation = "16",
        patient_not_attend_reservation = "17",
        reception_call_reservation = "18",
        reception_call_done_reservation = "19",
        update_patient = "20"
    ;
}