<?php

use core\diagnosis\disease\DiseaseRepository;
use core\diagnosis\enums\CommentsStatus;
use core\diagnosis\symptom\SymptomRepository;

class CommentController extends BaseController
{
    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listComment($type)
    {
        if (!$this->user->hasAccess('symptomComment.list') && !$this->user->hasAccess('admin')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['users'] = User::getAll();
        if ($type == 'symptom') {
            if (Input::has('symptom')) {
                $data['data'] = Comment::getAllWithFilters(Input::except('_token'), $type);
            } else {
                $data['data'] = Comment::getAll($type);
            }
        } else {
            $data['data'] = array();
        }
        return View::make('diagnosis/symptom/comments', $data);
    }

    public function statusComment($id, $type)
    {
        if ($type == 'read') {
            $array = array(
                'status' => CommentsStatus::read
            );
        } elseif ($type == 'pending') {
            $array = array(
                'status' => CommentsStatus::pending
            );
        } else {
            Flash::error('Updated Failed');
            return Redirect::back();
        }
        Comment::edit($array, $id);
        Flash::success('Updated Successfully');
        return Redirect::back();
    }
}
