<div class="box box-primary">
    <div class="box-header">
        Available Time
        <div class="pull-right">
            <b>{{ucfirst(lcfirst(date('l', strtotime($selectDate))))}}</b> {{$selectDate}} &nbsp;&nbsp;&nbsp;
        </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <input type="hidden" id="unlockSlotInput" autocomplete="off" value="0">
        <table class="table table-hover">
            <thead>
            <tr>
                <th width="15px">From</th>
                <th width="15px">To</th>
                <th>Res Status</th>
                <th>Phy Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach($availableTimes as $key => $val)
                <tr style="
                @if(isset($val['reserved']))
                @if($val['patient_attend'] == 1)
                        background:#84e184;
                @endif
                @if($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                        background:#32cd32;
                @endif
                @if($val['patient_status'] == \core\enums\PatientStatus::patient_out)
                        background:deepskyblue;
                @endif
                @if($val['patient_status'] == \core\enums\PatientStatus::cancel)
                        background:#ff8566;
                @endif
                @if($val['patient_status'] == \core\enums\PatientStatus::no_show)
                        background:#ff8566;
                @endif
                @if($val['patient_status'] == \core\enums\PatientStatus::pending)
                        background:#ffb84d;
                @endif
                @if($val['patient_status'] == \core\enums\PatientStatus::archive)
                        background:#ff8566;
                @endif
                @endif ">
                    <td>{{$val['time']}}</td>
                    <td>
                        @if(isset($val['time_to']))
                            {{$val['time_to']}}
                        @else
                            <?php
                            $seconds = Functions::hoursToSeconds($val['time']);
                            $newSeconds = $seconds + ($slots * 60);
                            $time_to = Functions::timeFromSeconds($newSeconds);
                            ?>
                            {{$time_to}}
                        @endif
                    </td>
                    <td>
                        @if(isset($val['reserved']))
                            @if($val['status'] == \core\enums\ReservationStatus::reserved)
                                <span>Reserved</span>
                            @elseif($val['status'] == \core\enums\ReservationStatus::on_progress)
                                <span>On Progress</span>
                            @elseif($val['status'] == \core\enums\ReservationStatus::accomplished)
                                <span>Accomplished</span>
                            @elseif($val['status'] == \core\enums\ReservationStatus::pending)
                                <span>Pending</span>
                            @elseif($val['status'] == \core\enums\ReservationStatus::no_show)
                                <span>No Show</span>
                            @elseif($val['status'] == \core\enums\ReservationStatus::not_available)
                                <span style="color: red">Not Available</span>
                            @endif
                        @else
                            <span style="color: green">Available</span>
                        @endif
                    </td>
                    <td>
                        @if(isset($val['reserved']) || isset($val['exception_reason']))
                            @if(isset($val['show_reason']) && $val['show_reason'] == 1)
                                <span style="">{{$val['exception_reason']}}</span>
                            @endif
                        @else
                            <span style="color: green">In Clinic</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            @if(empty($availableTimes))
                <tr>
                    <td colspan="10">
                        <center style="color: red">Not Available!</center>
                </tr>
            @endif
            </tbody>
        </table>
    </div>
</div>
