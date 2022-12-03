<?php


use core\hospital\HospitalRepository;

class ReceptionDelegateController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function receptionDelegate()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('receptionDelegate.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['details'] = true;
        $data['receptionDelegate'] = ReceptionDelegate::getAll($inputs);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('receptionDelegate/list', $data);
    }

    public function addReceptionDelegate()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('receptionDelegate.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['receptionDelegate'] = array(
            'id' => '',
            'hospital_id' => '',
            'reception_id' => '',
            'reception1_delegate_id' => '',
            'reception2_delegate_id' => '',
            'reception3_delegate_id' => '',
        );
        return View::make('receptionDelegate/add', $data);
    }

    public function createReceptionDelegate()
    {
        try {
            $inputs = (Input::except('_token'));
            $validator = Validator::make($inputs, array(
                'reception_id' => "required|unique:reception_delegate"
            ));
            if ($validator->fails()) {
                Flash::error($validator->messages());
                return Redirect::back()->withInput(Input::all());
            }
            ReceptionDelegate::add($inputs);
            Flash::success('Added Successfully');
            return Redirect::route('receptionDelegate');
        } catch (Exception $e) {
            dd($e->getMessage());
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editReceptionDelegate($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('receptionDelegate.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['receptionDelegate'] = ReceptionDelegate::getById($id);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('receptionDelegate/add', $data);
    }

    public function updateReceptionDelegate($id)
    {
        $inputs = (Input::except('_token'));
        $validator = Validator::make($inputs, array(
            'reception_id' => "required|unique:reception_delegate,reception_id,$id"
        ));
        if ($validator->fails()) {
            Flash::error($validator->messages());
            return Redirect::back()->withInput(Input::all());
        }
        try {
            ReceptionDelegate::edit($inputs, $id);
            Flash::success('Updated Successfully');
            return Redirect::route('receptionDelegate');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deleteReceptionDelegate($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('receptionDelegate.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        ReceptionDelegate::remove($id);
        Flash::success('Delete Successfully');
        return Redirect::back();
    }
}
