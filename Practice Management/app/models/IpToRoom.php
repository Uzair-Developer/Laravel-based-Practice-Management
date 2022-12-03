<?php

class IpToRoom extends Eloquent
{
    protected $table = 'ip_to_room';
    protected $guarded = array('');

    public static function add($inputs)
    {
        $inputs['create_timestamp'] = time();
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = '')
    {
        $data = self::whereRaw(' 1 = 1 ');
        if (isset($inputs['ip']) && $inputs['ip']) {
            $data = $data->where('ip', 'LIKE', '%' . $inputs['ip'] . '%');
        }
        if (isset($inputs['ip_to_screen_id']) && $inputs['ip_to_screen_id']) {
            $data = $data->where('ip_to_screen_id', $inputs['ip_to_screen_id']);
        }
        if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
            $data = $data->where('hospital_id', $inputs['hospital_id']);
        }
        if (isset($inputs['get_by_hospital_id'])) {
            $data = $data->where('hospital_id', $inputs['get_by_hospital_id']);
        }
        if (isset($inputs['room_id']) && $inputs['room_id']) {
            $data = $data->where(function ($q) use ($inputs) {
                $q->where('room_id', $inputs['room_id']);
                $q->orWhere('room_id', 'LIKE', $inputs['room_id'] . '%');
                $q->orWhere('room_id', 'LIKE', '%' . $inputs['room_id'] . '%');
                $q->orWhere('room_id', 'LIKE', '%' . $inputs['room_id']);
            });
        }
        if (isset($inputs['room_num']) && $inputs['room_num']) {
            $data = $data->where('room_num', $inputs['room_num']);
        }
        if (isset($inputs['corridor_num']) && $inputs['corridor_num']) {
            $data = $data->where('corridor_num', $inputs['corridor_num']);
        }
        if (isset($inputs['room_name']) && $inputs['room_name']) {
            $data = $data->whereRaw('LOWER(room_name) LIKE LOWER("%' . $inputs['room_name'] . '%")');
        }
        if (isset($inputs['type']) && $inputs['type']) {
            $data = $data->where('type', $inputs['type']);
        }
        if (isset($inputs['getFirst']) && $inputs['getFirst']) {
            $data = $data->first();
        } else {
            $data = $data->get();
        }
        if (isset($inputs['except_rooms_chosen']) && $inputs['except_rooms_chosen']) {
            foreach ($data as $key => $val) {
                if ($val['type'] == 1) {
                    if (isset($inputs['exceptIds']) && $inputs['exceptIds']) {
                        $exceptIds = explode(',', $inputs['exceptIds']);
                        if (in_array($val['id'], $exceptIds)) {
                            continue;
                        }
                    }
                    if (self::checkRoomChosen($val['id'])) {
                        unset($data[$key]);
                    }
                }
            }
        }
        if (isset($inputs['details']) && $inputs['details']) {
            foreach ($data as $key => $val) {
                $data[$key]['ip_to_screen_name'] = '';
                $data[$key]['hospital_name'] = '';
                $data[$key]['rooms_name'] = '';
                $data[$key]['hospital_name'] = Hospital::getName($val['hospital_id']);
                if ($val['type'] == 2) { // if queue system
                    $ip_to_screen = IpToScreen::getById($val['ip_to_screen_id']);
                    $data[$key]['ip_to_screen_name'] = $ip_to_screen['ip'] . ' [' . $ip_to_screen['screen_name'] . ']';

                    $rooms = explode(',', $val['room_id']);
                    $countRooms = count($rooms);
                    foreach ($rooms as $key2 => $val2) {
                        if ($key2 + 1 == $countRooms)
                            $data[$key]['rooms_name'] .= self::getNameById($val2);
                        else
                            $data[$key]['rooms_name'] .= self::getNameById($val2) . ', ';
                    }
                }
            }
        }
        return $data;
    }

    public static function checkRoomChosen($room_id)
    {
        return self::where('type', 2)
            ->where(function ($q) use ($room_id) {
                $q->where('room_id', $room_id);
                $q->orWhere('room_id', 'LIKE', $room_id . '%');
                $q->orWhere('room_id', 'LIKE', '%' . $room_id . '%');
                $q->orWhere('room_id', 'LIKE', '%' . $room_id);
            })->first();
    }

    public static function getNameById($id)
    {
        return self::where('id', $id)->pluck('room_name');
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
