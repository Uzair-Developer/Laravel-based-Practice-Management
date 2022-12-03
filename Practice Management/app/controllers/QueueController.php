<?php

use Cartalyst\Sentry\Facades\Laravel\Sentry;

class QueueController extends BaseController
{
    function __construct()
    {
        parent::__construct();
    }

    public function listQueue()
    {
        $screenIp = Functions::GetClientIp();
        $screenToRoom = IpToRoom::getAll(array(
            'ip_to_screen_id' => IpToScreen::getByIp($screenIp)['id'],
            'type' => 2, // screen to room
            'getFirst' => true
        ));
        if (empty($screenToRoom)) {
            Flash::error('No Rooms Assigned To This Screen!');
            return Redirect::route('login');
        }

        $noData = true;
        $rooms = explode(',', $screenToRoom['room_id']);
        if (empty($screenToRoom['room_id'])) {
            $noData = true;
        } else {
            foreach ($rooms as $key2 => $val2) {
                $singleRoom = IpToRoom::getById($val2);
                $userLoginIp = UserLoginIp::check(null, null, $singleRoom['ip'], 7);
                if (empty($userLoginIp)) {
                    continue;
                }
                $data['reservations'] = Reservation::getAttendByClinic(null, array(
                    'limit' => 5,
                    'getYesterdayAfter24Hour' => true,
//                'reception_call_flag' => 2, // get call done flag
                ), $userLoginIp['user_id']);
                if (!empty($data['reservations']->toArray())) {
                    $noData = false;
                    break;
                }
            }
        }
        if (!$noData) {
            $data['screenToRoom'] = $screenToRoom;
            $data['tables'] = View::make('queue/reservation_list', $data)->render();
            return View::make('queue/list', $data);
        } else {
            $data['screenToRoom'] = '';
            $data['tables'] = '';
            return View::make('queue/list', $data);
        }
//        Session::put('curr_key_queue', 0);
//        Session::put('curr_key_queue2', 0);
//        foreach ($ipToRoom as $key => $val) {
//            $rooms = explode(',', $val['room_id']);
//            foreach ($rooms as $key2 => $val2) {
//                $singleRoom = IpToRoom::getById($val2);
//                $userLoginIp = UserLoginIp::check(null, null, $singleRoom['ip'], 7);
//                if (empty($userLoginIp)) {
//                    continue;
//                }
//                $data['reservations'] = Reservation::getAttendByClinic(null, array('details' => true), $userLoginIp['user_id']);
//                if ($data['reservations']->toArray()) {
//                    Session::put('curr_key_queue', $key);
//                    Session::put('curr_key_queue2', $key2);
//                    $data['tables'] = View::make('queue/reservation_list', $data)->render();
//                    return View::make('queue/list', $data);
//                }
//            }
//        }
    }

    public function getNextQueue()
    {
//        $curr_key_queue = Session::get('curr_key_queue');
//        $curr_key_queue2 = Session::get('curr_key_queue2');
        $screenIp = Functions::GetClientIp();
        $screenToRoom = IpToRoom::getAll(array(
            'ip_to_screen_id' => IpToScreen::getByIp($screenIp)['id'],
            'type' => 2, // screen to room
            'getFirst' => true
        ));
        if (empty($screenToRoom)) {
            Flash::error('No Rooms Assigned To This Screen!');
            return Redirect::route('login');
        }

        $noData = true;
        $rooms = explode(',', $screenToRoom['room_id']);
        if (empty($screenToRoom['room_id'])) {
            $noData = true;
        } else {
            foreach ($rooms as $key2 => $val2) {
                $singleRoom = IpToRoom::getById($val2);
                $userLoginIp = UserLoginIp::check(null, null, $singleRoom['ip'], 7);
                if (empty($userLoginIp)) {
                    continue;
                }
                $data['reservations'] = Reservation::getAttendByClinic(null, array(
                    'limit' => 5,
                    'getYesterdayAfter24Hour' => true,
//                'reception_call_flag' => 2, // get call done flag
                ), $userLoginIp['user_id']);
                if (!empty($data['reservations']->toArray())) {
                    $noData = false;
                    break;
                }
            }
        }
        if (!$noData) {
            $data['screenToRoom'] = $screenToRoom;
            return View::make('queue/reservation_list', $data)->render();
        } else {
            return '
<img width="500" height="250" style="margin-top: 50%;margin-left: 27%;" src="' . asset('images/sgh-logo5.png') . '">
<h1 style="margin-left: 15%;font-size: 50px;">Welcome To Saudi German Hospital</h1>
<h1 style="margin-left: 36%;font-size: 50px;color:orange;">Queuing System</h1>
<h1 style="margin-left: 39%;font-size: 50px;color:orange;">Coming Soon</h1>
';
        }
//        $countIpToRoom = count($ipToRoom->toArray());
//        for ($i = $curr_key_queue; $i < $countIpToRoom; $i++) {
//            $rooms = explode(',', $ipToRoom[$i]['room_id']);
//            $countRooms = count($rooms);
//            for ($i2 = $curr_key_queue2; $i2 < $countRooms; $i2++) {
//                if (isset($rooms[$i2 + 1])) {
//                    $singleRoom = IpToRoom::getById($rooms[$i2 + 1]);
//                    $userLoginIp = UserLoginIp::check(null, null, $singleRoom['ip'], 7);
//                    if (empty($userLoginIp)) {
//                        continue;
//                    }
//                    $data['reservations'] = Reservation::getAttendByClinic(null, array('details' => true), $userLoginIp['user_id']);
//                    if ($data['reservations']->toArray()) {
//                        Session::put('curr_key_queue', $i);
//                        Session::put('curr_key_queue2', $i2 + 1);
//                        return View::make('queue/reservation_list', $data)->render();
//                    } else {
//                        continue;
//                    }
//                } else {
//                    continue;
//                }
//            }
//            $curr_key_queue2 = 0;
//        }
    }

    public
    function listReceptionQueue()
    {
        $screenIp = Functions::GetClientIp();
        $screen = IpToScreen::getByIp($screenIp);
        $reception = IpToReception::getAll(array(
            'ip_to_screen_id' => $screen['id'],
            'getFirst' => true
        ));
        $reservation = Reservation::getByReceptionIp(array(
            'ip' => $reception['ip']
        ));
        $data['reception'] = $reception;
        if ($reservation) {
            $code = explode('-', $reservation['code']);
            $data['success'] = 'yes';
            $data['number'] = $code[0] . '-' . $code[1];
            if (Request::ajax()) {
                return $data;
            }
            return View::make('queue/reception_queue', $data);
        }
        if (Request::ajax()) {
            $data['number'] = '00000';
            return $data;
        }
        $data['success'] = 'no';
        $data['number'] = '';
        return View::make('queue/reception_queue', $data);
    }
}