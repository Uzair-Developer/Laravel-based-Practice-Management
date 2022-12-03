<?php


class AgentCommentController extends BaseController
{

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function listAgentComment()
    {
        if (!$this->user->hasAccess('agentComment.list') && $this->user->user_type_id != 1) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $data['agentComments'] = AgentComments::getAll();
        return View::make('diagnosis/agentComment/list', $data);
    }

    public function createAgentComment()
    {
        $inputs = Input::except('_token');
        $validation = Validator::make($inputs, AgentComments::$rules);
        if ($validation->fails()) {
            Flash::error($validation->messages());
            return Redirect::back();
        }
        if (isset($inputs['all_users'])) {
            $inputs['to_all'] = 1;
            $inputs['user_id'] = '';
            $inputs['group_id'] = '';
        } else {
            $inputs['to_all'] = 0;
            $inputs['user_id'] = isset($inputs['user_id']) ? implode(',', $inputs['user_id']) : null;
            $inputs['group_id'] = isset($inputs['group_id']) ? implode(',', $inputs['group_id']) : null;
        }
        $inputs['create_by'] = $this->user->id;
        unset($inputs['all_users']);
        AgentComments::add($inputs);
        Flash::success('Added Successfully');
        return Redirect::back();
    }

    public function deleteAgentComment($id)
    {
        if (!$this->user->hasAccess('agentComment.delete') && $this->user->user_type_id != 1) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        AgentComments::remove($id);
        return Redirect::route('listAgentComment');

    }

    public function readAgentComment($id)
    {
        AgentComments::edit(array(
            'seen' => 1
        ), $id);
        Flash::success('Updated Successfully');
        return Redirect::route('listAgentComment');
    }
}
