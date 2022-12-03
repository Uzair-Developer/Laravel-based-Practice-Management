<?php

class SmsCampaign extends Eloquent
{

    protected $table = 'sms_campaign';
    protected $guarded = array('');

    public static $rules = array(
        "campaign_name" => "required",
        "sms_phone_id" => "required",
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
        return self::where(function ($q) use ($inputs) {
            if ($inputs) {
                if (isset($inputs['send']) && $inputs['send'] == 0) {
                    $q->where('send', 0);
                } else {
                    $q->where('send', 1);
                }
            }
        })->get()->toArray();
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

    public static function sendSMSToAll()
    {
        self::where('send', 0)->chunk(200, function ($sms) {
            foreach ($sms as $key => $val) {
                $person = SmsPhones::getById($val['sms_phone_id']);
                $val->update(['send' => 1]);
                $response = Functions::sendSMS($person['phone'], $val['message']);
                // Ezagel account
//            if (strpos($response, 'Success') !== false || strpos($response, 'Mobile') !== false
//                || strpos($response, 'Valdity') !== false
//                || strpos($response, 'Provider') !== false
//            ) {
//                PortalPatientSMS::edit(array(
//                    'send' => 1,
//                    'response' => $response,
//                ), $val['id']);
//            } else {
//                PortalPatientSMS::edit(array(
//                    'another_response' => $response,
//                ), $val['id']);
//            }
                // Victorylink account
                $val->update(['send' => 1, 'response' => $response]);
//            if ($response != '-5') {
//                PortalPatientSMS::edit(array(
//                    'send' => 1,
//                    'response' => $response,
//                ), $val['id']);
//            }
            }
        });
    }

}
