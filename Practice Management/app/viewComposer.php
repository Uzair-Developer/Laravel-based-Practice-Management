<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\enums\AttributeType;
use core\enums\UserRules;
use core\hospital\HospitalRepository;
use core\physician\PhysicianRepository;
use core\user\UserRepository;

View::composer('layout/flashMessages', function ($view) {
    $message = Session::get('flash_notification.message');
    try {
        if (is_array($message)) {
            $data['message'] = $message;
        } elseif (is_object($message)) {
            $data['message'] = $message->getMessages();
        } else {
            $data['message'] = $message;
        }
    } catch (Exception $e) {
        $data['message'] = 'Ops, Their is error please try again';
    }
    $view->with($data);
});

View::composer('layout/main', function ($view) {
    $user = Sentry::getUser();
    $data['user'] = $user;
    $hospitalRepo = new HospitalRepository();
    $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
    $data['experience'] = AttributePms::getAll(AttributeType::$pmsReturn['userExperience']);
    $data['notReadyReason'] = AttributePms::getAll(AttributeType::$pmsReturn['notReadyReason']);
//    $data['specialty'] = AttributePms::getAll(AttributeType::$pmsReturn['specialty']);
    $data['groups'] = Group::getAll();
    $data['users'] = User::getAllSystem();

    $data['diseaseSymptomsPending'] = count(DiseaseSymptoms::getPending());
    $data['agentComments'] = count(AgentComments::getUnSeen());

    $data['newComplains'] = Complain::getAll(array('read' => 2));

    $data['countPendingException'] = PhysicianException::getAll(array('status' => '0'));
    if ($user->user_type_id == UserRules::physician) {
        $data['countApprovedException'] = PhysicianException::getAll(array(
            'status' => '1',
            'start_created_at' => date("Y-m-d 00:00:00", strtotime("-1 month")),
            'end_created_at' => date('Y-m-d 23:59:59'),
        ));
        $data['countNotApprovedException'] = PhysicianException::getAll(array(
            'status' => '2',
            'start_created_at' => date("Y-m-d 00:00:00", strtotime("-1 month")),
            'end_created_at' => date('Y-m-d 23:59:59'),
        ));
        $data['countNewWalkInReservation'] = Reservation::getNewWalkInByPhysician($user->id);
    }
    if ($user->user_type_id == UserRules::receptionPersonnel || $user->user_type_id == UserRules::clinicManager || $user->user_type_id == 1) {
        $data['countNewWalkInReservation'] = Reservation::getNewWalkIn();
        $data['countApprovalWalkInReservation'] = Reservation::getApprovalWalkIn();
    }
    if ($user->user_type_id == 1 || $user->hasAccess('physician.list')) {
        $physicianRepo = new PhysicianRepository();
        $physicians = $physicianRepo->getAll(null, false);
        $physicianNoAction = 0;
        $physicianNeedApprove = 0;
        $physicianPublish = 0;
        foreach ($physicians as $key => $val) {
            $physicianData = Physician::getByPhysicianId($val['id']);
            if ($physicianData) {
                if ($physicianData['current_status'] == 0) {
                    $physicianNoAction++;
                } else if ($physicianData['current_status'] == 1) {
                    $physicianNeedApprove++;
                } else if ($physicianData['current_status'] == 2) {
                    $physicianPublish++;
                }
            } else {
                $physicianNoAction++;
            }
        }
        $data['physicianNoAction'] = $physicianNoAction;
        $data['physicianNeedApprove'] = $physicianNeedApprove;
        $data['physicianPublish'] = $physicianPublish;
    }
    $view->with($data);
});