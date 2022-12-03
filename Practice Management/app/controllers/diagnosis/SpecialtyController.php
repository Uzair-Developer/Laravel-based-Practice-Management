<?php


use Laracasts\Flash\Flash;

class SpecialtyController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function index()
    {
        if(!$this->user->hasAccess('clinicSpecialty.list') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['specialties'] = Specialty::getAll();
        return View::make('diagnosis/specialty.list', $data);
    }

    public function addSpecialty()
    {
        if(!$this->user->hasAccess('clinicSpecialty.add') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['clinics'] = Clinic::getAll();
        return View::make('diagnosis/specialty.add', $data);
    }

    public function createSpecialty()
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, Specialty::$rules);
        if ($validator->fails()) {
            Flash::error( $validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                Specialty::add($inputs);
                Flash::success('Added successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listSpecialty');
    }

    public function editSpecialty($id)
    {
        if(!$this->user->hasAccess('clinicSpecialty.edit') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['clinics'] = Clinic::getAll();
        $data['specialty'] = Specialty::getById($id);
        return View::make('diagnosis/specialty.edit', $data);
    }

    public function updateSpecialty($id)
    {
        $inputs = Input::except('_token');
        $validator = Validator::make($inputs, Specialty::$rules);
        if ($validator->fails()) {
            Flash::error( $validator->messages());
            return Redirect::back()->withInput(Input::all());
        } else {
            try {
                Specialty::edit($inputs, $id);
                Flash::success('Updated successfully');
            } catch (Exception $e) {
                Flash::error('Ops, try again later!');
                return Redirect::back()->withInput(Input::all());
            }
        }
        return Redirect::route('listSpecialty');
    }

    public function deleteSpecialty($id)
    {
        if(!$this->user->hasAccess('clinicSpecialty.delete') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        Specialty::remove($id);
        Flash::success('Deleted successfully');
        return Redirect::route('listSpecialty');
    }
}
