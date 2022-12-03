<?php

use core\clinic\ClinicRepository;
use core\clinicSchedule\ClinicScheduleRepository;
use core\enums\PatientGender;
use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use core\physician\PhysicianManager;
use core\user\UserRepository;

class WebsiteAPIController extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function webSiteAPIGetHospitals()
    {
        header("Access-Control-Allow-Origin: *");
        if (!Input::has('lang')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: lang'));
        }
        $hospitals = Hospital::getAll();
        $lang = Input::get('lang');
        if (isset($lang) && $lang == 'ar') {
            $hospitalsArray[0] = ['id' => '', 'value' => 'إختر'];
        } else {
            $hospitalsArray[0] = ['id' => '', 'value' => 'Choose'];
        }
        foreach ($hospitals as $key => $val) {
            $count = $key + 1;
            $hospitalsArray[$count] = ['id' => $val['id'], 'value' => $val['name']];
        }
        return json_encode(array('status' => '1', 'response' => $hospitalsArray));
    }

    public function webSiteAPIGetClinicByHospital()
    {
        header("Access-Control-Allow-Origin: *");
        if (!Input::has('lang')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: lang'));
        }
        $clinics = Clinic::getAllByHospitalId(2);
        $lang = Input::get('lang');
        if (isset($lang) && $lang == 'ar') {
            $clinicArray[0] = ['id' => '', 'value' => 'إختر'];
        } else {
            $clinicArray[0] = ['id' => '', 'value' => 'Choose'];
        }
        foreach ($clinics as $key => $val) {
            $count = $key + 1;
            if (isset($lang) && $lang == 'ar') {
                $clinicArray[$count] = ['id' => $val['id'], 'value' => $val['name_ar']];
            } else {
                $clinicArray[$count] = ['id' => $val['id'], 'value' => $val['name']];
            }
        }
        return json_encode(array('status' => '1', 'response' => $clinicArray));
    }

    public function webSiteAPIGetPhysicianByClinic()
    {
        header("Access-Control-Allow-Origin: *");
        if (!Input::has('lang')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: lang'));
        }
        if (!Input::has('clinic_id')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: clinic_id'));
        }
        $inputs = Input::except('_token');
        $inputs['hospital_id'] = 2;
        $clinic_id = $inputs['clinic_id'];
        $lang = $inputs['lang'];
        $physiciansIds = User::getPhysicianByClinicId($clinic_id);
        $physicians = User::getByIds($physiciansIds);
        if (empty($physicians)) {
            if ($lang == 'ar') {
                return json_encode(array('status' => '0', 'response' => '', 'msg' => 'لا يوجد دكاتره بهذه المدخلات'));
            } else {
                return json_encode(array('status' => '0', 'response' => '', 'msg' => 'No Physicians With This Criteria'));
            }
        }
        if ($lang == 'ar') {
            $physicianArray[0] = ['id' => '', 'value' => 'إختر'];
        } else {
            $physicianArray[0] = ['id' => '', 'value' => 'Choose'];
        }
        foreach ($physicians as $key => $val) {
            $count = $key + 1;
            if ($lang == 'ar') {
                $physicianArray[$count] = ['id' => $val['id'], 'value' => $val['first_name_ar'] . ' ' . $val['last_name_ar']];
            } else {
                $physicianArray[$count] = ['id' => $val['id'], 'value' => $val['full_name']];
            }
        }
        return json_encode(array('status' => '1', 'response' => $physicianArray));
    }

    public function webSiteAPIGetSlotsOfPhysician()
    {
        header("Access-Control-Allow-Origin: *");
        if (!Input::has('lang')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: lang'));
        }
        if (!Input::has('clinic_id')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: clinic_id'));
        }
        if (!Input::has('physician_id')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: physician_id'));
        }
        if (!Input::has('date')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: date'));
        }
        $inputs = Input::except('_token');
        $inputs['hospital_id'] = 2;
        $clinic_id = $inputs['clinic_id'];
        $physician_id = $inputs['physician_id'];
        $date = $inputs['date'];
        $lang = $inputs['lang'];
        $CSRepo = new ClinicScheduleRepository();
        $clinicSchedule = $CSRepo->getByClinicId($clinic_id, $date);
        if (empty($clinicSchedule)) {
            if ($inputs['lang'] == 'en') {
                $msg = 'Empty Clinic Schedule';
            } else {
                $msg = 'لا يوجد جدول مواعيد لهذه العيادة';
            }
            return json_encode(array('status' => '0', 'response' => '', 'msg' => $msg));
        }
        $userRepo = new UserRepository();
        $physician_selected = $userRepo->getByUserIdWithSchedule($physician_id, $clinicSchedule, $date);
        if (isset($physician_selected['schedules'][0])) {
            $physicianSchedule = $physician_selected['schedules'][0];
            if (empty($physicianSchedule)) {
                if ($inputs['lang'] == 'en') {
                    $msg = 'Empty Physician Schedule';
                } else {
                    $msg = 'لا يوجد جدول مواعيد لهذا الدكتور';
                }
                return json_encode(array('status' => '0', 'response' => '', 'msg' => $msg));
            }
            $physicianManager = new PhysicianManager();
            $availableTimes = array();
            $physicianManager->getAvailableTimeOfPhysician($availableTimes, $physicianSchedule, $clinicSchedule, $date);
            if (empty($availableTimes)) {
                if ($inputs['lang'] == 'en') {
                    $msg = 'Physician Day Off';
                } else {
                    $msg = 'هذا اليوم أجازه للدكتور';
                }
                return json_encode(array('status' => '0', 'response' => '', 'msg' => $msg));
            }
            $data['availableTimes'] = $availableTimes;
            $data['selectDate'] = $date;
            $data['slots'] = $physicianSchedule['slots'];
            $data['lang'] = $lang;
            $data['clinic'] = Clinic::getById($inputs['clinic_id']);
            $data['physician'] = User::getById($inputs['physician_id']);
            $freeTimesCounts = 0;
            $freeTimes = [];
            foreach ($availableTimes as $key => $val) {
                if (isset($val['reserved'])) {
                    continue;
                }
                if ($val['time'] >= date('H:i:s')) {
                    if ($val['time'] > '23:59:00') {
                        $seconds = Functions::hoursToSeconds($val['time']);
                        $newSeconds = $seconds - (24 * 60 * 60);
                        $freeTimes[$freeTimesCounts]['time_from'] = Functions::timeFromSeconds($newSeconds);
                    } else {
                        $freeTimes[$freeTimesCounts]['time_from'] = $val['time'];
                    }
                    if (isset($val['time_to'])) {
                        if ($val['time_to'] > '23:59:00') {
                            $seconds = Functions::hoursToSeconds($val['time_to']);
                            $newSeconds = $seconds - (24 * 60 * 60);
                            $freeTimes[$freeTimesCounts]['time_to'] = Functions::timeFromSeconds($newSeconds);
                        } else {
                            $freeTimes[$freeTimesCounts]['time_to'] = $val['time_to'];
                        }
                    } else {
                        $seconds = Functions::hoursToSeconds($val['time']);
                        $newSeconds = $seconds + ($physicianSchedule['slots'] * 60);
                        $time_to = Functions::timeFromSeconds($newSeconds);
                        if ($time_to > '23:59:00') {
                            $seconds = Functions::hoursToSeconds($time_to);
                            $newSeconds = $seconds - (24 * 60 * 60);
                            $time_to = Functions::timeFromSeconds($newSeconds);
                        }
                        $freeTimes[$freeTimesCounts]['time_to'] = $time_to;
                    }
                    $freeTimesCounts++;
                }
            }
//            $html = View::make('api/websiteAPI/physician_time', $data)->render();
            return json_encode(array('status' => '1', 'response' => $freeTimes));

        } else {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Empty Physician Schedule'));
        }
    }

    public function webSiteAPICheckPatient()
    {
        header("Access-Control-Allow-Origin: *");
        if (!Input::has('lang')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: lang'));
        }
        if (!Input::has('patient_id')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: patient_id'));
        }
        if (!Input::has('patient_phone')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: patient_phone'));
        }
        $inputs = Input::except('_token');
        $inputs['hospital_id'] = 2;
        $patient = Patient::getByPinNo($inputs['patient_id'], $inputs['hospital_id'], ['phone' => $inputs['patient_phone']]);
        if ($patient) {
//            $reservationsArray = $this->getReservationHistory($inputs, $patient);
            return json_encode(array('status' => '1', 'response' => $patient->toArray()));
        } else {
            if ($inputs['lang'] == 'en') {
                $msg = 'No Patient Found With This Credentials';
            } else {
                $msg = 'لا يوجد مريض بهذه المدخلات';
            }
            return json_encode(array('status' => '0', 'response' => '', 'msg' => $msg));
        }
    }

    public function getReservationHistory($inputs = [], $patient)
    {
        $reservations = Reservation::getByPatientsIdAndDates([
            'hospital_id' => $inputs['hospital_id'],
            'id' => $inputs['patient_id'],
            'phoneExact' => $inputs['patient_phone'],
        ], false);
        $reservationsArray = [];
        foreach ($reservations as $key => $val) {
            $reservationsArray[$key]['clinic_name'] = Clinic::getNameById($val['clinic_id']);
            $reservationsArray[$key]['physician_name'] = User::getNameById($val['physician_id']);
            $reservationsArray[$key]['patient_name'] = Patient::getName($val['patient_id']);
            $reservationsArray[$key]['date'] = $val['date'];
            $reservationsArray[$key]['time_from'] = $val['time_from'];
            if ($val['type'] == 3) {
                $seconds = Functions::hoursToSeconds($val['revisit_time_from']);
                $newSeconds = $seconds + (10 * 60);
                $val['time_from'] = Functions::timeFromSeconds($newSeconds);
            }
            if ($val['time_from'] && $val['time_from'] > '23:59:00') {
                $seconds = Functions::hoursToSeconds($val['time_from']);
                $newSeconds = $seconds - (24 * 60 * 60);
                $val['time_from'] = Functions::timeFromSeconds($newSeconds);
            }
            $reservationsArray[$key]['time_from'] = $val['time_from'];
        }
        return $reservationsArray;
    }

    public function webSiteAPIAddReservation()
    {
        header("Access-Control-Allow-Origin: *");
        if (!Input::has('lang')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: lang'));
        }
        if (!Input::has('clinic_id')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: clinic_id'));
        }
        if (!Input::has('physician_id')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: physician_id'));
        }
        if (!Input::has('patient_id')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: patient_id'));
        }
        if (!Input::has('date')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: date'));
        }
        if (!Input::has('time_from')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: time_from'));
        }
        if (!Input::has('time_to')) {
            return json_encode(array('status' => '0', 'response' => '', 'msg' => 'Missing Parameter: time_to'));
        }
        $inputs = Input::except('_token');
        $inputs['hospital_id'] = 2;
        $patient = Patient::getByPinNo($inputs['patient_id'], $inputs['hospital_id']);
        if (empty($patient)) {
            if ($inputs['lang'] == 'en') {
                $msg = 'No Patient Found With This Credentials';
            } else {
                $msg = 'لا يوجد مريض بهذه المدخلات';
            }
            return json_encode(array('status' => '0', 'response' => '', 'msg' => $msg));
        }
        if (Reservation::checkExistRecord($inputs['clinic_id'], $inputs['physician_id'], $inputs['date'], null, null, $patient['id'])) {
            if ($inputs['lang'] == 'en') {
                $msg = 'You Already Booked A Reservation Today In This Clinic';
            } else {
                $msg = 'لقد تم بالفعل حجز معاد لك اليوم فى هذه العياده';
            }
            return json_encode(array('status' => '0', 'response' => '', 'msg' => $msg));
        }
        if (Reservation::checkExistRecord($inputs['clinic_id'], $inputs['physician_id'], $inputs['date'], $inputs['time_from'], $inputs['time_to'])) {
            if ($inputs['lang'] == 'en') {
                $msg = 'This Slot Already Taken By Another Patient';
            } else {
                $msg = 'هذا الوقت تم حجزه, من فضلك اختر معاد أخر';
            }
            return json_encode(array('status' => '0', 'response' => '', 'msg' => $msg));
        }
        if ($patient) {
            $numOfOnlineBooked = Reservation::getCountOnlineReservationOfPatient([
                'patient_id' => $patient['id'],
                'from_date' => date('Y-m-01', strtotime(date('Y-m-d'))),
                'to_date' => date('Y-m-t', strtotime(date('Y-m-d'))),
            ]);
            if ($numOfOnlineBooked >= 4) {
                if ($inputs['lang'] == 'en') {
                    $msg = 'You Have Reached The Maximum Number Of Online Reservation In This Month (3), Please Contact Us With Our Hot Line';
                } else {
                    $msg = 'لقد وصلت إلى الحد الأقصى لعدد الحجوزات الشهريه (3) من الموقع, من فضلك اتصل بنا على الخط الساخن';
                }
                return json_encode(array('status' => '0', 'response' => '', 'msg' => $msg));
            }
            $clinicData = Clinic::getById($inputs['clinic_id']);
            $countReservations = Reservation::getCountByClinicAndData($inputs['clinic_id'], $inputs['date']);
            $reservationCode = $clinicData['code'] . '-' . Functions::make3D(++$countReservations) . '-' . date('ymd', strtotime($inputs['date']));
            $reservationArray = array(
                'code' => $reservationCode,
                'physician_id' => $inputs['physician_id'],
                'clinic_id' => $inputs['clinic_id'],
                'patient_id' => $patient['id'],
                'date' => $inputs['date'],
                'time_from' => $inputs['time_from'],
                'time_to' => $inputs['time_to'],
                'source_type' => 2,
            );
            if ($inputs['lang'] == 'ar') { // arabic
                $reservationArray['sms_lang'] = 1;
            } else {
                $reservationArray['sms_lang'] = 2;
            }
            $reservation = Reservation::add($reservationArray);
            $reservationData = Reservation::getById($reservation->id, true, true);
            if ($inputs['time_from'] > '23:59:00') {
                $seconds = Functions::hoursToSeconds($inputs['time_from']);
                $newSeconds = $seconds - (24 * 60 * 60);
                $reservationData['time_from'] = date('h:ia', strtotime(Functions::timeFromSeconds($newSeconds)));
                if ($inputs['lang'] == 'ar') { // arabic
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
            if ($inputs['lang'] == 'ar') { // arabic
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
            $patientData = Patient::getById($patient['id']);
            if ($patientData['gender'] == PatientGender::$genderReturn['Female']) {
                $patientName = 'Ms.' . $patientData['first_name'];
            } else {
                $patientName = 'Mr.' . $patientData['first_name'];
            }
            $reservationData['patient_name'] = $patientName;
            $reservationData['reservationCode'] = $reservationCode;
            if (app('send_sms')) {
                $smsArray = array(
                    'patient_id' => $patient['id'],
                    'reservation_id' => $reservation->id,
                    'type' => 'Create',
                );
                if ($inputs['lang'] == 'ar') { // arabic
                    $smsArray['message'] = trans('sms.create-ar', $reservationData->toArray());
                } else { // english
                    $smsArray['message'] = trans('sms.create', $reservationData->toArray());
                }
                PatientSMS::add($smsArray);
            }
            if ($inputs['lang'] == 'en') {
                $msg = 'Reservation Has Been Done Successfully';
            } else {
                $msg = 'تم الحجز بنجاح';
            }
            return json_encode(array('status' => '1', 'response' => '', 'msg' => $msg));
        } else {
            if ($inputs['lang'] == 'en') {
                $msg = 'Something Was Error! Please Contact With Our Hot Line';
            } else {
                $msg = 'حدث خطأ فى الحجز! من فضلك اتصل بنا على الخط الساخن';
            }
            return json_encode(array('status' => '0', 'response' => '', 'msg' => $msg));
        }
    }

    public function webSiteAPIGetPatientInstructions()
    {
        header("Access-Control-Allow-Origin: *");
        $inputs = Input::except('_token');
        $instructions = PatientInstruction::getById(1);
        if ($inputs['lang'] == 'ar') { // arabic
            return json_encode(array('status' => '1', 'response' => $instructions['patient_instruction_ar']));
        } else {
            return json_encode(array('status' => '1', 'response' => $instructions['patient_instruction_en']));
        }
    }
}
