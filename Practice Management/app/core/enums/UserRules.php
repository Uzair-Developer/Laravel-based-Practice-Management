<?php
namespace core\enums;


abstract class UserRules
{
    const  supperAdmin = "1",
        hospitalAdmin = "2",
        clinicManager = "3",
        callCenterAgent = "4",
        visitsCoordinator = "5",
        receptionPersonnel = "6",
        physician = "7",
        patientRelation = "8";
}