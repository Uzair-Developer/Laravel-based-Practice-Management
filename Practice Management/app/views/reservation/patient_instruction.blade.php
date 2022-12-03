<input type="hidden" name="reservation_ids" value="{{$reservation_ids}}">
<input type="hidden" name="reception_ip" value="{{$ipToReception['ip']}}">
<input type="hidden" name="ip_to_reception_id" value="{{$ipToReception['id']}}">
<input type="hidden" name="key_of_chosen" value="{{$keyOfChosen}}">
<input type="hidden" name="hospital_id" value="{{$ipToReception['hospital_id']}}">
@foreach($reservations as $key => $val)
    <?php
    $patient = Patient::getById($val['patient_id']);
    $physician = User::getById($val['physician_id']);
    $clinic = Clinic::getById($val['clinic_id']);
    $userLoginIp = UserLoginIp::check($val['physician_id'], null, null, 7);
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
    <div class="col-md-12 box box-primary collapsed-box">
        <div class="box-header">
            @if($patient['registration_no'])
                [{{$patient['registration_no']}}]
            @endif
            {{ucwords(strtolower($patient['name']))}} -
                @if($val['type'] == 3)
                    {{$val['revisit_time_from']}}
                @else
                    {{$val['time_from']}}
                @endif
                -
                <?php
                $code = explode('-', $val['code']);
                ?>
                @if($userLoginIp && $room)
                    {{$code[0] . '-' . $code[1]}}
                @endif
            <button type="button" class="btn btn-box-tool pull-right" data-widget="collapse">
                <i class="fa fa-plus"></i></button>
        </div>
        <div class="box-body" style="display: none;">
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
                    $code = explode('-', $val['code']);
                    ?>
                    @if($userLoginIp && $room)
                        {{$code[0] . '-' . $code[1]}}
                    @endif
                </div>
            </div>
            <div class="form-group col-md-6">
                <label>Time</label>

                <div>
                    @if($val['type'] == 3)
                        {{$val['revisit_time_from']}}
                    @else
                        {{$val['time_from']}}
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
        </div>
    </div>
@endforeach