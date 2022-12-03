<?php
use core\hospital\HospitalRepository;

class KioskToPrinterController extends BaseController
{
    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function kioskToPrinter()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('kioskToPrinter.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['details'] = true;
        $data['kioskToPrinter'] = KioskToPrinter::getAll($inputs);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('kioskToPrinter/list', $data);
    }

    public function addKioskToPrinter()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('kioskToPrinter.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['kioskToPrinter'] = array(
            'id' => '',
            'hospital_id' => '',
            'name' => '',
            'ip' => '',
            'printer_id' => '',
        );
        return View::make('kioskToPrinter/add', $data);
    }

    public function createKioskToPrinter()
    {
        try {
            $inputs = (Input::except('_token'));
            $validator = Validator::make($inputs, array(
                'ip' => "required|unique:kiosk_to_printer",
            ));
            if ($validator->fails()) {
                Flash::error($validator->messages());
                return Redirect::back()->withInput(Input::all());
            }
            KioskToPrinter::add($inputs);
            Flash::success('Added Successfully');
            return Redirect::route('kioskToPrinter');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editKioskToPrinter($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('kioskToPrinter.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['kioskToPrinter'] = KioskToPrinter::getById($id);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('kioskToPrinter/add', $data);
    }

    public function updateKioskToPrinter($id)
    {
        $inputs = (Input::except('_token'));
        $validator = Validator::make($inputs, array(
            'ip' => "required|unique:kiosk_to_printer,ip,$id",
        ));
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        }
        try {
            KioskToPrinter::edit($inputs, $id);
            Flash::success('Updated Successfully');
            return Redirect::route('kioskToPrinter');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deleteKioskToPrinter($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('kioskToPrinter.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        KioskToPrinter::remove($id);
        Flash::success('Delete Successfully');
        return Redirect::back();
    }

    public function getReceptionByHospitalId()
    {
        $hospital_id = Input::get('hospital_id');
        $array = ['hospital_id' => $hospital_id];
        $kiosk = KioskToPrinter::getAll($array);
        $html = '<option value="">Choose</option>';
        foreach ($kiosk as $key => $val) {
            $html .= '<option value="' . $val['id'] . '">' . $val['ip'] . ' [' . $val['name'] . ']' . '</option>';
        }
        return $html;
    }
}
