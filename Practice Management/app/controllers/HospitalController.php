<?php

use core\hospital\HospitalManager;
use core\hospital\HospitalRepository;
use core\currency\CurrencyRepository;
use core\hospitalContact\HospitalContactRepository;

class HospitalController extends BaseController
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
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('hospital.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('hospital/list', $data);
    }

    public function addHospital()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('hospital.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $currencyRepo = new CurrencyRepository();
        $data['currency'] = $currencyRepo->getAll();
        $data['countries'] = Country::getParents();
        $data['zones'] = TimeZone::getAll();
        return View::make('hospital/add', $data);
    }

    public function createHospital()
    {
        $hospitalManager = new HospitalManager();
        $inputs = (Input::except('_token'));
        $data = $hospitalManager->createHospital($inputs);
        if($data['status']) {
            return Redirect::route('hospitals');
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editHospital($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('hospital.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $currencyRepo = new CurrencyRepository();
        $hospitalRepo = new HospitalRepository();
        $data['currency'] = $currencyRepo->getAll();
        $data['hospital'] = $hospitalRepo->getById($id);
        $hospitalContactRepo = new HospitalContactRepository();
        $data['contacts'] = $hospitalContactRepo->getByHospitalId($id);

        $data['countries'] = Country::getParents();
        $data['zones'] = TimeZone::getAll();
        return View::make('hospital/edit', $data);
    }

    public function updateHospital($id)
    {
        $systemManager = new HospitalManager();
        $inputs = (Input::except('_token'));
        $data = $systemManager->updateHospital($inputs, $id);
        if($data['status']) {
            return Redirect::route('hospitals');
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deleteHospital($id)
    {
        if ($this->user->user_type_id != 1) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $hospitalRepo->delete($id);
        return Redirect::back();
    }

    public function deleteHospitalContact($contactId)
    {
        $hospitalContactRepo = new HospitalContactRepository();
        $hospitalContactRepo->delete($contactId);
        return 1;
    }

}
