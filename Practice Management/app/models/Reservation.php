<?php


use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\enums\LoggingAction;
use core\enums\PatientGender;
use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use core\enums\UserRules;
use core\physician\PhysicianRepository;

class Reservation extends Eloquent
{
    protected $table = 'reservations';
    protected $guarded = array('');

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function editByPatient($inputs, $patient_id)
    {
        return self::where('patient_id', $patient_id)->update($inputs);
    }

    public static function editByPhysicianAndDate($inputs, $physician_id, $date)
    {
        return self::where('physician_id', $physician_id)
            ->where('date', $date)
            ->update($inputs);
    }

    public static function getAll()
    {
        return self::all()->toArray();
    }

    public static function getBy($inputs)
    {
        if (Sentry::check()) {
            $user = Sentry::getUser();
        } else {
            $user = array();
        }
        $data = self::where(function ($q) use ($inputs, $user) {

            if ($user && $user->user_type_id == UserRules::physician) {
                $q->where('reservations.physician_id', $user->id);
            }
            if (isset($inputs['hospital_id']) && $inputs['hospital_id'] && empty($inputs['clinic_id'])) {
                $clinics = Clinic::getByHospitalId($inputs['hospital_id']);
                $q->whereIn('reservations.clinic_id', $clinics);
            }
            if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                $q->where('reservations.clinic_id', $inputs['clinic_id']);
            }
            if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                $q->where('reservations.physician_id', $inputs['physician_id']);
            }
            if (isset($inputs['date_from']) && $inputs['date_from']) {
                $q->where('reservations.date', '>=', $inputs['date_from']);
            }
            if (isset($inputs['date_to']) && $inputs['date_to']) {
                $q->where('reservations.date', '<=', $inputs['date_to']);
            }
            if (isset($inputs['code']) && $inputs['code']) {
                $q->where('reservations.code', 'LIKE', '%' . $inputs['code'] . '%');
            }
            if (isset($inputs['type']) && $inputs['type']) {
                $q->where('reservations.type', $inputs['type']);
            }
            if (isset($inputs['status']) && $inputs['status']) {
                $q->whereIn('reservations.status', $inputs['status']);
            }
            if (isset($inputs['patient_status']) && $inputs['patient_status']) {
                $q->whereIn('reservations.patient_status', $inputs['patient_status']);
            }
            if (isset($inputs['not_in_patient_status']) && $inputs['not_in_patient_status']) {
                $q->whereNotIn('reservations.patient_status', $inputs['not_in_patient_status']);
            }
            if ((isset($inputs['name']) && $inputs['name']) || (isset($inputs['phone']) && $inputs['phone'])
                || (isset($inputs['id']) && $inputs['id'])
                || (isset($inputs['registration_no']) && $inputs['registration_no'])
                || (isset($inputs['national_id']) && $inputs['national_id'])
            ) {
                $patients_id = Patient::searchPatient($inputs);
                $q->whereIn('reservations.patient_id', $patients_id);
            }
            if (isset($inputs['status']) && $inputs['status']) {
                $q->where('reservations.status', $inputs['status']);
            }
            if (isset($inputs['walk_in_approval'])) {
                $q->where('reservations.walk_in_approval', $inputs['walk_in_approval']);
            }
            if (isset($inputs['walk_in_time_from_is_null']) && $inputs['walk_in_time_from_is_null']) {
                $q->whereNull('reservations.walk_in_time_from');
            }
        });
        if (isset($inputs['history_status']) && $inputs['history_status']) {
//            $data = $data->join('reservation_history', function ($join) use ($inputs) {
//                $join->on('reservations.id', '=', 'reservation_history.reservation_id');
//                if ($inputs['history_status'] === "0") { // waiting
//                    $join->where('reservation_history.action', '=', 'Add');
//                } elseif ($inputs['history_status'] == '10') { // patient attend
//                    $join->where('reservation_history.action', '=', 'Patient Attend');
//                }
//            });
            $data = $data->whereExists(function ($query) use ($inputs) {
                $query->select(DB::raw(1))
                    ->from('reservation_history')
                    ->whereRaw('reservation_history.reservation_id = reservations.id');
                $query->where(function ($q) use ($inputs) {
                    if ($inputs['history_status'] === "0") { // waiting
                        $q->whereRaw('reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['add_reservation']);
                    } elseif ($inputs['history_status'] == '10') { // patient attend
                        $q->where(function ($q2) use ($inputs) {
                            $q2->whereRaw('reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['patient_attend'] . ' or reservations.type = 2');
                        });
                    } elseif ($inputs['history_status'] == '1') { // patient In
                        $q->whereRaw('reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['patient_in']);
                    } elseif ($inputs['history_status'] == '2') { // patient out
                        $q->whereRaw('reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['patient_out']);
                    } elseif ($inputs['history_status'] == '4') { // cancel
                        $q->whereRaw('reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['cancel_reservation']);
                    } elseif ($inputs['history_status'] == '5') { // pending
                        $q->whereRaw('reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['pending_from_un_archive'] . ' 
                    or reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['pending'] . ' or reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['add_exception_by_holiday'] . '
                    or reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['add_exception']);
                    } elseif ($inputs['history_status'] == '7') { // archive
                        $q->whereRaw('reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['archive_from_edit_schedule'] . ' 
                    or reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['archive_from_schedule_exception'] . ' or reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['archive_from_delete_the_localization_in_this_clinic'] . '
                    or reservation_history.action_id = ' . ReservationHistoryActions::$actionsRetn['archive_from_delete_the_localization_in_this_hospital']);
                    }
                });
            });
            $data->exists();
        }

        if (isset($inputs['orderBy']) && $inputs['orderBy']) {
            $data->orderBy($inputs['orderBy'][0], $inputs['orderBy'][1]);
        }
        $data = $data->orderBy('reservations.date', 'desc')->orderBy('reservations.time_from');
//        dd($data->toSql());
        if (isset($inputs['getCount']) && $inputs['getCount']) {
            $data = $data->count('reservations.id');
        } else if (isset($inputs['getCountAndData']) && $inputs['getCountAndData']) {
            $array['count'] = $data->count('reservations.id');
            if (isset($inputs['paginate']) && $inputs['paginate']) {
                $array['data'] = $data->paginate($inputs['paginate']);
            } else {
                $array['data'] = $data->get();
            }
            return $array;
        } else if (isset($inputs['getFirst']) && $inputs['getFirst']) {
            $data = $data->first();
        } else if (isset($inputs['getIds']) && $inputs['getIds']) {
            $data = $data->lists('reservations.id');
        } else {
            if (isset($inputs['paginate']) && $inputs['paginate']) {
                $data = $data->paginate($inputs['paginate']);
            } else {
                $data = $data->get();
            }
        }
        return $data;
    }

    public static function getById($id, $all = true, $clinicShort = false)
    {
        $data = self::where('id', $id)->first();
        if ($data) {
            if ($all) {
                if ($clinicShort) {
                    $data['clinic_name'] = $data ? Clinic::getSmsCode($data['clinic_id']) : '';
                    if (empty($data['clinic_name'])) {
                        $data['clinic_name'] = $data ? Clinic::getNameById($data['clinic_id']) : '';
                    }
                } else {
                    $data['clinic_name'] = $data ? Clinic::getNameById($data['clinic_id']) : '';
                }
                $data['physician_name'] = $data ? User::getNameById($data['physician_id']) : '';
                $data['patient_name'] = $data ? Patient::getName($data['patient_id']) : '';
                if ($data && $data['status'] == ReservationStatus::cancel) {
                    $data['cancel_reason_name'] = AttributePms::getById($data['cancel_reason_id'])['name'];
                }
            }
        }
        return $data;
    }

    public static function remove($id)
    {
        // return self::where('id', $id)->delete();
    }

    public static function getLastRecord($clinicId, $userId, $date)
    {
        return self::where('date', $date)->where('clinic_id', $clinicId)->where('physician_id', $userId)->get()->last();
    }

    public static function getCount($clinicId, $userId, $date)
    {
        return self::where('date', $date)->where('clinic_id', $clinicId)->where('physician_id', $userId)->count();
    }

    public static function getCountByClinicAndData($clinicId, $date)
    {
        return self::where('code', 'LIKE', '%-' . date('ymd', strtotime($date)))->where('clinic_id', $clinicId)->count();
    }

    public static function getByClinicAndDate($clinicId, $date)
    {
        return self::where('date', $date)
            ->where('clinic_id', $clinicId)
            ->whereIn('status', array(
                ReservationStatus::accomplished,
                ReservationStatus::on_progress,
                ReservationStatus::reserved,
                ReservationStatus::pending,
                ReservationStatus::no_show
            ))->get()->toArray();
    }

    public static function getByClinicAndPhysician($clinicId, $physician_id)
    {
        return self::where('physician_id', $physician_id)
            ->where('clinic_id', $clinicId)
            ->whereIn('status', array(
                ReservationStatus::accomplished,
                ReservationStatus::on_progress,
                ReservationStatus::reserved,
                ReservationStatus::pending,
                ReservationStatus::no_show
            ))->get()->toArray();
    }

    public static function getByClinic($id)
    {
        $data = self::where('clinic_id', $id)->paginate(20);
        foreach ($data as $key => $val) {
            $data[$key]['physician_name'] = User::getNameById($val['physician_id']);
            $data[$key]['patient_name'] = Patient::getName($val['patient_id']);
        }
        return $data;
    }

    public static function checkPatientInFromPhysicianClinicDate($clinicId, $physicianId, $date)
    {
        return self::where('date', $date)->where('clinic_id', $clinicId)
            ->where('physician_id', $physicianId)
            ->where('patient_status', PatientStatus::patient_in)
            ->first();
    }

    public static function checkPhysicianScheduleExist($clinicId, $physicianId, $start_date, $end_date)
    {
        return self::where('date', '>=', $start_date)
            ->where('date', '<=', $end_date)
            ->where('clinic_id', $clinicId)
            ->where('physician_id', $physicianId)
            ->where('status', '!=', ReservationStatus::cancel)
            ->first();
    }

    public static function getByPhysicianSchedule($clinicId = '', $physicianId, $start_date, $end_date = '', $clinicIds = '')
    {
        $data = self::where('date', '>=', $start_date);
        if ($end_date) {
            $data = $data->where('date', '<=', $end_date);
        }
        if ($clinicId) {
            $data = $data->where('clinic_id', $clinicId);
        }
        if ($clinicIds) {
            $data = $data->whereIn('clinic_id', $clinicIds);
        }
        $data = $data->where('physician_id', $physicianId)
            ->where('status', ReservationStatus::reserved)
            ->where('patient_attend', '!=', 1)
            ->get()->toArray();
        return $data;
    }

    public static function getAllFromPhysicianClinicDate($clinicId, $physicianId, $date)
    {
        return self::where('date', $date)->where('clinic_id', $clinicId)
            ->where('physician_id', $physicianId)
            ->where('patient_status', '!=', PatientStatus::cancel)
//            ->where('patient_status', '!=', PatientStatus::no_show)
            ->get()->toArray();
    }

    public static function getByPatientsIdAndDates($inputs, $with_paginate = true, $type = '')
    {
        if (Sentry::check()) {
            $user = Sentry::getUser();
        } else {
            $user = array();
        }
        $data = self::where(function ($q) use ($inputs, $user) {

            if ($user && $user->user_type_id == UserRules::physician) {
                $q->where('physician_id', $user->id);
            }
            if (isset($inputs['hospital_id']) && $inputs['hospital_id'] && empty($inputs['clinic_id'])) {
                $clinics = Clinic::getByHospitalId($inputs['hospital_id']);
                $q->whereIn('clinic_id', $clinics);
            }
            if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                $q->where('clinic_id', $inputs['clinic_id']);
            }
            if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                $q->where('physician_id', $inputs['physician_id']);
            }
            if (isset($inputs['date_from']) && $inputs['date_from']) {
                $q->where('date', '>=', $inputs['date_from']);
            }
            if (isset($inputs['date_to']) && $inputs['date_to']) {
                $q->where('date', '<=', $inputs['date_to']);
            }
            if (isset($inputs['time_from']) && $inputs['time_from']) {
                $q->where('time_from', '>=', $inputs['time_from']);
            }
            if (isset($inputs['time_to']) && $inputs['time_to']) {
                $q->where('time_to', '<=', $inputs['time_to']);
            }
            if (isset($inputs['code']) && $inputs['code']) {
                $q->where('code', 'LIKE', '%' . $inputs['code'] . '%');
            }
            if ((isset($inputs['name']) && $inputs['name']) || (isset($inputs['phone']) && $inputs['phone'])
                || (isset($inputs['id']) && $inputs['id'])
                || (isset($inputs['registration_no']) && $inputs['registration_no'])
                || (isset($inputs['national_id']) && $inputs['national_id'])
            ) {
                $patients_id = Patient::searchPatient($inputs);
                $q->whereIn('patient_id', $patients_id);
            }
            if (isset($inputs['status']) && $inputs['status']) {
                $q->where('status', $inputs['status']);
            }
            if (isset($inputs['patient_status']) && ($inputs['patient_status'] || $inputs['patient_status'] === "0")) {
                if ($inputs['patient_status'] == 10) { // patient attend = 1
                    $q->where('patient_attend', 1);
                } else {
                    $q->where('patient_status', $inputs['patient_status']);
                }
            }
            if (isset($inputs['patient_attend']) && $inputs['patient_attend'] === '0') {
                $q->where('patient_attend', 0);
            }
            if (isset($inputs['walk_in_approval']) && ($inputs['walk_in_approval'] || $inputs['walk_in_approval'] === "0")) {
                $q->where('type', 2);
                $q->where('walk_in_approval', $inputs['walk_in_approval']);
            }
            if (isset($inputs['type']) && $inputs['type']) {
                $q->where('type', $inputs['type']);
            }
            if (isset($inputs['standAloneRevisit']) && $inputs['standAloneRevisit']) {
                $q->whereNull('parent_id_of_revisit');
            }
            if (isset($inputs['created_by']) && $inputs['created_by']) {
                $group = Sentry::findGroupByName($inputs['created_by']);
                $users = Sentry::findAllUsersInGroup($group);
                $q->whereIn('create_by', $users->lists('id'));
            }
        });
        $data = $data->orderBy('date', 'desc')->orderBy('time_from');
        if (isset($inputs['getCount']) && $inputs['getCount']) {
            $data = $data->count('id');
        } else if (isset($inputs['getIds']) && $inputs['getIds']) {
            $data = $data->lists('id');
        } else if (isset($inputs['getCountAndData']) && $inputs['getCountAndData']) {
            $array['count'] = $data->count('id');
            if ($with_paginate) {
                $array['data'] = $data->paginate(50);
            } else {
                $array['data'] = $data->get();
            }
            return $array;
        } else {
            if ($with_paginate) {
                $data = $data->paginate(25);
            } else {
                $data = $data->get();
            }
        }
        return $data;
    }

    public static function countRevisitOfReservation($id)
    {
        return self::where('parent_id_of_revisit', $id)
            ->where('patient_status', '!=', PatientStatus::cancel)->count('id');
    }

    public static function getRevisitOfReservation($id)
    {
        $data = self::where('parent_id_of_revisit', $id)
            ->where('patient_status', '!=', PatientStatus::cancel)->first();
        return $data;
    }

    public static function getReservationCounts($inputs)
    {
        if (Sentry::check()) {
            $user = Sentry::getUser();
        } else {
            $user = array();
        }
        $data = self::where(function ($q) use ($inputs, $user) {

            if ($user && $user->user_type_id == UserRules::physician) {
                $q->where('physician_id', $user->id);
            }
            if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
                $clinics = Clinic::getByHospitalId($inputs['hospital_id']);
                $q->whereIn('clinic_id', $clinics);
            }
            if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                $q->where('physician_id', $inputs['physician_id']);
            }
            if (isset($inputs['clinic_id']) && $inputs['clinic_id']) {
                $q->where('clinic_id', $inputs['clinic_id']);
            }
            if (isset($inputs['date_from']) && $inputs['date_from']) {
                $q->where('date', '>=', $inputs['date_from']);
            }
            if (isset($inputs['date_to']) && $inputs['date_to']) {
                $q->where('date', '<=', $inputs['date_to']);
            }
            if (isset($inputs['status']) && $inputs['status']) {
                $q->where('status', $inputs['status']);
            }
            if (isset($inputs['type']) && $inputs['type']) {
                $q->where('type', $inputs['type']);
            }
            if (isset($inputs['patient_status']) && ($inputs['patient_status'] || $inputs['patient_status'] === "0")) {
                $q->where('patient_status', $inputs['patient_status']);
            }
            if (isset($inputs['in_patient_status']) && ($inputs['in_patient_status'] || $inputs['in_patient_status'] === "0")) {
                $q->whereIn('patient_status', $inputs['in_patient_status']);
            }
            if (isset($inputs['not_in_patient_status']) && $inputs['not_in_patient_status']) {
                $q->whereNotIn('patient_status', $inputs['not_in_patient_status']);
            }
            if (isset($inputs['patient_attend']) && ($inputs['patient_attend'] || $inputs['patient_attend'] === '0')) {
                $q->where('patient_attend', $inputs['patient_attend']);
            }
            if (isset($inputs['cancel_reason_id']) && ($inputs['cancel_reason_id'])) {
                $q->where('cancel_reason_id', $inputs['cancel_reason_id']);
            }
            if (isset($inputs['patient_in_service']) && ($inputs['patient_in_service'])) {
                $q->where('patient_in_service', $inputs['patient_in_service']);
            }

        })->count();
        return $data;
    }

    public static function countByPhysicianAndDate($physician_id, $date, $patientAttend = true, $exceptRevisit = false,
                                                   $getCount = true, $onlyRevisit = false)
    {
        $data = self::where('physician_id', $physician_id)
            ->where('date', $date)
            ->where('patient_status', '!=', PatientStatus::archive)
            ->where('patient_status', '!=', PatientStatus::cancel)
            ->where('patient_status', '!=', PatientStatus::pending);
        if ($patientAttend) {
            $data = $data->where('patient_attend', 1);
        }
        if ($exceptRevisit) {
            $data = $data->where('type', '!=', 3);
        }
        if ($onlyRevisit) {
            $data = $data->where('type', 3);
        }
        if ($getCount) {
            return $data->count('id');
        } else {
            return $data->get()->toArray();
        }
    }

    public static function updateWhenClinicClose($clinic_id, $date)
    {
        return self::where('date', $date)->where('clinic_id', $clinic_id)
            ->where('status', ReservationStatus::reserved)
            ->update(array(
                'status' => ReservationStatus::no_show,
                'patient_status' => PatientStatus::no_show
            ));
    }

    public static function checkExistRecord($clinicId, $physicianId = '', $date, $timeFrom = '', $timeTo = '', $patient_id = '')
    {
        $data = self::where('date', $date)->where('clinic_id', $clinicId);
        if ($physicianId) {
            $data = $data->where('physician_id', $physicianId);
        }
        if ($timeFrom) {
            $data = $data->where('time_from', $timeFrom);
        }
        if ($timeTo) {
            $data = $data->where('time_to', $timeTo);
        }
        if ($patient_id) {
            $data = $data->where('patient_id', $patient_id);
        }
        $data = $data->whereIn('patient_status', array(PatientStatus::waiting, PatientStatus::pending, PatientStatus::archive))
            ->first();
        return $data;
    }

    public static function getReservedWithPeriod($clinic_ids, $dateFrom, $dateTo, $timeFrom = '', $timeTo = '')
    {
        return self::whereIn('clinic_id', $clinic_ids)->where('status', ReservationStatus::reserved)->
        where(function ($q) use ($dateFrom, $dateTo, $timeFrom, $timeTo) {
            $q->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo);
            if ($timeFrom && $timeFrom != '00:00:00' && $timeTo && $timeTo != '00:00:00') {
                $q->where(function ($q2) use ($timeFrom, $timeTo) {
                    $q2->where(function ($q3) use ($timeFrom, $timeTo) {
                        $q3->where('time_from', '>=', $timeFrom)->where('time_from', '<', $timeTo);
                    });
                    $q2->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                        $q3->where('time_to', '>', $timeFrom)->where('time_to', '<=', $timeTo);
                    });
                    $q2->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                        $q3->where('time_from', '<=', $timeFrom)->where('time_to', '>=', $timeTo);
                    });
                });
                $q->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                    $q3->where('revisit_time_from', '>', $timeFrom)->where('revisit_time_from', '<=', $timeTo);
                });
            }
        })->get()->toArray();
    }

    public static function getPendingWithPeriod($clinic_ids, $dateFrom, $dateTo, $timeFrom = '', $timeTo = '')
    {
        return self::whereIn('clinic_id', $clinic_ids)->where('patient_status', PatientStatus::pending)->
        where(function ($q) use ($dateFrom, $dateTo, $timeFrom, $timeTo) {
            $q->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo);
            if ($timeFrom && $timeFrom != '00:00:00' && $timeTo && $timeTo != '00:00:00') {
                $q->where(function ($q2) use ($timeFrom, $timeTo) {
                    $q2->where(function ($q3) use ($timeFrom, $timeTo) {
                        $q3->where('time_from', '>=', $timeFrom)->where('time_from', '<', $timeTo);
                    });
                    $q2->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                        $q3->where('time_to', '>', $timeFrom)->where('time_to', '<=', $timeTo);
                    });
                    $q2->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                        $q3->where('time_from', '<=', $timeFrom)->where('time_to', '>=', $timeTo);
                    });
                });
                $q->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                    $q3->where('revisit_time_from', '>', $timeFrom)->where('revisit_time_from', '<=', $timeTo);
                });
            }
        })->get()->toArray();
    }

    public static function pendingWithPeriod($clinic_ids, $dateFrom, $dateTo, $timeFrom = '', $timeTo = '', $public_holiday_name = '')
    {
        $reservations = self::getReservedWithPeriod($clinic_ids, $dateFrom, $dateTo, $timeFrom, $timeTo);
        foreach ($reservations as $key => $val) {
            Reservation::edit(array(
                'patient_status' => PatientStatus::pending,
//                'status' => ReservationStatus::pending,
                'show_reason' => 1,
                'exception_reason' => "Holiday!",
            ), $val['id']);
            ReservationHistory::add([
                'action' => 'Add Exception By Holiday',
                'action_by' => Sentry::getUser()->id,
                'reservation_id' => $val['id'],
                'code' => $val['code'],
                'physician_id' => $val['physician_id'],
                'clinic_id' => $val['clinic_id'],
                'patient_id' => $val['patient_id'],
                'date' => $val['date'],
                'time_from' => $val['time_from'],
                'time_to' => $val['time_to'],
                'status' => $val['status'],
                'patient_status' => PatientStatus::pending,
                'exception_reason' => '',
            ]);
        }
        return 1;
    }

    public static function reservedWithPeriod($clinic_ids, $dateFrom, $dateTo, $timeFrom = '', $timeTo = '')
    {
        $reservations = self::getPendingWithPeriod($clinic_ids, $dateFrom, $dateTo, $timeFrom, $timeTo);
        foreach ($reservations as $key => $val) {
            Reservation::edit(array(
                'patient_status' => PatientStatus::waiting,
                'status' => ReservationStatus::reserved,
            ), $val['id']);
            ReservationHistory::add([
                'action' => 'Resume',
                'action_by' => Sentry::getUser()->id,
                'reservation_id' => $val['id'],
                'code' => $val['code'],
                'physician_id' => $val['physician_id'],
                'clinic_id' => $val['clinic_id'],
                'patient_id' => $val['patient_id'],
                'date' => $val['date'],
                'time_from' => $val['time_from'],
                'time_to' => $val['time_to'],
                'patient_status' => PatientStatus::waiting,
                'status' => ReservationStatus::reserved,
            ]);
        }
        return 1;
    }

    public static function getReservedWithPeriodByPhysician($physician_id, $dateFrom, $dateTo, $timeFrom = '', $timeTo = '')
    {
        return self::where('physician_id', $physician_id)->where('status', ReservationStatus::reserved)
            ->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo)
            ->where(function ($q) use ($timeFrom, $timeTo) {
                if ($timeFrom && $timeFrom != '00:00:00' && $timeTo && $timeTo != '00:00:00') {
                    $q->where(function ($q2) use ($timeFrom, $timeTo) {
                        $q2->where(function ($q3) use ($timeFrom, $timeTo) {
                            $q3->where('time_from', '>=', $timeFrom)->where('time_from', '<', $timeTo);
                        });
                        $q2->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                            $q3->where('time_to', '>', $timeFrom)->where('time_to', '<=', $timeTo);
                        });
                        $q2->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                            $q3->where('time_from', '<=', $timeFrom)->where('time_to', '>=', $timeTo);
                        });
                    });
                    $q->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                        $q3->where('revisit_time_from', '>', $timeFrom)->where('revisit_time_from', '<=', $timeTo);
                    });
                }
            })->get()->toArray();
    }

    public static function getPendingWithPeriodByPhysician($physician_id, $dateFrom, $dateTo, $timeFrom = '', $timeTo = '')
    {
        return self::where('physician_id', $physician_id)->where('patient_status', PatientStatus::pending)->
        where(function ($q) use ($dateFrom, $dateTo, $timeFrom, $timeTo) {
            $q->where('date', '>=', $dateFrom)->where('date', '<=', $dateTo);
            if ($timeFrom && $timeFrom != '00:00:00' && $timeTo && $timeTo != '00:00:00') {
                $q->where(function ($q2) use ($timeFrom, $timeTo) {
                    $q2->where(function ($q3) use ($timeFrom, $timeTo) {
                        $q3->where('time_from', '>=', $timeFrom)->where('time_from', '<', $timeTo);
                    });
                    $q2->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                        $q3->where('time_to', '>', $timeFrom)->where('time_to', '<=', $timeTo);
                    });
                    $q2->orWhere(function ($q3) use ($timeFrom, $timeTo) {
                        $q3->where('time_from', '<=', $timeFrom)->where('time_to', '>=', $timeTo);
                    });
                });
                $q->orWhere(function ($q2) use ($timeFrom, $timeTo) {
                    $q2->where('revisit_time_from', '>', $timeFrom)->where('revisit_time_from', '<=', $timeTo);
                });
            }
        })->get()->toArray();
    }

    public static function pendingWithPeriodByPhysician($physician_id, $dateFrom, $dateTo, $reason, $timeFrom = '', $timeTo = '', $effect
        , $schedule_times = '', $physician_schedule_id = '')
    {
//        dd($physician_id, $dateFrom, $dateTo, $timeFrom, $timeTo);
        $reservations = self::getReservedWithPeriodByPhysician($physician_id, $dateFrom, $dateTo, $timeFrom, $timeTo);
//        dd($reservations);
        $daysName = array(
            'saturday' => 'sat',
            'sunday' => 'sun',
            'monday' => 'mon',
            'tuesday' => 'tues',
            'wednesday' => 'wed',
            'thursday' => 'thurs',
            'friday' => 'fri',
        );
        foreach ($reservations as $key => $val) {
            if ($schedule_times) {
                $inputDayName = lcfirst(date('l', strtotime($val['date'])));
                $phySch = PhysicianSchedule::getById($physician_schedule_id, true, $val['date']);
                if ($phySch) {
                    $startTime = $phySch[$daysName[$inputDayName] . '_start_time_1'];
                    if (strpos($schedule_times, $startTime . ' ') === false) {
                        continue;
                    } else {
                        $finalArray = array(
                            'patient_status' => PatientStatus::pending,
                            'exception_reason' => $reason,
                            'show_reason' => 1,
                        );
                        if ($effect == 2) {
                            unset($finalArray['patient_status']);
                        }
                        Reservation::edit($finalArray, $val['id']);
                        Logging::add([
                            'action' => LoggingAction::pending_reservation,
                            'table' => 'reservations',
                            'ref_id' => $val['id'],
                            'user_id' => Sentry::getUser()->id,
                        ]);
                        $reservation = Reservation::getById($val['id']);
                        $history = [
                            'action' => 'Add Exception',
                            'action_by' => Sentry::getUser()->id,
                            'reservation_id' => $val['id'],
                            'code' => $val['code'],
                            'physician_id' => $val['physician_id'],
                            'clinic_id' => $val['clinic_id'],
                            'patient_id' => $val['patient_id'],
                            'date' => $val['date'],
                            'time_from' => $val['time_from'],
                            'time_to' => $val['time_to'],
                            'status' => $val['status'],
                            'patient_status' => $reservation['patient_status'],
                            'exception_reason' => $reason,
                        ];
                        ReservationHistory::add($history);
                    }
                } else {
                    continue;
                }
            } else {
                $finalArray = array(
                    'patient_status' => PatientStatus::pending,
                    'exception_reason' => $reason,
                    'show_reason' => 1,
                );
                if ($effect == 2) {
                    unset($finalArray['patient_status']);
                }
                Reservation::edit($finalArray, $val['id']);
                Logging::add([
                    'action' => LoggingAction::pending_reservation,
                    'table' => 'reservations',
                    'ref_id' => $val['id'],
                    'user_id' => Sentry::getUser()->id,
                ]);
                $reservation = Reservation::getById($val['id']);
                $history = [
                    'action' => 'Add Exception',
                    'action_by' => Sentry::getUser()->id,
                    'reservation_id' => $val['id'],
                    'code' => $val['code'],
                    'physician_id' => $val['physician_id'],
                    'clinic_id' => $val['clinic_id'],
                    'patient_id' => $val['patient_id'],
                    'date' => $val['date'],
                    'time_from' => $val['time_from'],
                    'time_to' => $val['time_to'],
                    'status' => $val['status'],
                    'patient_status' => $reservation['patient_status'],
                    'exception_reason' => $reason,
                ];
                ReservationHistory::add($history);
            }
            $reservationData = Reservation::getById($val['id']);
            $clinics = Clinic::getById($reservationData['clinic_id']);
            if ($clinics['hospital_id'] == 2) {
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
                $reservationData['reasonName'] = $reason;
                if (app('send_sms')) {
                    $smsArray = array(
                        'patient_id' => $reservationData['patient_id'],
                        'reservation_id' => $val['id'],
                        'type' => 'Pending_Exception',
                    );
                    if ($reservationData['sms_lang'] == 1) { // arabic
                        $smsArray['message'] = trans('sms.pending-ar', $reservationData->toArray());
                    } else { // english
                        $smsArray['message'] = trans('sms.pending', $reservationData->toArray());
                    }
                    PatientSMS::add($smsArray);
                }
            }
        }
        return 1;
    }

    public static function reservedWithPeriodByPhysician($physician_id, $dateFrom, $dateTo, $timeFrom = '', $timeTo = '', $schedule_times
        , $physician_schedule_id)
    {
        $reservations = self::getPendingWithPeriodByPhysician($physician_id, $dateFrom, $dateTo, $timeFrom, $timeTo);
        $daysName = array(
            'saturday' => 'sat',
            'sunday' => 'sun',
            'monday' => 'mon',
            'tuesday' => 'tues',
            'wednesday' => 'wed',
            'thursday' => 'thurs',
            'friday' => 'fri',
        );
        foreach ($reservations as $key => $val) {
            if ($schedule_times) {
                $inputDayName = lcfirst(date('l', strtotime($val['date'])));
                $phySch = PhysicianSchedule::getById($physician_schedule_id, true, $val['date']);
                if ($phySch) {
                    $startTime = $phySch[$daysName[$inputDayName] . '_start_time_1'];
                    if (strpos($schedule_times, $startTime . ' ') === false) {
                        continue;
                    } else {
                        Reservation::edit(array(
                            'patient_status' => PatientStatus::waiting,
                            'status' => ReservationStatus::reserved,
                            'show_reason' => 2,
                        ), $val['id']);
                        ReservationHistory::add([
                            'action' => 'Resume From Exception',
                            'action_by' => Sentry::getUser()->id,
                            'reservation_id' => $val['id'],
                            'code' => $val['code'],
                            'physician_id' => $val['physician_id'],
                            'clinic_id' => $val['clinic_id'],
                            'patient_id' => $val['patient_id'],
                            'date' => $val['date'],
                            'time_from' => $val['time_from'],
                            'time_to' => $val['time_to'],
                            'status' => ReservationStatus::reserved,
                            'patient_status' => PatientStatus::waiting,
                        ]);
                    }
                } else {
                    continue;
                }
            } else {
                Reservation::edit(array(
                    'patient_status' => PatientStatus::waiting,
                    'status' => ReservationStatus::reserved,
                    'show_reason' => 2,
                ), $val['id']);
                ReservationHistory::add([
                    'action' => 'Resume From Exception',
                    'action_by' => Sentry::getUser()->id,
                    'reservation_id' => $val['id'],
                    'code' => $val['code'],
                    'physician_id' => $val['physician_id'],
                    'clinic_id' => $val['clinic_id'],
                    'patient_id' => $val['patient_id'],
                    'date' => $val['date'],
                    'time_from' => $val['time_from'],
                    'time_to' => $val['time_to'],
                    'status' => ReservationStatus::reserved,
                    'patient_status' => PatientStatus::waiting,
                ]);
            }
        }
        return 1;
    }

    public static function getByClinicAndPhysicianIds($clinicId, $physicianIds)
    {
        $data = self::where('date', date('Y-m-d'))
            ->where('clinic_id', $clinicId)
            ->whereIn('physician_id', $physicianIds)
            ->whereIn('status', array(
                ReservationStatus::on_progress,
                ReservationStatus::reserved,
            ))->orderBy('time_from')->get()->toArray();

        foreach ($data as $key => $val) {
            $data[$key]['physician_name'] = User::getNameById($val['physician_id']);
            $data[$key]['patient_name'] = Patient::getName($val['patient_id']);
        }
        return $data;
    }

    public static function getAttendByClinic($clinicId = '', $inputs = '', $physician_id = '', $withPatientIn = true
        , $checkNextPatient = false)
    {
        $data = self::selectRaw('*, CASE
                                    WHEN type = 1 THEN time_from
                                    WHEN type = 2 THEN NULL
                                    WHEN type = 3 THEN revisit_time_from
                                    END AS times');
        if (isset($inputs['getYesterdayAfter24Hour']) && $inputs['getYesterdayAfter24Hour']) {
            $data = $data->where(function ($q) {
                $q->where('date', date('Y-m-d'));
                $q->orWhere(function ($q2) {
                    $yesterday = date("Y-m-d", strtotime("-1 days", strtotime(date('Y-m-d'))));
                    $q2->where('date', $yesterday);
                    $q2->where(function ($q3) {
                        $q3->where('time_from', '>', '23:59:00');
                        $q3->orWhere('revisit_time_from', '>', '23:59:00');
                    });
                });
            });
        } else {
            $data = $data->where('date', date('Y-m-d'));
        }
//        if (isset($inputs['reception_call_flag']) && $inputs['reception_call_flag']) {
//            $data = $data->where('reception_call_flag', 2);
//        } else {
        $data = $data->where('patient_attend', 1);
//        }
        $data = $data->where('patient_in_service', '!=', 1);
        if ($clinicId) {
            $data = $data->where('clinic_id', $clinicId);
        }
        if ($physician_id) {
            $data = $data->where('physician_id', $physician_id);
        }
        if ($withPatientIn) {
            $data = $data->whereIn('patient_status', array(
                PatientStatus::patient_in,
                PatientStatus::waiting,
            ));
        } else {
            $data = $data->where(function ($q) {
                $q->where('patient_status', PatientStatus::waiting);
                $q->orWhere(function ($q2) {
                    $q2->where('patient_status', PatientStatus::patient_in);
                    $q2->where('patient_in_service', 3); // service done
                });
            });
        }
        if ($checkNextPatient) {
            $data = $data->where('next_patient_flag', '!=', 1);
        }
        $data = $data->orderByRaw('- times DESC');
        if (isset($inputs['limit']) && $inputs['limit']) {
            $data = $data->limit($inputs['limit']);
        }
        if (isset($inputs['getFirst']) && $inputs['getFirst']) {
            $data = $data->first();
        } else {
            $data = $data->get();
        }
        if (isset($inputs['details']) && $inputs['details']) {
            foreach ($data as $key => $val) {
                if ($val['type'] == 2) { // if walk in and not approved
                    if ($val['walk_in_approval'] != 1) {
                        unset($data[$key]);
                        continue;
                    }
                }
                $data[$key]['physician'] = User::getById($val['physician_id']);
                $data[$key]['patient'] = Patient::getById($val['patient_id']);
                $data[$key]['clinic'] = Clinic::getById($val['clinic_id']);
            }
        }
        return $data;
    }

    public static function getIdsByCode($code)
    {
        return self::where('code', 'LIKE', '%' . $code . '%')->lists('id');
    }

    public static function getSumRevisitDuration($clinic_id, $physician_id, $date)
    {
        return self::where('date', $date)->where('physician_id', $physician_id)
            ->where('clinic_id', $clinic_id)
            ->where('type', 3)// type revisit
            ->sum('revisit_duration');
    }

    public static function checkPatientExistRecord($clinicId, $physicianId, $date, $patient_id)
    {
        return self::where('date', $date)->where('clinic_id', $clinicId)
            ->where('physician_id', $physicianId)
            ->where('patient_id', $patient_id)
            ->where('patient_status', '!=', PatientStatus::cancel)
            ->first();
    }

    public static function getPatientIdsByDate($date)
    {
        return self::where('date', $date)->lists('patient_id');
    }

    public static function getNewWalkInByPhysician($physicianId)
    {
        return self::where('date', '>=', date('Y-m-d'))
            ->where('physician_id', $physicianId)
            ->where('type', 2)
            ->where('walk_in_approval', 0)
            ->count();
    }

    public static function getNewWalkIn()
    {
        $data = self::where('date', '>=', date('Y-m-d'))
            ->where('type', 2)
            ->where('walk_in_approval', 0)
            ->count();
        return ($data);
    }

    public static function getApprovalWalkIn()
    {
        return self::where('date', '>=', date('Y-m-d'))
            ->where('type', 2)
            ->where('walk_in_approval', 1)
            ->count();
    }

    public static function noShowAllReservations()
    {
        $data = self::where('date', '<', date('Y-m-d'))
            ->where('date', '>=', date('Y-m-d', strtotime("-1 week +1 day")))
            ->where(function ($q) {
                $q->where('status', ReservationStatus::reserved);
                $q->orWhere('status', ReservationStatus::on_progress);
            })->where('patient_status', '!=', PatientStatus::pending)->get();
        foreach ($data as $key => $val) {
            if ($val['status'] == ReservationStatus::reserved) {
                if ($val['patient_attend'] == 1) {
                    continue;
                }
                Reservation::edit(array(
                    'status' => ReservationStatus::no_show,
                    'patient_status' => PatientStatus::no_show
                ), $val['id']);
            } else {
                $phySchedule = PhysicianSchedule::getByPhysicianId_Date($val['physician_id'], $val['date'], true, $val['clinic_id']);
                if ($phySchedule) {
                    $seconds = Functions::hoursToSeconds($val['actual_time_from']);
                    $newSeconds = $seconds + ($phySchedule['slots'] * 60);
                    $actual_time_to = Functions::timeFromSeconds($newSeconds);

                    Reservation::edit(array(
                        'status' => ReservationStatus::accomplished,
                        'patient_status' => PatientStatus::patient_out,
                        'actual_time_to' => $actual_time_to
                    ), $val['id']);
                }
            }
        }

    }

    public static function getLastOfPatient($patient_id)
    {
        return self::where('patient_id', $patient_id)->orderBy('id', 'desc')->first();
    }

    // not used!
    public static function checkByPatientIdAndDateAndDoctor($patient_registrationNo, $date, $physician_his)
    {
        $patient = Patient::getByRegistrationNo($patient_registrationNo);
        $physician = User::checkHisExist($physician_his);
        if ($physician && $patient) {
            return self::where('patient_id', $patient['id'])
                ->where('physician_id', $physician['id'])
                ->where('date', $date)
                ->first();
        } else {
            return array();
        }
    }

    public static function cancelAllReservationOfDoctor($physician_id, $date)
    {
        $user = Sentry::getUser();
        $data = self::where('date', '>=', $date)
            ->where('physician_id', $physician_id)
            ->where('status', '!=', ReservationStatus::cancel)
            ->get();
        foreach ($data as $key => $val) {
            $array = array(
                'status' => ReservationStatus::cancel,
                'patient_status' => PatientStatus::cancel,
                'update_by' => $user->id,
                'notes' => 'Doctor Resigned',
                'cancel_notes' => 'Doctor Resigned',
                'exception_reason' => 'Doctor Resigned',
                'show_reason' => 1,
                'send_cancel_sms' => 1,
            );
            Reservation::edit($array, $val['id']);
            ReservationHistory::add([
                'action' => 'Doctor Resigned',
                'action_by' => $user->id,
                'reservation_id' => $val['id'],
                'code' => $val['code'],
                'physician_id' => $val['physician_id'],
                'clinic_id' => $val['clinic_id'],
                'patient_id' => $val['patient_id'],
                'date' => $val['date'],
                'time_from' => $val['time_from'],
                'time_to' => $val['time_to'],
                'status' => ReservationStatus::cancel,
                'patient_status' => PatientStatus::cancel,
            ]);
            $reservationData = Reservation::getById($val['id']);
            $reservationData['date'] = date('dMY', strtotime($reservationData['date']));
            $reservationData['time_from'] = date('h:ia', strtotime($reservationData['time_from']));
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
                    'reservation_id' => $reservationData['id'],
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

    public static function getByReceptionIp($inputs = '')
    {
        return self::where('reception_ip', $inputs['ip'])
            ->where('reception_call_flag', 1)
            ->where('date', date('Y-m-d'))
            ->orderBy('reception_call_datetime', 'desc')
            ->first();
    }

    public static function getPatientWithReservation($patient_id, $reservation_id)
    {
        return self::where('id', $reservation_id)
            ->where('patient_id', $patient_id)
            ->first();
    }

    public static function getCountOnlineReservationOfPatient($inputs = '')
    {
        return self::where('patient_id', $inputs['patient_id'])
            ->where('source_type', 2)// online
            ->where('date', '>=', $inputs['from_date'])
            ->where('date', '<=', $inputs['to_date'])
            ->count('id');
    }
}
