<?php

use core\hospital\HospitalRepository;

class ReportsController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function getPhysicianReports()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('reports.physician_reports')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('reports/physician_report', $data);
    }

    public function postPhysicianReports()
    {
        $inputs = Input::except('_token');
        if ($inputs) {
            $inputs = Input::except('_token');
            $validator = Validator::make($inputs, array(
                "from_date" => "required",
                "to_date" => "required",
            ));
            if ($validator->fails()) {
                $data['message'] = 'Dates are required!';
                $data['response'] = 'false';
            } else {
                if ($inputs['from_date'] > $inputs['to_date']) {
                    $data['message'] = 'To Date Must Greater Than From Date!';
                    $data['response'] = 'false';
                }
                $data2['report'] = Physician::getReport($inputs);
                $data['html'] = View::make('reports/physician_report_html', $data2)->render();
                $data['response'] = 'true';
            }
            return $data;
        }
        return 0;
    }

    public function excelPhysicianReport()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('reports.physician_report_excel')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $report = Physician::getReport($inputs);
        Excel::create('PhysicianReport_' . date('Y-m-d H-i-s'), function ($excel) use ($report) {
            // Set the title
            $excel->setTitle('PhysicianReport');
            $excel->sheet('PhysicianReport', function ($sheet) use ($report) {
                $sheet->loadView('reports/physician_report_excel', array('report' => $report));
            });

        })->download('xlsx');
    }

    public function getClinicReports()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('reports.clinic_reports')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('reports/clinic_report', $data);
    }

    public function postClinicReports()
    {
        $inputs = Input::except('_token');
        if ($inputs) {
            $inputs = Input::except('_token');
            $validator = Validator::make($inputs, array(
                "hospital_id" => "required",
                "from_date" => "required",
                "to_date" => "required",
            ));
            if ($validator->fails()) {
                $data['message'] = 'Hospital And Dates are required!';
                $data['response'] = 'false';
            } else {
                if ($inputs['from_date'] > $inputs['to_date']) {
                    $data['message'] = 'To Date Must Greater Than From Date!';
                    $data['response'] = 'false';
                }
                $data2['report'] = Clinic::getReport($inputs);
                $data['html'] = View::make('reports/clinic_report_html', $data2)->render();
                $data['response'] = 'true';
            }
            return $data;
        }
        return 0;
    }

    public function excelClinicReport()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('reports.clinic_report_excel')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $report = Clinic::getReport($inputs);
        Excel::create('ClinicReport_' . date('Y-m-d H-i-s'), function ($excel) use ($report) {
            // Set the title
            $excel->setTitle('ClinicReport');
            $excel->sheet('ClinicReport', function ($sheet) use ($report) {
                $sheet->loadView('reports/clinic_report_excel', array('report' => $report));
            });

        })->download('xlsx');
    }

    public function getPhysicianExceptionReports()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('reports.physician_exception_reports')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('reports/physician_exception_report', $data);
    }

    public function postPhysicianExceptionReports()
    {
        $inputs = Input::except('_token');
        if ($inputs) {
            $inputs = Input::except('_token');
            $validator = Validator::make($inputs, array(
                "hospital_id" => "required",
                "from_date" => "required",
                "to_date" => "required",
            ));
            if ($validator->fails()) {
                $data['message'] = 'Hospital And Dates are required!';
                $data['response'] = 'false';
            } else {
                if ($inputs['from_date'] > $inputs['to_date']) {
                    $data['message'] = 'To Date Must Greater Than From Date!';
                    $data['response'] = 'false';
                }
                $data2['report'] = Physician::getExceptionReport($inputs);
                $data2['exceptions'] = AttributePms::getAllWithOutPaginate(1, ['effect' => 1]);
                $data['html'] = View::make('reports/physician_exception_report_html', $data2)->render();
                $data['response'] = 'true';
            }
            return $data;
        }
        return 0;
    }

    public function excelPhysicianExceptionReport()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('reports.physician_exception_excel')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $report = Physician::getExceptionReport($inputs);
        $exceptions = AttributePms::getAllWithOutPaginate(1, ['effect' => 1]);
        Excel::create('physicianException_' . date('Y-m-d H-i-s'), function ($excel) use ($report, $exceptions) {
            // Set the title
            $excel->setTitle('physicianException');
            $excel->sheet('physicianException', function ($sheet) use ($report, $exceptions) {
                $sheet->loadView('reports/physician_exception_report_excel', array('report' => $report, 'exceptions' => $exceptions));
            });

        })->download('xlsx');
    }
}
