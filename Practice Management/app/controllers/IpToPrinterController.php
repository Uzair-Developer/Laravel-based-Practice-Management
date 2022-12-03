<?php


use core\enums\AttributeType;
use core\hospital\HospitalRepository;

class IpToPrinterController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function ipToPrinter()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToPrinter.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['details'] = true;
        $data['ipToPrinter'] = IpToPrinter::getAll($inputs);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('ipToPrinter/list', $data);
    }

    public function addIpToPrinter()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToPrinter.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['ipToPrinter'] = array(
            'hospital_id' => '',
            'ip' => '',
            'name' => ''
        );
        return View::make('ipToPrinter/add', $data);
    }

    public function createIpToPrinter()
    {
        try {
            $inputs = (Input::except('_token'));
            $validator = Validator::make($inputs, array(
                'ip' => "required|unique:ip_to_printer",
            ));
            if ($validator->fails()) {
                Flash::error($validator->messages());
                return Redirect::back()->withInput(Input::all());
            }
            IpToPrinter::add($inputs);
            Flash::success('Added Successfully');
            return Redirect::route('ipToPrinter');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editIpToPrinter($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToPrinter.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['ipToPrinter'] = IpToPrinter::getById($id);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('ipToPrinter/add', $data);
    }

    public function updateIpToPrinter($id)
    {
        $inputs = (Input::except('_token'));
        $validator = Validator::make($inputs, array(
            'ip' => "required|unique:ip_to_printer,ip,$id",
        ));
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        }
        try {
            IpToPrinter::edit($inputs, $id);
            Flash::success('Updated Successfully');
            return Redirect::route('ipToPrinter');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deleteIpToPrinter($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToPrinter.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        IpToPrinter::remove($id);
        Flash::success('Delete Successfully');
        return Redirect::back();
    }

    public function getPrinterByHospital()
    {
        $inputs = (Input::except('_token'));
        $printers = IpToPrinter::getAll(['hospital_id' => $inputs['hospital_id']]);
        $html = '<option value="">Choose</option>';
        foreach ($printers as $key => $val) {
            $html .= '<option value="' . $val['id'] . '">' . $val['name'] . ' [' . $val['ip'] . ']</option>';
        }
        return $html;
    }

    public function getDefaultPrinter()
    {
        $inputs = (Input::except('_token'));
        $defaultPrinter = DefaultPrinter::getAll(['user_id' => $inputs['user_id'], 'getFirst' => true]);
        if ($defaultPrinter) {
            $data['success'] = 'yes';
            $printer = IpToPrinter::getById($defaultPrinter['printer_id']);
            $data['printer'] = $printer;
            $printers = IpToPrinter::getAll(['hospital_id' => $printer['hospital_id']]);
            $html = '<option value="">Choose</option>';
            foreach ($printers as $key => $val) {
                $html .= '<option value="' . $val['id'] . '">' . $val['name'] . ' [' . $val['ip'] . ']</option>';
            }
            $data['printers'] = $html;
        } else {
            $data['success'] = 'no';
        }
        return $data;
    }

    public function setDefaultPrinter()
    {
        $inputs = Input::except('_token');
        $defaultPrinter = DefaultPrinter::getAll(['user_id' => $inputs['user_id'], 'getFirst' => true]);
        if (empty($defaultPrinter)) {
            DefaultPrinter::add([
                'user_id' => $inputs['user_id'],
                'printer_id' => $inputs['printer_id'],
            ]);
        } else {
            DefaultPrinter::edit([
                'user_id' => $inputs['user_id'],
                'printer_id' => $inputs['printer_id'],
            ], $defaultPrinter['id']);
        }
    }
}
