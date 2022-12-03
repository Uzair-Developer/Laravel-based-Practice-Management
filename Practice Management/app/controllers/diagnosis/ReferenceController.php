<?php

class ReferenceController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listReference()
    {
        if (!$this->user->hasAccess('reference.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['references'] = Reference::getAll();
        return View::make('diagnosis/reference.list', $data);

    }


    public function addReference()
    {
        if (!$this->user->hasAccess('reference.add') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        return View::make('diagnosis/reference.add');
    }

    public function createReference()
    {
        $inputs = Input::except('_token');
        $validation = Validator::make($inputs, Reference::$rules);
        if ($validation->fails()) {
            Flash::error($validation->messages());
            return Redirect::back();
        }
        Reference::add($inputs);
        Flash::success('Added Successfully');
        return Redirect::route('listReference');
    }

    public function editReference($id)
    {
        if (!$this->user->hasAccess('reference.edit') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['reference'] = Reference::getById($id);
        return View::make('diagnosis/reference.edit', $data);
    }

    public function updateReference($id)
    {
        $inputs = Input::except('_token');
        $validation = Validator::make($inputs, Reference::$rules);
        if ($validation->fails()) {
            Flash::error($validation->messages());
            return Redirect::back();
        }
        Reference::edit($inputs, $id);
        Flash::success('Updated Successfully');
        return Redirect::route('listReference');
    }

    public function deleteReference($id)
    {
        if (!$this->user->hasAccess('reference.delete') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        Reference::remove($id);
        Flash::success('Removed Successfully');
        return Redirect::route('listReference');

    }
}
