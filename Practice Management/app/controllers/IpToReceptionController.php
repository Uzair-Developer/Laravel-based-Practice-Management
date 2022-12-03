<?php


use core\enums\AttributeType;
use core\hospital\HospitalRepository;

class IpToReceptionController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function ipToReception()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToReception.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['details'] = true;
        $data['ipToReception'] = IpToReception::getAll($inputs);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('ipToReception/list', $data);
    }

    public function addIpToReception()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToReception.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['ipToReception'] = array(
            'hospital_id' => '',
            'ip' => '',
            'name' => '',
            'wait_area_name' => '',
            'ip_to_screen_id' => '',
        );
        return View::make('ipToReception/add', $data);
    }

    public function createIpToReception()
    {
        try {
            $inputs = (Input::except('_token'));
            $validator = Validator::make($inputs, array(
                'ip' => "required|unique:ip_to_reception",
                'ip_to_screen_id' => "required|unique:ip_to_reception"
            ));
            if ($validator->fails()) {
                Flash::error($validator->messages());
                return Redirect::back()->withInput(Input::all());
            }
            IpToReception::add($inputs);
            Flash::success('Added Successfully');
            return Redirect::route('ipToReception');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editIpToReception($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToReception.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['ipToReception'] = IpToReception::getById($id);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('ipToReception/add', $data);
    }

    public function updateIpToReception($id)
    {
        $inputs = (Input::except('_token'));
        $validator = Validator::make($inputs, array(
            'ip' => "required|unique:ip_to_reception,ip,$id",
            'ip_to_screen_id' => "required|unique:ip_to_reception,ip_to_screen_id,$id"
        ));
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        }
        try {
            IpToReception::edit($inputs, $id);
            Flash::success('Updated Successfully');
            return Redirect::route('ipToReception');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deleteIpToReception($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToReception.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        IpToReception::remove($id);
        Flash::success('Delete Successfully');
        return Redirect::back();
    }
}
