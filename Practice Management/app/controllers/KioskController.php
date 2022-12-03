<?php


use core\enums\AttributeType;
use core\enums\LoggingAction;
use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use core\enums\UserRules;

class KioskController extends BaseController
{

    function __construct()
    {
        parent::__construct();
    }

    public function kioskStep1()
    {
        $data['buttons'] = View::make('kiosk/step1_buttons')->render();
        return View::make('kiosk/step1', $data);
    }

    public function kioskBack()
    {
        return View::make('kiosk/step1_buttons')->render();
    }

    public function kioskNoReservation()
    {

//        $receptions = IpToReception::getAll(array(
//            'no_reservation' => 1
//        ));
//        if (Session::has('curr_no_res_queue')) {
//            $curr_no_res_queue = Session::get('curr_no_res_queue');
//        } else {
//            Session::put('curr_no_res_queue', -1);
//            $curr_no_res_queue = -1;
//        }
//        if (isset($receptions[$curr_no_res_queue + 1])) {
//            $currReception = $receptions[$curr_no_res_queue + 1];
//            Session::put('curr_no_res_queue', $curr_no_res_queue + 1);
//        } else {
//            $currReception = $receptions[0];
//            Session::put('curr_no_res_queue', 0);
//        }
        $numOfNoPatient = NoReservationQueue::getAll([
            'hospital_id' => 1, // cairo
//            'reception_ip' => $currReception['ip'],
            'date' => date('Y-m-d'),
            'getCount' => true,
        ]);
        $queueCode = Functions::make3D($numOfNoPatient + 1);
        try {
            $kiosk = KioskToPrinter::getAll([
                'hospital_id' => 1,
                'ip' => Functions::GetClientIp(),
                'getFirst' => true,
            ]);
            if ($kiosk) {
                $printer = IpToPrinter::getById($kiosk['printer_id']);
                if ($printer) {
                    EPSON::noReservationPrint($queueCode, $printer['ip']);
                    $array = [
                        'hospital_id' => 1,
//                        'reception_ip' => $currReception['ip'],
                        'queue_code' => $queueCode,
                        'date' => date('Y-m-d'),
                    ];
                    NoReservationQueue::add($array);
                    $data['success'] = 'yes';
                } else {
                    $data['success'] = 'no';
                    $data['msg'] = 'This Kiosk Does Not Have Printer!';
                }
            } else {
                $data['success'] = 'no';
                $data['msg'] = 'This Kiosk Does Not Have Printer!';
            }
        } catch (Exception $e) {
            $data['success'] = 'no';
            $data['msg'] = 'Error In Printing Ticket: Connection Error Founded!';
        }
        return $data;
    }

    public function kioskGetWithReservation()
    {
        $inputs = Input::except('_token');
        $data['inputs'] = $inputs;
        return View::make('kiosk/list_with_reservation', $data);
    }

    public function kioskGetWithReservationCode()
    {
        $inputs = Input::except('_token');
        $reservations = Reservation::getIdsByCode2([
            'code' => $inputs['code'] ? $inputs['code'] . '-' . date('ymd', strtotime(date('Y-m-d'))) : null,
            'phone' => $inputs['phone'] ? $inputs['phone'] : null,
            'registration_no' => (int)$inputs['registration_no'] ? (int)$inputs['registration_no'] : null,
        ], date('Y-m-d'));
        if ($reservations->toArray()) {
            $data['success'] = 'yes';
            $data['reservations'] = $reservations;
            $data['return'] = View::make('kiosk/with_reservation_table', $data)->render();
        } else {
            $data['success'] = 'no';
            $data['msg'] = 'No Reservation With This Parameters!';
        }
        return $data;
    }

