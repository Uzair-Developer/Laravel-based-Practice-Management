<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\clinic\ClinicRepository;
use core\clinicSchedule\ClinicScheduleRepository;
use core\enums\AgeType;
use core\enums\AttributeType;
use core\enums\LoggingAction;
use core\enums\PatientGender;
use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use core\enums\UserRules;
use core\hospital\HospitalRepository;
use core\physician\PhysicianManager;
use core\physicianSchedule\PhysicianScheduleRepository;
use core\user\UserRepository;
use core\userLocalization\UserLocalizationRepository;

class ReservationController extends BaseController
{

    public $user = '';

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function reservationAddMethodGet()
    {
        return Redirect::route('home');
    }

    public function reservationAdd($inputs = null)
    {
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
//        dd(Input::except('_token'));
        $data['inputs'] = $inputs ? $inputs : Input::except('_token');
        $data['hospitalId'] = $inputs ? $inputs['hospital_id'] : Input::get('hospital_id');
        $data['clinicId'] = $inputs ? $inputs['clinic_id'] : Input::get('clinic_id');
        $data['physician_id'] = $inputs ? $inputs['physician_id'] : Input::get('physician_id');
        $data['user_experience_id'] = Input::get('user_experience_id');
        $data['user_specialty_id'] = Input::get('user_specialty_id');
        $data['experience'] = AttributePms::getAll(AttributeType::$pmsReturn['userExperience']);
        $data['specialty'] = AttributePms::getAll(AttributeType::$pmsReturn['specialty']);
        $data['relevant'] = AttributePms::getAll(AttributeType::$pmsReturn['relevantType']);
        $data['cancelResReason'] = AttributePms::getAll(AttributeType::$pmsReturn['cancelReservationReason']);
        if ($inputs) {
            $data['selectDate'] = $inputs['selectDate'];
        } else if (Input::has('date')) {
            $data['selectDate'] = Input::get('date');
        } else {
            $data['selectDate'] = date('Y-m-d');
            $data['inputs']['selectDate'] = date('Y-m-d');
        }
        $clinicRepo = new ClinicRepository();
        $data['clinic'] = $clinicRepo->getById($data['clinicId']);
        $CSRepo = new ClinicScheduleRepository();
        $clinicSchedule = $CSRepo->getByClinicId($data['clinicId'], $data['selectDate']);
        $data['clinicSchedule'] = $clinicSchedule;
        if (empty($data['clinicSchedule'])) {
            Flash::error('This clinic didn\'t have schedule!');
            return Redirect::route('home');
        }
        $allPhysiciansIds = User::getPhysiciansId();
        $ULRepo = new UserLocalizationRepository();
        $physiciansIds = $ULRepo->getPhysiciansByClinicId($allPhysiciansIds, $data['clinicId']);
        if (empty($physiciansIds)) {
            Flash::error('This clinic didn\'t have physicians!');
            return Redirect::route('home');
        }
        $userRepo = new UserRepository();
        $data['physicians'] = $userRepo->getByUsersIdWithSchedule($physiciansIds, $clinicSchedule, $data['selectDate']);
        foreach ($data['physicians'] as $key => $val) {
            if (!isset($val['schedules'][0]) && empty($val['schedules'][0])) {
                unset($data['physicians'][$key]);
                continue;
            }
            $data['physicians'][$key]['dayOff_1'] = $val['schedules'][0]['dayoff_1'];
            $data['physicians'][$key]['dayOff_2'] = $val['schedules'][0]['dayoff_2'];
            $data['physicians'][$key]['dayOff_3'] = $val['schedules'][0]['dayoff_3'];
        }
        ////////////////////////////selected physician//////////////////////////////////
        $daysName = array(
            'saturday' => 'sat',
            'sunday' => 'sun',
            'monday' => 'mon',
            'tuesday' => 'tues',
            'wednesday' => 'wed',
            'thursday' => 'thurs',
            'friday' => 'fri',
        );
        $inputDayName = lcfirst(date('l', strtotime($data['selectDate'])));
        $physician_selected = $userRepo->getByUserIdWithSchedule($data['physician_id'], $clinicSchedule, $data['selectDate']);
        $physicianSchedule = isset($physician_selected['schedules'][0]) ? $physician_selected['schedules'][0] : array();
        $availableTimes = array();
        if (empty($physicianSchedule)) {
            $physician_selected['haveSchedule'] = false;
        } else {
            $numShifts = $clinicSchedule['num_of_shifts'];
            $physician_selected['start_time_1'] = $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'];
            $physician_selected['end_time_1'] = $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'];
            if ($numShifts == 2) {
                $physician_selected['start_time_2'] = $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'];
                $physician_selected['end_time_2'] = $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'];
            }
            if ($numShifts == 3) {
                $physician_selected['start_time_2'] = $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'];
                $physician_selected['end_time_2'] = $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'];

                $physician_selected['start_time_3'] = $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'];
                $physician_selected['end_time_3'] = $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'];
            }
            $physician_selected['haveSchedule'] = true;
            $physician_selected['dayOff_1'] = $physicianSchedule['dayoff_1'];
            $physician_selected['dayOff_2'] = $physicianSchedule['dayoff_2'];
            $physician_selected['dayOff_3'] = $physicianSchedule['dayoff_3'];
            $physician_selected['slots'] = $physicianSchedule['slots'];
            $this->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $data['selectDate']);
        }
        $data2['availableTimes'] = $availableTimes;
        $data2['selectDate'] = $data['selectDate'];
        $data2['slots'] = isset($physicianSchedule['slots']) ? $physicianSchedule['slots'] : '';
        $data['physicianTimeHtml'] = View::make('reservation/physician_time', $data2)->render();
        $data['physician_selected'] = $physician_selected;
        return View::make('reservation/add', $data);
    }

    public function createReservation()
    {
        $inputs = (Input::except('_token'));
        $patient_id = $inputs['patientData']['patient_id'];
        if (isset($inputs['patientData']['phone2']) && $inputs['patientData']['phone2']) {
            $inputs['patientData']['phone'] = $inputs['patientData']['phone2'];
        }
        if (empty($inputs['patientData']['phone'])) {
            $data['physicianTimeHtml'] = 'Error';
            $data['message'] = 'Error In Patient Phone';
            return $data;
        }
        if (empty($inputs['patientData']['first_name']) || $inputs['patientData']['first_name'] == ' ') {
            $data['physicianTimeHtml'] = 'Error';
            $data['message'] = 'Error In Patient First Name';
            return $data;
        }
//        if (empty($inputs['patientData']['middle_name']) || $inputs['patientData']['middle_name'] == ' ') {
//            $data['physicianTimeHtml'] = 'Error';
//            $data['message'] = 'Error In Patient Middle Name';
//            return $data;
//        }
        if (empty($inputs['patientData']['last_name']) || $inputs['patientData']['last_name'] == ' ') {
            $data['physicianTimeHtml'] = 'Error';
            $data['message'] = 'Error In Patient Last Name';
            return $data;
        }
        if (empty($inputs['patientData']['gender'])) {
            $data['physicianTimeHtml'] = 'Error';
            $data['message'] = 'Error In Patient Gender';
            return $data;
        }
        if ($inputs['patientData']['preferred_contact'] == 2) {
            if (empty($inputs['patientData']['email']) || $inputs['patientData']['email'] == ' ') {
                $data['physicianTimeHtml'] = 'Error';
                $data['message'] = 'Error In Patient Email';
                return $data;
            }
        }
        $fName = $inputs['patientData']['first_name'] ? $inputs['patientData']['first_name'] : '';
        $mName = $inputs['patientData']['middle_name'] ? ' ' . $inputs['patientData']['middle_name'] : '';
        $lName = $inputs['patientData']['last_name'] ? ' ' . $inputs['patientData']['last_name'] : '';
        $fmName = $inputs['patientData']['family_name'] ? ' ' . $inputs['patientData']['family_name'] : '';
        $name = $fName . $mName . $lName . $fmName;

        $interval = Functions::getAgeDetails($inputs['patientData']['birthday']);
        $age = '';
        $age_type_id = '';
        if ($interval->y) {
            $age = $interval->y;
            $age_type_id = AgeType::$ageReturn['Years'];
        } else {
            if ($interval->m) {
                $age = $interval->m;
                $age_type_id = AgeType::$ageReturn['Months'];
            } else {
                if ($interval->d) {
                    $age = $interval->d;
                    $age_type_id = AgeType::$ageReturn['Days'];
                } else {
                    if ($interval->h) {
                        $age = $interval->h;
                        $age_type_id = AgeType::$ageReturn['Hours'];
                    }
                }
            }
        }
        if (isset($inputs['patientData']['birthday']) && $inputs['patientData']['birthday']) {
            if ($inputs['patientData']['birthday'] == '0000-00-00') {
                $inputs['patientData']['birthday'] = null;
            }
        } else {
            $inputs['patientData']['birthday'] = null;
        }
        if (!$patient_id) {
            unset($inputs['patientData']['patient_id']);
            unset($inputs['patientData']['id']);
            $patient = Patient::add(array(
                'hospital_id' => $inputs['hospital_id'],
                'phone' => $inputs['patientData']['phone'],
                'name' => $name,
                'first_name' => $fName,
                'middle_name' => $mName,
                'last_name' => $lName,
                'family_name' => $fmName,
                'national_id' => $inputs['patientData']['national_id'],
                'birthday' => $inputs['patientData']['birthday'],
                'age' => $age ? $age : null,
                'age_type_id' => $age_type_id ? $age_type_id : null,
                'age_year' => $interval->y,
                'age_month' => $interval->m,
                'age_day' => $interval->d,
                'age_hour' => $interval->h,
                'email' => $inputs['patientData']['email'],
                'preferred_contact' => $inputs['patientData']['preferred_contact'],
                'gender' => $inputs['patientData']['gender'],
                'address' => $inputs['patientData']['address'],
                'sync_flag' => 0,
            ));
            $patient_id = $patient->id;
            Logging::add([
                'action' => LoggingAction::add_patient,
                'table' => 'patients',
                'ref_id' => $patient_id,
                'user_id' => $this->user->id,
            ]);
            // removed add patient to his from  //
        } else {
            $patientArray = array();
            $patient = Patient::getById($patient_id);
            if (app('production') && empty($patient['registration_no'])) {
                $patientArray['sync_flag'] = 0;
            }
            if (isset($inputs['patientData']['phone2']) && $inputs['patientData']['phone2']) {
                $patientArray['phone'] = $inputs['patientData']['phone2'];
            }
            if ($patientArray) {
                Patient::edit($patientArray, $patient_id);
            }
        }
        $schedule_id = $inputs['schedule_id'];
        $physician_id = $inputs['physician_id'];
        $physicianSchedule = PhysicianSchedule::getClinicScheduleIdWithPhysicianId($schedule_id, $physician_id, $inputs['date']);
        $daysName = array(
            'saturday' => 'sat',
            'sunday' => 'sun',
            'monday' => 'mon',
            'tuesday' => 'tues',
            'wednesday' => 'wed',
            'thursday' => 'thurs',
            'friday' => 'fri',
        );
        if (isset($physicianSchedule)) {
            $inputDayName = lcfirst(date('l', strtotime($inputs['date'])));
            $start = '';
            $end = '';
            if ($physicianSchedule[$daysName[$inputDayName] . '_start_time_1'] && $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'] != '00:00:00') {
                $start = $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'];
            }
            if ($physicianSchedule['num_of_shifts'] == 1) {
                if ($physicianSchedule[$daysName[$inputDayName] . '_end_time_1'] && $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'] != '00:00:00') {
                    $end = $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'];
                }
            } elseif ($physicianSchedule['num_of_shifts'] == 2 || $physicianSchedule['num_of_shifts'] == 3) {
                if (empty($start) && $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'] && $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'] != '00:00:00') {
                    $start = $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'];
                }
                if ($physicianSchedule[$daysName[$inputDayName] . '_end_time_2'] && $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'] != '00:00:00') {
                    $end = $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'];
                }
                if ($physicianSchedule['num_of_shifts'] == 3) {
                    if (empty($start) && $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'] && $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'] != '00:00:00') {
                        $start = $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'];
                    }
                    if ($physicianSchedule[$daysName[$inputDayName] . '_end_time_3'] && $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'] != '00:00:00') {
                        $end = $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'];
                    }
                }
            }
            if ($start && $start != '00:00:00' && $end && $end != '00:00:00') {
                if ($inputs['time_from'] < $start || $inputs['time_from'] > $end) {
                    $data['physicianTimeHtml'] = 'Error';
                    $data['message'] = 'Invalid Time Selected! Please Refresh The Page!';
                    return $data;
                }
            }
        }


        $CSRepo = new ClinicScheduleRepository();
        $clinicSchedule = $CSRepo->getByClinicId($inputs['clinic_id'], $inputs['date']);
