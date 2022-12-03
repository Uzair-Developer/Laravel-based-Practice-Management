<?php

use core\enums\UserRules;

class PharmacyQueue extends Eloquent
{
    protected $table = 'pharmacy_queue';
    protected $guarded = array('');

    public function ticketType()
    {
        return $this->belongsTo('AttributePms', 'pharmacy_ticket_type_id', 'id');
    }

    public function userCallBy()
    {
        return $this->belongsTo('User', 'call_by', 'id');
    }

    public function userCallFromPassBy()
    {
        return $this->belongsTo('User', 'call_from_pass_by', 'id');
    }

    public function userCallDoneBy()
    {
        return $this->belongsTo('User', 'call_done_by', 'id');
    }

    public function userCancelBy()
    {
        return $this->belongsTo('User', 'cancel_by', 'id');
    }

    public static function add($inputs)
    {
        $inputs['create_timestamp'] = time();
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs = [])
    {
        $data = self::whereRaw('1 = 1');
        if (isset($inputs['pharmacy_ticket_type_id']) && $inputs['pharmacy_ticket_type_id']) {
            $data = $data->where('pharmacy_ticket_type_id', $inputs['pharmacy_ticket_type_id']);
        }
        if (isset($inputs['pharmacy_ticket_type_ids'])) {
            $data = $data->whereIn('pharmacy_ticket_type_id', $inputs['pharmacy_ticket_type_ids']);
        }
        if (isset($inputs['pharmacy_ip']) && $inputs['pharmacy_ip']) {
            $data = $data->where('pharmacy_ip', $inputs['pharmacy_ip']);
        }
        if (isset($inputs['call_pharmacy_ip']) && $inputs['call_pharmacy_ip']) {
            $data = $data->where('call_pharmacy_ip', $inputs['call_pharmacy_ip']);
        }
        if (isset($inputs['call_done_pharmacy_ip']) && $inputs['call_done_pharmacy_ip']) {
            $data = $data->where('call_done_pharmacy_ip', $inputs['call_done_pharmacy_ip']);
        }
        if (isset($inputs['hospital_id']) && $inputs['hospital_id']) {
            $data = $data->where('hospital_id', $inputs['hospital_id']);
        }
        if (isset($inputs['date']) && $inputs['date']) {
            $data = $data->where('date', $inputs['date']);
        }
        if (isset($inputs['call_flag']) && ($inputs['call_flag'] === "0" || $inputs['call_flag'])) {
            $data = $data->where('call_flag', $inputs['call_flag']);
        }
        if (isset($inputs['orderByCall']) && $inputs['orderByCall']) {
            $data = $data->orderBy('call_datetime', 'desc');
        }
        if (isset($inputs['orderBy']) && $inputs['orderBy']) {
            $data = $data->orderBy($inputs['orderBy'][0], $inputs['orderBy'][1]);
        }
        if (isset($inputs['getFirst']) && $inputs['getFirst']) {
            $data = $data->first();
        } else if (isset($inputs['getCount']) && $inputs['getCount']) {
            $data = $data->count('id');
        } else {
            $data = $data->get();
        }
        if (isset($inputs['details']) && $inputs['details']) {
            foreach ($data as $key => $val) {
                $data[$key]['hospital_name'] = Hospital::getName($val['hospital_id']);
            }
        }
        return $data;
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function getNameById($id)
    {
        return self::where('id', $id)->pluck('room_name');
    }

    public static function getReport($inputs = [])
    {
        if (empty($inputs['from_datetime'])) {
            $inputs['from_datetime'] = date('Y-m-d 00:00:00');
        }
        if ($inputs['to_datetime']) {
            $inputs['to_datetime'] = date('Y-m-d 23:59:59');
        }
        $users = [];
        if (isset($inputs['pharmacy_id']) && $inputs['pharmacy_id']) {
            $users[] = $inputs['pharmacy_id'];
        } else {
            $users = User::getByFilters([
                'user_type_id' => UserRules::pharmacy,
                'activated' => 1,
                'getIds' => true
            ]);
        }
        $result = [];
        foreach ($users as $key => $user) {
            $pharmacist = User::getById($user);
            $result[$key]['pharmacist'] = $pharmacist;
            $result[$key]['numCalls'] = 0;
            $result[$key]['numPass'] = 0;
            $result[$key]['numCallsDone'] = 0;
            $result[$key]['numCancel'] = 0;
            $data = self::where('created_at', '>=', $inputs['from_datetime'])
                ->where('created_at', '<=', $inputs['to_datetime'])
                ->where(function ($q) use ($user) {
                    $q->where('call_by', $user);
                    $q->orWhere('call_from_pass_by', $user);
                });
            $data->chunk(50, function ($queue) use ($user, &$key, &$result) {
                foreach ($queue as $key2 => $val) {
                    if ($val['call_by'] == $user) {
                        $result[$key]['numCalls']++;
                        if (!empty($val['pass_datetime'])) {
                            $result[$key]['numPass']++;
                        }
                    }
                    if ($val['call_from_pass_by'] == $user) {
                        $result[$key]['numCalls']++;
                    }
                    if ($val['call_done_by'] == $user) {
                        $result[$key]['numCallsDone']++;
                    }
                    if ($val['cancel_by'] == $user) {
                        $result[$key]['numCancel']++;
                    }
                }
            });
        }
        return $result;
    }

    public static function getPharmacistLog($inputs = [])
    {
        if (empty($inputs['from_datetime'])) {
            $inputs['from_datetime'] = date('Y-m-d 00:00:00');
        }
        if ($inputs['to_datetime']) {
            $inputs['to_datetime'] = date('Y-m-d 23:59:59');
        }
        $data = self::where('created_at', '>=', $inputs['from_datetime'])
            ->where('created_at', '<=', $inputs['to_datetime']);
        if (isset($inputs['pharmacist_id']) && $inputs['pharmacist_id']) {
            $data = $data->where(function ($q) use ($inputs) {
                $q->where('call_by', $inputs['pharmacist_id']);
                $q->orWhere('call_from_pass_by', $inputs['pharmacist_id']);
            });
        }
        if (isset($inputs['paginate']) && $inputs['paginate']) {
            $data = $data->paginate($inputs['paginate']);
        } else {
            $data = $data->get();
        }
        return $data;
    }
}
