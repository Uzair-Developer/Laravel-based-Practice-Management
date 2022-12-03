<?php

use core\diagnosis\organ\OrganManager;
use core\diagnosis\organ\OrganRepository;


class OrganController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->organRepo = new OrganRepository();
        $this->organManager = new OrganManager();
        $this->user = Sentry::getUser();
    }

    public function index()
    {
        if(!$this->user->hasAccess('organ.list') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['organs'] = $this->organRepo->getAll();
        return View::make('diagnosis/organ.list', $data);

    }


    public function addOrgan()
    {
        if(!$this->user->hasAccess('organ.add') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        return View::make('diagnosis/organ/add');
    }

    public function createOrgan()
    {
        $inputs = Input::except('_token');
        $organ = $this->organManager->addOrgan($inputs);
        if ($organ['status']) {
            return Redirect::route('listOrgan');
        } else {
            return Redirect::back()->withInput(Input::except('_token'));
        }
    }

    public function editOrgan($id)
    {
        if(!$this->user->hasAccess('organ.edit') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['organ'] = $this->organRepo->getById($id);
        return View::make('diagnosis/organ.edit', $data);
    }

    public function updateOrgan($id)
    {
        $this->organManager->updateOrgan(Input::except('_token'), $id);
        return Redirect::route('listOrgan');
    }

    public function deleteOrgan($id)
    {
        if(!$this->user->hasAccess('organ.delete') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $this->organManager->delete($id);
        return Redirect::route('listOrgan');

    }
}
