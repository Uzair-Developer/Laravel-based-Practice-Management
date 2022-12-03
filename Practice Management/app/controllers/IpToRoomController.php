<?php


use core\enums\AttributeType;
use core\hospital\HospitalRepository;

class IpToRoomController extends BaseController
{

    public $user = "";

    function __construct()
    {
        parent::__construct();
        $this->beforeFilter('login');
        $this->user = Sentry::getUser();
    }

    public function ipToRoom()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToRoom.list')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $inputs = Input::except('_token');
        $inputs['details'] = true;
        $data['ipToRoom'] = IpToRoom::getAll($inputs);

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getHospitalsLocalization();
        return View::make('ipToRoom/list', $data);
    }

    public function addIpToRoom()
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToRoom.add')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();
        $data['ipToRoom'] = array(
            'ip' => '',
            'hospital_id' => '',
            'room_num' => '',
            'room_name' => '',
            'corridor_num' => '',
            'ip_to_screen_id' => '',
            'room_id' => '',
            'type' => '',
        );
        return View::make('ipToRoom/add', $data);
    }

    public function createIpToRoom()
    {
        try {
            $inputs = (Input::except('_token'));
            if (isset($inputs['type']) && $inputs['type'] == 1) { // if sms system
                $validator = Validator::make($inputs, array(
                    'ip' => "required|unique:ip_to_room"
                ));
                if ($validator->fails()) {
                    Flash::error($validator->messages());
                    return Redirect::back()->withInput(Input::all());
                }
                $inputs['ip_to_screen_id'] = null;
                $inputs['room_id'] = null;
            } else { // if queue system
                $validator = Validator::make($inputs, array(
                    'ip_to_screen_id' => "required|unique:ip_to_room"
                ));
                if ($validator->fails()) {
                    Flash::error($validator->messages());
                    return Redirect::back()->withInput(Input::all());
                }
                $inputs['room_id'] = implode(',', $inputs['room_id']);
                $inputs['ip'] = null;
                $inputs['room_num'] = null;
                $inputs['room_name'] = null;
                $inputs['corridor_num'] = null;
            }
            IpToRoom::add($inputs);
            Flash::success('Added Successfully');
            return Redirect::route('ipToRoom');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function editIpToRoom($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToRoom.edit')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }

        $hospitalRepo = new HospitalRepository();
        $data['hospitals'] = $hospitalRepo->getAll();

        $data['ipToRoom'] = IpToRoom::getById($id);

        $data['ipToScreen'] = IpToScreen::getAll();
        return View::make('ipToRoom/add', $data);
    }

    public function updateIpToRoom($id)
    {
        $inputs = (Input::except('_token'));
        try {
            if (isset($inputs['type']) && $inputs['type'] == 1) { // if ip to screen
                $validator = Validator::make($inputs, array(
                    'ip' => "required|unique:ip_to_room,ip," . $id
                ));
                if ($validator->fails()) {
                    Flash::error($validator->messages());
                    return Redirect::back()->withInput(Input::all());
                }
                $inputs['ip_to_screen_id'] = null;
                $inputs['room_id'] = null;
            } else { // if screen to rooms
                $validator = Validator::make($inputs, array(
                    'ip_to_screen_id' => "required|unique:ip_to_room,ip_to_screen_id," . $id
                ));
                if ($validator->fails()) {
                    Flash::error($validator->messages());
                    return Redirect::back()->withInput(Input::all());
                }
                $inputs['room_id'] = implode(',', $inputs['room_id']);
                $inputs['ip'] = null;
                $inputs['room_num'] = null;
                $inputs['room_name'] = null;
                $inputs['corridor_num'] = null;
            }
            IpToRoom::edit($inputs, $id);
            Flash::success('Updated Successfully');
            return Redirect::route('ipToRoom');
        } catch (Exception $e) {
            Flash::error('Ops, try again later!');
            return Redirect::back()->withInput(Input::all());
        }
    }

    public function deleteIpToRoom($id)
    {
        if ($this->user->user_type_id != 1 && !$this->user->hasAccess('ipToRoom.delete')) {
            Flash::error('You don\'t have a permission to do this action');
            return Redirect::back();
        }
        IpToRoom::remove($id);
        Flash::success('Delete Successfully');
        return Redirect::back();
    }

    public function getRoomsByHospitalId()
    {
        $hospital_id = Input::get('hospital_id');
        $inputs = array(
            'get_by_hospital_id' => $hospital_id,
            'except_rooms_chosen' => true,
            'type' => 1,
        );
        if (Input::has('exceptIds')) {
           $inputs['exceptIds'] = Input::get('exceptIds');
        }
        $rooms = IpToRoom::getAll($inputs);
        $html = '<option value="">Choose</option>';
        foreach ($rooms as $key => $val) {
            $html .= '<option value="' . $val['id'] . '">' . $val['room_name'] . ' [' . $val['ip'] . ']</option>';
        }
        $data['rooms'] = $html;

        $screens = IpToScreen::getAll(array(
            'get_by_hospital_id' => $hospital_id,
        ));
        $html2 = '<option value="">Choose</option>';
        foreach ($screens as $key => $val) {
            $html2 .= '<option value="' . $val['id'] . '">' . $val['screen_name'] . ' [' . $val['ip'] . ']</option>';
        }
        $data['screens'] = $html2;
        return $data;
    }
}
