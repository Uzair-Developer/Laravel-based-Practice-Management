<?php

use core\clinic\ClinicRepository;
use core\enums\AttributeType;
use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use core\enums\UserRules;
use core\hospital\HospitalRepository;
use core\physician\PhysicianRepository;

class HomeController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function index()
    {
        $data = array();
        if ($this->user->user_type_id == UserRules::patientRelation) {
            $CRepo = new ClinicRepository();
            $data['clinics'] = $CRepo->getAll();
        } else {
            if ($this->user->user_type_id == 1 || $this->user->hasAccess('dashboard.reservationCounts')) {
                $hospitalRepo = new HospitalRepository();
                $data['hospitals'] = $hospitalRepo->getAll();
                $inputs = Input::except('_token');
                if (isset($inputs['date_from']) && $inputs['date_from']) {
                    $date_from = $inputs['date_from'];
                } else {
                    $date_from = date('Y-m-d');
                }
                if (isset($inputs['date_to']) && $inputs['date_to']) {
                    $date_to = $inputs['date_to'];
                } else {
                    $date_to = date('Y-m-d');
                }
                try {
                    if (app('production')) {
                        $data2['total_paid'] = HisBillDetail::getCount(array(
                            'date_from' => $date_from,
                            'date_to' => $date_to,
                            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                        ));
                    }
                } catch (Exception $e) {
                    $data2['total_paid'] = 'error';
                }
                $data2['total_res'] = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));
                $data2['waiting_res'] = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'in_patient_status' => array(PatientStatus::waiting, PatientStatus::patient_in, PatientStatus::patient_out),
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));
                $data2['attend_res'] = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'patient_attend' => 1,
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));

                $data2['patient_in_res'] = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'patient_status' => PatientStatus::patient_in,
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));

                $data2['patient_out_res'] = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'patient_status' => PatientStatus::patient_out,
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));

                $data2['cancelled_res'] = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'patient_status' => PatientStatus::cancel,
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));

                $data2['cancelled_reason_res'] = array();
                $cancelledReason = AttributePms::getAll(AttributeType::$pmsReturn['cancelReservationReason']);
                foreach ($cancelledReason as $key => $val) {
                    $data2['cancelled_reason_res'][$key]['count'] = Reservation::getReservationCounts(array(
                        'date_from' => $date_from,
                        'date_to' => $date_to,
                        'patient_status' => PatientStatus::cancel,
                        'cancel_reason_id' => $val['id'],
                        'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                        'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                        'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                    ));
                    $data2['cancelled_reason_res'][$key]['name'] = $val['name'];
                }

                $data2['pending_res'] = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'patient_status' => PatientStatus::pending,
                    'patient_attend' => 1,
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));
                $pendingAndNotAttend = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'patient_status' => PatientStatus::pending,
                    'patient_attend' => '0',
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));
                $data2['pendingAndNotAttend'] = $pendingAndNotAttend;

                $data2['archive_res'] = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'patient_status' => PatientStatus::archive,
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));

                //////////////////////////////////
                $no_show_res = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'patient_status' => PatientStatus::no_show,
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));

                if (app('production')) {
                    if ($data2['total_paid'] > $data2['attend_res']) {
                        $sub = $data2['total_paid'] - $data2['attend_res'];
                        $no_show_res -= $sub;
                    }
                }
                $data2['no_show_res'] = $no_show_res + $pendingAndNotAttend;
                ////////////////////////////////////
                $data2['in_service_res'] = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'patient_in_service' => 1,
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));
                $data2['service_done_res'] = Reservation::getReservationCounts(array(
                    'date_from' => $date_from,
                    'date_to' => $date_to,
                    'patient_in_service' => 3,
                    'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                    'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                    'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
                ));

                $data['count_reservations'] = View::make('layout/count_reservations', $data2)->render();
            }
        }
        return View::make('layout/home', $data);
    }

    public function getReservationCounts()
    {
        $inputs = Input::except('_token');
        if (isset($inputs['date_from']) && $inputs['date_from']) {
            $date_from = $inputs['date_from'];
        } else {
            $date_from = date('Y-m-d');
        }
        if (isset($inputs['date_to']) && $inputs['date_to']) {
            $date_to = $inputs['date_to'];
        } else {
            $date_to = date('Y-m-d');
        }
        try {
        if (app('production')) {
            $data2['total_paid'] = HisBillDetail::getCount(array(
                'date_from' => $date_from,
                'date_to' => $date_to,
                'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
            ));
        }
        } catch (Exception $e) {
            $data2['total_paid'] = 'error';
        }
        $data2['total_res'] = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));
        $data2['waiting_res'] = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'in_patient_status' => array(PatientStatus::waiting, PatientStatus::patient_in, PatientStatus::patient_out),
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));
        $data2['attend_res'] = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'patient_attend' => 1,
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));

        $data2['patient_in_res'] = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'patient_status' => PatientStatus::patient_in,
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));

        $data2['patient_out_res'] = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'patient_status' => PatientStatus::patient_out,
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));

        $data2['cancelled_res'] = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'patient_status' => PatientStatus::cancel,
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));
        $cancelledReason = AttributePms::getAll(AttributeType::$pmsReturn['cancelReservationReason']);
        foreach ($cancelledReason as $key => $val) {
            $data2['cancelled_reason_res'][$key]['count'] = Reservation::getReservationCounts(array(
                'date_from' => $date_from,
                'date_to' => $date_to,
                'patient_status' => PatientStatus::cancel,
                'cancel_reason_id' => $val['id'],
                'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
                'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
                'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
            ));
            $data2['cancelled_reason_res'][$key]['name'] = $val['name'];
        }

        $data2['pending_res'] = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'patient_status' => PatientStatus::pending,
            'patient_attend' => 1,
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));
        $pendingAndNotAttend = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'patient_status' => PatientStatus::pending,
            'patient_attend' => '0',
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));
        $data2['pendingAndNotAttend'] = $pendingAndNotAttend;

        $data2['archive_res'] = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'patient_status' => PatientStatus::archive,
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));

        //////////////////////////////////
        $no_show_res = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'patient_status' => PatientStatus::no_show,
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));

        if (app('production')) {
            if ($data2['total_paid'] > $data2['attend_res']) {
                $sub = $data2['total_paid'] - $data2['attend_res'];
                $no_show_res -= $sub;
            }
        }
        $data2['no_show_res'] = $no_show_res + $pendingAndNotAttend;
        ////////////////////////////////////

        $data2['in_service_res'] = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'patient_in_service' => 1,
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));
        $data2['service_done_res'] = Reservation::getReservationCounts(array(
            'date_from' => $date_from,
            'date_to' => $date_to,
            'patient_in_service' => 3,
            'hospital_id' => isset($inputs['hospital_id']) ? $inputs['hospital_id'] : '',
            'clinic_id' => isset($inputs['clinic_id']) ? $inputs['clinic_id'] : '',
            'physician_id' => isset($inputs['physician_id']) ? $inputs['physician_id'] : '',
        ));

        $data['count_reservations'] = View::make('layout/count_reservations', $data2)->render();
        return $data;
    }

}
