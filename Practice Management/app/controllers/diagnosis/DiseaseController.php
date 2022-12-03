<?php

use core\diagnosis\disease\DiseaseManager;
use core\diagnosis\disease\DiseaseRepository;
use core\diagnosis\enums\DiseaseSymptomsStatus;
use core\diagnosis\organ\OrganRepository;
use core\diagnosis\symptom\SymptomManager;
use core\diagnosis\symptom\SymptomRepository;


class DiseaseController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->diseaseRepo = new DiseaseRepository();
        $this->diseaseManager = new DiseaseManager();
        $this->user = Sentry::getUser();
    }

    public function index()
    {
        if(!$this->user->hasAccess('disease.list') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        if(!empty(Input::get('q'))){
            $data['diseases'] = $this->diseaseRepo->getAllPaginateWithFilter(Input::get('q'));
        } else {
            $data['diseases'] = $this->diseaseRepo->getAllPaginate();
        }
        return View::make('diagnosis/disease/list', $data);
    }

    public function addDisease()
    {
        if(!$this->user->hasAccess('disease.add') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['specialties'] = Specialty::getAll();
        $symptomRepo = new SymptomRepository();
        $data['symptoms'] = $symptomRepo->getAll();

        $organRepo = new OrganRepository();
        $data['organs'] = $organRepo->getAll();
        return View::make('diagnosis/disease/add', $data);
    }

    public function createDisease()
    {
        $inputs = Input::except('_token');
        $disease = $this->diseaseManager->addDisease($inputs);
        if ($disease['status']) {
            return Redirect::route('listDisease');
        } else {
            return Redirect::back()->withInput(Input::except('_token'));
        }
    }

    public function editDisease($id)
    {
        if(!$this->user->hasAccess('disease.edit') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['specialties'] = Specialty::getAll();
        $symptomRepo = new SymptomRepository();
        $data['symptoms'] = $symptomRepo->getAll();

        $data['disease_symptoms'] = DiseaseSymptoms::getByDiseaseId($id);
        $data['disease_symptoms_ids'] = DiseaseSymptoms::getIdsByDiseaseId($id);

        $data['disease_questions'] = DiseaseQuestions::getByDiseaseId($id);
        $data['disease_questions_ids'] = DiseaseQuestions::getIdsByDiseaseId($id);
        $data['disease'] = $this->diseaseRepo->getById($id);

        $organRepo = new OrganRepository();
        $data['organs'] = $organRepo->getAll();
        return View::make('diagnosis/disease.edit', $data);
    }

    public function updateDisease($id)
    {
        $this->diseaseManager->updateDisease(Input::except('_token'), $id);
        return Redirect::route('listDisease');
    }

    public function deleteDisease($id)
    {
        if(!$this->user->hasAccess('disease.delete') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $this->diseaseManager->delete($id);
        return Redirect::route('listDisease');

    }

    public function deleteDiseaseSymptom()
    {
        $id = Input::get('id');
        $diseaseId = Input::get('diseaseId');
        DiseaseSymptoms::remove($id);
        $data = DiseaseSymptoms::getIdsByDiseaseId($diseaseId);
        $return = '';
        foreach ($data as $val) {
            $return .= '<input type="hidden" value="' . $val . '" name="diseaseSymptomIds[]">';
        }
        return $return;
    }

    public function deleteDiseaseQuestion()
    {
        $id = Input::get('id');
        $diseaseId = Input::get('diseaseId');
        DiseaseQuestions::remove($id);
        $data = DiseaseQuestions::getIdsByDiseaseId($diseaseId);
        $return = '';
        foreach ($data as $val) {
            $return .= '<input type="hidden" value="' . $val . '" name="diseaseQuestionIds[]">';
        }
        return $return;
    }

    public function createSymptomInDisease()
    {
        $inputs = Input::except('_token');
        $symptomManager = new SymptomManager();
        $symptom = $symptomManager->addSymptom($inputs);
        if ($symptom['status']) {
            return Redirect::route('addDisease');
        } else {
            return Redirect::back()->withInput(Input::except('_token'));
        }
    }

    public function diseaseSymptomsPending()
    {
        if(!$this->user->hasAccess('pendingRelation.list') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        if(Input::all()){
            $data['diseaseSymptomsPending'] = DiseaseSymptoms::getAllWithFilter(Input::except('_token'));
        } else {
            $data['diseaseSymptomsPending'] = DiseaseSymptoms::getAll();
        }
        $data['links'] = $data['diseaseSymptomsPending']->appends(Input::except('_token'))->links();
        $symptomRepo = new SymptomRepository();
        $data['symptoms'] = $symptomRepo->getAll();
        $data['diseases'] = $this->diseaseRepo->getAll();
        return View::make('diagnosis/disease/diseaseSymptomsPending', $data);
    }

    public function approveRelation($id)
    {
        if(!$this->user->hasAccess('pendingRelation.approval') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        DiseaseSymptoms::edit(array('status' => DiseaseSymptomsStatus::approval), $id);
        Flash::success('Updated Successfully');
        return Redirect::back();
    }

    public function cancelRelation($id)
    {
        if(!$this->user->hasAccess('pendingRelation.cancel') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        return View::make('diagnosis/disease/cancelRelation');
    }

    public function cancelRelationPost($id)
    {
        $inputs = Input::except('_token');
        DiseaseSymptoms::edit(array(
            'status' => DiseaseSymptomsStatus::cancel,
            'cancel_note' => $inputs['cancel_note'] ? $inputs['cancel_note'] : ''
        ), $id);
        Flash::success('Updated Successfully');
        return Redirect::route('diseaseSymptomsPending');
    }

    public function editRelation()
    {
        if(!$this->user->hasAccess('pendingRelation.edit') && !$this->user->hasAccess('admin')){
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $id = $inputs['id'];
        unset($inputs['id']);
        DiseaseSymptoms::edit($inputs, $id);
        Flash::success('Updated Successfully');
        return Redirect::route('diseaseSymptomsPending');
    }
}