    public function kioskWithReservationPrint()
    {
        try {
            $inputs = Input::except('_token');
            $reservations = explode(',', $inputs['reservation_id']);
            if ($reservations) {
                foreach ($reservations as $key => $val) {
                    $continue = true;
                    $reservation = Reservation::getById($val);
                    if ($reservation['type'] == 3) {
                        $seconds = Functions::hoursToSeconds($reservation['revisit_time_from']);
                        $newSeconds = $seconds + (10 * 60);
                        $time_from = Functions::timeFromSeconds($newSeconds);
                    } else {
                        $time_from = $reservation['time_from'];
                    }
                    if ($reservation['patient_attend'] == 0 && $reservation['type'] != 2 && $time_from < date('H:i:s')
                        && ((strtotime(date('Y-m-d H:i:s')) - strtotime(date('Y-m-d') . ' ' . $time_from)) / 60) > 30
                    ) {
                        $data['success'] = 'no';
                        $data['msg'] = 'late';
                        $data['return'] = $reservation;
                        return $data;
                    }
                    $screen = IpToScreen::getAll([
                        'hospital_id' => 1,
                        'clinic_id' => $reservation['clinic_id'],
                        'getFirst' => true
                    ]);
                    if ($screen) {
                        $receptions = IpToReception::getAll([
                            'hospital_id' => 1,
                            'ip_to_screen_id' => $screen['id']
                        ]);
                        if ($receptions) {
                            try {
                                $kiosk = KioskToPrinter::getAll([
                                    'hospital_id' => 1,
                                    'ip' => Functions::GetClientIp(),
                                    'getFirst' => true,
                                ]);
                                if ($kiosk) {
                                    $printer = IpToPrinter::getById($kiosk['printer_id']);
                                    if ($printer) {
                                        $printer_ip = $printer['ip'];
                                    } else {
                                        $data['success'] = 'no';
                                        $data['msg'] = 'This Kiosk Does Not Have Printer!';
                                        return $data;
                                    }
                                } else {
                                    $data['success'] = 'no';
                                    $data['msg'] = 'This Kiosk Does Not Have Printer!';
                                    return $data;
                                }
                            } catch (Exception $e) {
                                $data['success'] = 'no';
                                $data['msg'] = 'Error In Printing Ticket: Connection Error Founded!';
                                return $data;
                            }

                            if (Session::has('clinic' . '_' . $reservation['clinic_id'])) {
                                $clinicQueue = Session::get('clinic' . '_' . $reservation['clinic_id']);
                            } else {
                                Session::put('clinic' . '_' . $reservation['clinic_id'], -1);
                                $clinicQueue = -1;
                            }
                            $countReception = count($receptions->toArray());
                            // start from last reception +1
                            for ($i = $clinicQueue + 1; $i <= $countReception; $i++) {
                                if (isset($receptions[$i])) {
                                    $curr_reception = $receptions[$i];
                                    $userLoginIp = UserLoginIp::check(null, date('Y-m-d'), $curr_reception['ip']
                                        , UserRules::receptionPersonnel, false);
                                    if (!$userLoginIp->isEmpty()) {
                                        foreach ($userLoginIp as $key2 => $val2) {
                                            $user = User::getById($val2['user_id']);
                                            if ($user['is_ready']) {
                                                $data['success'] = 'yes';
                                                Session::put('clinic' . '_' . $reservation['clinic_id'], $i);
                                                Reservation::openClinicAndAttendReservation($reservation, $curr_reception, $screen, $printer_ip);
                                                $continue = false;
                                                break 2;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($continue) {
                                // start from first reception
                                for ($i = 0; $i <= $countReception; $i++) {
                                    if (isset($receptions[$i])) {
                                        $curr_reception = $receptions[$i];
                                        $userLoginIp = UserLoginIp::check(null, date('Y-m-d'), $curr_reception['ip']
                                            , UserRules::receptionPersonnel, false);
                                        if (!$userLoginIp->isEmpty()) {
                                            foreach ($userLoginIp as $key2 => $val2) {
                                                $user = User::getById($val2['user_id']);
                                                if ($user['is_ready']) {
                                                    $data['success'] = 'yes';
                                                    Session::put('clinic' . '_' . $reservation['clinic_id'], $i);
                                                    Reservation::openClinicAndAttendReservation($reservation, $curr_reception, $screen, $printer_ip);
                                                    $continue = false;
                                                    break 2;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            // look for reception delegate
                            $screenDelegate = ReceptionDelegate::getAll([
                                'reception_id' => $screen['id'],
                                'getFirst' => true
                            ]);
                            if ($continue) {
                                // first reception delegate
                                $reception1 = IpToReception::getAll([
                                    'hospital_id' => 1,
                                    'ip_to_screen_id' => $screenDelegate['reception1_delegate_id'],
                                    'getFirst' => true
                                ]);
                                if ($reception1) {
                                    $userLoginIp = UserLoginIp::check(null, date('Y-m-d'), $reception1['ip']
                                        , UserRules::receptionPersonnel, false);
                                    if (!$userLoginIp->isEmpty()) {
                                        foreach ($userLoginIp as $key2 => $val2) {
                                            $user = User::getById($val2['user_id']);
                                            if ($user['is_ready']) {
                                                $data['success'] = 'yes';
                                                Session::put('clinic' . '_' . $reservation['clinic_id'], -1);
                                                Reservation::openClinicAndAttendReservation($reservation, $reception1, $screen, $printer_ip);
                                                $continue = false;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($continue) {
                                // second reception delegate
                                $reception2 = IpToReception::getAll([
                                    'hospital_id' => 1,
                                    'ip_to_screen_id' => $screenDelegate['reception2_delegate_id'],
                                    'getFirst' => true
                                ]);
                                if ($reception2) {
                                    $userLoginIp = UserLoginIp::check(null, date('Y-m-d'), $reception2['ip']
                                        , UserRules::receptionPersonnel, false);
                                    if (!$userLoginIp->isEmpty()) {
                                        foreach ($userLoginIp as $key2 => $val2) {
                                            $user = User::getById($val2['user_id']);
                                            if ($user['is_ready']) {
                                                $data['success'] = 'yes';
                                                Session::put('clinic' . '_' . $reservation['clinic_id'], -1);
                                                Reservation::openClinicAndAttendReservation($reservation, $reception2, $screen, $printer_ip);
                                                $continue = false;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($continue) {
                                // third reception delegate
                                $reception3 = IpToReception::getAll([
                                    'hospital_id' => 1,
                                    'ip_to_screen_id' => $screenDelegate['reception3_delegate_id'],
                                    'getFirst' => true
                                ]);
                                if ($reception3) {
                                    $userLoginIp = UserLoginIp::check(null, date('Y-m-d'), $reception3['ip']
                                        , UserRules::receptionPersonnel, false);
                                    if (!$userLoginIp->isEmpty()) {
                                        foreach ($userLoginIp as $key2 => $val2) {
                                            $user = User::getById($val2['user_id']);
                                            if ($user['is_ready']) {
                                                $data['success'] = 'yes';
                                                Session::put('clinic' . '_' . $reservation['clinic_id'], -1);
                                                Reservation::openClinicAndAttendReservation($reservation, $reception3, $screen, $printer_ip);
                                                $continue = false;
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            if ($continue) {
                                $data['success'] = 'no';
                                $data['msg'] = 'No Receptions Ready!';
                            } else {
                                $data['success'] = 'yes';
                                $data['buttons'] = View::make('kiosk/step1_buttons')->render();
                            }
                        } else {
                            $data['success'] = 'no';
                            $data['msg'] = 'No Receptions With This Clinic!';
                        }
                    } else {
                        $data['success'] = 'no';
                        $data['msg'] = 'No Screen With This Clinic!';
                    }
                }
            } else {
                $data['success'] = 'no';
                $data['msg'] = 'No Reservations Selected!';
            }
        } catch (Exception $e) {
            $data['success'] = 'no';
            $data['msg'] = $e->getFile() . '--' . $e->getLine() . '--' . $e->getMessage();
        }
        return $data;
    }

    public function kioskWithReservationConvertToWaiting()
    {
        try {
            $inputs = Input::except('_token');
            if (!isset($inputs['reservation_id'])) {
                $data['success'] = 'no';
                $data['msg'] = 'Error in reservation id, contact to administrator!';
                return $data;
            }
            Reservation::edit([
                'status' => ReservationStatus::cancel,
                'patient_status' => PatientStatus::cancel,
                'cancel_notes' => 'Delayed',
                'times_before_convert' => 'Delayed',
                'cancel_reason_id' => 165,
                'send_cancel_sms' => 0,
                'show_reason' => 2
            ], $inputs['reservation_id']);
            Logging::add([
                'action' => LoggingAction::cancel_reservation,
                'table' => 'reservations',
                'ref_id' => $inputs['reservation_id'],
            ]);
            $reservationData = Reservation::getById($inputs['reservation_id']);
            ReservationHistory::add([
                'action' => 'Cancel',
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
            $data['success'] = 'yes';
            $data['buttons'] = View::make('kiosk/step1_buttons')->render();
            return $data;
        } catch (Exception $e) {
            $data['success'] = 'no';
            $data['msg'] = $e->getFile() . '--' . $e->getLine() . '--' . $e->getMessage();
            return $data;
        }
    }

    public function kioskPharmacy()
    {
        $data1['ticketType'] = AttributePms::getAll(AttributeType::$pmsReturn['PharmacyTicketType']);
        $data['buttons'] = View::make('kiosk/pharmacy_buttons', $data1)->render();
        return View::make('kiosk/pharmacy', $data);
    }

    public function kioskPrintPharmacyTicket()
    {
        $inputs = Input::except('_token');
        if (isset($inputs['pharmacy_ticket_type_id']) && $inputs['pharmacy_ticket_type_id']) {
            $pharmacyTicket = AttributePms::getById($inputs['pharmacy_ticket_type_id']);
            if ($pharmacyTicket) {
                $numOfNoPatient = PharmacyQueue::getAll([
                    'hospital_id' => 1, // cairo
                    'date' => date('Y-m-d'),
                    'pharmacy_ticket_type_id' => $inputs['pharmacy_ticket_type_id'],
                    'getCount' => true,
                ]);
                $queueCode = $pharmacyTicket['code'] . Functions::make3D($numOfNoPatient + 1);
                try {
                    $kiosk = KioskToPrinter::getAll([
                        'hospital_id' => 1,
                        'ip' => Functions::GetClientIp(),
                        'getFirst' => true,
                    ]);
                    if ($kiosk) {
                        $printer = IpToPrinter::getById($kiosk['printer_id']);
                        if ($printer) {
                            EPSON::noReservationPrint($queueCode, $printer['ip']);
                            $array = [
                                'hospital_id' => 1,
                                'queue_code' => $queueCode,
                                'pharmacy_ticket_type_id' => $inputs['pharmacy_ticket_type_id'],
                                'date' => date('Y-m-d'),
                            ];
                            PharmacyQueue::add($array);
                            $data['success'] = 'yes';
                        } else {
                            $data['success'] = 'no';
                            $data['msg'] = 'This Kiosk Does Not Have Printer!';
                        }
                    } else {
                        $data['success'] = 'no';
                        $data['msg'] = 'This Kiosk Does Not Have Printer!';
                    }
                } catch (Exception $e) {
                    $data['success'] = 'no';
                    $data['msg'] = 'Error In Printing Ticket: Connection Error Founded!';
                }
            } else {
                $data['success'] = 'no';
                $data['msg'] = 'Error In Pharmacy Ticket ID: Please Contact To Administrator!';
            }
        } else {
            $data['success'] = 'no';
            $data['msg'] = 'Error In Pharmacy Ticket ID: Please Contact To Administrator!';
        }
        return $data;
    }
}
