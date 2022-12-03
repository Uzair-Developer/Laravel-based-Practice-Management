<?php

class UnlockSlot extends Eloquent
{
    protected $table = 'unlock_slots';
    protected $guarded = array('');

    public static $rules = array(
        "user_id" => "required",
    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id', $id)->update($inputs);
    }

    public static function getAll($inputs)
    {
        $data = self::where(function ($q) use ($inputs) {
            if (isset($inputs['date_from']) && $inputs['date_from']) {
                $q->where('date', '>=', $inputs['date_from']);
            }
            if (isset($inputs['date_to']) && $inputs['date_to']) {
                $q->where('date', '<=', $inputs['date_to']);
            }
            if (isset($inputs['physician_id']) && $inputs['physician_id']) {
                $q->where('user_id', $inputs['physician_id']);
            }
        })->orderBy('date')->orderBy('from_time');

        $data = $data->get()->toArray();

        foreach ($data as $key => $val) {
            $data[$key]['create_by'] = User::getName($val['created_by']);
            $data[$key]['physician_name'] = User::getName($val['user_id']);

        }
        return $data;
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getName($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }

    public static function checkByPhysicianDateTime($user_id, $date, $time_from, $time_to)
    {
        return self::where('user_id', $user_id)
            ->where('date', $date)
            ->where('from_time', $time_from)
            ->where('to_time', $time_to)
            ->first();
    }

    public static function removeWithPhysicianDateTime($user_id, $date, $time_from, $time_to)
    {
        return self::where('user_id', $user_id)
            ->where('date', $date)
            ->where('from_time', $time_from)
            ->where('to_time', $time_to)->delete();
    }

    public static function removeWithPeriod($user_id, $from_date, $to_date, $from_time, $to_time)
    {
        if ($user_id && $from_date && $to_date && $from_time && $to_time) {
            while (1 == 1) {
                self::where('user_id', $user_id)
                    ->where('date', $from_date)
                    ->where(function ($q) use ($from_time, $to_time) {
                        if ($from_time && $to_time) {
                            $q->where(function ($q2) use ($from_time, $to_time) {
                                $q2->where(function ($q3) use ($from_time, $to_time) {
                                    $q3->where('from_time', '>=', $from_time)->where('from_time', '<', $to_time);
                                });
                                $q2->orWhere(function ($q3) use ($from_time, $to_time) {
                                    $q3->where('to_time', '>', $from_time)->where('to_time', '<=', $to_time);
                                });
                                $q2->orWhere(function ($q3) use ($from_time, $to_time) {
                                    $q3->where('from_time', '<=', $from_time)->where('to_time', '>=', $to_time);
                                });
                            });
                        }
                    })->delete();
                if ($from_date == $to_date) {
                    break;
                } else {
                    $from_date = date('Y-m-d', strtotime("+1 day", strtotime($from_date)));
                }
            }
        }
    }

}
