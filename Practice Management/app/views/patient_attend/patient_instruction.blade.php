<?php
$patient = Patient::getById($reservation['patient_id']);
$physician = User::getById($reservation['physician_id']);
$clinic = Clinic::getById($reservation['clinic_id']);
$userLoginIp = UserLoginIp::check($reservation['physician_id'], null, null, 7);
$room = 'N/A';
$screen = 'N/A';
$screenData = 'N/A';
if ($userLoginIp) {
    $room = IpToRoom::getAll(array(
            'getFirst' => true,
            'type' => 1, // ip to room
            'ip' => $userLoginIp['ip'],
    ));
    if ($room) {
        $screen = IpToRoom::getAll(array(
                'getFirst' => true,
                'room_id' => $room['id'],
                'type' => 2, // screen to room
        ));
        if ($screen) {
            $screenData = IpToScreen::getById($screen['ip_to_screen_id']);
        }
    }
}
?>

<div class="form-group col-md-12">
    <label>Patient Name</label>

    <div>
        @if($patient['registration_no'])
            [{{$patient['registration_no']}}]
        @endif
        {{ucwords(strtolower($patient['name']))}}
    </div>
</div>
<div class="form-group col-md-12">
    <label>Physician Name</label>

    <div>
        {{ucwords(strtolower($physician['full_name']))}}
    </div>
</div>
<div class="form-group col-md-12">
    <label>Clinic Name</label>

    <div>
        {{$clinic['name']}}
    </div>
</div>
<div class="form-group col-md-6">
    <label>Reservation Num</label>

    <div>
        <?php
        $code = explode('-', $reservation['code']);
        ?>
        @if($userLoginIp && $room)
            {{$room['room_num'] . $code[1]}}
        @endif
    </div>
</div>
<div class="form-group col-md-6">
    <label>Time</label>

    <div>
        @if($reservation['type'] == 3)
            {{$reservation['revisit_time_from']}}
        @else
            {{$reservation['time_from']}}
        @endif
    </div>
</div>
<div class="form-group col-md-6">
    <label>Reception Area</label>

    <div>{{$ipToReception['name']}}</div>
</div>
<div class="form-group col-md-6">
    <label>Waiting Area</label>

    <div>
        @if($screenData)
            {{$screenData['wait_area_name']}}
        @endif
    </div>
</div>
<div class="form-group col-md-6">
    <label>Corridor Num</label>

    <div>
        @if($room)
            {{$room['corridor_num']}}
        @endif
    </div>
</div>
<div class="form-group col-md-6">
    <label>Room Num</label>

    <div>
        @if($room)
            {{$room['room_num']}}
        @endif
    </div>
</div>
