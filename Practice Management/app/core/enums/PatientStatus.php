<?php
namespace core\enums;


abstract class PatientStatus
{
    const waiting = "0",
        patient_in = "1",
        patient_out = "2",
        no_show = "3",
        cancel = "4",
        pending = "5",
        not_available = "6",
        archive = "7";
}