<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;
use core\hospital\HospitalRepository;
use core\physician\PhysicianRepository;

class PatientLapRadiologyController extends BaseController
{
    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listPatientLapRadiology()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('patientLapRadiology.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        if ($inputs) {
            $inputs['paginate'] = true;
            $data['patientLapRadiology'] = PatientLabRadiology::getAll($inputs);
        }
        unset($inputs['page']);
        $data['inputs'] = $inputs;
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('patientLabRadiology/list', $data);
    }

    public function getPatientOrderLapRadiology()
    {
        $inputs = Input::except('_token');
        if (app('production')) {
            $order1 = HisPatientLabRadiology::getByOrderId($inputs['order_id']);
            foreach ($order1 as $key => $val) {
                PatientLabRadiology::editByOrder_PatientReg_TestId($val['OrderID'], $val['Registrationno'], $val['TestId'], array(
                    'verifieddatetime' => $val['verifieddatetime']
                ));
            }
        }
        $orders = PatientLabRadiology::getByOrder($inputs['order_id']);
        $data2['patientLapRadiology'] = $orders;
        $data['orderDetails'] = View::make('patientLabRadiology/orders', $data2)->render();
        $data['patient_name'] = Patient::getName($orders[0]['patient_id']);
        return $data;
    }

    public function resetPatientPassword($patient_reg_no)
    {
        $result = DB::connection('sqlsrv2')->select('UserResetPassword_SP ?', array($patient_reg_no));
        if ($result) {
            Flash::success('New Password Is: ' . $result[0]->InitialPassword);
            if (app('portal_send_sms')) {
                $patient = Patient::getByRegistrationNo($patient_reg_no, 1);
                $smsArray = array(
                    'patient_id' => $patient['id'],
                    'type' => 'Reset_password',
                );
                if ($patient) {
                    if ($patient['lab_sms'] == 1) {
                        $reservation = Reservation::getLastOfPatient($patient['id']);
                        $messageArray = array(
                            'patient_password' => $result[0]->InitialPassword,
                        );
                        if ($reservation) {
                            if ($reservation['sms_lang'] == 1) { // arabic
                                $smsArray['message'] = trans('sms.reset-password-patient-portal-ar', $messageArray);
                            } else {
                                $smsArray['message'] = trans('sms.reset-password-patient-portal', $messageArray);
                            }
                        } else {
                            $smsArray['message'] = trans('sms.reset-password-patient-portal-ar', $messageArray);
                        }
                        PortalPatientSMS::add($smsArray);
                    }
                }
            }
        } else {
            Flash::success('Ops, Try Again Later!');
        }
        return Redirect::back();
    }

    public function editPatientPhone()
    {
        $inputs = Input::except('_token');
        if (isset($inputs['phone']) && isset($inputs['id']) && $inputs['phone'] && $inputs['id']) {
            $patient = Patient::getById($inputs['id']);
            $caller = CallerInfo::getByPhone($inputs['phone'], $patient['hospital_id']);
            if ($caller) {
                $patientArray['caller_id'] = $caller['id'];
                $patientArray['phone'] = $inputs['phone'];
            } else {
                $newCallerInfo = CallerInfo::add(array(
                    'phone' => $inputs['phone'],
                    'name' => $patient['name'],
                ));
                $patientArray['caller_id'] = $newCallerInfo->id;
                $patientArray['phone'] = $inputs['phone'];
            }
            Patient::edit($patientArray, $inputs['id']);
            Flash::success('Updated Successfully');
        } else {
            Flash::error('Ops, Try Again Later!');
        }
        return Redirect::back();
    }

    public function changeSendLabSMS($patient_id)
    {
        $patient = Patient::getById($patient_id);
        $lab_sms = $patient['lab_sms'] == 1 ? 2 : 1;
        Patient::edit(array(
            'lab_sms' => $lab_sms
        ), $patient_id);
        Flash::success('Updated Successfully');
        return Redirect::back();
    }
}
