<?php

use core\enums\PatientGender;
use core\enums\PatientStatus;

class CronController extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function getAllSMS()
    {
        ini_set('max_execution_time', 0);
        PatientSMS::sendSMSToAll();
    }

    public function getAllPortalPatientSMS()
    {
        ini_set('max_execution_time', 0);
        PortalPatientSMS::sendSMSToAll();
    }

    public function getAllCampaignSMS()
    {
        ini_set('max_execution_time', 0);
        SmsCampaign::sendSMSToAll();
    }

    public function closeAllClinics()
    {
        $openedClinics = Clinic::getAllOpened();
        foreach ($openedClinics as $key => $val) {
            Clinic::edit(array(
                'status' => 0
            ), $val['id']);
            ManageClinics::add(array(
                'clinic_id' => $val['id'],
                'date' => date('Y-m-d H:i:s'),
                'status' => '0',
            ));
        }
        Reservation::noShowAllReservations();
    }

    public function saveSmsPatientLabRadiology()
    {
        $date = '2018-01-01';
        $patientLabs = PatientLabRadiology::getAll(array(
            'finished' => 2,
            'date_from' => $date,
            'withoutDetails' => true,
        ));
        foreach ($patientLabs as $key => $val) {
            $sendSms = true;
            $hisPatientLabs = HisPatientLabRadiology::getByOrder_PatientReg($val['order_id'], $val['patient_reg_no']);
            foreach ($hisPatientLabs as $key2 => $val2) {
                if (empty($val2['verifieddatetime'])) {
                    $sendSms = false;
                    break;
                }
            }
            if (empty($hisPatientLabs)) {
                $sendSms = false;
            }
            if ($sendSms) {
                //////////////Edit Our DB//////////////////
                foreach ($hisPatientLabs as $key2 => $val2) {
                    PatientLabRadiology::editByOrder_PatientReg_TestId($val['order_id'], $val['patient_reg_no'], $val2['TestId'], array(
                        'verifieddatetime' => $val2['verifieddatetime']
                    ));
                }
                ////////////////Send SMS///////////////////
                $patient = Patient::getById($val['patient_id']);
                if ($patient['lab_sms'] == 1 && app('portal_send_sms')) {
                    $smsArray = array(
                        'patient_id' => $patient['id'],
                        'order_id' => $val['order_id'],
                        'type' => 'Portal_link',
                    );
                    $reservation = Reservation::getLastOfPatient($patient['id']);
                    $initialPassword = HisUserProfile::getInitialPasswordByPatient($val['patient_reg_no']);
                    $messageArray = array(
                        'patient_reg_no' => $val['patient_reg_no'],
                        'patient_password' => $initialPassword,
                    );
                    if ($reservation) {
                        if ($reservation['sms_lang'] == 1) { // arabic
                            if (!empty($initialPassword)) {
                                $smsArray['message'] = trans('sms.patient-portal-credentials-ar', $messageArray);
                            } else {
                                $smsArray['message'] = trans('sms.patient-portal-ar');
                            }
                        } else {
                            if (!empty($initialPassword)) {
                                $smsArray['message'] = trans('sms.patient-portal-credentials', $messageArray);
                            } else {
                                $smsArray['message'] = trans('sms.patient-portal');
                            }
                        }
                    } else {
                        if (!empty($initialPassword)) {
                            $smsArray['message'] = trans('sms.patient-portal-credentials-ar', $messageArray);
                        } else {
                            $smsArray['message'] = trans('sms.patient-portal-ar');
                        }
                    }
                    PortalPatientSMS::add($smsArray);
                }
            }
        }
    }

    public function getCountriesAndCities()
    {
        $lookupCity = HisLookUp::getAll('City');
        foreach ($lookupCity as $key => $val) {
            if (Lookup::checkExist('City', $val['HIS_ID'])) {
                Lookup::editByHis(array(
                    'name' => $val['HIS_MEANING']
                ), 'City', $val['HIS_ID']);
            } else {
                Lookup::add(array(
                    'name' => $val['HIS_MEANING'],
                    'his_id' => $val['HIS_ID'],
                    'type' => 'City',
                ));
            }
        }
        $lookupCountry = HisLookUp::getAll('Country');
        foreach ($lookupCountry as $key => $val) {
            if (Lookup::checkExist('Country', $val['HIS_ID'])) {
                Lookup::editByHis(array(
                    'name' => $val['HIS_MEANING']
                ), 'Country', $val['HIS_ID']);
            } else {
                Lookup::add(array(
                    'name' => $val['HIS_MEANING'],
                    'his_id' => $val['HIS_ID'],
                    'type' => 'Country',
                ));
            }
        }
    }

    public function reservationsSendSurveyUrl()
    {
        $yesterday = date('Y-m-d', strtotime('-1 days' . date('Y-m-d')));
        $reservations = Reservation::getByPatientsIdAndDates([
            'date_from' => $yesterday,
            'date_to' => $yesterday,
            'patient_status' => 10,
            'type' => 1,
        ], false);
        foreach ($reservations as $key => $val) {
            $patient = Patient::getById($val['patient_id']);
            if ($patient) {
                if (HisBillDetail::checkPatientFees($patient['registration_no'], $yesterday)) {
                    $arr['reservation_id'] = $val['id'];
                    if ($val['sms_lang'] == 1) { // arabic
                        $body = trans('sms.out-patient-survey-ar', $arr);
                    } else { // english
                        $body = trans('sms.out-patient-survey', $arr);
                    }
                    $smsArray = array(
                        'patient_id' => $patient['id'],
                        'reservation_id' => $val['id'],
                        'type' => 'Survey',
                        'message' => $body,
                    );
                    PatientSMS::add($smsArray);
                }
            }
        }
    }

    public function inPatientSendSurveyUrl()
    {
        $lastMonth = date('Y-m-d', strtotime('-30 days' . date('Y-m-d')));
        $ipids = InPatient::getAll(['from_admitdatetime' => $lastMonth, 'getIds' => 'ipid']);
        $patients = AllinPatient_V::getAll(['not_ipids' => $ipids, 'from_admitdatetime' => $lastMonth, 'discharge' => 'Y']);
        foreach ($patients as $key => $val) {
            $patient = Patient::getByRegistrationNo($val['RegistrationNo']);
            if ($patient) {
                $lastRes = Reservation::getLastOfPatient($patient['id']);
                if ($lastRes) {
                    if ($lastRes['sms_lang'] == 1) { // arabic
                        $body = trans('sms.in_patient_survey-ar');
                    } else { // english
                        $body = trans('sms.in_patient_survey');
                    }
                } else {
                    $body = trans('sms.in_patient_survey-ar');
                }
                $smsArray = array(
                    'patient_id' => $patient['id'],
                    'reservation_id' => null,
                    'type' => 'in_patient_Survey',
                    'message' => $body,
                );
                PatientSMS::add($smsArray);

                // add to our database
                $physician = User::checkHisExist($val['DoctorID']);
                if (empty(InPatient::checkExist($patient['id'], $val['ipid']))) {
                    $array = [
                        'patient_id' => $patient['id'],
                        'registration_no' => $val['RegistrationNo'],
                        'ipid' => $val['ipid'],
                        'admitdatetime' => $val['admitdatetime'],
                        'physician_id' => isset($physician['id']) ? $physician['id'] : null,
                        'physician_his_id' => $val['DoctorID'],
                    ];
                    InPatient::add($array);
                }
            }
        }
    }

    public function saveSMSReminderReservation()
    {
        $nextDay = date('Y-m-d', strtotime('+1 days' . date('Y-m-d')));
        Reservation::where('patient_status', PatientStatus::waiting)
            ->where('date', $nextDay)
            ->where('patient_attend', 0)
            ->chunk(25, function ($reservations) {
                foreach ($reservations as $key => $val) {
                    $reservationData = $val;
                    if ($reservationData['type'] == 3) {
                        // revisit type
                        $reservationData['time_from'] = $reservationData['revisit_time_from'];
                    }
                    if ($reservationData['time_from'] > '23:59:00') {
                        $seconds = Functions::hoursToSeconds($reservationData['time_from']);
                        $newSeconds = $seconds - (24 * 60 * 60);
                        $reservationData['time_from'] = date('h:ia', strtotime(Functions::timeFromSeconds($newSeconds)));
                        if ($reservationData['sms_lang'] == 1) { // arabic
                            $reservationData['time_from'] = $reservationData['time_from'] . ' -بعد منتصف الليل-';
                        } else {
                            $reservationData['time_from'] = $reservationData['time_from'] . ' -after mid night-';
                        }
                    } else {
                        $reservationData['time_from'] = date('h:ia', strtotime($reservationData['time_from']));
                    }
                    $reservationData['date'] = date('dMY', strtotime($reservationData['date']));
                    /////////////////////////////
                    $clinicData = Clinic::getById($reservationData['clinic_id']);
                    $physicianData = User::getById($reservationData['physician_id']);
                    if ($reservationData['sms_lang'] == 1) { // arabic
                        if (empty($physicianData['first_name_ar'])) {
                            $reservationData['physician_name'] = $physicianData['first_name'] . ' ' . $physicianData['middle_name'];
                        } else {
                            $reservationData['physician_name'] = $physicianData['first_name_ar'] . ' ' . $physicianData['last_name_ar'];
                        }
                        if (empty($clinicData['name_ar'])) {
                            $reservationData['clinic_name'] = $clinicData['name'];
                        } else {
                            $reservationData['clinic_name'] = $clinicData['name_ar'];
                        }
                    } else {
                        $reservationData['physician_name'] = $physicianData['first_name'] . ' ' . $physicianData['middle_name'];
                        $reservationData['clinic_name'] = $clinicData['name'];
                    }
                    /////////////////////////////
                    $patientData = Patient::getById($reservationData['patient_id']);
                    if ($patientData['gender'] == PatientGender::$genderReturn['Female']) {
                        $patientName = 'Ms.' . $patientData['first_name'];
                    } else {
                        $patientName = 'Mr.' . $patientData['first_name'];
                    }
                    $reservationData['patient_name'] = $patientName;
                    if (app('send_sms') && app('send_sms_reminder')) {
                        $smsArray = array(
                            'patient_id' => $reservationData['patient_id'],
                            'reservation_id' => $reservationData['id'],
                            'type' => 'Reminder',
                        );
                        if ($reservationData['sms_lang'] == 1) { // arabic
                            $smsArray['message'] = trans('sms.reminder-ar', $reservationData->toArray());
                        } else { // english
                            $smsArray['message'] = trans('sms.reminder', $reservationData->toArray());
                        }
                        PatientSMS::add($smsArray);
                    }
                }
            });
    }

    public function sendNewPatientsToHIS()
    {
        if (app('production')) {
            ini_set('max_execution_time', 0);
            RiyadhPatient::sendNewPatientsToHIS();
        }
    }
}