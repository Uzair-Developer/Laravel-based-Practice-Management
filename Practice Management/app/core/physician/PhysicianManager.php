<?php
namespace core\physician;


use AttributePms;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\BaseManager;
use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use core\enums\ResponseTypes;
use core\enums\UserRules;
use core\user\UserRepository;
use core\userLocalization\UserLocalizationRepository;
use Functions;
use Input;
use PhysicianException;
use PublicHoliday;
use Reservation;
use User;

class PhysicianManager extends BaseManager
{
    function __construct()
    {
        $this->PhysicianValidator = new PhysicianValidator();
        $this->user = Sentry::getUser();
    }

    public function updatePhysician($inputs, $id)
    {
        $validator = $this->PhysicianValidator->validatePhysician($inputs);
        if ($validator->fails()) {
            return $this->response()->ResponseObject(ResponseTypes::error, $validator->messages());
        }
        try {
            $user = Sentry::getUserProvider()->findById($id);
            if ($inputs['password']) {
                if ($inputs['password_confirmation']) {
                    if ($inputs['password'] == $inputs['password_confirmation']) {
                        $user->password = $inputs['password'];
                    } else {
                        return $this->response()->ResponseObject(ResponseTypes::error, 'Password Confirmation must equal Password field!');
                    }
                } else {
                    return $this->response()->ResponseObject(ResponseTypes::error, 'Password Confirmation is required!');
                }
            }
            if (!empty($inputs['image_url'])) {
                $file = Input::file('image_url');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path() . '/uploads/physician/images';
                $upload_success = $file->move($destinationPath, $filename);
                if ($upload_success) {
                    $inputs['image_url'] = 'uploads/physician/images/' . $filename;
                    $user->image_url = $inputs['image_url'];
                }
            } else {
                unset($inputs['image_url']);
            }
            $user->user_specialty_id = isset($inputs['specialty_id']) ? implode(',', $inputs['specialty_id']) : null;
            $user->user_experience_id = isset($inputs['user_experience_id']) ? $inputs['user_experience_id'] : null;
            $user->extension_num = $inputs['extension_num'];
            $user->email = $inputs['email'];
            $user->full_name = $inputs['first_name'] . ' ' . $inputs['middle_name'] . ' ' . $inputs['last_name'] . ' ' . $inputs['family_name'];
            $user->first_name = $inputs['first_name'];
            $user->middle_name = $inputs['middle_name'];
            $user->last_name = $inputs['last_name'];
            $user->family_name = $inputs['family_name'];
            $user->first_name_ar = $inputs['first_name_ar'];
            $user->last_name_ar = $inputs['last_name_ar'];
            $user->phone_number = isset($inputs['phone_number']) ? $inputs['phone_number'] : '';
            $user->mobile1 = $inputs['mobile1'];
            $user->mobile2 = isset($inputs['mobile2']) ? $inputs['mobile2'] : '';
            $user->address = isset($inputs['address']) ? $inputs['address'] : '';
            if ($this->user->user_type_id == 1 || $this->user->user_type_id != 7) {
                $user->bookable = $inputs['bookable'] == 1 ? 1 : 2;
                $user->in_report = isset($inputs['in_report']) ? 1 : 2;
                $user->revisit_limit = $inputs['revisit_limit'] ? $inputs['revisit_limit'] : 0;
            }
            $user->save();
            $save_status = $inputs['save_status'];
            unset($inputs['save_status']);
            unset($inputs['password']);
            unset($inputs['password_confirmation']);
            unset($inputs['image_url']);
            unset($inputs['first_name']);
            unset($inputs['middle_name']);
            unset($inputs['last_name']);
            unset($inputs['family_name']);
            unset($inputs['first_name_ar']);
            unset($inputs['last_name_ar']);
            unset($inputs['extension_num']);
            unset($inputs['email']);
            unset($inputs['phone_number']);
            unset($inputs['mobile1']);
            unset($inputs['mobile2']);
            unset($inputs['address']);
            unset($inputs['specialty_id']);
            unset($inputs['user_experience_id']);
            unset($inputs['bookable']);
            unset($inputs['in_report']);
            unset($inputs['revisit_limit']);
            if (isset($inputs['clinic_services'])) {
                $inputs['clinic_services'] = implode(',', $inputs['clinic_services']);
            }
            if (isset($inputs['performed_operations'])) {
                $inputs['performed_operations'] = implode(',', $inputs['performed_operations']);
            }
            if (isset($inputs['equipments'])) {
                $inputs['equipments'] = implode(',', $inputs['equipments']);
            }
            if (!empty($inputs['attaches'])) {
                $file = Input::file('attaches');
                $filename = time() . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path() . '/uploads/physician/attaches';
                $upload_success = $file->move($destinationPath, $filename);
                if ($upload_success) {
                    $inputs['attaches'] = 'uploads/physician/attaches/' . $filename;
                }
            } else {
                unset($inputs['attaches']);
            }
            $inputs['country_id2'] = isset($inputs['country_id']) ? $inputs['country_id'] : '';
            $inputs['city_id2'] = isset($inputs['city_id']) ? $inputs['city_id'] : '';
            $inputs['birthdate2'] = isset($inputs['birthdate']) ? $inputs['birthdate'] : '';
            $inputs['gender2'] = isset($inputs['gender']) ? $inputs['gender'] : '';
            $inputs['specialty_id2'] = isset($inputs['specialty_id']) ? $inputs['specialty_id'] : '';
            $inputs['graduation2'] = isset($inputs['graduation']) ? $inputs['graduation'] : '';
            $inputs['graduated_from2'] = isset($inputs['graduated_from']) ? $inputs['graduated_from'] : '';
            $inputs['degree2'] = isset($inputs['degree']) ? $inputs['degree'] : '';
            $inputs['job_position2'] = isset($inputs['job_position']) ? $inputs['job_position'] : '';
            $inputs['about2'] = isset($inputs['about']) ? $inputs['about'] : '';
            $inputs['attaches2'] = isset($inputs['attaches']) ? $inputs['attaches'] : '';
            $inputs['license_number2'] = isset($inputs['license_number']) ? $inputs['license_number'] : '';
            $inputs['license_activation2'] = isset($inputs['license_activation']) ? $inputs['license_activation'] : '';
            $inputs['certificates2'] = isset($inputs['certificates']) ? $inputs['certificates'] : '';
            $inputs['awards2'] = isset($inputs['awards']) ? $inputs['awards'] : '';
            $inputs['credentials2'] = isset($inputs['credentials']) ? $inputs['credentials'] : '';
            $inputs['equipments2'] = isset($inputs['equipments']) ? $inputs['equipments'] : '';
            $inputs['clinic_services2'] = isset($inputs['clinic_services']) ? $inputs['clinic_services'] : '';
            $inputs['performed_operations2'] = isset($inputs['performed_operations']) ? $inputs['performed_operations'] : '';
            $inputs['notes2'] = isset($inputs['notes']) ? $inputs['notes'] : '';
            $inputs['user_id'] = $id;
            $Repo = new PhysicianRepository();
            $physicianData = $Repo->getByUserId($id);
            if ($physicianData) {
                if ($this->user->user_type_id == 7 && !$this->user->hasAccess('head_dept.access')) { // doctor is login
                    if ($save_status == 'physician_save_submit') {
                        $inputs['current_status'] = 1; // wait to approve
                        $inputs['previous_status'] = $physicianData['current_status'];
                    }
                }
                $Repo->update($inputs, $id);
            } else {
                if ($this->user->user_type_id == 7 && !$this->user->hasAccess('head_dept.access')) { // doctor is login
                    if ($save_status == 'physician_save_submit') {
                        $inputs['current_status'] = 1; // wait to approve
                        $inputs['previous_status'] = 0;
                    }
                }
                $Repo->save($inputs);
            }
            return $this->response()->ResponseObject(ResponseTypes::success, 'Updated Successfully');
        } catch (\Exception $e) {
            return $this->response()->ResponseObject(ResponseTypes::error, "Ops, Their is error please try again");
        }
    }

