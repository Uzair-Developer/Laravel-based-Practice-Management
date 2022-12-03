<?php

use core\diagnosis\organ\OrganRepository;
use core\diagnosis\symptom\SymptomManager;
use core\diagnosis\symptom\SymptomRepository;


class SymptomController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->symptomRepo = new SymptomRepository();
        $this->symptomManager = new SymptomManager();
        $this->user = Sentry::getUser();
    }

    public function index()
    {
        if(!$this->user->hasAccess('symptom.list') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        if(!empty(Input::get('search'))){
            $data['symptoms'] = $this->symptomRepo->getAllPaginateWithFilter(Input::except('_token'));
        } else {
            $data['symptoms'] = $this->symptomRepo->getAllPaginate();
        }
        return View::make('diagnosis/symptom/list', $data);

    }

    public function addSymptom()
    {
        if(!$this->user->hasAccess('symptom.add') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $organRepo = new OrganRepository();
        $data['organs'] = $organRepo->getAll();
        return View::make('diagnosis/symptom.add', $data);
    }

    public function createSymptom()
    {
        $inputs = Input::except('_token');
        $symptom = $this->symptomManager->addSymptom($inputs);
        if ($symptom['status']) {
            return Redirect::route('listSymptom');
        } else {
            return Redirect::back()->withInput(Input::except('_token'));
        }
    }

    public function editSymptom($id)
    {
        if(!$this->user->hasAccess('symptom.edit') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $organRepo = new OrganRepository();
        $data['organs'] = $organRepo->getAll();
        $data['symptom'] = $this->symptomRepo->getById($id);
        return View::make('diagnosis/symptom.edit', $data);


    }

    public function updateSymptom($id)
    {
        $this->symptomManager->updateSymptom(Input::except('_token'), $id);
        return Redirect::route('listSymptom');
    }

    public function deleteSymptom($id)
    {
        if(!$this->user->hasAccess('symptom.delete') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $this->symptomManager->delete($id);
        return Redirect::route('listSymptom');

    }
}
