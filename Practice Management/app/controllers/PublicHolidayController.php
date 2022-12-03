<?php

use core\hospital\HospitalRepository;
use core\publicHoliday\PublicHolidayManager;
use core\publicHoliday\PublicHolidayRepository;

class PublicHolidayController extends BaseController
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
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('publicHoliday.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new PublicHolidayRepository();
        $data['publicHolidays'] = $hospitalRepo->getAll();
        return View::make('publicHoliday/list', $data);
    }

    public function addPublicHoliday()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('publicHoliday.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('publicHoliday/add', $data);
    }

    public function createPublicHoliday()
    {
        $hospitalManager = new PublicHolidayManager();
        $inputs = (Input::except('_token'));
        $data = $hospitalManager->createPublicHoliday($inputs);
        if ($data['status']) {
            return Redirect::route('publicHoliday');
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editPublicHoliday($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('publicHoliday.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $Repo = new PublicHolidayRepository();
        $data['data'] = $Repo->getById($id);
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        return View::make('publicHoliday/edit', $data);
    }

    public function updatePublicHoliday($id)
    {
        $publicHolidayManager = new PublicHolidayManager();
        $inputs = (Input::except('_token'));
        $data = $publicHolidayManager->updatePublicHoliday($inputs, $id);
        if ($data['status']) {
            return Redirect::route('publicHoliday');
        } else {
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deletePublicHoliday($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('publicHoliday.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $public = PublicHoliday::getById($id);
        $clinic_ids = Clinic::getByHospitalId($public['hospital_id']);
        Reservation::reservedWithPeriod($clinic_ids, $public['from_date'], $public['to_date']);

        $hospitalRepo = new PublicHolidayRepository();
        $hospitalRepo->delete($id);
        return Redirect::back();
    }

    public function changeStatusPublicHoliday($id)
    {
        $public = PublicHoliday::getById($id);
        if($public['status'] == 1){
            PublicHoliday::edit(array('status' => 0), $id);
            $clinic_ids = Clinic::getByHospitalId($public['hospital_id']);
            Reservation::reservedWithPeriod($clinic_ids, $public['from_date'], $public['to_date']);
        } else {
            PublicHoliday::edit(array('status' => 1), $id);
            $clinic_ids = Clinic::getByHospitalId($public['hospital_id']);
            Reservation::pendingWithPeriod($clinic_ids, $public['from_date'], $public['to_date']);
        }
        Flash::success('Updated Successfully');
        return Redirect::route('publicHoliday');
    }
}
