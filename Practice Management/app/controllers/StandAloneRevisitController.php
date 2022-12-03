<?php

use core\hospital\HospitalRepository;

class StandAloneRevisitController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function standAloneRevisit()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('standAlonReservation.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['type'] = 3;
        $inputs['standAloneRevisit'] = true;
        $allReservations = Reservation::getByPatientsIdAndDates($inputs, true);
        $data['reservations'] = $allReservations;

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['groups'] = Group::getAll(['in_filter' => 1]);

        return View::make('standAloneRevisit/list', $data);
    }
}
