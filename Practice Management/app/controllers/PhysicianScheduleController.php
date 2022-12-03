<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\clinic\ClinicRepository;
use core\clinicSchedule\ClinicScheduleRepository;
use core\enums\PatientStatus;
use core\enums\ReservationStatus;
use core\hospital\HospitalRepository;
use core\physician\PhysicianRepository;
use core\physicianSchedule\PhysicianScheduleManager;
use core\physicianSchedule\PhysicianScheduleRepository;
use core\user\UserRepository;
use core\userLocalization\UserLocalizationRepository;

class PhysicianScheduleController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function index()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianSchedule.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        if ($inputs) {
            $physicianScheduleRepo = new PhysicianScheduleRepository();
            $data['physicianSchedules'] = $physicianScheduleRepo->getAllWithFilter($inputs);
        } else {
            $physicianScheduleRepo = new PhysicianScheduleRepository();
            $data['physicianSchedules'] = $physicianScheduleRepo->getAll();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();

        return View::make('physicianSchedule/list', $data);
    }

    public function addPhysicianSchedule()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianSchedule.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $phRepo = new PhysicianRepository();
        $data['physicians'] = $phRepo->getAll();

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('physicianSchedule/add', $data);
    }

    public function createPhysicianSchedule()
    {
        $physicianScheduleManager = new PhysicianScheduleManager();
        $inputs = (Input::except('_token'));
        unset($inputs['hospital_id']);
        $data = $physicianScheduleManager->createPhysicianSchedule($inputs);
        if ($data['status']) {
            return Redirect::route('physicianSchedules');
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editPhysicianSchedule($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianSchedule.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $phChRepo = new PhysicianScheduleRepository();
        $physicianSchedule = $phChRepo->getById($id);
        $cSRepo = new ClinicScheduleRepository();
        $clinicRepo = new ClinicRepository();
//        $reservations = Reservation::checkPhysicianScheduleExist($physicianSchedule['clinic_id']
//            , $physicianSchedule['user_id'], $physicianSchedule['start_date'], $physicianSchedule['end_date']);
//        if ($reservations) {
//            Flash::error('Can\'t edit this schedule, their is related records with reservations');
//            return Redirect::route('physicianSchedules');
//        }
        $data['clinicName'] = $clinicRepo->getName($physicianSchedule['clinic_id']);
        $data['physicianName'] = User::getNameById($physicianSchedule['user_id']);
        $data['clinicSchedule'] = $cSRepo->getById($physicianSchedule['clinic_schedule_id']);
        $data['schedules'] = $cSRepo->getAllByClinicId($physicianSchedule['clinic_id']);
        $data['physicianSchedule'] = $physicianSchedule;

        return View::make('physicianSchedule/edit', $data);
    }

    public function updatePhysicianSchedule($id)
    {
        $systemManager = new PhysicianScheduleManager();
        $inputs = (Input::except('_token'));
        $data = $systemManager->updatePhysicianSchedule($inputs, $id);
        if ($data['status']) {
            return Redirect::route('physicianSchedules');
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deletePhysicianSchedule($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianSchedule.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $schedule = PhysicianSchedule::getById($id);
        $reservations = Reservation::checkPhysicianScheduleExist($schedule['clinic_id']
            , $schedule['user_id'], $schedule['start_date'], $schedule['end_date']);
        if ($reservations) {
            Flash::error('Can\'t edit this schedule, their is related records with reservations');
            return Redirect::route('physicianSchedules');
        }
        $physicianScheduleRepo = new PhysicianScheduleRepository();
        $physicianScheduleRepo->delete($id);
        PhysicianException::removeByPhysicianSchedule($id);
        Flash::success('Deleted Successfully');
        return Redirect::back();
    }

    public function getPhysicianByClinic()
    {
        $id = Input::get('clinic_id');
        if ($this->user->user_type_id == 7) {
            $physiciansIds = array($this->user->id);
        } else {
            $physiciansIds = User::getPhysiciansId();
        }
        $userRepo = new UserRepository();
        $ULRepo = new UserLocalizationRepository();
        $usersId = $ULRepo->getUsersByUsersIdAndClinicId($physiciansIds, $id);
        $physicians = $userRepo->getUsers($usersId);
        $data['physiciansHtml'] = "<option value=''>Choose</option>";
        foreach ($physicians as $key => $val) {
            $data['physiciansHtml'] .= "<option value='" . $val['id'] . "'>" . $val['full_name'] . "</option>";
        }
        $cSRepo = new ClinicScheduleRepository();
        $schedules = $cSRepo->getAllByClinicId($id);
        $data['schedulesHtml'] = "<option value=''>Choose</option>";
        foreach ($schedules as $key => $val) {
            $data['schedulesHtml'] .= "<option value='" . $val['id'] . "'>" . $val['name'] . "</option>";
        }
        return $data;
    }

    public function getPhysicianScheduleByClinicSchedule()
    {
        $clinic_schedule_id = Input::get('clinic_schedule_id');
        $user_id = Input::get('user_id');
        $data2['schedule'] = PhysicianSchedule::getPhysicianScheduleByClinicSchedule($clinic_schedule_id, $user_id);
        $data['schedulesHtml'] = View::make('physicianSchedule/listSchedules', $data2)->render();
        return $data;
    }

    public function getPhysicianScheduleByPhysicianId()
    {
        $physician_id = Input::get('physician_id');
        $clinic_id = Input::get('clinic_id');
        $schedule = PhysicianSchedule::getByPhysicianId($physician_id, $clinic_id);
        $html = '<option value="">Choose</option>';
        foreach ($schedule as $key => $val) {
            $html .= '<option value="' . $val['id'] . '">' . $val['start_date'] . ' / ' . $val['end_date'] . '</option>';
        }
        return $html;
    }

    public function getPhysicianScheduleView()
    {
        $physician_schedule_id = Input::get('physician_schedule_id');
        $data2['physicianSchedule'] = PhysicianSchedule::getById($physician_schedule_id);
        $data['schedulesHtml'] = View::make('physicianSchedule/listSchedules', $data2)->render();
        return $data;
    }

    public function getPhysicianSchedule()
    {
        $physician_schedule_id = Input::get('physician_schedule_id');
        $schedule = PhysicianSchedule::getById($physician_schedule_id);
        if (Input::has('withScheduleTimesSelect')) {
            $schedule['scheduleTimesSelect'] = PhysicianSchedule::scheduleTimeInSelect($schedule);
        }
        return $schedule;
    }

    public function importExcelPhysicianSchedule()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('physicianSchedule.importExcel')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('physicianSchedule/importExcel', $data);
    }

    public function downloadExcelPhysicianSchedule()
    {
        $inputs = (Input::except('_token'));
        $rules = array(
            'hospital_id' => "required",
            "clinic_schedule_id" => "required_with:clinic_id"
        );

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back();
        }
        $hospital = Hospital::getById($inputs['hospital_id']);
        $userRepo = new UserRepository();
        $ULRepo = new UserLocalizationRepository();
        $physiciansIds = User::getPhysiciansId();

        if (!empty($inputs['clinic_id'])) {
            $clinic = Clinic::getById($inputs['clinic_id']);
            $clinicSchedule = ClinicSchedule::getById($inputs['clinic_schedule_id']);
            $usersId = $ULRepo->getUsersByUsersIdAndClinicId($physiciansIds, $inputs['clinic_id']);
        } else {
            $clinic = array();
            $clinicSchedule = array();
            $usersId = $ULRepo->getUsersByUsersIdAndHospitalId($physiciansIds, $inputs['hospital_id']);
        }
        $physicians = $userRepo->getUsers($usersId);
        Excel::create('physicians_' . date('Y-m-d H-i-s'), function ($excel) use ($clinic, $hospital, $clinicSchedule, $physicians) {
            // Set the title
            $excel->setTitle('physicians_' . date('Y-m-d H-i-s'));
            $excel->sheet('physicians', function ($sheet) use ($clinic, $hospital, $clinicSchedule, $physicians) {
                if (!empty($clinic)) {
                    $this->preparedDownloadWithClinic($sheet, $clinic, $hospital, $clinicSchedule, $physicians);
                } else {
                    $this->preparedDownloadWithOutClinic($sheet, $hospital, $physicians);
                }
            });

        })->download('xlsx');
    }

    public function preparedDownloadWithClinic($sheet, $clinic, $hospital, $clinicSchedule, $physicians)
    {
        $row1 = array(
            'hospital_name', 'clinic_id', 'clinic_name', 'clinic_schedule_id', 'clinic_schedule_name'
        , 'physician_id', 'physician_name', 'visit_duration_in_minuets', 'num_of_shifts',
            'shift1_sat_start', 'shift1_sat_end', 'shift1_sat_day_off',
            'shift1_sun_start', 'shift1_sun_end', 'shift1_sun_day_off',
            'shift1_mon_start', 'shift1_mon_end', 'shift1_mon_day_off',
            'shift1_tue_start', 'shift1_tue_end', 'shift1_tue_day_off',
            'shift1_wed_start', 'shift1_wed_end', 'shift1_wed_day_off',
            'shift1_thu_start', 'shift1_thu_end', 'shift1_thu_day_off',
            'shift1_fri_start', 'shift1_fri_end', 'shift1_fri_day_off',
            /////////////
            'shift2_sat_start', 'shift2_sat_end', 'shift2_sat_day_off',
            'shift2_sun_start', 'shift2_sun_end', 'shift2_sun_day_off',
            'shift2_mon_start', 'shift2_mon_end', 'shift2_mon_day_off',
            'shift2_tue_start', 'shift2_tue_end', 'shift2_tue_day_off',
            'shift2_wed_start', 'shift2_wed_end', 'shift2_wed_day_off',
            'shift2_thu_start', 'shift2_thu_end', 'shift2_thu_day_off',
            'shift2_fri_start', 'shift2_fri_end', 'shift2_fri_day_off',
            /////////////
            'shift3_sat_start', 'shift3_sat_end', 'shift3_sat_day_off',
            'shift3_sun_start', 'shift3_sun_end', 'shift3_sun_day_off',
            'shift3_mon_start', 'shift3_mon_end', 'shift3_mon_day_off',
            'shift3_tue_start', 'shift3_tue_end', 'shift3_tue_day_off',
            'shift3_wed_start', 'shift3_wed_end', 'shift3_wed_day_off',
            'shift3_thu_start', 'shift3_thu_end', 'shift3_thu_day_off',
            'shift3_fri_start', 'shift3_fri_end', 'shift3_fri_day_off',
        );

        $shift1DaysOff = explode(',', $clinicSchedule['shift1_day_of']);
        $shift2DaysOff = explode(',', $clinicSchedule['shift2_day_of']);
        $shift3DaysOff = explode(',', $clinicSchedule['shift3_day_of']);
        $sheet->row(1, $row1);
        foreach ($physicians as $key => $val) {
            $row2 = array(
                $hospital['name'], $clinic['id'], $clinic['name'], $clinicSchedule['id'], $clinicSchedule['name'],
                $val['id'], $val['full_name'], '', $clinicSchedule['num_of_shifts']
            );
            if (!in_array('saturday', $shift1DaysOff)) {
                $row2[] = $clinicSchedule['shift1_start_time'];
                $row2[] = $clinicSchedule['shift1_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('sunday', $shift1DaysOff)) {
                $row2[] = $clinicSchedule['shift1_start_time'];
                $row2[] = $clinicSchedule['shift1_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('monday', $shift1DaysOff)) {
                $row2[] = $clinicSchedule['shift1_start_time'];
                $row2[] = $clinicSchedule['shift1_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('tuesday', $shift1DaysOff)) {
                $row2[] = $clinicSchedule['shift1_start_time'];
                $row2[] = $clinicSchedule['shift1_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('wednesday', $shift1DaysOff)) {
                $row2[] = $clinicSchedule['shift1_start_time'];
                $row2[] = $clinicSchedule['shift1_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('thursday', $shift1DaysOff)) {
                $row2[] = $clinicSchedule['shift1_start_time'];
                $row2[] = $clinicSchedule['shift1_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('friday', $shift1DaysOff)) {
                $row2[] = $clinicSchedule['shift1_start_time'];
                $row2[] = $clinicSchedule['shift1_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            ///////////////////shift 2//////////////////////
            if (!in_array('saturday', $shift2DaysOff)) {
                $row2[] = $clinicSchedule['shift2_start_time'];
                $row2[] = $clinicSchedule['shift2_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('sunday', $shift2DaysOff)) {
                $row2[] = $clinicSchedule['shift2_start_time'];
                $row2[] = $clinicSchedule['shift2_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('monday', $shift2DaysOff)) {
                $row2[] = $clinicSchedule['shift2_start_time'];
                $row2[] = $clinicSchedule['shift2_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('tuesday', $shift2DaysOff)) {
                $row2[] = $clinicSchedule['shift2_start_time'];
                $row2[] = $clinicSchedule['shift2_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('wednesday', $shift2DaysOff)) {
                $row2[] = $clinicSchedule['shift2_start_time'];
                $row2[] = $clinicSchedule['shift2_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('thursday', $shift2DaysOff)) {
                $row2[] = $clinicSchedule['shift2_start_time'];
                $row2[] = $clinicSchedule['shift2_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('friday', $shift2DaysOff)) {
                $row2[] = $clinicSchedule['shift2_start_time'];
                $row2[] = $clinicSchedule['shift2_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            ///////////////////shift 3//////////////////////
            if (!in_array('saturday', $shift3DaysOff)) {
                $row2[] = $clinicSchedule['shift3_start_time'];
                $row2[] = $clinicSchedule['shift3_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('sunday', $shift3DaysOff)) {
                $row2[] = $clinicSchedule['shift3_start_time'];
                $row2[] = $clinicSchedule['shift3_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('monday', $shift3DaysOff)) {
                $row2[] = $clinicSchedule['shift3_start_time'];
                $row2[] = $clinicSchedule['shift3_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('tuesday', $shift3DaysOff)) {
                $row2[] = $clinicSchedule['shift3_start_time'];
                $row2[] = $clinicSchedule['shift3_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('wednesday', $shift3DaysOff)) {
                $row2[] = $clinicSchedule['shift3_start_time'];
                $row2[] = $clinicSchedule['shift3_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('thursday', $shift3DaysOff)) {
                $row2[] = $clinicSchedule['shift3_start_time'];
                $row2[] = $clinicSchedule['shift3_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
            if (!in_array('friday', $shift3DaysOff)) {
                $row2[] = $clinicSchedule['shift3_start_time'];
                $row2[] = $clinicSchedule['shift3_end_time'];
                $row2[] = 'no';
            } else {
                $row2[] = 'off';
                $row2[] = 'off';
                $row2[] = 'yes';
            }
//            ksort($row2);
            $sheet->row($key + 2, $row2);
        }
        $sheet->setAutoSize(true);
        $sheet->setWidth('B', 0);
        $sheet->setWidth('D', 0);
        $sheet->setWidth('F', 0);
    }

    public function preparedDownloadWithOutClinic($sheet, $hospital, $physicians)
    {
        $row1 = array(
            'hospital_name', 'clinic_id', 'clinic_name', 'clinic_schedule_id', 'clinic_schedule_name'
        , 'physician_id', 'physician_name', 'visit_duration_in_minuets', 'num_of_shifts',
            'shift1_sat_start', 'shift1_sat_end', 'shift1_sat_day_off',
            'shift1_sun_start', 'shift1_sun_end', 'shift1_sun_day_off',
            'shift1_mon_start', 'shift1_mon_end', 'shift1_mon_day_off',
            'shift1_tue_start', 'shift1_tue_end', 'shift1_tue_day_off',
            'shift1_wed_start', 'shift1_wed_end', 'shift1_wed_day_off',
            'shift1_thu_start', 'shift1_thu_end', 'shift1_thu_day_off',
            'shift1_fri_start', 'shift1_fri_end', 'shift1_fri_day_off',
            /////////////
            'shift2_sat_start', 'shift2_sat_end', 'shift2_sat_day_off',
            'shift2_sun_start', 'shift2_sun_end', 'shift2_sun_day_off',
            'shift2_mon_start', 'shift2_mon_end', 'shift2_mon_day_off',
            'shift2_tue_start', 'shift2_tue_end', 'shift2_tue_day_off',
            'shift2_wed_start', 'shift2_wed_end', 'shift2_wed_day_off',
            'shift2_thu_start', 'shift2_thu_end', 'shift2_thu_day_off',
            'shift2_fri_start', 'shift2_fri_end', 'shift2_fri_day_off',
            /////////////
            'shift3_sat_start', 'shift3_sat_end', 'shift3_sat_day_off',
            'shift3_sun_start', 'shift3_sun_end', 'shift3_sun_day_off',
            'shift3_mon_start', 'shift3_mon_end', 'shift3_mon_day_off',
            'shift3_tue_start', 'shift3_tue_end', 'shift3_tue_day_off',
            'shift3_wed_start', 'shift3_wed_end', 'shift3_wed_day_off',
            'shift3_thu_start', 'shift3_thu_end', 'shift3_thu_day_off',
            'shift3_fri_start', 'shift3_fri_end', 'shift3_fri_day_off',
        );
        $sheet->row(1, $row1);
        $ULRepo = new UserLocalizationRepository();
        $CSHRepo = new ClinicScheduleRepository();
        $count = 2;
        foreach ($physicians as $key => $val) {
            $clinics = $ULRepo->getClinicsByUserId($val['id']);
            if (empty($clinics)) {
                continue;
            }
            $allClinics = Clinic::getByIds($clinics);
            foreach ($allClinics as $key2 => $val2) {
                $clinicSchedule = last($CSHRepo->getAllByClinicId($val2['id']));
                if (empty($clinicSchedule)) {
                    continue;
                }
                $row2 = array(
                    $hospital['name'], $val2['id'], $val2['name'], $clinicSchedule['id'], $clinicSchedule['name'],
                    $val['id'], $val['full_name'], '', $clinicSchedule['num_of_shifts']
                );

                $shift1DaysOff = explode(',', $clinicSchedule['shift1_day_of']);
                if (!in_array('saturday', $shift1DaysOff)) {
                    $row2[] = $clinicSchedule['shift1_start_time'];
                    $row2[] = $clinicSchedule['shift1_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('sunday', $shift1DaysOff)) {
                    $row2[] = $clinicSchedule['shift1_start_time'];
                    $row2[] = $clinicSchedule['shift1_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('monday', $shift1DaysOff)) {
                    $row2[] = $clinicSchedule['shift1_start_time'];
                    $row2[] = $clinicSchedule['shift1_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('tuesday', $shift1DaysOff)) {
                    $row2[] = $clinicSchedule['shift1_start_time'];
                    $row2[] = $clinicSchedule['shift1_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('wednesday', $shift1DaysOff)) {
                    $row2[] = $clinicSchedule['shift1_start_time'];
                    $row2[] = $clinicSchedule['shift1_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('thursday', $shift1DaysOff)) {
                    $row2[] = $clinicSchedule['shift1_start_time'];
                    $row2[] = $clinicSchedule['shift1_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('friday', $shift1DaysOff)) {
                    $row2[] = $clinicSchedule['shift1_start_time'];
                    $row2[] = $clinicSchedule['shift1_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                ////////////shift 2/////////////////
                $shift2DaysOff = explode(',', $clinicSchedule['shift2_day_of']);
                if (!in_array('saturday', $shift2DaysOff)) {
                    $row2[] = $clinicSchedule['shift2_start_time'];
                    $row2[] = $clinicSchedule['shift2_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('sunday', $shift2DaysOff)) {
                    $row2[] = $clinicSchedule['shift2_start_time'];
                    $row2[] = $clinicSchedule['shift2_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('monday', $shift2DaysOff)) {
                    $row2[] = $clinicSchedule['shift2_start_time'];
                    $row2[] = $clinicSchedule['shift2_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('tuesday', $shift2DaysOff)) {
                    $row2[] = $clinicSchedule['shift2_start_time'];
                    $row2[] = $clinicSchedule['shift2_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('wednesday', $shift2DaysOff)) {
                    $row2[] = $clinicSchedule['shift2_start_time'];
                    $row2[] = $clinicSchedule['shift2_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('thursday', $shift2DaysOff)) {
                    $row2[] = $clinicSchedule['shift2_start_time'];
                    $row2[] = $clinicSchedule['shift2_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('friday', $shift2DaysOff)) {
                    $row2[] = $clinicSchedule['shift2_start_time'];
                    $row2[] = $clinicSchedule['shift2_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                ////////////shift 3/////////////////
                $shift3DaysOff = explode(',', $clinicSchedule['shift3_day_of']);
                if (!in_array('saturday', $shift3DaysOff)) {
                    $row2[] = $clinicSchedule['shift3_start_time'];
                    $row2[] = $clinicSchedule['shift3_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('sunday', $shift3DaysOff)) {
                    $row2[] = $clinicSchedule['shift3_start_time'];
                    $row2[] = $clinicSchedule['shift3_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('monday', $shift3DaysOff)) {
                    $row2[] = $clinicSchedule['shift3_start_time'];
                    $row2[] = $clinicSchedule['shift3_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('tuesday', $shift3DaysOff)) {
                    $row2[] = $clinicSchedule['shift3_start_time'];
                    $row2[] = $clinicSchedule['shift3_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('wednesday', $shift3DaysOff)) {
                    $row2[] = $clinicSchedule['shift3_start_time'];
                    $row2[] = $clinicSchedule['shift3_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('thursday', $shift3DaysOff)) {
                    $row2[] = $clinicSchedule['shift3_start_time'];
                    $row2[] = $clinicSchedule['shift3_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
                if (!in_array('friday', $shift3DaysOff)) {
                    $row2[] = $clinicSchedule['shift3_start_time'];
                    $row2[] = $clinicSchedule['shift3_end_time'];
                    $row2[] = 'no';
                } else {
                    $row2[] = 'off';
                    $row2[] = 'off';
                    $row2[] = 'yes';
                }
//                ksort($row2);
                $sheet->row($count, $row2);
                $count++;
            }
        }
        $sheet->setAutoSize(true);
        $sheet->setWidth('B', 0);
        $sheet->setWidth('D', 0);
        $sheet->setWidth('F', 0);
    }

    public function postImportExcelPhysicianSchedule()
    {
        $inputs = (Input::except('_token'));

        if (!empty($inputs['template'])) {
            ini_set('max_execution_time', 0);
            $file = Input::file('template');
            $filename = date('Y-m-d_H-i-s') . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path() . '/excel/physicians';
            $upload_success = $file->move($destinationPath, $filename);
            if ($upload_success) {
                $inputs['template'] = 'excel/physicians/' . $filename;
            } else {
                Flash::error("The File Not Uploaded Correctly");
                return Redirect::back();
            }
        } else {
            Flash::error("Import Template File Is Required");
            return Redirect::back();
        }
        $error = false;
        $dataArray = array();
        Excel::load($inputs['template'], function ($reader) use ($inputs, &$error, &$dataArray) {
            $sheet = $reader->toArray();
            $Repo = new PhysicianScheduleRepository();
            foreach ($sheet as $key => $val) {
                $rowNum = $key + 2;
                $clinicSchedule = ClinicSchedule::getById($val['clinic_schedule_id']);
                $start_date = $clinicSchedule['start_date'];
                $end_date = $clinicSchedule['end_date'];
                $count = 0;
                if (isset($inputs['split']) && $inputs['split'] == 1) {
                    while (date("Y-m-t", strtotime($start_date)) <= date("Y-m-t", strtotime($end_date))) {
                        if (date("Y-m-t", strtotime($start_date)) == date("Y-m-t", strtotime($end_date))) {
                            if ($count == 0) {
                                $dataArray[$key][$count]['start_date'] = $start_date;
                                $dataArray[$key][$count]['end_date'] = $end_date;
                            } else {
                                $dataArray[$key][$count]['start_date'] = date("Y-m-01", strtotime($start_date));
                                $dataArray[$key][$count]['end_date'] = $end_date;
                            }
                        } else {
                            if ($count == 0) {
                                $dataArray[$key][$count]['start_date'] = $start_date;
                                $dataArray[$key][$count]['end_date'] = date("Y-m-t", strtotime($start_date));
                            } else {
                                $dataArray[$key][$count]['start_date'] = date("Y-m-01", strtotime($start_date));
                                $dataArray[$key][$count]['end_date'] = date("Y-m-t", strtotime($start_date));
                            }
                        }
                        $start_date = date("Y-m-d", strtotime("+1 month", strtotime($start_date)));
                        $dataArray[$key][$count]['clinic_schedule_id'] = $val['clinic_schedule_id'];
                        $dataArray[$key][$count]['num_of_shifts'] = $val['num_of_shifts'];
                        $dataArray[$key][$count]['clinic_id'] = $val['clinic_id'];
                        $dataArray[$key][$count]['user_id'] = $val['physician_id'];
                        if ($Repo->checkExist($val['physician_id'], $val['clinic_schedule_id'])) {
                            Flash::error("Row: $rowNum The physician take this schedule before");
                            $error = true;
                            return;
                        }
                        if (empty($val['visit_duration_in_minuets'])) {
                            Flash::error("Row: $rowNum Visit duration in minuets record is required");
                            $error = true;
                            return;
                        } else {
                            $dataArray[$key][$count]['slots'] = $val['visit_duration_in_minuets'];
                        }
                        //////////////////////////////////////////////
                        if ($val['shift1_sat_day_off'] == 'no') {
                            if (!$val['shift1_sat_start'] && !$val['shift1_sat_end']) {
                                Flash::error("Row: $rowNum (shift 1) saturday times record is required");
                                $error = true;
                                return;
                            } else {
                                if ($val['shift1_sat_start'] > $val['shift1_sat_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 1) saturday end time is greater than saturday start time");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift1_sat_start'] != 'off') {
                                        $dataArray[$key][$count]['sat_start_time_1'] = $val['shift1_sat_start'];
                                        $dataArray[$key][$count]['sat_end_time_1'] = $val['shift1_sat_end'];
                                    } else {
                                        $dataArray[$key][$count]['sat_start_time_1'] = null;
                                        $dataArray[$key][$count]['sat_end_time_1'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift1_sun_day_off'] == 'no') {
                            if (!$val['shift1_sun_start'] && !$val['shift1_sun_end']) {
                                Flash::error("Row: $rowNum (shift 1) sunday times record is required");
                                $error = true;
                                return;
                            } else {
                                if ($val['shift1_sun_start'] > $val['shift1_sun_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 1) sunday end time is greater than sunday start time");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift1_sun_start'] != 'off') {
                                        $dataArray[$key][$count]['sun_start_time_1'] = $val['shift1_sun_start'];
                                        $dataArray[$key][$count]['sun_end_time_1'] = $val['shift1_sun_end'];
                                    } else {
                                        $dataArray[$key][$count]['sun_start_time_1'] = null;
                                        $dataArray[$key][$count]['sun_end_time_1'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift1_mon_day_off'] == 'no') {
                            if (!$val['shift1_mon_start'] && !$val['shift1_mon_end']) {
                                Flash::error("Row: $rowNum (shift 1) monday times record is required");
                                $error = true;
                                return;
                            } else {
                                if ($val['shift1_mon_start'] > $val['shift1_mon_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 1) monday end time is greater than monday start time");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift1_mon_start'] != 'off') {
                                        $dataArray[$key][$count]['mon_start_time_1'] = $val['shift1_mon_start'];
                                        $dataArray[$key][$count]['mon_end_time_1'] = $val['shift1_mon_end'];
                                    } else {
                                        $dataArray[$key][$count]['mon_start_time_1'] = null;
                                        $dataArray[$key][$count]['mon_end_time_1'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift1_tue_day_off'] == 'no') {
                            if (!$val['shift1_tue_start'] && !$val['shift1_tue_end']) {
                                Flash::error("Row: $rowNum (shift 1) tuesday times record is required");
                                $error = true;
                                return;
                            } else {
                                if ($val['shift1_tue_start'] > $val['shift1_tue_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 1) tuesday end time is greater than tuesday start time");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift1_tue_start'] != 'off') {
                                        $dataArray[$key][$count]['tues_start_time_1'] = $val['shift1_tue_start'];
                                        $dataArray[$key][$count]['tues_end_time_1'] = $val['shift1_tue_end'];
                                    } else {
                                        $dataArray[$key][$count]['tues_start_time_1'] = null;
                                        $dataArray[$key][$count]['tues_end_time_1'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift1_wed_day_off'] == 'no') {
                            if (!$val['shift1_wed_start'] && !$val['shift1_wed_end']) {
                                Flash::error("Row: $rowNum (shift 1) wednesday times record is required");
                                $error = true;
                                return;
                            } else {
                                if ($val['shift1_wed_start'] > $val['shift1_wed_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 1) wednesday end time is greater than wednesday start time");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift1_wed_start'] != 'off') {
                                        $dataArray[$key][$count]['wed_start_time_1'] = $val['shift1_wed_start'];
                                        $dataArray[$key][$count]['wed_end_time_1'] = $val['shift1_wed_end'];
                                    } else {
                                        $dataArray[$key][$count]['wed_start_time_1'] = null;
                                        $dataArray[$key][$count]['wed_end_time_1'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift1_thu_day_off'] == 'no') {
                            if (!$val['shift1_thu_start'] && !$val['shift1_thu_end']) {
                                Flash::error("Row: $rowNum (shift 1) thursday times record is required");
                                $error = true;
                                return;
                            } else {
                                if ($val['shift1_thu_start'] > $val['shift1_thu_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 1) thursday end time is greater than thursday start time");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift1_thu_start'] != 'off') {
                                        $dataArray[$key][$count]['thurs_start_time_1'] = $val['shift1_thu_start'];
                                        $dataArray[$key][$count]['thurs_end_time_1'] = $val['shift1_thu_end'];
                                    } else {
                                        $dataArray[$key][$count]['thurs_start_time_1'] = null;
                                        $dataArray[$key][$count]['thurs_end_time_1'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift1_fri_day_off'] == 'no') {
                            if (!$val['shift1_fri_start'] && !$val['shift1_fri_end']) {
                                Flash::error("Row: $rowNum (shift 1) friday times record is required");
                                $error = true;
                                return;
                            } else {
                                if ($val['shift1_fri_start'] > $val['shift1_fri_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 1) friday end time is greater than friday start time");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift1_fri_start'] != 'off') {
                                        $dataArray[$key][$count]['fri_start_time_1'] = $val['shift1_fri_start'];
                                        $dataArray[$key][$count]['fri_end_time_1'] = $val['shift1_fri_end'];
                                    } else {
                                        $dataArray[$key][$count]['fri_start_time_1'] = null;
                                        $dataArray[$key][$count]['fri_end_time_1'] = null;
                                    }
                                }
                            }
                        }
                        if ($val['num_of_shifts'] == 1) {
                            $dataArray[$key][$count]['sat_start_time_2'] = null;
                            $dataArray[$key][$count]['sat_end_time_2'] = null;
                            $dataArray[$key][$count]['sun_start_time_2'] = null;
                            $dataArray[$key][$count]['sun_end_time_2'] = null;
                            $dataArray[$key][$count]['mon_start_time_2'] = null;
                            $dataArray[$key][$count]['mon_end_time_2'] = null;
                            $dataArray[$key][$count]['tues_start_time_2'] = null;
                            $dataArray[$key][$count]['tues_end_time_2'] = null;
                            $dataArray[$key][$count]['wed_start_time_2'] = null;
                            $dataArray[$key][$count]['wed_end_time_2'] = null;
                            $dataArray[$key][$count]['thurs_start_time_2'] = null;
                            $dataArray[$key][$count]['thurs_end_time_2'] = null;
                            $dataArray[$key][$count]['fri_start_time_2'] = null;
                            $dataArray[$key][$count]['fri_end_time_2'] = null;
                            $dataArray[$key][$count]['sat_start_time_3'] = null;
                            $dataArray[$key][$count]['sat_end_time_3'] = null;
                            $dataArray[$key][$count]['sun_start_time_3'] = null;
                            $dataArray[$key][$count]['sun_end_time_3'] = null;
                            $dataArray[$key][$count]['mon_start_time_3'] = null;
                            $dataArray[$key][$count]['mon_end_time_3'] = null;
                            $dataArray[$key][$count]['tues_start_time_3'] = null;
                            $dataArray[$key][$count]['tues_end_time_3'] = null;
                            $dataArray[$key][$count]['wed_start_time_3'] = null;
                            $dataArray[$key][$count]['wed_end_time_3'] = null;
                            $dataArray[$key][$count]['thurs_start_time_3'] = null;
                            $dataArray[$key][$count]['thurs_end_time_3'] = null;
                            $dataArray[$key][$count]['fri_start_time_3'] = null;
                            $dataArray[$key][$count]['fri_end_time_3'] = null;
                        }
                        if ($val['num_of_shifts'] == 2 || $val['num_of_shifts'] == 3) {
                            ////////////////////shift 2 times//////////////////////////
                            if ($val['shift2_sat_day_off'] == 'no') {
                                if (!$val['shift2_sat_start'] && !$val['shift2_sat_end']) {
                                    Flash::error("Row: $rowNum (shift 2) saturday times record is required");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift2_sat_start'] > $val['shift2_sat_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 2) saturday end time is greater than saturday start time");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift2_sat_start'] != 'off') {
                                            $dataArray[$key][$count]['sat_start_time_2'] = $val['shift2_sat_start'];
                                            $dataArray[$key][$count]['sat_end_time_2'] = $val['shift2_sat_end'];
                                        } else {
                                            $dataArray[$key][$count]['sat_start_time_2'] = null;
                                            $dataArray[$key][$count]['sat_end_time_2'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift2_sun_day_off'] == 'no') {
                                if (!$val['shift2_sun_start'] && !$val['shift2_sun_end']) {
                                    Flash::error("Row: $rowNum (shift 2) sunday times record is required");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift2_sun_start'] > $val['shift2_sun_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 2) sunday end time is greater than sunday start time");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift2_sun_start'] != 'off') {
                                            $dataArray[$key][$count]['sun_start_time_2'] = $val['shift2_sun_start'];
                                            $dataArray[$key][$count]['sun_end_time_2'] = $val['shift2_sun_end'];
                                        } else {
                                            $dataArray[$key][$count]['sun_start_time_2'] = null;
                                            $dataArray[$key][$count]['sun_end_time_2'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift2_mon_day_off'] == 'no') {
                                if (!$val['shift2_mon_start'] && !$val['shift2_mon_end']) {
                                    Flash::error("Row: $rowNum (shift 2) monday times record is required");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift2_mon_start'] > $val['shift2_mon_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 2) monday end time is greater than monday start time");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift2_mon_start'] != 'off') {
                                            $dataArray[$key][$count]['mon_start_time_2'] = $val['shift2_mon_start'];
                                            $dataArray[$key][$count]['mon_end_time_2'] = $val['shift2_mon_end'];
                                        } else {
                                            $dataArray[$key][$count]['mon_start_time_2'] = null;
                                            $dataArray[$key][$count]['mon_end_time_2'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift2_tue_day_off'] == 'no') {
                                if (!$val['shift2_tue_start'] && !$val['shift2_tue_end']) {
                                    Flash::error("Row: $rowNum (shift 2) tuesday times record is required");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift2_tue_start'] > $val['shift2_tue_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 2) tuesday end time is greater than tuesday start time");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift2_tue_start'] != 'off') {
                                            $dataArray[$key][$count]['tues_start_time_2'] = $val['shift2_tue_start'];
                                            $dataArray[$key][$count]['tues_end_time_2'] = $val['shift2_tue_end'];
                                        } else {
                                            $dataArray[$key][$count]['tues_start_time_2'] = null;
                                            $dataArray[$key][$count]['tues_end_time_2'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift2_wed_day_off'] == 'no') {
                                if (!$val['shift2_wed_start'] && !$val['shift2_wed_end']) {
                                    Flash::error("Row: $rowNum (shift 2) wednesday times record is required");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift2_wed_start'] > $val['shift2_wed_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 2) wednesday end time is greater than wednesday start time");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift2_wed_start'] != 'off') {
                                            $dataArray[$key][$count]['wed_start_time_2'] = $val['shift2_wed_start'];
                                            $dataArray[$key][$count]['wed_end_time_2'] = $val['shift2_wed_end'];
                                        } else {
                                            $dataArray[$key][$count]['wed_start_time_2'] = null;
                                            $dataArray[$key][$count]['wed_end_time_2'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift2_thu_day_off'] == 'no') {
                                if (!$val['shift2_thu_start'] && !$val['shift2_thu_end']) {
                                    Flash::error("Row: $rowNum (shift 2) thursday times record is required");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift2_thu_start'] > $val['shift2_thu_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 2) thursday end time is greater than thursday start time");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift2_thu_start'] != 'off') {
                                            $dataArray[$key][$count]['thurs_start_time_2'] = $val['shift2_thu_start'];
                                            $dataArray[$key][$count]['thurs_end_time_2'] = $val['shift2_thu_end'];
                                        } else {
                                            $dataArray[$key][$count]['thurs_start_time_2'] = null;
                                            $dataArray[$key][$count]['thurs_end_time_2'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift2_fri_day_off'] == 'no') {
                                if (!$val['shift2_fri_start'] && !$val['shift2_fri_end']) {
                                    Flash::error("Row: $rowNum (shift 2) friday times record is required");
                                    $error = true;
                                    return;
                                } else {
                                    if ($val['shift2_fri_start'] > $val['shift2_fri_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 2) friday end time is greater than friday start time");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift2_fri_start'] != 'off') {
                                            $dataArray[$key][$count]['fri_start_time_2'] = $val['shift2_fri_start'];
                                            $dataArray[$key][$count]['fri_end_time_2'] = $val['shift2_fri_end'];
                                        } else {
                                            $dataArray[$key][$count]['fri_start_time_2'] = null;
                                            $dataArray[$key][$count]['fri_end_time_2'] = null;
                                        }
                                    }
                                }
                            }
                            if ($val['num_of_shifts'] == 2) {
                                $dataArray[$key][$count]['sat_start_time_3'] = null;
                                $dataArray[$key][$count]['sat_end_time_3'] = null;
                                $dataArray[$key][$count]['sun_start_time_3'] = null;
                                $dataArray[$key][$count]['sun_end_time_3'] = null;
                                $dataArray[$key][$count]['mon_start_time_3'] = null;
                                $dataArray[$key][$count]['mon_end_time_3'] = null;
                                $dataArray[$key][$count]['tues_start_time_3'] = null;
                                $dataArray[$key][$count]['tues_end_time_3'] = null;
                                $dataArray[$key][$count]['wed_start_time_3'] = null;
                                $dataArray[$key][$count]['wed_end_time_3'] = null;
                                $dataArray[$key][$count]['thurs_start_time_3'] = null;
                                $dataArray[$key][$count]['thurs_end_time_3'] = null;
                                $dataArray[$key][$count]['fri_start_time_3'] = null;
                                $dataArray[$key][$count]['fri_end_time_3'] = null;
                            }
                            if ($val['num_of_shifts'] == 3) {
                                ////////////////shift 3 times///////////////////
                                if ($val['shift3_sat_day_off'] == 'no') {
                                    if (!$val['shift3_sat_start'] && !$val['shift3_sat_end']) {
                                        Flash::error("Row: $rowNum (shift 3) saturday times record is required");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift3_sat_start'] > $val['shift3_sat_end']) {
                                            Flash::error("Row: $rowNum make sure (shift 3) saturday end time is greater than saturday start time");
                                            $error = true;
                                            return;
                                        } else {
                                            if ($val['shift3_sat_start'] != 'off') {
                                                $dataArray[$key][$count]['sat_start_time_3'] = $val['shift3_sat_start'];
                                                $dataArray[$key][$count]['sat_end_time_3'] = $val['shift3_sat_end'];
                                            } else {
                                                $dataArray[$key][$count]['sat_start_time_3'] = null;
                                                $dataArray[$key][$count]['sat_end_time_3'] = null;
                                            }
                                        }
                                    }
                                }
                                //////////////////////////////////////////////
                                if ($val['shift3_sun_day_off'] == 'no') {
                                    if (!$val['shift3_sun_start'] && !$val['shift3_sun_end']) {
                                        Flash::error("Row: $rowNum (shift 3) sunday times record is required");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift3_sun_start'] > $val['shift3_sun_end']) {
                                            Flash::error("Row: $rowNum make sure (shift 3) sunday end time is greater than sunday start time");
                                            $error = true;
                                            return;
                                        } else {
                                            if ($val['shift3_sun_start'] != 'off') {
                                                $dataArray[$key][$count]['sun_start_time_3'] = null;
                                                $dataArray[$key][$count]['sun_end_time_3'] = null;
                                            }
                                        }
                                    }
                                }
                                //////////////////////////////////////////////
                                if ($val['shift3_mon_day_off'] == 'no') {
                                    if (!$val['shift3_mon_start'] && !$val['shift3_mon_end']) {
                                        Flash::error("Row: $rowNum (shift 3) monday times record is required");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift3_mon_start'] > $val['shift3_mon_end']) {
                                            Flash::error("Row: $rowNum make sure (shift 3) monday end time is greater than monday start time");
                                            $error = true;
                                            return;
                                        } else {
                                            if ($val['shift3_mon_start'] != 'off') {
                                                $dataArray[$key][$count]['mon_start_time_3'] = $val['shift3_mon_start'];
                                                $dataArray[$key][$count]['mon_end_time_3'] = $val['shift3_mon_end'];
                                            } else {
                                                $dataArray[$key][$count]['mon_start_time_3'] = null;
                                                $dataArray[$key][$count]['mon_end_time_3'] = null;
                                            }
                                        }
                                    }
                                }
                                //////////////////////////////////////////////
                                if ($val['shift3_tue_day_off'] == 'no') {
                                    if (!$val['shift3_tue_start'] && !$val['shift3_tue_end']) {
                                        Flash::error("Row: $rowNum (shift 3) tuesday times record is required");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift3_tue_start'] > $val['shift3_tue_end']) {
                                            Flash::error("Row: $rowNum make sure (shift 3) tuesday end time is greater than tuesday start time");
                                            $error = true;
                                            return;
                                        } else {
                                            if ($val['shift3_tue_start'] != 'off') {
                                                $dataArray[$key][$count]['tues_start_time_3'] = $val['shift3_tue_start'];
                                                $dataArray[$key][$count]['tues_end_time_3'] = $val['shift3_tue_end'];
                                            } else {
                                                $dataArray[$key][$count]['tues_start_time_3'] = null;
                                                $dataArray[$key][$count]['tues_end_time_3'] = null;
                                            }
                                        }
                                    }
                                }
                                //////////////////////////////////////////////
                                if ($val['shift3_wed_day_off'] == 'no') {
                                    if (!$val['shift3_wed_start'] && !$val['shift3_wed_end']) {
                                        Flash::error("Row: $rowNum (shift 3) wednesday times record is required");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift3_wed_start'] > $val['shift3_wed_end']) {
                                            Flash::error("Row: $rowNum make sure (shift 3) wednesday end time is greater than wednesday start time");
                                            $error = true;
                                            return;
                                        } else {
                                            if ($val['shift3_wed_start'] != 'off') {
                                                $dataArray[$key][$count]['wed_start_time_3'] = $val['shift3_wed_start'];
                                                $dataArray[$key][$count]['wed_end_time_3'] = $val['shift3_wed_end'];
                                            } else {
                                                $dataArray[$key][$count]['wed_start_time_3'] = null;
                                                $dataArray[$key][$count]['wed_end_time_3'] = null;
                                            }
                                        }
                                    }
                                }
                                //////////////////////////////////////////////
                                if ($val['shift3_thu_day_off'] == 'no') {
                                    if (!$val['shift3_thu_start'] && !$val['shift3_thu_end']) {
                                        Flash::error("Row: $rowNum (shift 3) thursday times record is required");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift3_thu_start'] > $val['shift3_thu_end']) {
                                            Flash::error("Row: $rowNum make sure (shift 3) thursday end time is greater than thursday start time");
                                            $error = true;
                                            return;
                                        } else {
                                            if ($val['shift3_thu_start'] != 'off') {
                                                $dataArray[$key][$count]['thurs_start_time_3'] = $val['shift3_thu_start'];
                                                $dataArray[$key][$count]['thurs_end_time_3'] = $val['shift3_thu_end'];
                                            } else {
                                                $dataArray[$key][$count]['thurs_start_time_3'] = null;
                                                $dataArray[$key][$count]['thurs_end_time_3'] = null;
                                            }
                                        }
                                    }
                                }
                                //////////////////////////////////////////////
                                if ($val['shift3_fri_day_off'] == 'no') {
                                    if (!$val['shift3_fri_start'] && !$val['shift3_fri_end']) {
                                        Flash::error("Row: $rowNum (shift 3) friday times record is required");
                                        $error = true;
                                        return;
                                    } else {
                                        if ($val['shift3_fri_start'] > $val['shift3_fri_end']) {
                                            Flash::error("Row: $rowNum make sure (shift 3) friday end time is greater than friday start time");
                                            $error = true;
                                            return;
                                        } else {
                                            if ($val['shift3_fri_start'] != 'off') {
                                                $dataArray[$key][$count]['fri_start_time_3'] = $val['shift3_fri_start'];
                                                $dataArray[$key][$count]['fri_end_time_3'] = $val['shift3_fri_end'];
                                            } else {
                                                $dataArray[$key][$count]['fri_start_time_3'] = null;
                                                $dataArray[$key][$count]['fri_end_time_3'] = null;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        ////////////////days off shift 1///////////////////
                        $shift1DaysOff = '';
                        if ((strtolower($val['shift1_sat_day_off']) == 'yes' || strtolower($val['shift1_sat_day_off']) == '1')) {
                            if (empty($shift1DaysOff)) {
                                $shift1DaysOff .= 'saturday';
                            } else {
                                $shift1DaysOff .= ',saturday';
                            }
                        }
                        if ((strtolower($val['shift1_sun_day_off']) == 'yes' || strtolower($val['shift1_sun_day_off']) == '1')) {
                            if (empty($shift1DaysOff)) {
                                $shift1DaysOff .= 'sunday';
                            } else {
                                $shift1DaysOff .= ',sunday';
                            }
                        }
                        if ((strtolower($val['shift1_mon_day_off']) == 'yes' || strtolower($val['shift1_mon_day_off']) == '1')) {
                            if (empty($shift1DaysOff)) {
                                $shift1DaysOff .= 'monday';
                            } else {
                                $shift1DaysOff .= ',monday';
                            }
                        }
                        if ((strtolower($val['shift1_tue_day_off']) == 'yes' || strtolower($val['shift1_tue_day_off']) == '1')) {
                            if (empty($shift1DaysOff)) {
                                $shift1DaysOff .= 'tuesday';
                            } else {
                                $shift1DaysOff .= ',tuesday';
                            }
                        }
                        if ((strtolower($val['shift1_wed_day_off']) == 'yes' || strtolower($val['shift1_wed_day_off']) == '1')) {
                            if (empty($shift1DaysOff)) {
                                $shift1DaysOff .= 'wednesday';
                            } else {
                                $shift1DaysOff .= ',wednesday';
                            }
                        }
                        if ((strtolower($val['shift1_thu_day_off']) == 'yes' || strtolower($val['shift1_thu_day_off']) == '1')) {
                            if (empty($shift1DaysOff)) {
                                $shift1DaysOff .= 'thursday';
                            } else {
                                $shift1DaysOff .= ',thursday';
                            }
                        }
                        if ((strtolower($val['shift1_fri_day_off']) == 'yes' || strtolower($val['shift1_fri_day_off']) == '1')) {
                            if (empty($shift1DaysOff)) {
                                $shift1DaysOff .= 'friday';
                            } else {
                                $shift1DaysOff .= ',friday';
                            }
                        }
                        $dataArray[$key][$count]['dayoff_1'] = $shift1DaysOff;
                        if ($val['num_of_shifts'] == 1) {
                            $dataArray[$key][$count]['dayoff_2'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                            $dataArray[$key][$count]['dayoff_3'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                        }
                        if ($val['num_of_shifts'] == 2 || $val['num_of_shifts'] == 3) {
                            ////////////////days off shift 2///////////////////
                            $shift2DaysOff = '';
                            if ((strtolower($val['shift2_sat_day_off']) == 'yes' || strtolower($val['shift2_sat_day_off']) == '1')) {
                                if (empty($shift2DaysOff)) {
                                    $shift2DaysOff .= 'saturday';
                                } else {
                                    $shift2DaysOff .= ',saturday';
                                }
                            }
                            if ((strtolower($val['shift2_sun_day_off']) == 'yes' || strtolower($val['shift2_sun_day_off']) == '1')) {
                                if (empty($shift2DaysOff)) {
                                    $shift2DaysOff .= 'sunday';
                                } else {
                                    $shift2DaysOff .= ',sunday';
                                }
                            }
                            if ((strtolower($val['shift2_mon_day_off']) == 'yes' || strtolower($val['shift2_mon_day_off']) == '1')) {
                                if (empty($shift2DaysOff)) {
                                    $shift2DaysOff .= 'monday';
                                } else {
                                    $shift2DaysOff .= ',monday';
                                }
                            }
                            if ((strtolower($val['shift2_tue_day_off']) == 'yes' || strtolower($val['shift2_tue_day_off']) == '1')) {
                                if (empty($shift2DaysOff)) {
                                    $shift2DaysOff .= 'tuesday';
                                } else {
                                    $shift2DaysOff .= ',tuesday';
                                }
                            }
                            if ((strtolower($val['shift2_wed_day_off']) == 'yes' || strtolower($val['shift2_wed_day_off']) == '1')) {
                                if (empty($shift2DaysOff)) {
                                    $shift2DaysOff .= 'wednesday';
                                } else {
                                    $shift2DaysOff .= ',wednesday';
                                }
                            }
                            if ((strtolower($val['shift2_thu_day_off']) == 'yes' || strtolower($val['shift2_thu_day_off']) == '1')) {
                                if (empty($shift2DaysOff)) {
                                    $shift2DaysOff .= 'thursday';
                                } else {
                                    $shift2DaysOff .= ',thursday';
                                }
                            }
                            if ((strtolower($val['shift2_fri_day_off']) == 'yes' || strtolower($val['shift2_fri_day_off']) == '1')) {
                                if (empty($shift2DaysOff)) {
                                    $shift2DaysOff .= 'friday';
                                } else {
                                    $shift2DaysOff .= ',friday';
                                }
                            }
                            $dataArray[$key][$count]['dayoff_2'] = $shift2DaysOff;
                            if ($val['num_of_shifts'] == 2) {
                                $dataArray[$key][$count]['dayoff_3'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                            }
                            if ($val['num_of_shifts'] == 3) {
                                ////////////////days off shift 3///////////////////
                                $shift3DaysOff = '';
                                if ((strtolower($val['shift3_sat_day_off']) == 'yes' || strtolower($val['shift3_sat_day_off']) == '1')) {
                                    if (empty($shift3DaysOff)) {
                                        $shift3DaysOff .= 'saturday';
                                    } else {
                                        $shift3DaysOff .= ',saturday';
                                    }
                                }
                                if ((strtolower($val['shift3_sun_day_off']) == 'yes' || strtolower($val['shift3_sun_day_off']) == '1')) {
                                    if (empty($shift3DaysOff)) {
                                        $shift3DaysOff .= 'sunday';
                                    } else {
                                        $shift3DaysOff .= ',sunday';
                                    }
                                }
                                if ((strtolower($val['shift3_mon_day_off']) == 'yes' || strtolower($val['shift3_mon_day_off']) == '1')) {
                                    if (empty($shift3DaysOff)) {
                                        $shift3DaysOff .= 'monday';
                                    } else {
                                        $shift3DaysOff .= ',monday';
                                    }
                                }
                                if ((strtolower($val['shift3_tue_day_off']) == 'yes' || strtolower($val['shift3_tue_day_off']) == '1')) {
                                    if (empty($shift3DaysOff)) {
                                        $shift3DaysOff .= 'tuesday';
                                    } else {
                                        $shift3DaysOff .= ',tuesday';
                                    }
                                }
                                if ((strtolower($val['shift3_wed_day_off']) == 'yes' || strtolower($val['shift3_wed_day_off']) == '1')) {
                                    if (empty($shift3DaysOff)) {
                                        $shift3DaysOff .= 'wednesday';
                                    } else {
                                        $shift3DaysOff .= ',wednesday';
                                    }
                                }
                                if ((strtolower($val['shift3_thu_day_off']) == 'yes' || strtolower($val['shift3_thu_day_off']) == '1')) {
                                    if (empty($shift3DaysOff)) {
                                        $shift3DaysOff .= 'thursday';
                                    } else {
                                        $shift3DaysOff .= ',thursday';
                                    }
                                }
                                if ((strtolower($val['shift3_fri_day_off']) == 'yes' || strtolower($val['shift3_fri_day_off']) == '1')) {
                                    if (empty($shift3DaysOff)) {
                                        $shift3DaysOff .= 'friday';
                                    } else {
                                        $shift3DaysOff .= ',friday';
                                    }
                                }
                                $dataArray[$key][$count]['dayoff_3'] = $shift3DaysOff;
                                //////////////////////////
                            }
                        }
                        $count++;
                    }
                } elseif (isset($inputs['split']) && $inputs['split'] == 2) {
                    $dataArray[$key]['start_date'] = $start_date;
                    $dataArray[$key]['end_date'] = $end_date;
                    $dataArray[$key]['clinic_schedule_id'] = $val['clinic_schedule_id'];
                    $dataArray[$key]['num_of_shifts'] = $val['num_of_shifts'];
                    $dataArray[$key]['clinic_id'] = $val['clinic_id'];
                    $dataArray[$key]['user_id'] = $val['physician_id'];
                    if ($Repo->checkExist($val['physician_id'], $val['clinic_schedule_id'])) {
                        Flash::error("Row: $rowNum The physician take this schedule before");
                        $error = true;
                        break;
                    }
                    if (empty($val['visit_duration_in_minuets'])) {
                        Flash::error("Row: $rowNum Visit duration in minuets record is required");
                        $error = true;
                        break;
                    } else {
                        $dataArray[$key]['slots'] = $val['visit_duration_in_minuets'];
                    }
                    //////////////////////////////////////////////
                    if ($val['shift1_sat_day_off'] == 'no') {
                        if (!$val['shift1_sat_start'] && !$val['shift1_sat_end']) {
                            Flash::error("Row: $rowNum (shift 1) saturday times record is required");
                            $error = true;
                            break;
                        } else {
                            if ($val['shift1_sat_start'] > $val['shift1_sat_end']) {
                                Flash::error("Row: $rowNum make sure (shift 1) saturday end time is greater than saturday start time");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift1_sat_start'] != 'off') {
                                    $dataArray[$key]['sat_start_time_1'] = $val['shift1_sat_start'];
                                    $dataArray[$key]['sat_end_time_1'] = $val['shift1_sat_end'];
                                } else {
                                    $dataArray[$key]['sat_start_time_1'] = null;
                                    $dataArray[$key]['sat_end_time_1'] = null;
                                }
                            }
                        }
                    }
                    //////////////////////////////////////////////
                    if ($val['shift1_sun_day_off'] == 'no') {
                        if (!$val['shift1_sun_start'] && !$val['shift1_sun_end']) {
                            Flash::error("Row: $rowNum (shift 1) sunday times record is required");
                            $error = true;
                            break;
                        } else {
                            if ($val['shift1_sun_start'] > $val['shift1_sun_end']) {
                                Flash::error("Row: $rowNum make sure (shift 1) sunday end time is greater than sunday start time");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift1_sun_start'] != 'off') {
                                    $dataArray[$key]['sun_start_time_1'] = $val['shift1_sun_start'];
                                    $dataArray[$key]['sun_end_time_1'] = $val['shift1_sun_end'];
                                } else {
                                    $dataArray[$key]['sun_start_time_1'] = null;
                                    $dataArray[$key]['sun_end_time_1'] = null;
                                }
                            }
                        }
                    }
                    //////////////////////////////////////////////
                    if ($val['shift1_mon_day_off'] == 'no') {
                        if (!$val['shift1_mon_start'] && !$val['shift1_mon_end']) {
                            Flash::error("Row: $rowNum (shift 1) monday times record is required");
                            $error = true;
                            break;
                        } else {
                            if ($val['shift1_mon_start'] > $val['shift1_mon_end']) {
                                Flash::error("Row: $rowNum make sure (shift 1) monday end time is greater than monday start time");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift1_mon_start'] != 'off') {
                                    $dataArray[$key]['mon_start_time_1'] = $val['shift1_mon_start'];
                                    $dataArray[$key]['mon_end_time_1'] = $val['shift1_mon_end'];
                                } else {
                                    $dataArray[$key]['mon_start_time_1'] = null;
                                    $dataArray[$key]['mon_end_time_1'] = null;
                                }
                            }
                        }
                    }
                    //////////////////////////////////////////////
                    if ($val['shift1_tue_day_off'] == 'no') {
                        if (!$val['shift1_tue_start'] && !$val['shift1_tue_end']) {
                            Flash::error("Row: $rowNum (shift 1) tuesday times record is required");
                            $error = true;
                            break;
                        } else {
                            if ($val['shift1_tue_start'] > $val['shift1_tue_end']) {
                                Flash::error("Row: $rowNum make sure (shift 1) tuesday end time is greater than tuesday start time");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift1_tue_start'] != 'off') {
                                    $dataArray[$key]['tues_start_time_1'] = $val['shift1_tue_start'];
                                    $dataArray[$key]['tues_end_time_1'] = $val['shift1_tue_end'];
                                } else {
                                    $dataArray[$key]['tues_start_time_1'] = null;
                                    $dataArray[$key]['tues_end_time_1'] = null;
                                }
                            }
                        }
                    }
                    //////////////////////////////////////////////
                    if ($val['shift1_wed_day_off'] == 'no') {
                        if (!$val['shift1_wed_start'] && !$val['shift1_wed_end']) {
                            Flash::error("Row: $rowNum (shift 1) wednesday times record is required");
                            $error = true;
                            break;
                        } else {
                            if ($val['shift1_wed_start'] > $val['shift1_wed_end']) {
                                Flash::error("Row: $rowNum make sure (shift 1) wednesday end time is greater than wednesday start time");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift1_wed_start'] != 'off') {
                                    $dataArray[$key]['wed_start_time_1'] = $val['shift1_wed_start'];
                                    $dataArray[$key]['wed_end_time_1'] = $val['shift1_wed_end'];
                                } else {
                                    $dataArray[$key]['wed_start_time_1'] = null;
                                    $dataArray[$key]['wed_end_time_1'] = null;
                                }
                            }
                        }
                    }
                    //////////////////////////////////////////////
                    if ($val['shift1_thu_day_off'] == 'no') {
                        if (!$val['shift1_thu_start'] && !$val['shift1_thu_end']) {
                            Flash::error("Row: $rowNum (shift 1) thursday times record is required");
                            $error = true;
                            break;
                        } else {
                            if ($val['shift1_thu_start'] > $val['shift1_thu_end']) {
                                Flash::error("Row: $rowNum make sure (shift 1) thursday end time is greater than thursday start time");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift1_thu_start'] != 'off') {
                                    $dataArray[$key]['thurs_start_time_1'] = $val['shift1_thu_start'];
                                    $dataArray[$key]['thurs_end_time_1'] = $val['shift1_thu_end'];
                                } else {
                                    $dataArray[$key]['thurs_start_time_1'] = null;
                                    $dataArray[$key]['thurs_end_time_1'] = null;
                                }
                            }
                        }
                    }
                    //////////////////////////////////////////////
                    if ($val['shift1_fri_day_off'] == 'no') {
                        if (!$val['shift1_fri_start'] && !$val['shift1_fri_end']) {
                            Flash::error("Row: $rowNum (shift 1) friday times record is required");
                            $error = true;
                            break;
                        } else {
                            if ($val['shift1_fri_start'] > $val['shift1_fri_end']) {
                                Flash::error("Row: $rowNum make sure (shift 1) friday end time is greater than friday start time");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift1_fri_start'] != 'off') {
                                    $dataArray[$key]['fri_start_time_1'] = $val['shift1_fri_start'];
                                    $dataArray[$key]['fri_end_time_1'] = $val['shift1_fri_end'];
                                } else {
                                    $dataArray[$key]['fri_start_time_1'] = null;
                                    $dataArray[$key]['fri_end_time_1'] = null;
                                }
                            }
                        }
                    }
                    if ($val['num_of_shifts'] == 1) {
                        $dataArray[$key]['sat_start_time_2'] = null;
                        $dataArray[$key]['sat_end_time_2'] = null;
                        $dataArray[$key]['sun_start_time_2'] = null;
                        $dataArray[$key]['sun_end_time_2'] = null;
                        $dataArray[$key]['mon_start_time_2'] = null;
                        $dataArray[$key]['mon_end_time_2'] = null;
                        $dataArray[$key]['tues_start_time_2'] = null;
                        $dataArray[$key]['tues_end_time_2'] = null;
                        $dataArray[$key]['wed_start_time_2'] = null;
                        $dataArray[$key]['wed_end_time_2'] = null;
                        $dataArray[$key]['thurs_start_time_2'] = null;
                        $dataArray[$key]['thurs_end_time_2'] = null;
                        $dataArray[$key]['fri_start_time_2'] = null;
                        $dataArray[$key]['fri_end_time_2'] = null;
                        $dataArray[$key]['sat_start_time_3'] = null;
                        $dataArray[$key]['sat_end_time_3'] = null;
                        $dataArray[$key]['sun_start_time_3'] = null;
                        $dataArray[$key]['sun_end_time_3'] = null;
                        $dataArray[$key]['mon_start_time_3'] = null;
                        $dataArray[$key]['mon_end_time_3'] = null;
                        $dataArray[$key]['tues_start_time_3'] = null;
                        $dataArray[$key]['tues_end_time_3'] = null;
                        $dataArray[$key]['wed_start_time_3'] = null;
                        $dataArray[$key]['wed_end_time_3'] = null;
                        $dataArray[$key]['thurs_start_time_3'] = null;
                        $dataArray[$key]['thurs_end_time_3'] = null;
                        $dataArray[$key]['fri_start_time_3'] = null;
                        $dataArray[$key]['fri_end_time_3'] = null;
                    }
                    if ($val['num_of_shifts'] == 2 || $val['num_of_shifts'] == 3) {
                        ////////////////////shift 2 times//////////////////////////
                        if ($val['shift2_sat_day_off'] == 'no') {
                            if (!$val['shift2_sat_start'] && !$val['shift2_sat_end']) {
                                Flash::error("Row: $rowNum (shift 2) saturday times record is required");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift2_sat_start'] > $val['shift2_sat_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 2) saturday end time is greater than saturday start time");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift2_sat_start'] != 'off') {
                                        $dataArray[$key]['sat_start_time_2'] = $val['shift2_sat_start'];
                                        $dataArray[$key]['sat_end_time_2'] = $val['shift2_sat_end'];
                                    } else {
                                        $dataArray[$key]['sat_start_time_2'] = null;
                                        $dataArray[$key]['sat_end_time_2'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift2_sun_day_off'] == 'no') {
                            if (!$val['shift2_sun_start'] && !$val['shift2_sun_end']) {
                                Flash::error("Row: $rowNum (shift 2) sunday times record is required");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift2_sun_start'] > $val['shift2_sun_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 2) sunday end time is greater than sunday start time");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift2_sun_start'] != 'off') {
                                        $dataArray[$key]['sun_start_time_2'] = $val['shift2_sun_start'];
                                        $dataArray[$key]['sun_end_time_2'] = $val['shift2_sun_end'];
                                    } else {
                                        $dataArray[$key]['sun_start_time_2'] = null;
                                        $dataArray[$key]['sun_end_time_2'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift2_mon_day_off'] == 'no') {
                            if (!$val['shift2_mon_start'] && !$val['shift2_mon_end']) {
                                Flash::error("Row: $rowNum (shift 2) monday times record is required");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift2_mon_start'] > $val['shift2_mon_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 2) monday end time is greater than monday start time");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift2_mon_start'] != 'off') {
                                        $dataArray[$key]['mon_start_time_2'] = $val['shift2_mon_start'];
                                        $dataArray[$key]['mon_end_time_2'] = $val['shift2_mon_end'];
                                    } else {
                                        $dataArray[$key]['mon_start_time_2'] = null;
                                        $dataArray[$key]['mon_end_time_2'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift2_tue_day_off'] == 'no') {
                            if (!$val['shift2_tue_start'] && !$val['shift2_tue_end']) {
                                Flash::error("Row: $rowNum (shift 2) tuesday times record is required");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift2_tue_start'] > $val['shift2_tue_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 2) tuesday end time is greater than tuesday start time");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift2_tue_start'] != 'off') {
                                        $dataArray[$key]['tues_start_time_2'] = $val['shift2_tue_start'];
                                        $dataArray[$key]['tues_end_time_2'] = $val['shift2_tue_end'];
                                    } else {
                                        $dataArray[$key]['tues_start_time_2'] = null;
                                        $dataArray[$key]['tues_end_time_2'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift2_wed_day_off'] == 'no') {
                            if (!$val['shift2_wed_start'] && !$val['shift2_wed_end']) {
                                Flash::error("Row: $rowNum (shift 2) wednesday times record is required");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift2_wed_start'] > $val['shift2_wed_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 2) wednesday end time is greater than wednesday start time");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift2_wed_start'] != 'off') {
                                        $dataArray[$key]['wed_start_time_2'] = $val['shift2_wed_start'];
                                        $dataArray[$key]['wed_end_time_2'] = $val['shift2_wed_end'];
                                    } else {
                                        $dataArray[$key]['wed_start_time_2'] = null;
                                        $dataArray[$key]['wed_end_time_2'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift2_thu_day_off'] == 'no') {
                            if (!$val['shift2_thu_start'] && !$val['shift2_thu_end']) {
                                Flash::error("Row: $rowNum (shift 2) thursday times record is required");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift2_thu_start'] > $val['shift2_thu_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 2) thursday end time is greater than thursday start time");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift2_thu_start'] != 'off') {
                                        $dataArray[$key]['thurs_start_time_2'] = $val['shift2_thu_start'];
                                        $dataArray[$key]['thurs_end_time_2'] = $val['shift2_thu_end'];
                                    } else {
                                        $dataArray[$key]['thurs_start_time_2'] = null;
                                        $dataArray[$key]['thurs_end_time_2'] = null;
                                    }
                                }
                            }
                        }
                        //////////////////////////////////////////////
                        if ($val['shift2_fri_day_off'] == 'no') {
                            if (!$val['shift2_fri_start'] && !$val['shift2_fri_end']) {
                                Flash::error("Row: $rowNum (shift 2) friday times record is required");
                                $error = true;
                                break;
                            } else {
                                if ($val['shift2_fri_start'] > $val['shift2_fri_end']) {
                                    Flash::error("Row: $rowNum make sure (shift 2) friday end time is greater than friday start time");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift2_fri_start'] != 'off') {
                                        $dataArray[$key]['fri_start_time_2'] = $val['shift2_fri_start'];
                                        $dataArray[$key]['fri_end_time_2'] = $val['shift2_fri_end'];
                                    } else {
                                        $dataArray[$key]['fri_start_time_2'] = null;
                                        $dataArray[$key]['fri_end_time_2'] = null;
                                    }
                                }
                            }
                        }
                        if ($val['num_of_shifts'] == 2) {
                            $dataArray[$key]['sat_start_time_3'] = null;
                            $dataArray[$key]['sat_end_time_3'] = null;
                            $dataArray[$key]['sun_start_time_3'] = null;
                            $dataArray[$key]['sun_end_time_3'] = null;
                            $dataArray[$key]['mon_start_time_3'] = null;
                            $dataArray[$key]['mon_end_time_3'] = null;
                            $dataArray[$key]['tues_start_time_3'] = null;
                            $dataArray[$key]['tues_end_time_3'] = null;
                            $dataArray[$key]['wed_start_time_3'] = null;
                            $dataArray[$key]['wed_end_time_3'] = null;
                            $dataArray[$key]['thurs_start_time_3'] = null;
                            $dataArray[$key]['thurs_end_time_3'] = null;
                            $dataArray[$key]['fri_start_time_3'] = null;
                            $dataArray[$key]['fri_end_time_3'] = null;
                        }
                        if ($val['num_of_shifts'] == 3) {
                            ////////////////shift 3 times///////////////////
                            if ($val['shift3_sat_day_off'] == 'no') {
                                if (!$val['shift3_sat_start'] && !$val['shift3_sat_end']) {
                                    Flash::error("Row: $rowNum (shift 3) saturday times record is required");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift3_sat_start'] > $val['shift3_sat_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 3) saturday end time is greater than saturday start time");
                                        $error = true;
                                        break;
                                    } else {
                                        if ($val['shift3_sat_start'] != 'off') {
                                            $dataArray[$key]['sat_start_time_3'] = $val['shift3_sat_start'];
                                            $dataArray[$key]['sat_end_time_3'] = $val['shift3_sat_end'];
                                        } else {
                                            $dataArray[$key]['sat_start_time_3'] = null;
                                            $dataArray[$key]['sat_end_time_3'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift3_sun_day_off'] == 'no') {
                                if (!$val['shift3_sun_start'] && !$val['shift3_sun_end']) {
                                    Flash::error("Row: $rowNum (shift 3) sunday times record is required");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift3_sun_start'] > $val['shift3_sun_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 3) sunday end time is greater than sunday start time");
                                        $error = true;
                                        break;
                                    } else {
                                        if ($val['shift3_sun_start'] != 'off') {
                                            $dataArray[$key]['sun_start_time_3'] = null;
                                            $dataArray[$key]['sun_end_time_3'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift3_mon_day_off'] == 'no') {
                                if (!$val['shift3_mon_start'] && !$val['shift3_mon_end']) {
                                    Flash::error("Row: $rowNum (shift 3) monday times record is required");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift3_mon_start'] > $val['shift3_mon_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 3) monday end time is greater than monday start time");
                                        $error = true;
                                        break;
                                    } else {
                                        if ($val['shift3_mon_start'] != 'off') {
                                            $dataArray[$key]['mon_start_time_3'] = $val['shift3_mon_start'];
                                            $dataArray[$key]['mon_end_time_3'] = $val['shift3_mon_end'];
                                        } else {
                                            $dataArray[$key]['mon_start_time_3'] = null;
                                            $dataArray[$key]['mon_end_time_3'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift3_tue_day_off'] == 'no') {
                                if (!$val['shift3_tue_start'] && !$val['shift3_tue_end']) {
                                    Flash::error("Row: $rowNum (shift 3) tuesday times record is required");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift3_tue_start'] > $val['shift3_tue_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 3) tuesday end time is greater than tuesday start time");
                                        $error = true;
                                        break;
                                    } else {
                                        if ($val['shift3_tue_start'] != 'off') {
                                            $dataArray[$key]['tues_start_time_3'] = $val['shift3_tue_start'];
                                            $dataArray[$key]['tues_end_time_3'] = $val['shift3_tue_end'];
                                        } else {
                                            $dataArray[$key]['tues_start_time_3'] = null;
                                            $dataArray[$key]['tues_end_time_3'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift3_wed_day_off'] == 'no') {
                                if (!$val['shift3_wed_start'] && !$val['shift3_wed_end']) {
                                    Flash::error("Row: $rowNum (shift 3) wednesday times record is required");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift3_wed_start'] > $val['shift3_wed_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 3) wednesday end time is greater than wednesday start time");
                                        $error = true;
                                        break;
                                    } else {
                                        if ($val['shift3_wed_start'] != 'off') {
                                            $dataArray[$key]['wed_start_time_3'] = $val['shift3_wed_start'];
                                            $dataArray[$key]['wed_end_time_3'] = $val['shift3_wed_end'];
                                        } else {
                                            $dataArray[$key]['wed_start_time_3'] = null;
                                            $dataArray[$key]['wed_end_time_3'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift3_thu_day_off'] == 'no') {
                                if (!$val['shift3_thu_start'] && !$val['shift3_thu_end']) {
                                    Flash::error("Row: $rowNum (shift 3) thursday times record is required");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift3_thu_start'] > $val['shift3_thu_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 3) thursday end time is greater than thursday start time");
                                        $error = true;
                                        break;
                                    } else {
                                        if ($val['shift3_thu_start'] != 'off') {
                                            $dataArray[$key]['thurs_start_time_3'] = $val['shift3_thu_start'];
                                            $dataArray[$key]['thurs_end_time_3'] = $val['shift3_thu_end'];
                                        } else {
                                            $dataArray[$key]['thurs_start_time_3'] = null;
                                            $dataArray[$key]['thurs_end_time_3'] = null;
                                        }
                                    }
                                }
                            }
                            //////////////////////////////////////////////
                            if ($val['shift3_fri_day_off'] == 'no') {
                                if (!$val['shift3_fri_start'] && !$val['shift3_fri_end']) {
                                    Flash::error("Row: $rowNum (shift 3) friday times record is required");
                                    $error = true;
                                    break;
                                } else {
                                    if ($val['shift3_fri_start'] > $val['shift3_fri_end']) {
                                        Flash::error("Row: $rowNum make sure (shift 3) friday end time is greater than friday start time");
                                        $error = true;
                                        break;
                                    } else {
                                        if ($val['shift3_fri_start'] != 'off') {
                                            $dataArray[$key]['fri_start_time_3'] = $val['shift3_fri_start'];
                                            $dataArray[$key]['fri_end_time_3'] = $val['shift3_fri_end'];
                                        } else {
                                            $dataArray[$key]['fri_start_time_3'] = null;
                                            $dataArray[$key]['fri_end_time_3'] = null;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    ////////////////days off shift 1///////////////////
                    $shift1DaysOff = '';
                    if ((strtolower($val['shift1_sat_day_off']) == 'yes' || strtolower($val['shift1_sat_day_off']) == '1')) {
                        if (empty($shift1DaysOff)) {
                            $shift1DaysOff .= 'saturday';
                        } else {
                            $shift1DaysOff .= ',saturday';
                        }
                    }
                    if ((strtolower($val['shift1_sun_day_off']) == 'yes' || strtolower($val['shift1_sun_day_off']) == '1')) {
                        if (empty($shift1DaysOff)) {
                            $shift1DaysOff .= 'sunday';
                        } else {
                            $shift1DaysOff .= ',sunday';
                        }
                    }
                    if ((strtolower($val['shift1_mon_day_off']) == 'yes' || strtolower($val['shift1_mon_day_off']) == '1')) {
                        if (empty($shift1DaysOff)) {
                            $shift1DaysOff .= 'monday';
                        } else {
                            $shift1DaysOff .= ',monday';
                        }
                    }
                    if ((strtolower($val['shift1_tue_day_off']) == 'yes' || strtolower($val['shift1_tue_day_off']) == '1')) {
                        if (empty($shift1DaysOff)) {
                            $shift1DaysOff .= 'tuesday';
                        } else {
                            $shift1DaysOff .= ',tuesday';
                        }
                    }
                    if ((strtolower($val['shift1_wed_day_off']) == 'yes' || strtolower($val['shift1_wed_day_off']) == '1')) {
                        if (empty($shift1DaysOff)) {
                            $shift1DaysOff .= 'wednesday';
                        } else {
                            $shift1DaysOff .= ',wednesday';
                        }
                    }
                    if ((strtolower($val['shift1_thu_day_off']) == 'yes' || strtolower($val['shift1_thu_day_off']) == '1')) {
                        if (empty($shift1DaysOff)) {
                            $shift1DaysOff .= 'thursday';
                        } else {
                            $shift1DaysOff .= ',thursday';
                        }
                    }
                    if ((strtolower($val['shift1_fri_day_off']) == 'yes' || strtolower($val['shift1_fri_day_off']) == '1')) {
                        if (empty($shift1DaysOff)) {
                            $shift1DaysOff .= 'friday';
                        } else {
                            $shift1DaysOff .= ',friday';
                        }
                    }
                    $dataArray[$key]['dayoff_1'] = $shift1DaysOff;
                    if ($val['num_of_shifts'] == 1) {
                        $dataArray[$key]['dayoff_2'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                        $dataArray[$key]['dayoff_3'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                    }
                    if ($val['num_of_shifts'] == 2 || $val['num_of_shifts'] == 3) {
                        ////////////////days off shift 2///////////////////
                        $shift2DaysOff = '';
                        if ((strtolower($val['shift2_sat_day_off']) == 'yes' || strtolower($val['shift2_sat_day_off']) == '1')) {
                            if (empty($shift2DaysOff)) {
                                $shift2DaysOff .= 'saturday';
                            } else {
                                $shift2DaysOff .= ',saturday';
                            }
                        }
                        if ((strtolower($val['shift2_sun_day_off']) == 'yes' || strtolower($val['shift2_sun_day_off']) == '1')) {
                            if (empty($shift2DaysOff)) {
                                $shift2DaysOff .= 'sunday';
                            } else {
                                $shift2DaysOff .= ',sunday';
                            }
                        }
                        if ((strtolower($val['shift2_mon_day_off']) == 'yes' || strtolower($val['shift2_mon_day_off']) == '1')) {
                            if (empty($shift2DaysOff)) {
                                $shift2DaysOff .= 'monday';
                            } else {
                                $shift2DaysOff .= ',monday';
                            }
                        }
                        if ((strtolower($val['shift2_tue_day_off']) == 'yes' || strtolower($val['shift2_tue_day_off']) == '1')) {
                            if (empty($shift2DaysOff)) {
                                $shift2DaysOff .= 'tuesday';
                            } else {
                                $shift2DaysOff .= ',tuesday';
                            }
                        }
                        if ((strtolower($val['shift2_wed_day_off']) == 'yes' || strtolower($val['shift2_wed_day_off']) == '1')) {
                            if (empty($shift2DaysOff)) {
                                $shift2DaysOff .= 'wednesday';
                            } else {
                                $shift2DaysOff .= ',wednesday';
                            }
                        }
                        if ((strtolower($val['shift2_thu_day_off']) == 'yes' || strtolower($val['shift2_thu_day_off']) == '1')) {
                            if (empty($shift2DaysOff)) {
                                $shift2DaysOff .= 'thursday';
                            } else {
                                $shift2DaysOff .= ',thursday';
                            }
                        }
                        if ((strtolower($val['shift2_fri_day_off']) == 'yes' || strtolower($val['shift2_fri_day_off']) == '1')) {
                            if (empty($shift2DaysOff)) {
                                $shift2DaysOff .= 'friday';
                            } else {
                                $shift2DaysOff .= ',friday';
                            }
                        }
                        $dataArray[$key]['dayoff_2'] = $shift2DaysOff;
                        if ($val['num_of_shifts'] == 2) {
                            $dataArray[$key]['dayoff_3'] = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                        }
                        if ($val['num_of_shifts'] == 3) {
                            ////////////////days off shift 3///////////////////
                            $shift3DaysOff = '';
                            if ((strtolower($val['shift3_sat_day_off']) == 'yes' || strtolower($val['shift3_sat_day_off']) == '1')) {
                                if (empty($shift3DaysOff)) {
                                    $shift3DaysOff .= 'saturday';
                                } else {
                                    $shift3DaysOff .= ',saturday';
                                }
                            }
                            if ((strtolower($val['shift3_sun_day_off']) == 'yes' || strtolower($val['shift3_sun_day_off']) == '1')) {
                                if (empty($shift3DaysOff)) {
                                    $shift3DaysOff .= 'sunday';
                                } else {
                                    $shift3DaysOff .= ',sunday';
                                }
                            }
                            if ((strtolower($val['shift3_mon_day_off']) == 'yes' || strtolower($val['shift3_mon_day_off']) == '1')) {
                                if (empty($shift3DaysOff)) {
                                    $shift3DaysOff .= 'monday';
                                } else {
                                    $shift3DaysOff .= ',monday';
                                }
                            }
                            if ((strtolower($val['shift3_tue_day_off']) == 'yes' || strtolower($val['shift3_tue_day_off']) == '1')) {
                                if (empty($shift3DaysOff)) {
                                    $shift3DaysOff .= 'tuesday';
                                } else {
                                    $shift3DaysOff .= ',tuesday';
                                }
                            }
                            if ((strtolower($val['shift3_wed_day_off']) == 'yes' || strtolower($val['shift3_wed_day_off']) == '1')) {
                                if (empty($shift3DaysOff)) {
                                    $shift3DaysOff .= 'wednesday';
                                } else {
                                    $shift3DaysOff .= ',wednesday';
                                }
                            }
                            if ((strtolower($val['shift3_thu_day_off']) == 'yes' || strtolower($val['shift3_thu_day_off']) == '1')) {
                                if (empty($shift3DaysOff)) {
                                    $shift3DaysOff .= 'thursday';
                                } else {
                                    $shift3DaysOff .= ',thursday';
                                }
                            }
                            if ((strtolower($val['shift3_fri_day_off']) == 'yes' || strtolower($val['shift3_fri_day_off']) == '1')) {
                                if (empty($shift3DaysOff)) {
                                    $shift3DaysOff .= 'friday';
                                } else {
                                    $shift3DaysOff .= ',friday';
                                }
                            }
                            $dataArray[$key]['dayoff_3'] = $shift3DaysOff;
                            //////////////////////////
                        }
                    }
                }
            }
        });
        if (!$error) {
            $Repo = new PhysicianScheduleRepository();
            if (isset($inputs['split']) && $inputs['split'] == 1) {
                foreach ($dataArray as $key => $val) {
                    foreach ($val as $key2 => $val2) {
                        $val2['publish'] = 2; // unpublished
                        $Repo->save($val2);
                    }
                }
            } elseif (isset($inputs['split']) && $inputs['split'] == 2) {
                foreach ($dataArray as $key => $val) {
                    $val['publish'] = 2; // unpublished
                    $Repo->save($val);
                }
            }
            Flash::success("Imported Successfully");
        }
        unlink($inputs['template']);
        return Redirect::back();
    }

    public function changeStatusPhysicianSchedule($id)
    {
        $physicianSchedule = PhysicianSchedule::getById($id);
        if ($physicianSchedule['publish'] == 1) {
            $status = 2;
            Flash::success('Unpublished Successfully');
        } else {
            $status = 1;
            Flash::success('Published Successfully');
        }
        PhysicianSchedule::edit(array('publish' => $status), $id);
        return Redirect::back();
    }

    public function changeDatePhysicianSchedule()
    {
        $inputs = (Input::except('_token'));
        $physicianSchedule = PhysicianSchedule::getById($inputs['id']);
        $checkSchedule = PhysicianSchedule::checkExistDate($physicianSchedule['user_id'], $inputs['end_date'], $inputs['id']);
        if ($checkSchedule) {
            Flash::error("The end date is used in another schedule!");
            return Redirect::back();
        }

        PhysicianSchedule::edit(array(
            'end_date' => $inputs['end_date']
        ), $inputs['id']);
        $nextSchedule = PhysicianSchedule::getNextWithDate($physicianSchedule['user_id'], $inputs['end_date'], $inputs['id']);
        if ($nextSchedule) {
            $end_date = date('Y-m-d', strtotime($nextSchedule['start_date'] . "-1 days"));
        } else {
            $end_date = null;
        }
        $reservations = Reservation::getByPhysicianSchedule($physicianSchedule['clinic_id']
            , $physicianSchedule['user_id'], date('Y-m-d', strtotime($inputs['end_date'] . "+1 days")), $end_date);
        if ($reservations) {
            foreach ($reservations as $key => $val) {
                Reservation::edit(array(
                    'update_by' => $this->user->id,
                    'status' => ReservationStatus::archive,
                    'patient_status' => PatientStatus::archive,
                    'exception_reason' => 'Edit In Physician Schedule',
                    'show_reason' => 1,
                ), $val['id']);
                ReservationHistory::add([
                    'action' => 'Archive From Edit Schedule',
                    'action_by' => $this->user->id,
                    'reservation_id' => $val['id'],
                    'code' => $val['code'],
                    'physician_id' => $val['physician_id'],
                    'clinic_id' => $val['clinic_id'],
                    'patient_id' => $val['patient_id'],
                    'date' => $val['date'],
                    'time_from' => $val['time_from'],
                    'time_to' => $val['time_to'],
                    'status' => ReservationStatus::archive,
                    'patient_status' => PatientStatus::archive,
                    'exception_reason' => 'Edit In Physician Schedule',
                ]);
            }
        }
        Flash::success('Updated Successfully');

        return Redirect::back();
    }

    public function changeStatusPhysicianScheduleArray()
    {
        $inputs = (Input::except('_token'));
        $ids = explode(',', $inputs['ids']);
        if ($ids) {
            foreach ($ids as $key => $val) {
                $status = 1;
                PhysicianSchedule::edit(array('publish' => $status), $val);
            }
            Flash::success('Published Successfully');
            return Redirect::back();
        } else {
            Flash::error('Missing date!');
            return Redirect::back();
        }
    }

    public function deletePhysicianScheduleArray()
    {
        $inputs = (Input::except('_token'));
        $ids = explode(',', $inputs['ids']);
        if ($ids) {
            PhysicianSchedule::removeArray($ids);
            Flash::success('Deleted Successfully');
            return Redirect::back();
        } else {
            Flash::error('Missing date!');
            return Redirect::back();
        }
    }
}
