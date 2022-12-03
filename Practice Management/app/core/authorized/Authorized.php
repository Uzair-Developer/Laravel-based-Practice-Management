<?php
namespace core\authorized;

use core\enums\UserRules;
use core\user\UserRepository;

class Authorized
{
    public static function isAdmin($userObj)
    {
        if ($userObj->hasAccess('admin')) {
            return true;
        } else {
            return false;
        }
    }

    public static function isSupperAdmin()
    {
        $userRepo = new UserRepository();
        $user = $userRepo->getCurrentUser();
        if ($user->user_type_id == UserRules::supperAdmin) {
            return true;
        } else {
            return false;
        }
    }

    public static function isHospitalAdmin()
    {
        $userRepo = new UserRepository();
        $user = $userRepo->getCurrentUser();
        if ($user->user_type_id == (UserRules::hospitalAdmin)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isPhysician()
    {
        $userRepo = new UserRepository();
        $user = $userRepo->getCurrentUser();
        if ($user->user_type_id == (UserRules::physician)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isCallCenterAgent()
    {
        $userRepo = new UserRepository();
        $user = $userRepo->getCurrentUser();
        if ($user->user_type_id == (UserRules::callCenterAgent)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isClinicManager()
    {
        $userRepo = new UserRepository();
        $user = $userRepo->getCurrentUser();
        if ($user->user_type_id == (UserRules::clinicManager)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isReceptionPersonnel()
    {
        $userRepo = new UserRepository();
        $user = $userRepo->getCurrentUser();
        if ($user->user_type_id == (UserRules::receptionPersonnel)) {
            return true;
        } else {
            return false;
        }
    }

    public static function isVisitsCoordinator()
    {
        $userRepo = new UserRepository();
        $user = $userRepo->getCurrentUser();
        if ($user->user_type_id == (UserRules::visitsCoordinator)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getSystemRule()
    {
        $userRepo = new UserRepository();
        $user = $userRepo->getCurrentUser();
        if ($user->user_type_id == (UserRules::supperAdmin)) {
            return UserRules::supperAdmin;
        } elseif($user->user_type_id == (UserRules::hospitalAdmin)) {
            return UserRules::hospitalAdmin;
        } elseif($user->user_type_id == (UserRules::clinicManager)) {
            return UserRules::clinicManager;
        } elseif($user->user_type_id == (UserRules::physician)) {
            return UserRules::physician;
        } elseif($user->user_type_id == (UserRules::callCenterAgent)) {
            return UserRules::callCenterAgent;
        } elseif($user->user_type_id == (UserRules::receptionPersonnel)) {
            return UserRules::receptionPersonnel;
        } elseif($user->user_type_id == (UserRules::visitsCoordinator)) {
            return UserRules::visitsCoordinator;
        } else {
            return false;
        }
    }

}