//        $time_to = date("H:i:s", strtotime($physicianSchedule['slots'] . ' minutes', strtotime($inputs['time_from'])));
        $seconds = Functions::hoursToSeconds($inputs['time_from']);
        $newSeconds = $seconds + ($physicianSchedule['slots'] * 60);
        $time_to = Functions::timeFromSeconds($newSeconds);
        if (Reservation::checkExistRecord($inputs['clinic_id'], $inputs['physician_id'], $inputs['date'], $inputs['time_from'], $time_to)) {
            $data['physicianTimeHtml'] = 'No';
        } else {
            $clinicData = Clinic::getById($inputs['clinic_id']);
            $countReservations = Reservation::getCountByClinicAndData($inputs['clinic_id'], $inputs['date']);
            $reservationCode = $clinicData['code'] . '-' . Functions::make3D($countReservations + 1) . '-' . date('ymd', strtotime($inputs['date']));
            $reservationArray = array(
                'code' => $reservationCode,
                'physician_id' => $inputs['physician_id'],
                'clinic_id' => $inputs['clinic_id'],
                'patient_id' => $patient_id,
                'date' => $inputs['date'],
                'time_from' => $inputs['time_from'],
                'time_to' => $time_to,
                'create_by' => $this->user->id,
                'sms_lang' => $inputs['sms_lang']
            );
            $reservation = Reservation::add($reservationArray);
            Logging::add([
                'action' => LoggingAction::add_reservation,
                'table' => 'reservations',
                'ref_id' => $reservation->id,
                'user_id' => $this->user->id,
            ]);
            ReservationHistory::add([
                'action' => 'Add',
                'action_by' => $this->user->id,
                'reservation_id' => $reservation->id,
                'code' => $reservationCode,
                'physician_id' => $inputs['physician_id'],
                'clinic_id' => $inputs['clinic_id'],
                'patient_id' => $patient_id,
                'date' => $inputs['date'],
                'time_from' => $inputs['time_from'],
                'time_to' => $time_to,
            ]);
            if ($inputs['hospital_id'] == 2) {
                $reservationData = Reservation::getById($reservation->id, true, true);
                if ($inputs['time_from'] > '23:59:00') {
                    $seconds = Functions::hoursToSeconds($inputs['time_from']);
                    $newSeconds = $seconds - (24 * 60 * 60);
                    $reservationData['time_from'] = date('h:ia', strtotime(Functions::timeFromSeconds($newSeconds)));
                    if ($inputs['sms_lang'] == 1) { // arabic
                        $reservationData['time_from'] = $reservationData['time_from'] . ' -بعد منتصف الليل-';
                    } else {
                        $reservationData['time_from'] = $reservationData['time_from'] . ' -after mid night-';
                    }
                } else {
                    $reservationData['time_from'] = date('h:ia', strtotime($reservationData['time_from']));
                }
                $reservationData['date'] = date('dMY', strtotime($reservationData['date']));
                $clinicData = Clinic::getById($reservationData['clinic_id']);
                $physicianData = User::getById($reservationData['physician_id']);
                if ($inputs['sms_lang'] == 1) { // arabic
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
                $patientData = Patient::getById($patient_id);
                if ($patientData['gender'] == PatientGender::$genderReturn['Female']) {
                    $patientName = 'Ms.' . $patientData['first_name'];
                } else {
                    $patientName = 'Mr.' . $patientData['first_name'];
                }
                $reservationData['patient_name'] = $patientName;
                $reservationData['reservationCode'] = $reservationCode;
                if (app('send_sms')) {
                    $smsArray = array(
                        'patient_id' => $patient_id,
                        'reservation_id' => $reservation->id,
                        'type' => 'Create',
                    );
                    if ($inputs['sms_lang'] == 1) { // arabic
                        $smsArray['message'] = trans('sms.create-ar', $reservationData->toArray());
                    } else { // english
                        $smsArray['message'] = trans('sms.create', $reservationData->toArray());
                    }
                    PatientSMS::add($smsArray);
                }
            }
            $availableTimes = array();
            $this->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $inputs['date']);
            $data2['availableTimes'] = $availableTimes;
            $data2['selectDate'] = $inputs['date'];
            $data2['slots'] = $physicianSchedule['slots'];
            $data['physicianTimeHtml'] = View::make('reservation/physician_time', $data2)->render();
        }
        return $data;
    }

    public function reservationGetEvents()
    {
        $clinicId = Input::get('clinic_id');
        $start = Input::get('start');
        $data = Reservation::getByClinicAndDate($clinicId, $start);
        $return = array();
        foreach ($data as $key => $val) {
            $return[$key]['id'] = $key;
            $return[$key]['resourceId'] = $val['physician_id'];
            $return[$key]['start'] = $val['date'] . 'T' . $val['time_from'];
            $return[$key]['end'] = $val['date'] . 'T' . $val['time_to'];
            $return[$key]['title'] = $key + 1;
            if (PatientStatus::patient_in == $val['patient_status']) {
                $return[$key]['color'] = '#FFA200'; // orange
            } elseif (PatientStatus::patient_out == $val['patient_status']) {
                $return[$key]['color'] = '#9C9B9A'; // gray
            } elseif (PatientStatus::no_show == $val['patient_status']) {
                $return[$key]['color'] = '#FF0000'; // red
            }
        }
        echo(json_encode($return));
    }

    public function getAvailableTimeOfPhysician(&$availableTimes, $physicianSchedule, $clinicSchedule, $selected_date, $revisit = false)
    {
        $physicianManager = new PhysicianManager();
        $physicianManager->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $selected_date, $revisit);
    }

    public function deleteReservation()
    {
        $inputs = (Input::except('_token'));
        if (!isset($inputs['send_sms'])) {
            $inputs['send_sms'] = 1;
        }
        Reservation::edit(array(
            'update_by' => $this->user->id,
            'status' => ReservationStatus::cancel,
            'patient_status' => PatientStatus::cancel,
            'cancel_notes' => $inputs['cancel_notes'],
            'cancel_reason_id' => $inputs['cancel_reason_id'],
            'send_cancel_sms' => $inputs['send_sms'],
            'show_reason' => 2,
        ), $inputs['reservation_id']);
        $reservationData = Reservation::getById($inputs['reservation_id']);
        Logging::add([
            'action' => LoggingAction::cancel_reservation,
            'table' => 'reservations',
            'ref_id' => $inputs['reservation_id'],
            'user_id' => $this->user->id,
        ]);
        ReservationHistory::add([
            'action' => 'Cancel',
            'action_by' => $this->user->id,
            'reservation_id' => $reservationData['id'],
            'code' => $reservationData['code'],
            'physician_id' => $reservationData['physician_id'],
            'clinic_id' => $reservationData['clinic_id'],
            'patient_id' => $reservationData['patient_id'],
            'date' => $reservationData['date'],
            'time_from' => $reservationData['time_from'],
            'time_to' => $reservationData['time_to'],
            'status' => ReservationStatus::cancel,
            'patient_status' => PatientStatus::cancel,
        ]);
        $clinics = Clinic::getById($reservationData['clinic_id']);
        if ($clinics['hospital_id'] == 2) {
            if ($inputs['send_sms'] == 1) { // send sms 1 => true, 2 => false
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
                $patientData = Patient::getById($reservationData['patient_id']);
                if ($patientData['gender'] == PatientGender::$genderReturn['Female']) {
                    $patientName = 'Ms.' . $patientData['first_name'];
                } else {
                    $patientName = 'Mr.' . $patientData['first_name'];
                }
                $reservationData['patient_name'] = $patientName;
                $reservationData['reservationCode'] = $reservationData['code'];
                if (app('send_sms')) {
                    $smsArray = array(
                        'patient_id' => $reservationData['patient_id'],
                        'reservation_id' => $inputs['reservation_id'],
                        'type' => 'Cancel',
                    );
                    if ($reservationData['sms_lang'] == 1) { // arabic
                        $smsArray['message'] = trans('sms.cancel-ar', $reservationData->toArray());
                    } else { // english
                        $smsArray['message'] = trans('sms.cancel', $reservationData->toArray());
                    }
                    PatientSMS::add($smsArray);
                }
            }
        }
        if (isset($inputs['return_str']) && $inputs['return_str'] == 'yes') {
            return array();
        }
        $schedule_id = $inputs['schedule_id'];
        $physician_id = $inputs['physician_id'];
        $physicianSchedule = PhysicianSchedule::getClinicScheduleIdWithPhysicianId($schedule_id, $physician_id, $inputs['date']);
        $CSRepo = new ClinicScheduleRepository();
        $clinicSchedule = $CSRepo->getByClinicId($inputs['clinic_id'], $inputs['date']);
        $availableTimes = array();
        $this->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $inputs['date']);
        $data2['availableTimes'] = $availableTimes;
        $data2['selectDate'] = $inputs['date'];
        $data2['slots'] = $physicianSchedule['slots'];
        $data['physicianTimeHtml'] = View::make('reservation/physician_time', $data2)->render();
        return $data;
    }

    public function addNoteReservation()
    {
        $inputs = (Input::except('_token'));
        Reservation::edit(array(
            'notes' => $inputs['notes'],
        ), $inputs['reservation_id']);
        $reservationData = Reservation::getById($inputs['reservation_id']);
        ReservationHistory::add([
            'action' => 'Add Notes',
            'action_by' => $this->user->id,
            'reservation_id' => $reservationData['id'],
            'code' => $reservationData['code'],
            'physician_id' => $reservationData['physician_id'],
            'clinic_id' => $reservationData['clinic_id'],
            'patient_id' => $reservationData['patient_id'],
            'date' => $reservationData['date'],
            'time_from' => $reservationData['time_from'],
            'time_to' => $reservationData['time_to'],
            'status' => $reservationData['status'],
            'patient_status' => $reservationData['patient_status'],
            'notes' => $inputs['notes'],
        ]);
    }

    public function searchPatientReservation()
    {
        $inputs = (Input::except('_token'));
        $data = $inputs['data'];
        $data['current_date'] = $inputs['date'];
        $return['reservations'] = Reservation::getByPatientsIdAndDates($data);
        return View::make('reservation/patients_reservations', $return)->render();
    }

    public function loadPhysicianTime()
    {
        $inputs = (Input::except('_token'));
        $schedule_id = $inputs['schedule_id'];
        $physician_id = $inputs['physician_id'];
        $physicianSchedule = PhysicianSchedule::getClinicScheduleIdWithPhysicianId($schedule_id, $physician_id, $inputs['date']);
        $CSRepo = new ClinicScheduleRepository();
        $clinicSchedule = $CSRepo->getByClinicId($inputs['clinic_id'], $inputs['date']);
        $availableTimes = array();
        $this->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $inputs['date']);
        $data2['availableTimes'] = $availableTimes;
        $data2['selectDate'] = $inputs['date'];
        $data2['slots'] = $physicianSchedule['slots'];
        $data['physicianTimeHtml'] = View::make('reservation/physician_time', $data2)->render();
        return $data;
    }

    public function editReservation()
    {
        $inputs = (Input::except('_token'));
        $reservation = Reservation::getById($inputs['reservation_id']);
        if ($reservation) {
            $data['reservation'] = $reservation->toArray();
            $patient = Patient::getById($reservation['patient_id']);
            $data['patient'] = $patient->toArray();

            $data['hospital_id'] = Clinic::getById($reservation['clinic_id'])['hospital_id'];
//            $CSRepo = new ClinicScheduleRepository();
//            $clinicSchedule = $CSRepo->getByClinicId($reservation['clinic_id'], $reservation['date']);
//            $userRepo = new UserRepository();
//            $physician_selected = $userRepo->getByUserIdWithSchedule($reservation['physician_id'], $clinicSchedule, $reservation['date']);
//            $physicianSchedule = $physician_selected['schedules'][0];
//            $availableTimes = array();
//            $this->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $reservation['date']);
//            $data['modal_time_html'] = '<option value="">Choose</option>';
//            foreach ($availableTimes as $key => $val) {
//                if (!isset($val['reserved'])) {
//                    if ($key % 2 == 0) {
//                        $data['modal_time_html'] .= '<option value="' . $val['time'] . '">' . $val['time'] . '</option>';
//                    } else {
//                        $data['modal_time_html'] .= '<option style="background:lightgrey;" value="' . $val['time'] . '">' . $val['time'] . '</option>';
//                    }
//                }
//            }
            return $data;
        } else {
            return array();
        }
    }

    public function updateReservation()
    {
        $inputs = Input::except('_token')['data'];
        $reservation_id = $inputs['reservation_id'];
        $reservation = Reservation::getById($reservation_id);
        $patient_id = $reservation['patient_id'];

        $CSRepo = new ClinicScheduleRepository();
        $clinicSchedule = $CSRepo->getByClinicId($reservation['clinic_id'], $inputs['date']);
        $userRepo = new UserRepository();
        $physician_selected = $userRepo->getByUserIdWithSchedule($inputs['physician_id'], $clinicSchedule, $inputs['date']);
        $physicianSchedule = $physician_selected['schedules'][0];
        $reservationArray = array(
            'show_reason' => 2,
            'update_by' => $this->user->id,
            'status' => ReservationStatus::reserved,
            'patient_status' => PatientStatus::waiting,
            'physician_id' => $inputs['physician_id'],
            'clinic_id' => $reservation['clinic_id'],
            'date' => $inputs['date'],
            'time_from' => $inputs['time'],
        );
        ///////////////////////////////////////////////////////////////////////////////////////
        $seconds = Functions::hoursToSeconds($inputs['time']);
        $newSeconds = $seconds + ($physicianSchedule['slots'] * 60);
        $reservationArray['time_to'] = Functions::timeFromSeconds($newSeconds);

        ///////////////////////////////////////////////////////////////////////////////////////
        Reservation::edit($reservationArray, $reservation_id);
        Logging::add([
            'action' => LoggingAction::update_reservation,
            'table' => 'reservations',
            'ref_id' => $reservation_id,
            'user_id' => $this->user->id,
        ]);
        $reservationData = Reservation::getById($reservation_id, false);
        ReservationHistory::add([
            'action' => 'Update',
            'action_by' => $this->user->id,
            'reservation_id' => $reservationData['id'],
            'code' => $reservationData['code'],
            'physician_id' => $reservationData['physician_id'],
            'clinic_id' => $reservationData['clinic_id'],
            'patient_id' => $reservationData['patient_id'],
            'date' => $reservationData['date'],
            'time_from' => $reservationData['time_from'],
            'time_to' => $reservationData['time_to'],
            'status' => $reservationData['status'],
            'patient_status' => $reservationData['patient_status'],
        ]);
        $clinics = Clinic::getById($reservation['clinic_id']);
        if ($clinics['hospital_id'] == 2) {
            if ($reservation['type'] == 1) { // call reserve
                $reservationData = Reservation::getById($reservation_id);
                ///////////////////////////////////////////////////////////////////////////////////////
                $reservationData['time_from'] = date('h:ia', strtotime($inputs['time']));
                $reservationData['date'] = date('dMY', strtotime($inputs['date']));
                if ($inputs['time'] > '23:59:00') {
                    $seconds = Functions::hoursToSeconds($inputs['time']);
                    $newSeconds = $seconds - (24 * 60 * 60);
                    $reservationData['time_from'] = date('h:ia', strtotime(Functions::timeFromSeconds($newSeconds)));
                    if ($reservationData['sms_lang'] == 1) { // arabic
                        $reservationData['time_from'] = $reservationData['time_from'] . ' -بعد منتصف الليل-';
                    } else {
                        $reservationData['time_from'] = $reservationData['time_from'] . ' -after mid night-';
                    }
                }
                ///////////////////////////////////////////////////////////////////////////////////////
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
                $patientData = Patient::getById($patient_id);
                if ($patientData['gender'] == PatientGender::$genderReturn['Female']) {
                    $patientName = 'Ms.' . $patientData['first_name'];
                } else {
                    $patientName = 'Mr.' . $patientData['first_name'];
                }
                $reservationData['patient_name'] = $patientName;
                $reservationData['reservationCode'] = $reservationData['code'];
                if (app('send_sms')) {
                    $smsArray = array(
                        'patient_id' => $patient_id,
                        'reservation_id' => $reservation->id,
                        'type' => 'Modify',
                    );
                    if ($reservationData['sms_lang'] == 1) { // arabic
                        $smsArray['message'] = trans('sms.modify-ar', $reservationData->toArray());
                    } else { // english
                        $smsArray['message'] = trans('sms.modify', $reservationData->toArray());
                    }
                    PatientSMS::add($smsArray);
                }
            }
        }
        return array();
    }

    public function getAvailablePhysicianTime()
    {
        $inputs = (Input::except('_token'));
        $reservation = Reservation::getById($inputs['reservation_id']);
        if (isset($inputs['clinic_id']) && $inputs['clinic_id'] && isset($inputs['physician_id']) && $inputs['physician_id']) {
            $reservation['clinic_id'] = $inputs['clinic_id'];
            $reservation['physician_id'] = $inputs['physician_id'];
        }
        $CSRepo = new ClinicScheduleRepository();
        $clinicSchedule = $CSRepo->getByClinicId($reservation['clinic_id'], $inputs['date']);

        $userRepo = new UserRepository();
        $physician_selected = $userRepo->getByUserIdWithSchedule($reservation['physician_id'], $clinicSchedule, $inputs['date']);
        $data = array();
        if (isset($physician_selected['schedules'][0])) {
            $physicianSchedule = $physician_selected['schedules'][0];

            $availableTimes = array();
            $this->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $inputs['date']);
            $data['modal_time_html'] = '<option value="">Choose</option>';
            $dataFill = false;
            foreach ($availableTimes as $key => $val) {
                if (!isset($val['reserved'])) {
                    if ($inputs['date'] == date('Y-m-d')) {
                        if ($val['time'] < date('H:i:s', strtotime("-30 minutes"))) {
                            continue;
                        }
                    }
                    if ($key % 2 == 0) {
                        $dataFill = true;
                        $data['modal_time_html'] .= '<option value="' . $val['time'] . '">' . $val['time'] . '</option>';
                    } else {
                        $dataFill = true;
                        $data['modal_time_html'] .= '<option style="background:lightgrey;" value="' . $val['time'] . '">' . $val['time'] . '</option>';
                    }
                }
            }
            if (!$dataFill) {
                $data['modal_time_html'] = '<option value="">Not Available</option>';
            }
        }
        return $data;
    }

    public function getReservationData()
    {
        $inputs = Input::except('_token');
        $reservation = Reservation::getById($inputs['reservation_id']);
        $data['patient'] = Patient::getById($reservation['patient_id']);
        $data['reservation'] = $reservation;
        if (isset($inputs['last_sms']) && $inputs['last_sms']) {
            $sms = PatientSMS::getByReservationId($inputs['reservation_id'])->toArray();
            $sms = end($sms);
            if ($sms) {
                $data['resend_last_sms'] = true;
                $sms['message'] = str_replace('\n', '<br>', $sms['message']);
                $data['last_sms'] = $sms;
            } else {
                $data['resend_last_sms'] = false;
            }
        }
        if (isset($inputs['getPhysicians']) && $inputs['getPhysicians']) {
            $physiciansIds = User::getPhysicianByClinicId($reservation['clinic_id']);
            $physicians = User::getByIds($physiciansIds, null, null, true);
            $physicianHtml = '<option value="">Choose</option>';
            foreach ($physicians as $key => $val) {
                if ($reservation['physician_id'] == $val['id']) {
                    $physicianHtml .= '<option selected value="' . $val['id'] . '">' . $val['full_name'] . '</option>';
                } else {
                    $physicianHtml .= '<option value="' . $val['id'] . '">' . $val['full_name'] . '</option>';
                }
            }
            $data['physicianHtml'] = $physicianHtml;
            $physician = User::getById($reservation['physician_id']);
            $increment = 0;
            $start_date = $reservation["date"];
            $incrementStartDate = $reservation["date"];
            if ($physician['revisit_limit']) {
                $increment = $physician['revisit_limit'];
            }
            if ($start_date < date('Y-m-d')) {
                $start_date = date('Y-m-d');
            }
            if (isset($inputs['editRevisit']) && $inputs['editRevisit']) {
                if ($start_date >= date('Y-m-d')) {
                    $start_date = date('Y-m-d');
                }
            }
            $nextLimitDays = date('Y-m-d', strtotime("+" . $increment . " day", strtotime($incrementStartDate)));
            if ($start_date <= date('Y-m-d') && $nextLimitDays <= date('Y-m-d')) {
                $start_date = 0;
                $nextLimitDays = 0;
            }
            $data['inputDate'] = '<input autocomplete="off" id="revisit_date" required type="text" data-date-format="yyyy-mm-dd"
                               name="date" class="form-control limit_datepicker">
                               <script>
                               $("#revisit_date").datepicker({
                                startDate: "' . $start_date . '",
                                endDate: "' . $nextLimitDays . '",
                                todayHighlight: true,
                                autoclose: true,
                                beforeShowDay: function (date) {
                                    var string = jQuery.datepicker.formatDate("yy-mm-dd", date);
                                    if (string < "' . $start_date . '" || string > "' . $nextLimitDays . '") {
                                        return {
                                            classes: "disabled line-through text-red"
                                        };
                                    }
                                }
                            });
</script>';
        }
        return $data;
    }

    public function getParentReservationData()
    {
        $inputs = Input::except('_token');
        $reservation = Reservation::getById($inputs['reservation_id']);
        $parentReservation = Reservation::getById($reservation['parent_id_of_revisit']);
        $data['patient'] = Patient::getById($parentReservation['patient_id']);
        $data['reservation'] = $parentReservation;
        return $data;
    }

    public function getRevisitReservationData()
    {
        $inputs = Input::except('_token');
        $reservation = Reservation::getRevisitOfReservation($inputs['reservation_id']);
        $revisitReservation = Reservation::getById($reservation['id']);
        $data['patient'] = Patient::getById($revisitReservation['patient_id']);
        $data['reservation'] = $revisitReservation;
        return $data;
    }

    public function createRevisitReservation()
    {
        $inputs = Input::except('_token');
//        dd($inputs);
        $reservation = Reservation::getById($inputs['reservation_id'], false);
        if (Reservation::checkPatientExistRecord($reservation['clinic_id'], $inputs['physician_id'], $inputs['date'], $reservation['patient_id'])) {
            Flash::error('This patient already exist with this day and this physician!');
            return Redirect::back();
        }

        $clinicData = Clinic::getById($reservation['clinic_id']);
        $countReservations = Reservation::getCountByClinicAndData($reservation['clinic_id'], $inputs['date']);
        $reservationCode = $clinicData['code'] . '-' . Functions::make3D($countReservations + 1) . '-' . date('ymd', strtotime($inputs['date']));
        $reservationArray = array(
            'code' => $reservationCode,
            'physician_id' => $inputs['physician_id'],
            'clinic_id' => $reservation['clinic_id'],
            'patient_id' => $reservation['patient_id'],
            'date' => $inputs['date'],
            'parent_id_of_revisit' => $inputs['reservation_id'],
            'revisit_time_from' => $inputs['time'],
            'type' => 3, // type revisit
            'create_by' => $this->user->id
        );
        $reservation = Reservation::add($reservationArray);
        Logging::add([
            'action' => LoggingAction::add_revisit_reservation,
            'table' => 'reservations',
            'ref_id' => $reservation->id,
            'user_id' => $this->user->id,
        ]);
        $reservationData = Reservation::getById($reservation->id, true, true);
        ReservationHistory::add([
            'action' => 'Add',
            'action_by' => $this->user->id,
            'reservation_id' => $reservationData['id'],
            'code' => $reservationData['code'],
            'physician_id' => $reservationData['physician_id'],
            'clinic_id' => $reservationData['clinic_id'],
            'patient_id' => $reservationData['patient_id'],
            'date' => $reservationData['date'],
            'time_from' => $reservationData['revisit_time_from'],
//            'time_to' => $reservationData['time_to'],
            'status' => $reservationData['status'],
            'patient_status' => $reservationData['patient_status'],
        ]);
        $reservationData['date'] = date('dMY', strtotime($inputs['date']));
        $currentTime = strtotime($inputs['time']);
        $futureTime = $currentTime + (60 * 5);
        $reservationData['time_from'] = date("h:ia", strtotime($futureTime));
        if ($inputs['time'] > '23:59:00') {
            $seconds = Functions::hoursToSeconds($inputs['time']);
            $newSeconds = $seconds - (24 * 60 * 60);
            $reservationData['time_from'] = date("h:ia", strtotime(Functions::timeFromSeconds($newSeconds + (10 * 60))));
            if ($reservationData['sms_lang'] == 1) { // arabic
                $reservationData['time_from'] = $reservationData['time_from'] . ' -بعد منتصف الليل-';
            } else {
                $reservationData['time_from'] = $reservationData['time_from'] . ' -after mid night-';
            }
        }
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
        $patientData = Patient::getById($reservationData['patient_id']);
        if ($patientData['gender'] == PatientGender::$genderReturn['Female']) {
            $patientName = 'Ms.' . $patientData['first_name'];
        } else {
            $patientName = 'Mr.' . $patientData['first_name'];
        }
        $reservationData['patient_name'] = $patientName;
        $reservationData['reservationCode'] = $reservationData['code'];
        if (app('send_sms')) {
            $smsArray = array(
                'patient_id' => $reservationData['patient_id'],
                'reservation_id' => $reservation->id,
                'type' => 'Revisit',
            );
            if ($reservationData['sms_lang'] == 1) { // arabic
                $smsArray['message'] = trans('sms.revisit-ar', $reservationData->toArray());
            } else { // english
                $smsArray['message'] = trans('sms.revisit', $reservationData->toArray());
            }
            PatientSMS::add($smsArray);
        }

        Flash::success('Added Successfully');
        return Redirect::back();
    }

    public function getAvailableRevisitTime()
    {
        $inputs = Input::except('_token');
        $phySchedule = PhysicianSchedule::getByPhysicianId_Date($inputs['physician_id'], $inputs['date'], true, $inputs['clinic_id']);
        $CSRepo = new ClinicScheduleRepository();
        if (isset($inputs['stand_alon']) && $inputs['stand_alon']) {
            $clinicSchedule = ClinicSchedule::getById($phySchedule['clinic_schedule_id']);
        } else {
            $reservationData = Reservation::getById($inputs['reservation_id'], false, false);
            $clinicSchedule = $CSRepo->getByClinicId($reservationData['clinic_id'], $inputs['date']);
        }
        $availableTimes = array();
        $timesFree = array();
        $availableSlots = array();
        $html = '<option value="">Not Available</option>';
        if ($phySchedule && $clinicSchedule) {
            $html = '';
            $this->getAvailableTimeOfPhysician($availableTimes, $phySchedule, $clinicSchedule, $inputs['date'], true);
            foreach ($availableTimes as $key => $val) {
                $check1 = (isset($val['status']) && $val['status'] == ReservationStatus::not_available
                    || (isset($val['patient_status']) && $val['patient_status'] == PatientStatus::pending));
                if ($check1) {
                    continue;
                } else {
                    $availableSlots[] = $val['time'];
                }
            }
            if (count($availableSlots) == 1) {
                $acceptFreeSlot = 1;
            } else {
                $revisit_percentage_limit = 30;
                $acceptFreeSlot = (int)round(count($availableSlots) * ($revisit_percentage_limit / 100)); // 30% of all free slots
            }
            $countRevisit = Reservation::getBy([
                'type' => 3,
                'physician_id' => $inputs['physician_id'],
                'clinic_id' => $clinicSchedule['clinic_id'],
                'date_from' => $inputs['date'],
                'date_to' => $inputs['date'],
                'getCount' => true
            ]);
            if ($acceptFreeSlot >= $countRevisit) {
                $acceptFreeSlot -= $countRevisit;
            } else {
                $acceptFreeSlot = 0;
            }
            foreach ($availableTimes as $key => $val) {
                if ((isset($val['status']) && $val['status'] == ReservationStatus::not_available
                        || (isset($val['patient_status']) && $val['patient_status'] == PatientStatus::pending))
                    || (isset($val['type']) && $val['type'] == 'revisit')
                ) {
                    continue;
                } else {
                    if ($inputs['date'] == date('Y-m-d')) {
                        if ($val['time'] < date('H:i:s', strtotime("-30 minutes"))) {
                            continue;
                        }
                    }
                    $timesFree[] = $val['time'];
                }
            }
            if ($acceptFreeSlot) {
                if ($timesFree) {
                    $acceptTime = array();
                    $countTry = 0;
                    for ($i = 0; $i < 500; $i++) {
                        $rand_keys = array_rand($timesFree);
                        if ($acceptTime) {
                            $time = strtotime($timesFree[$rand_keys]);
                            $lastTime = strtotime(end($acceptTime));
                            $diffMinutes = round(abs($lastTime - $time) / 60, 2);
                            if ($diffMinutes < 20) {
                                $countTry++;
                                if ($countTry == 10) {
                                    $countTry = 0;
                                    $acceptTime[] = $timesFree[$rand_keys];
                                    unset($timesFree[$rand_keys]);
                                    if (count($acceptTime) == $acceptFreeSlot) {
                                        break;
                                    }
                                }
                            } else {
                                $countTry = 0;
                                $acceptTime[] = $timesFree[$rand_keys];
                                unset($timesFree[$rand_keys]);
                                if (count($acceptTime) == $acceptFreeSlot) {
                                    break;
                                }
                            }
                        } else {
                            $acceptTime[] = $timesFree[$rand_keys];
                            unset($timesFree[$rand_keys]);
                            if (count($acceptTime) == $acceptFreeSlot) {
                                break;
                            }
                        }
                    }

                    sort($acceptTime);
                    foreach ($acceptTime as $key => $val) {
                        $seconds = Functions::hoursToSeconds($val);
                        $newSeconds = $seconds + (10 * 60);
                        $futureTime = Functions::timeFromSeconds($newSeconds);
                        $html .= '<option value="' . $val . '">' . $futureTime . '</option>';
                    }
                } else {
                    $html = '<option value="">Not Available: No Slots Free</option>';
                }
            } else {
                $html = '<option value="">Not Available: Exceed The Limit No Of Revisits (30%)</option>';
            }
        }
        return $html;
    }

    public function getFirstFreeSlot()
    {
        $inputs = Input::except('_token');
        if (isset($inputs['selectDate']) && $inputs['selectDate']) {
            $date = $inputs['selectDate'];
        } elseif (isset($inputs['date']) && $inputs['date']) {
            $date = $inputs['date'];
        } else {
            $date = date('Y-m-d');
        }
        $count = 0;
        $currentDate = $date;
        while (1 == 1) {
            if ($count == 29) {
                Flash::error('No Slots Are Available!');
                return App::make('ReservationController')->reservationAdd(array(
                    'date' => $currentDate,
                    'selectDate' => $currentDate,
                    'physician_id' => $inputs['physician_id'],
                    'clinic_id' => $inputs['clinic_id'],
                    'hospital_id' => $inputs['hospital_id'],
                ));
            }
            $date = date('Y-m-d', strtotime("+1 day", strtotime($date)));
            $inputs['date'] = $date;
            $inputs['selectDate'] = $date;
            $phySchedule = PhysicianSchedule::getByPhysicianId_Date($inputs['physician_id'], $date, true, $inputs['clinic_id']);
            $CSRepo = new ClinicScheduleRepository();
            $clinicSchedule = $CSRepo->getByClinicId($inputs['clinic_id'], $date);
            $availableTimes = array();
            $this->getAvailableTimeOfPhysician($availableTimes, $phySchedule, $clinicSchedule, $date);
            foreach ($availableTimes as $key => $val) {
                if (!isset($val['reserved'])) {
                    return App::make('ReservationController')->reservationAdd($inputs);
//                    return $controller->callAction('reservationAdd', $parameters = $inputs);
                }
            }
            $count++;
        }
    }

    public function unArchiveReservation()
    {
        $inputs = Input::except('_token');
        $reservation = Reservation::getById($inputs['reservation_id']);
        if ($reservation['status'] == ReservationStatus::archive) {
            $physicianSchedule = PhysicianSchedule::getClinicScheduleIdWithPhysicianId($inputs['schedule_id'],
                $inputs['physician_id'], $inputs['date']);
            $check = PhysicianException::checkSlotClosedByException($inputs['physician_id'], $inputs['date'], $reservation['time_from'],
                $physicianSchedule['slots'], null, $physicianSchedule); // new_exceptions
            if (isset($check['close'])) {
                if ($check['close']) {
                    if ($check['effect']) {
                        // effect
                        Reservation::edit(array(
                            'status' => ReservationStatus::reserved,
                            'patient_status' => PatientStatus::pending,
                            'exception_reason' => $check['reason'],
                            'show_reason' => 1,
                        ), $inputs['reservation_id']);
                        ReservationHistory::add([
                            'action' => 'Pending From Un Archive',
                            'action_by' => $this->user->id,
                            'reservation_id' => $reservation['id'],
                            'code' => $reservation['code'],
                            'physician_id' => $reservation['physician_id'],
                            'clinic_id' => $reservation['clinic_id'],
                            'patient_id' => $reservation['patient_id'],
                            'date' => $reservation['date'],
                            'time_from' => $reservation['time_from'],
                            'time_to' => $reservation['time_to'],
                            'status' => ReservationStatus::reserved,
                            'patient_status' => PatientStatus::pending,
                        ]);
                    } else {
                        // non effect
                        Reservation::edit(array(
                            'status' => ReservationStatus::reserved,
                            'patient_status' => PatientStatus::waiting,
                            'exception_reason' => $check['reason'],
                            'show_reason' => 1,
                        ), $inputs['reservation_id']);
                        ReservationHistory::add([
                            'action' => 'Un Archive',
                            'action_by' => $this->user->id,
                            'reservation_id' => $reservation['id'],
                            'code' => $reservation['code'],
                            'physician_id' => $reservation['physician_id'],
                            'clinic_id' => $reservation['clinic_id'],
                            'patient_id' => $reservation['patient_id'],
                            'date' => $reservation['date'],
                            'time_from' => $reservation['time_from'],
                            'time_to' => $reservation['time_to'],
                            'status' => ReservationStatus::reserved,
                            'patient_status' => PatientStatus::waiting,
                        ]);
                    }
                } else {
                    Reservation::edit(array(
                        'status' => ReservationStatus::reserved,
                        'patient_status' => PatientStatus::waiting,
                        'show_reason' => 0,
                    ), $inputs['reservation_id']);
                    ReservationHistory::add([
                        'action' => 'Un Archive',
                        'action_by' => $this->user->id,
                        'reservation_id' => $reservation['id'],
                        'code' => $reservation['code'],
                        'physician_id' => $reservation['physician_id'],
                        'clinic_id' => $reservation['clinic_id'],
                        'patient_id' => $reservation['patient_id'],
                        'date' => $reservation['date'],
                        'time_from' => $reservation['time_from'],
                        'time_to' => $reservation['time_to'],
                        'status' => ReservationStatus::reserved,
                        'patient_status' => PatientStatus::waiting,
                    ]);
                }
            }

        }
        return 1;
    }

    public function updateRevisitReservation()
    {
        $inputs = Input::except('_token');
        // check if exceed 30% of num slots
        $reservation = Reservation::getById($inputs['reservation_id'], false);
        $phySchedule = PhysicianSchedule::getByPhysicianId_Date($reservation['physician_id'], $inputs['date'], true, $reservation['clinic_id']);
        $CSRepo = new ClinicScheduleRepository();
        $clinicSchedule = $CSRepo->getByClinicId($reservation['clinic_id'], $inputs['date']);
        $availableTimes = array();
        $timesFree = array();
        $availableSlots = array();
        if ($phySchedule && $clinicSchedule) {
            $this->getAvailableTimeOfPhysician($availableTimes, $phySchedule, $clinicSchedule, $inputs['date'], true);
            foreach ($availableTimes as $key => $val) {
                $check1 = (isset($val['status']) && $val['status'] == ReservationStatus::not_available
                    || (isset($val['patient_status']) && $val['patient_status'] == PatientStatus::pending));
                if ($check1) {
                    continue;
                } else {
                    $availableSlots[] = $val['time'];
                }
            }
            if (count($availableSlots) == 1) {
                $acceptFreeSlot = 1;
            } else {
                $revisit_percentage_limit = 30;
                $acceptFreeSlot = (int)round(count($availableSlots) * ($revisit_percentage_limit / 100)); // 30% of all free slots
            }
            $countRevisit = Reservation::getBy([
                'type' => 3,
                'physician_id' => $inputs['physician_id'],
                'clinic_id' => $clinicSchedule['clinic_id'],
                'date_from' => $inputs['date'],
                'date_to' => $inputs['date'],
                'getCount' => true
            ]);
            if ($acceptFreeSlot >= $countRevisit) {
                $acceptFreeSlot -= $countRevisit;
            } else {
                $acceptFreeSlot = 0;
            }

            foreach ($availableTimes as $key => $val) {
                if ((isset($val['status']) && $val['status'] == ReservationStatus::not_available
                        || (isset($val['patient_status']) && $val['patient_status'] == PatientStatus::pending))
                    || (isset($val['type']) && $val['type'] == 'revisit')
                ) {
                    continue;
                } else {
                    $timesFree[] = $val['time'];
                }
            }
        }
        if ($timesFree) {
            if ($acceptFreeSlot < 1) {
                Flash::error('Not Successfully; Exceed The Limit Num Of Revisits (30%)');
                return Redirect::back();
            }
        }
        /////////////////////////////////////////

        $reservationArray = array(
            'show_reason' => 2,
            'update_by' => $this->user->id,
            'status' => ReservationStatus::reserved,
            'patient_status' => PatientStatus::waiting,
            'date' => $inputs['date'],
            'physician_id' => $inputs['physician_id'],
            'revisit_time_from' => $inputs['time'],
        );
        Reservation::edit($reservationArray, $inputs['reservation_id']);
        Logging::add([
            'action' => LoggingAction::update_revisit_reservation,
            'table' => 'reservations',
            'ref_id' => $inputs['reservation_id'],
            'user_id' => $this->user->id,
        ]);
        $reservationData = Reservation::getById($inputs['reservation_id'], false);
        ReservationHistory::add([
            'action' => 'Update',
            'action_by' => $this->user->id,
            'reservation_id' => $reservationData['id'],
            'code' => $reservationData['code'],
            'physician_id' => $reservationData['physician_id'],
            'clinic_id' => $reservationData['clinic_id'],
            'patient_id' => $reservationData['patient_id'],
            'date' => $reservationData['date'],
            'time_from' => $reservationData['revisit_time_from'],
//            'time_to' => $reservationData['time_to'],
            'status' => $reservationData['status'],
            'patient_status' => $reservationData['patient_status'],
        ]);
        Flash::success('Updated Successfully');
        return Redirect::back();
    }

    public function standAlonRevisitReservation()
    {
        $inputs = Input::except('_token');
        $patient_id = $inputs['patient_id'];
        $physician_id = $inputs['physician_id'];
        $clinic_id = $inputs['clinic_id'];

        $fName = $inputs['first_name'] ? $inputs['first_name'] : '';
        $mName = $inputs['middle_name'] ? ' ' . $inputs['middle_name'] : '';
        $lName = $inputs['last_name'] ? ' ' . $inputs['last_name'] : '';
        $fmName = $inputs['family_name'] ? ' ' . $inputs['family_name'] : '';
        $name = $fName . $mName . $lName . $fmName;

        $interval = Functions::getAgeDetails($inputs['birthday']);
        $age = '';
        $age_type_id = '';
        if ($interval->y) {
            $age = $interval->y;
            $age_type_id = AgeType::$ageReturn['Years'];
        } else {
            if ($interval->m) {
                $age = $interval->m;
                $age_type_id = AgeType::$ageReturn['Months'];
            } else {
                if ($interval->d) {
                    $age = $interval->d;
                    $age_type_id = AgeType::$ageReturn['Days'];
                } else {
                    if ($interval->h) {
                        $age = $interval->h;
                        $age_type_id = AgeType::$ageReturn['Hours'];
                    }
                }
            }
        }
        if (isset($inputs['birthday']) && $inputs['birthday']) {
            if ($inputs['birthday'] == '0000-00-00') {
                $inputs['birthday'] = null;
            }
        } else {
            $inputs['birthday'] = null;
        }
        if (isset($inputs['phone2']) && $inputs['phone2']) {
            $inputs['phone'] = $inputs['phone2'];
        }
        if (!$patient_id) {
            $patient = Patient::add(array(
                'hospital_id' => $inputs['hospital_id'],
                'phone' => $inputs['phone'],
                'name' => $name,
                'first_name' => $fName,
                'middle_name' => $mName,
                'last_name' => $lName,
                'family_name' => $fmName,
                'national_id' => $inputs['national_id'],
                'birthday' => $inputs['birthday'],
                'age' => $age,
                'age_type_id' => $age_type_id,
                'age_year' => $interval->y,
                'age_month' => $interval->m,
                'age_day' => $interval->d,
                'age_hour' => $interval->h,
                'email' => $inputs['email'],
                'gender' => $inputs['gender'],
                'address' => $inputs['address'],
                'sync_flag' => 0,
            ));
            $patient_id = $patient->id;
            Logging::add([
                'action' => LoggingAction::add_patient,
                'table' => 'patients',
                'ref_id' => $patient_id,
                'user_id' => $this->user->id,
            ]);
        } else {
            $patientArray = array();
            $patient = Patient::getById($patient_id);
            if (app('production') && empty($patient['registration_no'])) {
                $patientArray['sync_flag'] = 0;
            }
            if (isset($inputs['phone2']) && $inputs['phone2']) {
                $patientArray['phone'] = $inputs['phone2'];
            }
            if ($patientArray) {
                Patient::edit($patientArray, $patient_id);
            }
        }
        $clinicData = Clinic::getById($clinic_id);
        $countReservations = Reservation::getCountByClinicAndData($clinic_id, date('Y-m-d'));
        $reservationCode = $clinicData['code'] . '-' . Functions::make3D($countReservations + 1) . '-' . date('ymd');
        $reservationArray = array(
            'code' => $reservationCode,
            'physician_id' => $physician_id,
            'clinic_id' => $clinic_id,
            'patient_id' => $patient_id,
            'date' => $inputs['date'],
            'type' => 3, // revisit
            'create_by' => $this->user->id,
            'revisit_time_from' => $inputs['time'],
        );
        if (isset($inputs['sms_lang']) && $inputs['sms_lang']) {
            $reservationArray['sms_lang'] = $inputs['sms_lang'];
        }
        $reservation = Reservation::add($reservationArray);
        Logging::add([
            'action' => LoggingAction::add_stand_alone_reservation,
            'table' => 'reservations',
            'ref_id' => $reservation->id,
            'user_id' => $this->user->id,
        ]);
        $reservationData = Reservation::getById($reservation->id, false);
        ReservationHistory::add([
            'action' => 'Add',
            'action_by' => $this->user->id,
            'reservation_id' => $reservationData['id'],
            'code' => $reservationData['code'],
            'physician_id' => $reservationData['physician_id'],
            'clinic_id' => $reservationData['clinic_id'],
            'patient_id' => $reservationData['patient_id'],
            'date' => $reservationData['date'],
            'time_from' => $reservationData['revisit_time_from'],
//            'time_to' => $reservationData['time_to'],
            'status' => $reservationData['status'],
            'patient_status' => $reservationData['patient_status'],
        ]);
        if ($inputs['hospital_id'] == 2) {
            $reservationData = Reservation::getById($reservation->id, true, true);
            $reservationData['date'] = date('dMY', strtotime($inputs['date']));
            $reservationData['time_from'] = date('h:ia', strtotime($inputs['time']));
            $currentTime = strtotime($reservationData['time_from']);
            $futureTime = $currentTime + (60 * 5);
            $reservationData['time_from'] = date("h:ia", $futureTime);
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
            $patientData = Patient::getById($reservationData['patient_id']);
            if ($patientData['gender'] == PatientGender::$genderReturn['Female']) {
                $patientName = 'Ms.' . $patientData['first_name'];
            } else {
                $patientName = 'Mr.' . $patientData['first_name'];
            }
            $reservationData['patient_name'] = $patientName;
            $reservationData['reservationCode'] = $reservationData['code'];
            if (app('send_sms')) {
                $smsArray = array(
                    'patient_id' => $reservationData['patient_id'],
                    'reservation_id' => $reservation->id,
                    'type' => 'Revisit',
                );
                if ($reservationData['sms_lang'] == 1) { // arabic
                    $smsArray['message'] = trans('sms.revisit-ar', $reservationData->toArray());
                } else { // english
                    $smsArray['message'] = trans('sms.revisit', $reservationData->toArray());
                }
                PatientSMS::add($smsArray);
            }
        }

        Flash::success('Added Successfully');
        return Redirect::back();
    }

    public function resendLastSms()
    {
        $inputs = Input::except('_token');
        $sms = PatientSMS::getByReservationId($inputs['reservation_id'])->toArray();
        $sms = end($sms);
        if ($sms) {
            $reservationData = Reservation::getById($inputs['reservation_id'], true, true);
            if ($reservationData['sms_lang'] != $inputs['sms_lang']) {
                Reservation::edit(array('sms_lang' => $inputs['sms_lang']), $inputs['reservation_id']);
            }
            if ($reservationData['time_from'] > '23:59:00') {
                $seconds = Functions::hoursToSeconds($reservationData['time_from']);
                $newSeconds = $seconds - (24 * 60 * 60);
                $reservationData['time_from'] = date('h:ia', strtotime(Functions::timeFromSeconds($newSeconds)));
                if ($inputs['sms_lang'] == 1) { // arabic
                    $reservationData['time_from'] = $reservationData['time_from'] . ' -بعد منتصف الليل-';
                } else {
                    $reservationData['time_from'] = $reservationData['time_from'] . ' -after mid night-';
                }
            } else {
                $reservationData['time_from'] = date('h:ia', strtotime($reservationData['time_from']));
            }
            $reservationData['date'] = date('dMY', strtotime($reservationData['date']));
            $clinicData = Clinic::getById($reservationData['clinic_id']);
            $physicianData = User::getById($reservationData['physician_id']);
            if ($inputs['sms_lang'] == 1) { // arabic
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
            $patientData = Patient::getById($reservationData['patient_id']);
            if ($patientData['gender'] == PatientGender::$genderReturn['Female']) {
                $patientName = 'Ms.' . $patientData['first_name'];
            } else {
                $patientName = 'Mr.' . $patientData['first_name'];
            }
            $reservationData['patient_name'] = $patientName;
            $resCode = explode('-', $reservationData['code']);
            if ($inputs['sms_lang'] == 1) { // arabic
                $reservationData['reservationCode'] = $resCode[1] . '-' . $resCode[0];
            } else {
                $reservationData['reservationCode'] = $resCode[0] . '-' . $resCode[1];
            }
            if (app('send_sms')) {
                $smsArray = array(
                    'patient_id' => $reservationData['patient_id'],
                    'reservation_id' => $inputs['reservation_id'],
                );
                if ($sms['type'] == 'Create') {
                    $smsArray['type'] = 'Create';
                    if ($inputs['sms_lang'] == 1) { // arabic
                        $smsArray['message'] = trans('sms.create-ar', $reservationData->toArray());
                    } else { // english
                        $smsArray['message'] = trans('sms.create', $reservationData->toArray());
                    }
                } elseif ($sms['type'] == 'Revisit') {
                    $smsArray['type'] = 'Revisit';
                    $seconds = Functions::hoursToSeconds($reservationData['revisit_time_from']);
                    $newSeconds = $seconds + (10 * 60);
                    $futureTime = Functions::timeFromSeconds($newSeconds);
                    $reservationData['time_from'] = date("h:ia", strtotime($futureTime));
                    if ($reservationData['time_from'] > '23:59:00') {
                        $seconds = Functions::hoursToSeconds($reservationData['revisit_time_from']);
                        $newSeconds = $seconds - (24 * 60 * 60);
                        $reservationData['time_from'] = date("h:ia", strtotime(Functions::timeFromSeconds($newSeconds + (10 * 60))));
                        if ($inputs['sms_lang'] == 1) { // arabic
                            $reservationData['time_from'] = $reservationData['time_from'] . ' -بعد منتصف الليل-';
                        } else {
                            $reservationData['time_from'] = $reservationData['time_from'] . ' -after mid night-';
                        }
                    }
                    if ($inputs['sms_lang'] == 1) { // arabic
                        $smsArray['message'] = trans('sms.revisit-ar', $reservationData->toArray());
                    } else { // english
                        $smsArray['message'] = trans('sms.revisit', $reservationData->toArray());
                    }
                } elseif ($sms['type'] == 'Edit Revisit') {
                    $smsArray['type'] = 'Edit Revisit';
                    $seconds = Functions::hoursToSeconds($reservationData['revisit_time_from']);
                    $newSeconds = $seconds + (10 * 60);
                    $futureTime = Functions::timeFromSeconds($newSeconds);
                    $reservationData['time_from'] = date("h:ia", strtotime($futureTime));
                    if ($reservationData['time_from'] > '23:59:00') {
                        $seconds = Functions::hoursToSeconds($reservationData['revisit_time_from']);
                        $newSeconds = $seconds - (24 * 60 * 60);
                        $reservationData['time_from'] = date("h:ia", strtotime(Functions::timeFromSeconds($newSeconds + (10 * 60))));
                        if ($inputs['sms_lang'] == 1) { // arabic
                            $reservationData['time_from'] = $reservationData['time_from'] . ' -بعد منتصف الليل-';
                        } else {
                            $reservationData['time_from'] = $reservationData['time_from'] . ' -after mid night-';
                        }
                    }
                    if ($inputs['sms_lang'] == 1) { // arabic
                        $smsArray['message'] = trans('sms.edit-revisit-ar', $reservationData->toArray());
                    } else { // english
                        $smsArray['message'] = trans('sms.edit-revisit', $reservationData->toArray());
                    }
                } elseif ($sms['type'] == 'Cancel') {
                    $smsArray['type'] = 'Cancel';
                    if ($inputs['sms_lang'] == 1) { // arabic
                        $smsArray['message'] = trans('sms.cancel-ar', $reservationData->toArray());
                    } else { // english
                        $smsArray['message'] = trans('sms.cancel', $reservationData->toArray());
                    }
                } elseif ($sms['type'] == 'Modify') {
                    $smsArray['type'] = 'Modify';
                    if ($inputs['sms_lang'] == 1) { // arabic
                        $smsArray['message'] = trans('sms.modify-ar', $reservationData->toArray());
                    } else { // english
                        $smsArray['message'] = trans('sms.modify', $reservationData->toArray());
                    }
                } elseif ($sms['type'] == 'Pending_Exception') {
                    $smsArray['type'] = 'Pending_Exception';
                    if ($inputs['sms_lang'] == 1) { // arabic
                        $smsArray['message'] = trans('sms.pending-ar', $reservationData->toArray());
                    } else { // english
                        $smsArray['message'] = trans('sms.pending', $reservationData->toArray());
                    }
                }
                if (isset($smsArray['type'])) {
                    PatientSMS::add($smsArray);
                    Logging::add([
                        'action' => LoggingAction::resend_sms_reservation,
                        'table' => 'reservations',
                        'ref_id' => $inputs['reservation_id'],
                        'user_id' => $this->user->id,
                    ]);
                }
//                else {
//                    PatientSMS::edit(array(
//                        'send' => 0
//                    ), $sms['id']);
//                }
            }

            Flash::success('Resend SMS Successfully');
            return Redirect::back();
        } else {
            Flash::error('Resend SMS Unsuccessfully!');
            return Redirect::back();
        }
    }
}
