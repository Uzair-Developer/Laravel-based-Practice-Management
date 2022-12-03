<?php


class EventDetails extends Eloquent
{
    protected $table = 'diagnosis_event_details';
    protected $guarded = array('');

//    public static  $rules = array(
//        "name" => "required",
//        "phone" => "required|unique:patients",
//    );

    public static function add($inputs)
    {
        return self::create($inputs);
    }

    public static function edit($inputs, $id)
    {
        return self::where('id',$id)->update($inputs);
    }

    public static function checkExist($eventId, $type, $refId, $response = 'undefined')
    {
        if($response != 'undefined'){
            return self::where('event_id',$eventId)
                ->where('event_type', $type)
                ->where('reference_id', $refId)
                ->where('response', $response)
                ->first();
        }
        return self::where('event_id',$eventId)
            ->where('event_type', $type)
            ->where('reference_id', $refId)
            ->first();
    }

    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }

    public static function getNameById($id)
    {
        return self::where('id', $id)->pluck('name');
    }

    public static function remove($id)
    {
        return self::where('id', $id)->delete();
    }
}
