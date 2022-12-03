<?php


use core\enums\AttributeType;
use core\hospital\HospitalRepository;

class IpToScreenController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function ipToScreen()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToScreen.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['details'] = true;
        $data['ipToScreen'] = IpToScreen::getAll($inputs);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('ipToScreen/list', $data);
    }

    public function addIpToScreen()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToScreen.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['ipToScreen'] = array(
            'ip' => '',
            'hospital_id' => '',
            'screen_name' => '',
            'wait_area_name' => '',
        );
        return View::make('ipToScreen/add', $data);
    }

    public function createIpToScreen()
    {
        try {
            $inputs = (Input::except('_token'));
            $validator = Validator::make($inputs, array(
                'ip' => "required|unique:ip_to_screen"
            ));
            if ($validator->fails()) {
                Flash::error($validator->messages());
                return Redirect::back()->withInput(Input::all());
            }
            IpToScreen::add($inputs);
            Flash::success('Added Successfully');
            return Redirect::route('ipToScreen');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editIpToScreen($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToScreen.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['ipToScreen'] = IpToScreen::getById($id);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('ipToScreen/add', $data);
    }

    public function updateIpToScreen($id)
    {
        $inputs = (Input::except('_token'));
        $validator = Validator::make($inputs, array(
            'ip' => "required|unique:ip_to_screen,ip,$id"
        ));
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        }
        try {
            IpToScreen::edit($inputs, $id);
            Flash::success('Updated Successfully');
            return Redirect::route('ipToScreen');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deleteIpToScreen($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToScreen.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        IpToScreen::remove($id);
        Flash::success('Delete Successfully');
        return Redirect::back();
    }
}
