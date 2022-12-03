<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\clinic\ClinicRepository;
use core\clinicSchedule\ClinicScheduleManager;
use core\clinicSchedule\ClinicScheduleRepository;
use core\enums\ResponseTypes;
use core\hospital\HospitalRepository;

class ClinicScheduleController extends BaseController
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
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinicSchedule.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        if ($inputs) {
            $clinicScheduleRepo = new ClinicScheduleRepository();
            $data['clinicSchedules'] = $clinicScheduleRepo->getAllWithFilter($inputs);
        } else {
            $clinicScheduleRepo = new ClinicScheduleRepository();
            $data['clinicSchedules'] = $clinicScheduleRepo->getAll();
        }

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('clinicSchedule/list', $data);
    }

    public function addClinicSchedule()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinicSchedule.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('clinicSchedule/add', $data);
    }

    public function createClinicSchedule()
    {
        $clinicScheduleManager = new ClinicScheduleManager();
        $inputs = (Input::except('_token'));
        $data = $clinicScheduleManager->createClinicSchedule($inputs);
        if ($data['status']) {
            return Redirect::route('clinicSchedules');
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editClinicSchedule($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinicSchedule.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $phySchRepo = new PhysicianSchedule();
//        if ($phySchRepo->getClinicScheduleId($id)) {
//            $response = new ResponseClass();
//            $response->ResponseObject(ResponseTypes::error, 'Can\'t edit this schedule, their is related records with physician schedule');
//            return Redirect::back();
//        }
        $clinicScheduleRepo = new ClinicScheduleRepository();
        $data['clinicSchedule'] = $clinicScheduleRepo->getById($id);
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('clinicSchedule/edit', $data);
    }

    public function updateClinicSchedule($id)
    {
        $systemManager = new ClinicScheduleManager();
        $inputs = (Input::except('_token'));
        $data = $systemManager->updateClinicSchedule($inputs, $id);
        if ($data['status']) {
            return Redirect::route('clinicSchedules');
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deleteClinicSchedule($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinicSchedule.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $phySchRepo = new PhysicianSchedule();
        if ($phySchRepo->getClinicScheduleId($id)) {
            $response = new ResponseClass();
            $response->ResponseObject(ResponseTypes::error, 'Can\'t edit this schedule, their is related records with physician schedule');
            return Redirect::route('clinicSchedules');
        }
        $clinicScheduleRepo = new ClinicScheduleRepository();
        $clinicScheduleRepo->delete($id);
        return Redirect::back();
    }

    public function duplicateClinicSchedule()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinicSchedule.duplicate')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $validation = Validator::make(Input::all(), ClinicSchedule::$rules);
        if ($validation->fails()) {
            return Flash::error($validation->messages());
        }
        $id = Input::get('clinic_schedule_id');
        $cSRepo = new ClinicScheduleRepository();
        $inputs = $cSRepo->getById($id)->toArray();

        $Repo = new ClinicScheduleRepository();
        if ($Repo->checkDataIsAvailable(Input::get('end_date'), null, $inputs['clinic_id'])) {
            Flash::error("End date is used in another schedule in clinic");
            return Redirect::back();
        }
        unset($inputs['id']);
        unset($inputs['created_at']);
        unset($inputs['updated_at']);
        unset($inputs['create_timestamp']);
        $inputs['start_date'] = Input::get('start_date');
        $inputs['end_date'] = Input::get('end_date');
        $inputs['name'] = $inputs['start_date'] . ' ' . $inputs['end_date'];
        $cSRepo->save($inputs);
        Flash::success('Duplicated Successfully');
        return Redirect::back();
    }

    public function getLastScheduleOfClinic()
    {
        $id = Input::get('clinic_id');
        $schedule_id = Input::get('schedule_id');
        $cSRepo = new ClinicScheduleRepository();
        $clinicSchedules = $cSRepo->getAllByClinicId($id);
        $lastSchedule = end($clinicSchedules);
        if ($lastSchedule) {
            $lastSchedule['true'] = 1;
        } else {
            $lastSchedule['true'] = 0;
        }
        if (!empty($schedule_id)) {
            $lastSchedule['schedule_id'] = $schedule_id;
        }
        return $lastSchedule;
    }

    public function getScheduleId()
    {
        $id = Input::get('clinic_schedule_id');
        $sCRepo = new ClinicScheduleRepository();
        return $sCRepo->getById($id)->toArray();
    }

    public function importExcelClinicSchedule()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('clinicSchedule.importExcel')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('clinicSchedule/importExcel', $data);
    }

    public function postImportExcelClinicSchedule()
    {
        $inputs = (Input::except('_token'));
        if (!empty($inputs['start_date']) && !empty($inputs['end_date'])) {
            if ($inputs['start_date'] > $inputs['end_date']) {
                Flash::error("Make sure the end date is greater than start date");
                return Redirect::back();
            }
        } else {
            Flash::error("Dates Fields Is Required");
            return Redirect::back();
        }
        if (!empty($inputs['template'])) {
            $file = Input::file('template');
            $filename = date('Y-m-d_H-i-s') . '.' . $file->getClientOriginalExtension();
            $destinationPath = public_path() . '/excel/clinics';
            $upload_success = $file->move($destinationPath, $filename);
            if ($upload_success) {
                $inputs['template'] = 'excel/clinics/' . $filename;
            } else {
                Flash::error("The File Not Uploaded Correctly");
                return Redirect::back();
            }
        } else {
            Flash::error("Import Template File Is Required");
            return Redirect::back();
        }
        ini_set('max_execution_time', 3000);
        $error = false;
        $dataArray = array();
        Excel::load($inputs['template'], function ($reader) use ($inputs, &$error, &$dataArray) {
            $sheet = $reader->toArray();
            $Repo = new ClinicScheduleRepository();
            foreach ($sheet as $key => $val) {
                $rowNum = $key + 2;
                $dataArray[$key]['hospital_id'] = $val['hospital_id'];
                $dataArray[$key]['clinic_id'] = $val['clinic_id'];
                $dataArray[$key]['num_of_shifts'] = $val['num_of_shifts'];
                if ($Repo->checkDataIsAvailable($inputs['start_date'], null, $val['clinic_id'])) {
                    Flash::error("Row: $rowNum Start date is used in another schedule in clinic: " . $val['clinic_name']);
                    $error = true;
                    return;
                } else {
                    $lastSchedule = ClinicSchedule::getAllByClinicId($val['clinic_id'])->last();
                    if ($lastSchedule) {
                        $start_date = date("Y-m-d", strtotime("+1 day", strtotime($lastSchedule['end_date'])));
                        if ($start_date != $inputs['start_date']) {
                            Flash::error("Row: $rowNum We found blank days from end date of last schedule: " . $lastSchedule['end_date']);
                            $error = true;
                            return;
                        }
                        $dataArray[$key]['start_date'] = $inputs['start_date'];
                    } else {
                        $dataArray[$key]['start_date'] = $inputs['start_date'];
                    }
                }
                if ($Repo->checkDataIsAvailable($inputs['end_date'], null, $val['clinic_id'])) {
                    Flash::error("Row: $rowNum End date is used in another schedule in clinic: " . $val['clinic_name']);
                    $error = true;
                    return;
                } else {
                    $dataArray[$key]['end_date'] = $inputs['end_date'];
                }
                $dataArray[$key]['name'] = $inputs['start_date'] . ' ' . $inputs['end_date'];
                if (!$val['shift1_start'] && !$val['shift1_end']) {
                    Flash::error("Row: $rowNum shift 1 times records is required");
                    $error = true;
                    return;
                } else {
                    if ($val['shift1_start'] >= $val['shift1_end']) {
                        Flash::error("Row: $rowNum (shift 1) make sure end time is greater than start time");
                        $error = true;
                        return;
                    } else {
                        $dataArray[$key]['shift1_start_time'] = $val['shift1_start'] ? $val['shift1_start'] : null;
                        $dataArray[$key]['shift1_end_time'] = $val['shift1_end'] ? $val['shift1_end'] : null;
                    }
                }
                if ($val['num_of_shifts'] == 2 || $val['num_of_shifts'] == 3) {
                    if (!$val['shift2_start'] && !$val['shift2_end']) {
                        Flash::error("Row: $rowNum shift 2 times records is required");
                        $error = true;
                        return;
                    } else {
                        if ($val['shift2_start'] >= $val['shift2_end']) {
                            Flash::error("Row: $rowNum (shift 2) make sure end time is greater than start time");
                            $error = true;
                            return;
                        } else {
                            $dataArray[$key]['shift2_start_time'] = $val['shift2_start'] ? $val['shift2_start'] : null;
                            $dataArray[$key]['shift2_end_time'] = $val['shift2_end'] ? $val['shift2_end'] : null;
                        }
                    }
                    if ($val['num_of_shifts'] == 3) {
                        if (!$val['shift3_start'] && !$val['shift3_end']) {
                            Flash::error("Row: $rowNum shift 3 times records is required");
                            $error = true;
                            return;
                        } else {
                            if ($val['shift3_start'] >= $val['shift3_end']) {
                                Flash::error("Row: $rowNum (shift 3) make sure end time is greater than start time");
                                $error = true;
                                return;
                            } else {
                                $dataArray[$key]['shift3_start_time'] = $val['shift3_start'] ? $val['shift3_start'] : null;
                                $dataArray[$key]['shift3_end_time'] = $val['shift3_end'] ? $val['shift3_end'] : null;
                            }
                        }
                    }
                }
                $shift1DaysOff = '';
                if (strtolower($val['shift1_sat_day_off']) == 'yes' || strtolower($val['shift1_sat_day_off']) == '1') {
                    if (empty($shift1DaysOff)) {
                        $shift1DaysOff .= 'saturday';
                    } else {
                        $shift1DaysOff .= ',saturday';
                    }
                }
                if (strtolower($val['shift1_sun_day_off']) == 'yes' || strtolower($val['shift1_sun_day_off']) == '1') {
                    if (empty($shift1DaysOff)) {
                        $shift1DaysOff .= 'sunday';
                    } else {
                        $shift1DaysOff .= ',sunday';
                    }
                }
                if (strtolower($val['shift1_mon_day_off']) == 'yes' || strtolower($val['shift1_mon_day_off']) == '1') {
                    if (empty($shift1DaysOff)) {
                        $shift1DaysOff .= 'monday';
                    } else {
                        $shift1DaysOff .= ',monday';
                    }
                }
                if (strtolower($val['shift1_tue_day_off']) == 'yes' || strtolower($val['shift1_tue_day_off']) == '1') {
                    if (empty($shift1DaysOff)) {
                        $shift1DaysOff .= 'tuesday';
                    } else {
                        $shift1DaysOff .= ',tuesday';
                    }
                }
                if (strtolower($val['shift1_wed_day_off']) == 'yes' || strtolower($val['shift1_wed_day_off']) == '1') {
                    if (empty($shift1DaysOff)) {
                        $shift1DaysOff .= 'wednesday';
                    } else {
                        $shift1DaysOff .= ',wednesday';
                    }
                }
                if (strtolower($val['shift1_thu_day_off']) == 'yes' || strtolower($val['shift1_thu_day_off']) == '1') {
                    if (empty($shift1DaysOff)) {
                        $shift1DaysOff .= 'thursday';
                    } else {
                        $shift1DaysOff .= ',thursday';
                    }
                }
                if (strtolower($val['shift1_fri_day_off']) == 'yes' || strtolower($val['shift1_fri_day_off']) == '1') {
                    if (empty($shift1DaysOff)) {
                        $shift1DaysOff .= 'friday';
                    } else {
                        $shift1DaysOff .= ',friday';
                    }
                }
                $dataArray[$key]['shift1_day_of'] = $shift1DaysOff;
                if ($val['num_of_shifts'] == 1) {
                    $shift2DaysOff = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                    $shift3DaysOff = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                    $dataArray[$key]['shift2_day_of'] = $shift2DaysOff;
                    $dataArray[$key]['shift3_day_of'] = $shift3DaysOff;
                }
                /////////////////////shift 2 days off///////////////////////////
                if ($val['num_of_shifts'] == 2 || $val['num_of_shifts'] == 3) {
                    $shift2DaysOff = '';
                    if (strtolower($val['shift2_sat_day_off']) == 'yes' || strtolower($val['shift2_sat_day_off']) == '1') {
                        if (empty($shift2DaysOff)) {
                            $shift2DaysOff .= 'saturday';
                        } else {
                            $shift2DaysOff .= ',saturday';
                        }
                    }
                    if (strtolower($val['shift2_sun_day_off']) == 'yes' || strtolower($val['shift2_sun_day_off']) == '1') {
                        if (empty($shift2DaysOff)) {
                            $shift2DaysOff .= 'sunday';
                        } else {
                            $shift2DaysOff .= ',sunday';
                        }
                    }
                    if (strtolower($val['shift2_mon_day_off']) == 'yes' || strtolower($val['shift2_mon_day_off']) == '1') {
                        if (empty($shift2DaysOff)) {
                            $shift2DaysOff .= 'monday';
                        } else {
                            $shift2DaysOff .= ',monday';
                        }
                    }
                    if (strtolower($val['shift2_tue_day_off']) == 'yes' || strtolower($val['shift2_tue_day_off']) == '1') {
                        if (empty($shift2DaysOff)) {
                            $shift2DaysOff .= 'tuesday';
                        } else {
                            $shift2DaysOff .= ',tuesday';
                        }
                    }
                    if (strtolower($val['shift2_wed_day_off']) == 'yes' || strtolower($val['shift2_wed_day_off']) == '1') {
                        if (empty($shift2DaysOff)) {
                            $shift2DaysOff .= 'wednesday';
                        } else {
                            $shift2DaysOff .= ',wednesday';
                        }
                    }
                    if (strtolower($val['shift2_thu_day_off']) == 'yes' || strtolower($val['shift2_thu_day_off']) == '1') {
                        if (empty($shift2DaysOff)) {
                            $shift2DaysOff .= 'thursday';
                        } else {
                            $shift2DaysOff .= ',thursday';
                        }
                    }
                    if (strtolower($val['shift2_fri_day_off']) == 'yes' || strtolower($val['shift2_fri_day_off']) == '1') {
                        if (empty($shift2DaysOff)) {
                            $shift2DaysOff .= 'friday';
                        } else {
                            $shift2DaysOff .= ',friday';
                        }
                    }
                    $dataArray[$key]['shift2_day_of'] = $shift2DaysOff;
                    if ($val['num_of_shifts'] == 2) {
                        $shift3DaysOff = 'saturday,sunday,monday,tuesday,wednesday,thursday,friday';
                        $dataArray[$key]['shift3_day_of'] = $shift3DaysOff;
                    }
                    /////////////////////shift 3 days off///////////////////////////
                    if ($val['num_of_shifts'] == 3) {
                        $shift3DaysOff = '';
                        if (strtolower($val['shift3_sat_day_off']) == 'yes' || strtolower($val['shift3_sat_day_off']) == '1') {
                            if (empty($shift3DaysOff)) {
                                $shift3DaysOff .= 'saturday';
                            } else {
                                $shift3DaysOff .= ',saturday';
                            }
                        }
                        if (strtolower($val['shift3_sun_day_off']) == 'yes' || strtolower($val['shift3_sun_day_off']) == '1') {
                            if (empty($shift3DaysOff)) {
                                $shift3DaysOff .= 'sunday';
                            } else {
                                $shift3DaysOff .= ',sunday';
                            }
                        }
                        if (strtolower($val['shift3_mon_day_off']) == 'yes' || strtolower($val['shift3_mon_day_off']) == '1') {
                            if (empty($shift3DaysOff)) {
                                $shift3DaysOff .= 'monday';
                            } else {
                                $shift3DaysOff .= ',monday';
                            }
                        }
                        if (strtolower($val['shift3_tue_day_off']) == 'yes' || strtolower($val['shift3_tue_day_off']) == '1') {
                            if (empty($shift3DaysOff)) {
                                $shift3DaysOff .= 'tuesday';
                            } else {
                                $shift3DaysOff .= ',tuesday';
                            }
                        }
                        if (strtolower($val['shift3_wed_day_off']) == 'yes' || strtolower($val['shift3_wed_day_off']) == '1') {
                            if (empty($shift3DaysOff)) {
                                $shift3DaysOff .= 'wednesday';
                            } else {
                                $shift3DaysOff .= ',wednesday';
                            }
                        }
                        if (strtolower($val['shift3_thu_day_off']) == 'yes' || strtolower($val['shift3_thu_day_off']) == '1') {
                            if (empty($shift3DaysOff)) {
                                $shift3DaysOff .= 'thursday';
                            } else {
                                $shift3DaysOff .= ',thursday';
                            }
                        }
                        if (strtolower($val['shift3_fri_day_off']) == 'yes' || strtolower($val['shift3_fri_day_off']) == '1') {
                            if (empty($shift3DaysOff)) {
                                $shift3DaysOff .= 'friday';
                            } else {
                                $shift3DaysOff .= ',friday';
                            }
                        }
                        $dataArray[$key]['shift3_day_of'] = $shift3DaysOff;
                    }
                }

                $dataArray[$key]['notes'] = $val['notes'];
            }
        });
        if (!$error) {
            $Repo = new ClinicScheduleRepository();
            foreach ($dataArray as $key => $val) {
                $Repo->save($val);
            }
            Flash::success("Imported Successfully");
        }
        unlink($inputs['template']);
        return Redirect::back();
    }

    public function downloadExcelClinicSchedule()
    {
        $inputs = (Input::except('_token'));
        $hospital = Hospital::getById($inputs['hospital_id']);
        $clinics = Clinic::getAllByHospitalId($inputs['hospital_id']);
        Excel::create('clinics_' . date('Y-m-d H-i-s'), function ($excel) use ($clinics, $hospital) {

            // Set the title
            $excel->setTitle('clinics_' . date('Y-m-d H-i-s'));
            $excel->sheet('clinics', function ($sheet) use ($clinics, $hospital) {
                $sheet->setColumnFormat(array(
                    'E' => '@'
                ));
                $sheet->row(1, array(
                    'hospital_id', 'hospital_name', 'clinic_id', 'clinic_name',
                    'num_of_shifts', 'shift1_start', 'shift1_end', 'shift2_start', 'shift2_end', 'shift3_start', 'shift3_end', 'notes',
                    'shift1_sat_day_off', 'shift1_sun_day_off', 'shift1_mon_day_off', 'shift1_tue_day_off', 'shift1_wed_day_off', 'shift1_thu_day_off', 'shift1_fri_day_off',
                    'shift2_sat_day_off', 'shift2_sun_day_off', 'shift2_mon_day_off', 'shift2_tue_day_off', 'shift2_wed_day_off', 'shift2_thu_day_off', 'shift2_fri_day_off',
                    'shift3_sat_day_off', 'shift3_sun_day_off', 'shift3_mon_day_off', 'shift3_tue_day_off', 'shift3_wed_day_off', 'shift3_thu_day_off', 'shift3_fri_day_off',
                ));
                foreach ($clinics as $key => $val) {
                    $sheet->row($key + 2, array(
                        $hospital['id'], $hospital['name'], $val['id'], $val['name'],
                        '1', '', '', '', '', '', '', '',
                        'no', 'no', 'no', 'no', 'no', 'no', 'yes',
                        'no', 'no', 'no', 'no', 'no', 'no', 'yes',
                        'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes'
                    ));
                }
                $sheet->setAutoSize(true);
                $sheet->setWidth('A', 0);
                $sheet->setWidth('C', 0);
            });

        })->download('xlsx');
    }

    public function checkDateIsAvailable()
    {
        $clinic_id = Input::get('clinic_id');
        $date = Input::get('date');
        $exceptId = Input::get('exceptId');
        return ClinicSchedule::checkDateIsAvailable($date, $clinic_id, $exceptId);
    }
}