    public function getPhysicianByClinicId($clinic_id, $bookable = false)
    {
        $ULRepo = new UserLocalizationRepository();
        if ($this->user->user_type_id == UserRules::physician) {
            if ($ULRepo->isClinicExistForUser($this->user->id, $clinic_id)) {
                $physicians[0] = User::getById($this->user->id);
            } else {
                return array();
            }
        } else {
            $physiciansIds = User::getPhysicianByClinicId($clinic_id);
            $physicians = User::getByIds($physiciansIds, null, null, $bookable);
        }
        return $physicians;
    }

    public function getAvailableTimeOfPhysician(&$availableTimes, $physicianSchedule, $clinicSchedule, $selected_date, $revisit = false)
    {
        $hospital_id = $clinicSchedule['hospital_id'];
        $publicHoliday = PublicHoliday::checkExist($hospital_id, $selected_date);
        if (!empty($publicHoliday)) {
            return;
        }
        $daysName = array(
            'saturday' => 'sat',
            'sunday' => 'sun',
            'monday' => 'mon',
            'tuesday' => 'tues',
            'wednesday' => 'wed',
            'thursday' => 'thurs',
            'friday' => 'fri',
        );
        $inputDayName = lcfirst(date('l', strtotime($selected_date)));
        $physicianException = PhysicianException::checkExist($physicianSchedule['user_id'], $selected_date);
        if (!empty($physicianException)) {
            foreach ($physicianException as $key => $val) {
                if ($val['schedule_times']) {
                    if ($physicianSchedule) {
                        $startTime = $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'];
                        if (strpos($val['schedule_times'], $startTime . ' ') !== false) {
                            if ($val['all_day'] && $val['effect'] == 1) {
                                return;
                            }
                        }
                    }
                } else {
                    if ($val['all_day'] && $val['effect'] == 1) {
                        return;
                    }
                }
            }
        }
        $counts = 0;
        if (strpos($clinicSchedule['shift1_day_of'], $inputDayName) === false
            || strpos($physicianSchedule['dayoff_1'], $inputDayName) === false
        ) {
            $start = $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'];
            $end = $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'];
            if ($start < $end) {
                while (1 == 1) {
                    if ($end > $start) {
                        $physicianException = PhysicianException::checkExist($physicianSchedule['user_id'], $selected_date, $start, $physicianSchedule['slots']);
                        if (empty($physicianException)) {
                            $availableTimes[$counts]['time'] = $start;
                            $availableTimes[$counts]['shift'] = 1;
                            $counts++;
                        } else {
                            foreach ($physicianException as $key => $val) {
                                if ($val['schedule_times']) {
                                    if ($physicianSchedule) {
                                        $startTime_1 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'] ?
                                            $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'] : 'null';
                                        $endTime_1 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'] ?
                                            $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'] : 'null';
                                        if ($physicianSchedule['num_of_shifts'] == 2 || $physicianSchedule['num_of_shifts'] == 3) {
                                            $startTime_2 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'] ?
                                                $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'] : 'null';
                                            $endTime_2 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'] ?
                                                $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'] : 'null';
                                            if ($physicianSchedule['num_of_shifts'] == 3) {
                                                $startTime_3 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'] ?
                                                    $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'] : 'null';
                                                $endTime_3 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'] ?
                                                    $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'] : 'null';
                                            }
                                        }
                                        if (
                                            ($physicianSchedule['num_of_shifts'] == 1 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                                                && strpos($val['schedule_times'], $endTime_1) !== false)
                                            ||
                                            ($physicianSchedule['num_of_shifts'] == 2 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                                                && strpos($val['schedule_times'], $endTime_1) !== false && strpos($val['schedule_times'], $startTime_2 . ' ') !== false
                                                && strpos($val['schedule_times'], $endTime_2) !== false)
                                            ||
                                            ($physicianSchedule['num_of_shifts'] == 3 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                                                && strpos($val['schedule_times'], $endTime_1) !== false && strpos($val['schedule_times'], $startTime_2 . ' ') !== false
                                                && strpos($val['schedule_times'], $endTime_2) !== false && strpos($val['schedule_times'], $startTime_3 . ' ') !== false
                                                && strpos($val['schedule_times'], $endTime_3) !== false)

                                        ) {
                                            if ($val['effect'] == 1) {
                                                $availableTimes[$counts]['reserved'] = true;
                                                $availableTimes[$counts]['effect'] = 1;
                                                $availableTimes[$counts]['time'] = $start;
                                                $availableTimes[$counts]['shift'] = 1;
                                                $availableTimes[$counts]['patient_attend'] = 0;
                                                $availableTimes[$counts]['status'] = ReservationStatus::not_available;
                                                $availableTimes[$counts]['patient_status'] = PatientStatus::not_available;
                                                $availableTimes[$counts]['show_reason'] = 1;
                                                $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                                $availableTimes[$counts]['exception_reason_id'] = $val['reason_id'];
                                                $counts++;
                                                break;
                                            } else {
                                                $availableTimes[$counts]['time'] = $start;
                                                $availableTimes[$counts]['effect'] = 2;
                                                $availableTimes[$counts]['shift'] = 1;
                                                $availableTimes[$counts]['show_reason'] = 1;
                                                $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                                $counts++;
                                                break;
                                            }
                                        } else {
                                            if (isset($physicianException[$key + 1])) {
                                                continue;
                                            }
                                            $availableTimes[$counts]['time'] = $start;
                                            $availableTimes[$counts]['shift'] = 1;
                                            $counts++;
                                            break;
                                        }
                                    }
                                } else {
                                    if ($val['effect'] == 1) {
                                        $availableTimes[$counts]['reserved'] = true;
                                        $availableTimes[$counts]['effect'] = 1;
                                        $availableTimes[$counts]['time'] = $start;
                                        $availableTimes[$counts]['shift'] = 1;
                                        $availableTimes[$counts]['patient_attend'] = 0;
                                        $availableTimes[$counts]['status'] = ReservationStatus::not_available;
                                        $availableTimes[$counts]['patient_status'] = PatientStatus::not_available;
                                        $availableTimes[$counts]['show_reason'] = 1;
                                        $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                        $availableTimes[$counts]['exception_reason_id'] = $val['reason_id'];
                                        $counts++;
                                        break;
                                    } else {
                                        $availableTimes[$counts]['time'] = $start;
                                        $availableTimes[$counts]['effect'] = 2;
                                        $availableTimes[$counts]['shift'] = 1;
                                        $availableTimes[$counts]['show_reason'] = 1;
                                        $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                        $counts++;
                                        break;
                                    }
                                }
                            }
                        }
                        $seconds = Functions::hoursToSeconds($start);
                        $newSeconds = $seconds + ($physicianSchedule['slots'] * 60);
                        $start = Functions::timeFromSeconds($newSeconds);
//                        $start = date("H:i:s", strtotime($physicianSchedule['slots'] . ' minutes', strtotime($start)));
                    } else {
                        break;
                    }
                }
            }
        }
        //////////////////Shift 2 and Shift 3/////////////////////////
        if ($physicianSchedule['num_of_shifts'] == 2 || $physicianSchedule['num_of_shifts'] == 3) {
            if (strpos($clinicSchedule['shift2_day_of'], $inputDayName) === false
                || strpos($physicianSchedule['dayoff_2'], $inputDayName) === false
            ) {
                $start = $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'];
                $end = $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'];
                if ($start < $end) {
                    while (1 == 1) {
                        if ($end > $start) {
                            $physicianException = PhysicianException::checkExist($physicianSchedule['user_id'], $selected_date, $start, $physicianSchedule['slots']);
                            if (empty($physicianException)) {
                                $availableTimes[$counts]['time'] = $start;
                                $availableTimes[$counts]['shift'] = 2;
                                $counts++;
                            } else {
                                foreach ($physicianException as $key => $val) {
                                    if ($val['schedule_times']) {
                                        if ($physicianSchedule) {
                                            $startTime_1 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'] ?
                                                $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'] : 'null';
                                            $endTime_1 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'] ?
                                                $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'] : 'null';
                                            if ($physicianSchedule['num_of_shifts'] == 2 || $physicianSchedule['num_of_shifts'] == 3) {
                                                $startTime_2 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'] ?
                                                    $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'] : 'null';
                                                $endTime_2 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'] ?
                                                    $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'] : 'null';
                                                if ($physicianSchedule['num_of_shifts'] == 3) {
                                                    $startTime_3 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'] ?
                                                        $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'] : 'null';
                                                    $endTime_3 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'] ?
                                                        $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'] : 'null';
                                                }
                                            }
                                            if (
                                                ($physicianSchedule['num_of_shifts'] == 1 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                                                    && strpos($val['schedule_times'], $endTime_1) !== false)
                                                ||
                                                ($physicianSchedule['num_of_shifts'] == 2 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                                                    && strpos($val['schedule_times'], $endTime_1) !== false && strpos($val['schedule_times'], $startTime_2 . ' ') !== false
                                                    && strpos($val['schedule_times'], $endTime_2) !== false)
                                                ||
                                                ($physicianSchedule['num_of_shifts'] == 3 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                                                    && strpos($val['schedule_times'], $endTime_1) !== false && strpos($val['schedule_times'], $startTime_2 . ' ') !== false
                                                    && strpos($val['schedule_times'], $endTime_2) !== false && strpos($val['schedule_times'], $startTime_3 . ' ') !== false
                                                    && strpos($val['schedule_times'], $endTime_3) !== false)

                                            ) {
                                                if ($val['effect'] == 1) {
                                                    $availableTimes[$counts]['reserved'] = true;
                                                    $availableTimes[$counts]['effect'] = 1;
                                                    $availableTimes[$counts]['time'] = $start;
                                                    $availableTimes[$counts]['shift'] = 2;
                                                    $availableTimes[$counts]['patient_attend'] = 0;
                                                    $availableTimes[$counts]['status'] = ReservationStatus::not_available;
                                                    $availableTimes[$counts]['patient_status'] = PatientStatus::not_available;
                                                    $availableTimes[$counts]['show_reason'] = 1;
                                                    $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                                    $availableTimes[$counts]['exception_reason_id'] = $val['reason_id'];
                                                    $counts++;
                                                    break;
                                                } else {
                                                    $availableTimes[$counts]['time'] = $start;
                                                    $availableTimes[$counts]['effect'] = 2;
                                                    $availableTimes[$counts]['shift'] = 2;
                                                    $availableTimes[$counts]['show_reason'] = 1;
                                                    $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                                    $counts++;
                                                    break;
                                                }
                                            } else {
                                                if (isset($physicianException[$key + 1])) {
                                                    continue;
                                                }
                                                $availableTimes[$counts]['time'] = $start;
                                                $availableTimes[$counts]['shift'] = 2;
                                                $counts++;
                                                break;
                                            }
                                        }
                                    } else {
                                        if ($val['effect'] == 1) {
                                            $availableTimes[$counts]['reserved'] = true;
                                            $availableTimes[$counts]['effect'] = 1;
                                            $availableTimes[$counts]['time'] = $start;
                                            $availableTimes[$counts]['shift'] = 2;
                                            $availableTimes[$counts]['patient_attend'] = 0;
                                            $availableTimes[$counts]['status'] = ReservationStatus::not_available;
                                            $availableTimes[$counts]['patient_status'] = PatientStatus::not_available;
                                            $availableTimes[$counts]['show_reason'] = 1;
                                            $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                            $availableTimes[$counts]['exception_reason_id'] = $val['reason_id'];
                                            $counts++;
                                            break;
                                        } else {
                                            $availableTimes[$counts]['time'] = $start;
                                            $availableTimes[$counts]['effect'] = 2;
                                            $availableTimes[$counts]['shift'] = 2;
                                            $availableTimes[$counts]['show_reason'] = 1;
                                            $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                            $counts++;
                                            break;
                                        }
                                    }
                                }
                            }
                            $seconds = Functions::hoursToSeconds($start);
                            $newSeconds = $seconds + ($physicianSchedule['slots'] * 60);
                            $start = Functions::timeFromSeconds($newSeconds);
//                            $start = date("H:i:s", strtotime($physicianSchedule['slots'] . ' minutes', strtotime($start)));
                        } else {
                            break;
                        }
                    }
                }
            }
            if ($physicianSchedule['num_of_shifts'] == 3) {
                if (strpos($clinicSchedule['shift3_day_of'], $inputDayName) === false
                    || strpos($physicianSchedule['dayoff_3'], $inputDayName) === false
                ) {
                    $start = $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'];
                    $end = $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'];
                    if ($start < $end) {
                        while (1 == 1) {
                            if ($end > $start) {
                                $parts = explode(":", $start);
                                if ($parts[0] == 00) {
                                    break;
                                }
                                $physicianException = PhysicianException::checkExist($physicianSchedule['user_id'], $selected_date, $start, $physicianSchedule['slots']);
                                if (empty($physicianException)) {
                                    $availableTimes[$counts]['time'] = $start;
                                    $availableTimes[$counts]['shift'] = 3;
                                    $counts++;
                                } else {
                                    foreach ($physicianException as $key => $val) {
                                        if ($val['schedule_times']) {
                                            if ($physicianSchedule) {
                                                $startTime_1 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'] ?
                                                    $physicianSchedule[$daysName[$inputDayName] . '_start_time_1'] : 'null';
                                                $endTime_1 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'] ?
                                                    $physicianSchedule[$daysName[$inputDayName] . '_end_time_1'] : 'null';
                                                if ($physicianSchedule['num_of_shifts'] == 2 || $physicianSchedule['num_of_shifts'] == 3) {
                                                    $startTime_2 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'] ?
                                                        $physicianSchedule[$daysName[$inputDayName] . '_start_time_2'] : 'null';
                                                    $endTime_2 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'] ?
                                                        $physicianSchedule[$daysName[$inputDayName] . '_end_time_2'] : 'null';
                                                    if ($physicianSchedule['num_of_shifts'] == 3) {
                                                        $startTime_3 = $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'] ?
                                                            $physicianSchedule[$daysName[$inputDayName] . '_start_time_3'] : 'null';
                                                        $endTime_3 = $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'] ?
                                                            $physicianSchedule[$daysName[$inputDayName] . '_end_time_3'] : 'null';
                                                    }
                                                }
                                                if (
                                                    ($physicianSchedule['num_of_shifts'] == 1 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                                                        && strpos($val['schedule_times'], $endTime_1) !== false)
                                                    ||
                                                    ($physicianSchedule['num_of_shifts'] == 2 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                                                        && strpos($val['schedule_times'], $endTime_1) !== false && strpos($val['schedule_times'], $startTime_2 . ' ') !== false
                                                        && strpos($val['schedule_times'], $endTime_2) !== false)
                                                    ||
                                                    ($physicianSchedule['num_of_shifts'] == 3 && strpos($val['schedule_times'], $startTime_1 . ' ') !== false
                                                        && strpos($val['schedule_times'], $endTime_1) !== false && strpos($val['schedule_times'], $startTime_2 . ' ') !== false
                                                        && strpos($val['schedule_times'], $endTime_2) !== false && strpos($val['schedule_times'], $startTime_3 . ' ') !== false
                                                        && strpos($val['schedule_times'], $endTime_3) !== false)

                                                ) {
                                                    if ($val['effect'] == 1) {
                                                        $availableTimes[$counts]['reserved'] = true;
                                                        $availableTimes[$counts]['effect'] = 1;
                                                        $availableTimes[$counts]['time'] = $start;
                                                        $availableTimes[$counts]['shift'] = 3;
                                                        $availableTimes[$counts]['patient_attend'] = 0;
                                                        $availableTimes[$counts]['status'] = ReservationStatus::not_available;
                                                        $availableTimes[$counts]['patient_status'] = PatientStatus::not_available;
                                                        $availableTimes[$counts]['show_reason'] = 1;
                                                        $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                                        $availableTimes[$counts]['exception_reason_id'] = $val['reason_id'];
                                                        $counts++;
                                                        break;
                                                    } else {
                                                        $availableTimes[$counts]['time'] = $start;
                                                        $availableTimes[$counts]['effect'] = 2;
                                                        $availableTimes[$counts]['shift'] = 3;
                                                        $availableTimes[$counts]['show_reason'] = 1;
                                                        $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                                        $counts++;
                                                        break;
                                                    }
                                                } else {
                                                    if (isset($physicianException[$key + 1])) {
                                                        continue;
                                                    }
                                                    $availableTimes[$counts]['time'] = $start;
                                                    $availableTimes[$counts]['shift'] = 3;
                                                    $counts++;
                                                    break;
                                                }
                                            }
                                        } else {
                                            if ($val['effect'] == 1) {
                                                $availableTimes[$counts]['reserved'] = true;
                                                $availableTimes[$counts]['effect'] = 1;
                                                $availableTimes[$counts]['time'] = $start;
                                                $availableTimes[$counts]['shift'] = 3;
                                                $availableTimes[$counts]['patient_attend'] = 0;
                                                $availableTimes[$counts]['status'] = ReservationStatus::not_available;
                                                $availableTimes[$counts]['patient_status'] = PatientStatus::not_available;
                                                $availableTimes[$counts]['show_reason'] = 1;
                                                $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                                $availableTimes[$counts]['exception_reason_id'] = $val['reason_id'];
                                                $counts++;
                                                break;
                                            } else {
                                                $availableTimes[$counts]['time'] = $start;
                                                $availableTimes[$counts]['effect'] = 2;
                                                $availableTimes[$counts]['shift'] = 3;
                                                $availableTimes[$counts]['show_reason'] = 1;
                                                $availableTimes[$counts]['exception_reason'] = AttributePms::getById($val['reason_id'])['name'];
                                                $counts++;
                                                break;
                                            }
                                        }
                                    }
                                }
                                $seconds = Functions::hoursToSeconds($start);
                                $newSeconds = $seconds + ($physicianSchedule['slots'] * 60);
                                $start = Functions::timeFromSeconds($newSeconds);
//                                $start = date("H:i:s", strtotime($physicianSchedule['slots'] . ' minutes', strtotime($start)));
                            } else {
                                break;
                            }
                        }
                    }
                }
            }
        }
        //////////////////////get reserved records//////////////////////////////
        $physician_id = $physicianSchedule['user_id'];
        $reservations = Reservation::getAllFromPhysicianClinicDate($clinicSchedule['clinic_id'], $physician_id, $selected_date);
        foreach ($reservations as $key => $val) {
            foreach ($availableTimes as $key2 => $val2) {
                if ($val['time_from'] == $val2['time']) {
                    $availableTimes[$key2]['reserved'] = true;
                    $availableTimes[$key2]['effect'] = 1;
                    $availableTimes[$key2]['time_to'] = $val['time_to'];
                    $availableTimes[$key2]['reservation_id'] = $val['id'];
                    $availableTimes[$key2]['patient_status'] = $val['patient_status'];
                    $availableTimes[$key2]['status'] = $val['status'];
                    $availableTimes[$key2]['patient_attend'] = $val['patient_attend'];
                    $availableTimes[$key2]['show_reason'] = $val['show_reason'];
                    $availableTimes[$key2]['exception_reason'] = $val['exception_reason'];
                }
                if ($revisit) {
                    if ($val['revisit_time_from'] == $val2['time']) {
                        $availableTimes[$key2]['reserved'] = true;
                        $availableTimes[$key2]['type'] = 'revisit';
                    }
                }
            }
        }
    }


}