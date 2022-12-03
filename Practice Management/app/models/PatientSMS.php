<?php

class PatientSMS extends Eloquent
{

    protected $table = 'patient_sms';
    protected $guarded = array('');


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
        return self::where(function($q) use ($inputs) {
           if($inputs){
               if(isset($inputs['send']) && $inputs['send'] == 0) {
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

    public static function getByReservationId($reservation_id)
    {
        return self::where('reservation_id', $reservation_id)->get();
    }

    public static function sendSMSToAll()
    {
        self::where('send', 0)->chunk(200, function ($sms) {
            foreach ($sms as $key => $val) {
                $patient = Patient::getById($val['patient_id']);
                $val->update(['send' => 1]);
                $response = Functions::sendSMS($patient['phone'], $val['message']);
                // Ezagel account
//            if (strpos($response, 'Success') !== false || strpos($response, 'Mobile') !== false
//                || strpos($response, 'Valdity') !== false
//                || strpos($response, 'Provider') !== false
//            ) {
//                PatientSMS::edit(array(
//                    'send' => 1,
//                    'response' => $response,
//                ), $val['id']);
//            } else {
//                PatientSMS::edit(array(
//                    'another_response' => $response,
//                ), $val['id']);
//            }

                // Victorylink account
                $val->update(['send' => 1, 'response' => $response]);
//            if ($response != '-5') {
//                PatientSMS::edit(array(
//                    'send' => 1,
//                    'response' => $response,
//                ), $val['id']);
//            } else {
//                PatientSMS::edit(array(
//                    'another_response' => $response,
//                ), $val['id']);
//            }
            }
        });
    }

}
