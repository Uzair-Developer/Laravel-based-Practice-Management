<?php

use core\clinic\ClinicRepository;
use core\enums\AgeType;
use core\enums\AttributeType;
use core\enums\LoggingAction;
use core\enums\PatientGender;
use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use core\enums\UserRules;
use core\hospital\HospitalRepository;
use core\userLocalization\UserLocalizationRepository;

class ReservationManagementController extends BaseController
{
    public $user = '';

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function reservationManage()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('manageReservation.open_close')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();

        $CRepo = new ClinicRepository();
        $inputs['paginate'] = true;
        $data['clinics'] = $CRepo->getAll($inputs);
        return View::make('reservation/manage', $data);
    }

    public function manageClinic($id)
    {
        $CRepo = new ClinicRepository();
        $clinic = $CRepo->getById($id);
        $hospital = Hospital::getById($clinic['hospital_id']);
        date_default_timezone_set($hospital['time_zone']);
        $array = array(
            'clinic_id' => $id,
            'date' => date('Y-m-d H:i:s'),
        );
        if ($clinic['status'] == 0) {
            $CRepo->update(array('status' => 1), $id);
            $array['status'] = 1;
            ManageClinics::add($array);
        } else {
            $CRepo->update(array('status' => 0), $id);
            $array['status'] = 0;
            ManageClinics::add($array);
            Reservation::updateWhenClinicClose($id, date('Y-m-d'));
        }
        Flash::success('Updated Successfully');
        date_default_timezone_set('Africa/Cairo');
        return Redirect::back();
    }

    public function manageClinicReservations()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('manageReservation.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        if ($inputs) {
            $allReservations = Reservation::getByPatientsIdAndDates($inputs, true);
            $data['reservations'] = $allReservations;

            if (isset($inputs['today_only']) && $inputs['today_only'] == 1) {
                $inputs['getCount'] = true;
                $allCountReservations = Reservation::getByPatientsIdAndDates($inputs, false);
                $data['allCountReservations'] = $allCountReservations;

                $inputs['patient_status'] = 10;
                $attendCountReservations = Reservation::getByPatientsIdAndDates($inputs, false);
                $data['attendCountReservations'] = $attendCountReservations;
                $data['total_count_refresh'] = View::make('reservation/total_count_refresh', $data)->render();
            }
        } else {
            return Redirect::route('reservationManage');
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['relevant'] = AttributePms::getAll(AttributeType::$pmsReturn['relevantType']);
        $data['cancelResReason'] = AttributePms::getAll(AttributeType::$pmsReturn['cancelReservationReason']);
        $data['groups'] = Group::getAll(['in_filter' => 1]);
//        dd('done');
        return View::make('reservation/list', $data);
    }

    public function managePatientReservation($id, $status)
    {
        try {
            $reservation = Reservation::getById($id);
            $clinic = Clinic::getById($reservation['clinic_id']);
            $hospital = Hospital::getById($clinic['hospital_id']);
            $physician = User::getById($reservation['physician_id']);
            date_default_timezone_set($hospital['time_zone']);
            if ($status == PatientStatus::patient_in) {
                if ($reservation['patient_attend'] == 0) {
                    Flash::error('The patient not attended yet!');
                    return Redirect::back();
                }
                if ($physician['is_ready'] == 0) {
                    Flash::error('The physician not ready yet!');
                    return Redirect::back();
                }
                /// if have old patient in then do patient out first to it.
                $reservations = Reservation::checkPatientInFromPhysicianClinicDate($reservation['clinic_id'],
                    $reservation['physician_id'], $reservation['date']);
                if ($reservations) {
                    Reservation::edit(array(
                        'patient_status' => PatientStatus::patient_out,
                        'status' => ReservationStatus::accomplished,
                        'actual_time_from' => date('H:i:s'),
                        'show_reason' => 2,
                    ), $reservations['id']);
                    Logging::add([
                        'action' => LoggingAction::patient_out_reservation,
                        'table' => 'reservations',
                        'ref_id' => $reservations['id'],
                        'user_id' => $this->user->id,
                    ]);
                    ReservationHistory::add([
                        'action' => 'Patient Out',
                        'action_by' => $this->user->id,
                        'reservation_id' => $reservations['id'],
                        'code' => $reservations['code'],
                        'physician_id' => $reservations['physician_id'],
                        'clinic_id' => $reservations['clinic_id'],
                        'patient_id' => $reservations['patient_id'],
                        'date' => $reservations['date'],
                        'time_from' => $reservations['time_from'],
                        'time_to' => $reservations['time_to'],
                        'patient_status' => PatientStatus::patient_out,
                        'status' => ReservationStatus::accomplished,
                    ]);
                }
                ////////////////////////////////////////////////////////////
                if ($clinic['status'] == 0) {
                    Clinic::edit(array('status' => 1), $clinic['id']);
                    ManageClinics::add(array(
                        'clinic_id' => $clinic['id'],
                        'date' => date('Y-m-d H:i:s'),
                        'status' => '1',
                    ));
                }
                Reservation::edit(array(
                    'patient_status' => PatientStatus::patient_in,
                    'status' => ReservationStatus::on_progress,
                    'actual_time_from' => date('H:i:s'),
                    'show_reason' => 2,
                ), $id);
                Logging::add([
                    'action' => LoggingAction::patient_in_reservation,
                    'table' => 'reservations',
                    'ref_id' => $id,
                    'user_id' => $this->user->id,
                ]);
                $reservationData = Reservation::getById($id);
                ReservationHistory::add([
                    'action' => 'Patient In',
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
                Flash::success('Updated Successfully');
            } elseif ($status == PatientStatus::patient_out) {
                if ($reservation['patient_status'] == PatientStatus::patient_in) {
                    Reservation::edit(array(
                        'patient_status' => PatientStatus::patient_out,
                        'status' => ReservationStatus::accomplished,
                        'actual_time_to' => date('H:i:s'),
                        'show_reason' => 2
                    ), $id);
                    Logging::add([
                        'action' => LoggingAction::patient_out_reservation,
                        'table' => 'reservations',
                        'ref_id' => $id,
                        'user_id' => $this->user->id,
                    ]);
                    $reservationData = Reservation::getById($id);
                    ReservationHistory::add([
                        'action' => 'Patient Out',
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
                }
                Flash::success('Updated Successfully');
            }
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back();
        }
        date_default_timezone_set('Africa/Cairo');
        return Redirect::back();
    }

    public function changeStatusPatientReservation($id, $status)
    {
        $reservationData = Reservation::getById($id);
        if ($status == PatientStatus::pending) {
            Reservation::edit(array(
                'patient_status' => PatientStatus::pending,
//                'status' => ReservationStatus::pending,
                'update_by' => $this->user->id,
                'exception_reason' => 'Manually',
                'show_reason' => '1',
            ), $id);
            Logging::add([
                'action' => LoggingAction::pending_reservation,
                'table' => 'reservations',
                'ref_id' => $id,
                'user_id' => $this->user->id,
            ]);
            ReservationHistory::add([
                'action' => 'Pending',
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
                'patient_status' => PatientStatus::pending,
            ]);
        }
        if ($status == PatientStatus::waiting) {
            Reservation::edit(array(
                'patient_status' => PatientStatus::waiting,
                'status' => ReservationStatus::reserved,
                'update_by' => $this->user->id,
                'show_reason' => 2
            ), $id);
            Logging::add([
                'action' => LoggingAction::waiting_reservation,
                'table' => 'reservations',
                'ref_id' => $id,
                'user_id' => $this->user->id,
            ]);
            ReservationHistory::add([
                'action' => 'Resume',
                'action_by' => $this->user->id,
                'reservation_id' => $reservationData['id'],
                'code' => $reservationData['code'],
                'physician_id' => $reservationData['physician_id'],
                'clinic_id' => $reservationData['clinic_id'],
                'patient_id' => $reservationData['patient_id'],
                'date' => $reservationData['date'],
                'time_from' => $reservationData['time_from'],
                'time_to' => $reservationData['time_to'],
                'patient_status' => PatientStatus::waiting,
                'status' => ReservationStatus::reserved,
            ]);
        }
        if ($status == PatientStatus::cancel) {
            Reservation::edit(array(
                'patient_status' => PatientStatus::cancel,
                'status' => ReservationStatus::cancel,
                'update_by' => $this->user->id,
                'show_reason' => 2
            ), $id);
            Logging::add([
                'action' => LoggingAction::cancel_reservation,
                'table' => 'reservations',
                'ref_id' => $id,
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
                'patient_status' => PatientStatus::cancel,
                'status' => ReservationStatus::cancel,
            ]);
        }
        Flash::success('Updated Successfully');
        return Redirect::back();
    }

    public function printExcelManageClinicReservations()
    {
        ini_set('max_execution_time', 3000);
        $inputs = Input::except('_token');
        $inputs['patients_id'] = Patient::searchPatient($inputs);
        if ((isset($inputs['name']) || isset($inputs['phone']) || isset($inputs['id'])
                || isset($inputs['registration_no']) || isset($inputs['national_id'])) && empty($inputs['patients_id'])
        ) {
            $inputs['patients_id'] = 0;
        }
        $reservations = Reservation::getByPatientsIdAndDates($inputs, false);
        Excel::create('reservations_' . date('Y-m-d H-i-s'), function ($excel) use ($reservations) {
            // Set the title
            $excel->setTitle('reservations');
            $excel->sheet('physicians', function ($sheet) use ($reservations) {
                $sheet->loadView('reservation/printExcel', array('reservations' => $reservations));
            });

        })->download('xlsx');
    }

    public function addWalkInReservation()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('manageReservation.walkIn_add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');

        $patient_id = $inputs['patient_id'];
        $physician_id = $inputs['physician_id'];
        $clinic_id = $inputs['clinic_id'];
        $date = date('Y-m-d');
        $physicianSchedule = PhysicianSchedule::getByPhysicianId_Date($physician_id, $date, true, $inputs['clinic_id']);
        if (empty($physicianSchedule)) {
            Flash::error('The physician didn\'t have schedule for this day!');
            return Redirect::back();
        }

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
                'age' => $age ? $age : null,
                'age_type_id' => $age_type_id ? $age_type_id : null,
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
            // removed add patient to his from  //
        } else {
            $patient = Patient::getById($patient_id);
            $patientArray = array();
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
//            'walk_in_type' => $walk_in_type,
            'patient_id' => $patient_id,
            'date' => $date,
            'type' => 2, // type walk in
            'walk_in_duration' => $physicianSchedule['slots'],
//            'walk_in_approval' => 0, // not approved yet
            'walk_in_approval' => 1, // approved true
            'walk_in_approval_by' => $this->user->id,
            'walk_in_approval_at' => date('Y-m-d H:i:s'),
            'create_by' => $this->user->id,
            'patient_attend' => 1, // patient attend true
            'patient_attend_datetime' => date('Y-m-d H:i:s')
        );
        if (isset($inputs['sms_lang']) && $inputs['sms_lang']) {
            $reservationArray['sms_lang'] = $inputs['sms_lang'];
        }
        $reservation = Reservation::add($reservationArray);
        Logging::add([
            'action' => LoggingAction::add_reservation,
            'table' => 'reservations',
            'ref_id' => $reservation->id,
            'user_id' => $this->user->id,
        ]);
        $reservationData = Reservation::getById($reservation->id);
        ReservationHistory::add([
            'action' => 'Add',
            'action_by' => $this->user->id,
            'reservation_id' => $reservationData['id'],
            'code' => $reservationData['code'],
            'physician_id' => $reservationData['physician_id'],
            'clinic_id' => $reservationData['clinic_id'],
            'patient_id' => $reservationData['patient_id'],
            'date' => $reservationData['date'],
//            'time_from' => $reservationData['time_from'],
//            'time_to' => $reservationData['time_to'],
            'status' => $reservationData['status'],
            'patient_status' => $reservationData['patient_status'],
        ]);
        Flash::success('Added Successfully');
        return Redirect::back();
    }

    public function managePatientAttendReservation($reservation_id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('manageReservation.patient_attend')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        try {
            $reservation = Reservation::getById($reservation_id);
            $reservationData = $reservation;
            $clinic = Clinic::getById($reservation['clinic_id']);
            if ($clinic['status'] == 0) {
                Clinic::edit(array('status' => 1), $clinic['id']);
                ManageClinics::add(array(
                    'clinic_id' => $clinic['id'],
                    'date' => date('Y-m-d H:i:s'),
                    'status' => '1',
                ));
            }
            if ($reservation['patient_attend'] == 0) {
                $reservationArray = array(
                    'update_by' => $this->user->id,
                    'patient_attend' => 1, // patient attend true
                    'patient_attend_datetime' => date('Y-m-d H:i:s'),
                );
                ////////// if physician then attend and patient in
                if ($this->user->user_type_id == UserRules::physician) {
                    $reservationArray['patient_status'] = PatientStatus::patient_in;
                    $reservationArray['status'] = ReservationStatus::on_progress;
                    $reservationArray['actual_time_from'] = date('H:i:s');
                    $reservationArray['show_reason'] = 2;

                    /// if have old patient in then do patient out first to it.
                    $reservations = Reservation::checkPatientInFromPhysicianClinicDate($reservation['clinic_id'],
                        $reservation['physician_id'], $reservation['date']);
                    if ($reservations) {
                        Reservation::edit(array(
                            'patient_status' => PatientStatus::patient_out,
                            'status' => ReservationStatus::accomplished,
                            'actual_time_from' => date('H:i:s'),
                            'show_reason' => 2,
                        ), $reservations['id']);
                        Logging::add([
                            'action' => LoggingAction::patient_out_reservation,
                            'table' => 'reservations',
                            'ref_id' => $reservations['id'],
                            'user_id' => $this->user->id,
                        ]);
                        ReservationHistory::add([
                            'action' => 'Patient Out',
                            'action_by' => $this->user->id,
                            'reservation_id' => $reservationData['id'],
                            'code' => $reservationData['code'],
                            'physician_id' => $reservationData['physician_id'],
                            'clinic_id' => $reservationData['clinic_id'],
                            'patient_id' => $reservationData['patient_id'],
                            'date' => $reservationData['date'],
                            'time_from' => $reservationData['time_from'],
                            'time_to' => $reservationData['time_to'],
                            'status' => ReservationStatus::accomplished,
                            'patient_status' => PatientStatus::patient_out,
                        ]);
                    }
                    ////////////////////////////////////////////////////////////
                }
                //////////////////////////////////////////////////
                Reservation::edit($reservationArray, $reservation_id);
                PatientAttend::add(array(
                    'status' => 1, // patient attend true
                    'reservation_id' => $reservation_id,
                    'patient_id' => $reservation['patient_id'],
                    'created_by' => $this->user->id,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                ));
                Logging::add([
                    'action' => LoggingAction::patient_attend_reservation,
                    'table' => 'reservations',
                    'ref_id' => $reservation_id,
                    'user_id' => $this->user->id,
                ]);
                ReservationHistory::add([
                    'action' => 'Patient Attend',
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
            } else {
                Reservation::edit(array(
                    'update_by' => $this->user->id,
                    'patient_attend' => 0, // patient attend false
                ), $reservation_id);
                PatientAttend::add(array(
                    'status' => 0, // patient attend false
                    'reservation_id' => $reservation_id,
                    'patient_id' => $reservation['patient_id'],
                    'created_by' => $this->user->id,
                    'date' => date('Y-m-d'),
                    'time' => date('H:i:s'),
                ));
                Logging::add([
                    'action' => LoggingAction::patient_not_attend_reservation,
                    'table' => 'reservations',
                    'ref_id' => $reservation_id,
                    'user_id' => $this->user->id,
                ]);
                ReservationHistory::add([
                    'action' => 'Patient Not Attend',
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
            }
            Flash::success('Updated Successfully');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
        }
        return Redirect::back();
    }

    public function approvedWalkInReservation($id)
    {
        $reservation = Reservation::getById($id);
        $reservationData = $reservation;
        if ($reservation['type'] == 2) {
            if ($reservation['walk_in_approval'] == 0) {
                Reservation::edit(array(
                    'update_by' => $this->user->id,
                    'walk_in_approval' => 1, // approved true
                    'walk_in_approval_by' => $this->user->id,
                    'walk_in_approval_at' => date('Y-m-d H:i:s'),
                ), $id);
                ReservationHistory::add([
                    'action' => 'Walk In Approved',
                    'action_by' => $this->user->id,
                    'reservation_id' => $reservationData['id'],
                    'code' => $reservationData['code'],
                    'physician_id' => $reservationData['physician_id'],
                    'clinic_id' => $reservationData['clinic_id'],
                    'patient_id' => $reservationData['patient_id'],
                    'date' => $reservationData['date'],
//                    'time_from' => $reservationData['time_from'],
//                    'time_to' => $reservationData['time_to'],
                    'status' => $reservationData['status'],
                    'patient_status' => $reservationData['patient_status'],
                ]);
            } else {
                Flash::error('This walk in reservation already approved');
                return Redirect::back();
            }
        } else {
            Flash::error('This reservation didn\'t walk in type');
            return Redirect::back();
        }
        Flash::success('Updated Successfully');
        return Redirect::back();
    }

    public function nextPatientInReservation()
    {
        $reservation = Reservation::getAttendByClinic(null, array('getFirst' => true), $this->user->id, false, true);
        if ($reservation) {
            $hospitalId = Clinic::getById($reservation['clinic_id'])['hospital_id'];
            $url = route('manageClinicReservations') . '?hospital_id=' . $hospitalId . '&date_from=' . date('Y-m-d')
                . '&date_to=' . date('Y-m-d') . '&code=' . $reservation['code'] . '&type=' . $reservation['type'];
            Reservation::editByPhysicianAndDate(array(
                'next_patient_flag' => 0
            ), $this->user->id, date('Y-m-d'));
            Reservation::edit(array('next_patient_flag' => 1), $reservation['id']);
            Session::flash('next_patient_flag', 1);
            return Redirect::to($url);
        } else {
            Flash::error('No Reservations Found!');
            return Redirect::back();
        }
    }

    public function getReservationTotalCountRefresh()
    {
        $inputs = Input::except('_token');
        unset($inputs['code']);
        unset($inputs['type']);
        $inputs['getCount'] = true;
        $allCountReservations = Reservation::getByPatientsIdAndDates($inputs, false);
        $data['allCountReservations'] = $allCountReservations;

        $inputs['patient_status'] = 10;
        $attendCountReservations = Reservation::getByPatientsIdAndDates($inputs, false);
        $data['attendCountReservations'] = $attendCountReservations;
        return View::make('reservation/total_count_refresh', $data)->render();
    }

    public function PrintReservation()
    {
        $inputs = Input::except('_token');
        if (!isset($inputs['reservation_id']) || empty($inputs['reservation_id'])) {
            die('No Reservations Found!');
        }
        $data['lang'] = $inputs['lang'];
        $reservation = Reservation::getById($inputs['reservation_id']);
        $data['reservation'] = $reservation;
        $data['physician'] = User::getById($reservation['physician_id']);
        $data['patient'] = Patient::getById($reservation['patient_id']);
        $data['clinic'] = Clinic::getById($reservation['clinic_id']);
        return View::make('reservation/print_reservation', $data);
    }

    public function reservationHistory()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('manageReservation.view_history')
        ) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::route('home');
        }
        $inputs = Input::except('_token');
        $inputs['getCountAndData'] = true;
        $allReservations = Reservation::getByPatientsIdAndDates($inputs, true);
        $data['reservations'] = $allReservations['data'];
        $data['reservationsCount'] = $allReservations['count'];

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();

        return View::make('reservation/reservations_history', $data);
    }

    public function reservationViewHistory()
    {
        $inputs = Input::except('_token');
        if ($inputs['reservation_id']) {
            $data['reservations'] = ReservationHistory::getAll(['reservation_id' => $inputs['reservation_id']]);
            return View::make('reservation/view_res_history', $data)->render();
        } else {
            return 'Error in reservation data!';
        }
    }
}
