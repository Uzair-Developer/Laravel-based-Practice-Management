<style>
    .shift_1 {
        border-left: 5px solid #00a157;
    }

    .shift_2 {
        border-left: 5px solid blue;
        background: lightgrey;
    }

    .shift_3 {
        border-left: 5px solid #db8b0b;
    }
</style>
<div class="col-md-8">
    <div class="box box-primary">
        <div class="box-header">
            Available Time

            <div class="pull-right">
                <b>{{ucfirst(lcfirst(date('l', strtotime($selectDate))))}}</b> {{$selectDate}} &nbsp;&nbsp;&nbsp;
                <i style="cursor: pointer" id="refreshPhysicianTime" title="Refresh" class="fa fa-refresh"></i>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body table-responsive no-padding">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th width="15px">From</th>
                    <th width="15px">To</th>
                    <th>Res Status</th>
                    <th>P Status</th>
                    <th>Phy Status</th>
                    <th>Options</th>
                </tr>
                </thead>
                <tbody>
                @foreach($availableTimes as $key => $val)
                    <tr class="@if(isset($val['shift'])) shift_{{$val['shift']}} @endif" style="
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
                        <td>
                            @if($val['time'] > '23:59:00')
                                <?php
                                $seconds = Functions::hoursToSeconds($val['time']);
                                $newSeconds = $seconds - (24 * 60 * 60);
                                $time = Functions::timeFromSeconds($newSeconds);
                                ?>
                                {{$time}}
                            @else
                                {{$val['time']}}
                            @endif
                        </td>
                        <td>
                            @if(isset($val['time_to']))
                                @if($val['time_to'] > '23:59:00')
                                    <?php
                                    $seconds = Functions::hoursToSeconds($val['time_to']);
                                    $newSeconds = $seconds - (24 * 60 * 60);
                                    $time_to = Functions::timeFromSeconds($newSeconds);
                                    ?>
                                    {{$time_to}}
                                @else
                                    {{$val['time_to']}}
                                @endif
                            @else
                                <?php
                                $seconds = Functions::hoursToSeconds($val['time']);
                                $newSeconds = $seconds + ($slots * 60);
                                $time_to = Functions::timeFromSeconds($newSeconds);
                                if($time_to > '23:59:00'){
                                    $seconds = Functions::hoursToSeconds($time_to);
                                    $newSeconds = $seconds - (24 * 60 * 60);
                                    $time_to = Functions::timeFromSeconds($newSeconds);
                                }
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
                                    <span style="color:red">Not Available</span>
                                @elseif($val['status'] == \core\enums\ReservationStatus::archive)
                                    <span style="color:red">Archive</span>
                                @endif
                            @else
                                <span style="color: green">Available</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($val['reserved']))
                                @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                                    <span>Waiting</span>
                                @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_in)
                                    <span>Patient In</span>
                                @elseif($val['patient_status'] == \core\enums\PatientStatus::patient_out)
                                    <span>Patient Out</span>
                                @elseif($val['patient_status'] == \core\enums\PatientStatus::pending)
                                    <span>Pending</span>
                                @elseif($val['patient_status'] == \core\enums\PatientStatus::no_show)
                                    <span>No Show</span>
                                @elseif($val['patient_status'] == \core\enums\PatientStatus::not_available)
                                    <span style="color:red">Not Available</span>
                                @elseif($val['patient_status'] == \core\enums\PatientStatus::archive)
                                    <span style="color:red">Archive</span>
                                @endif
                            @else
                                <span style="color: green">Available</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($val['reserved']) || isset($val['exception_reason']))
                                @if(isset($val['show_reason']) && $val['show_reason'] == 1)
                                    @if(empty($val['exception_reason']))
                                        <span style="color: green">In Clinic</span>
                                    @else
                                        <span>{{$val['exception_reason']}}</span>
                                    @endif
                                @endif
                            @else
                                <span style="color: green">In Clinic</span>
                            @endif
                        </td>
                        <td>
                            @if(isset($val['reserved']))

                                @if($val['patient_status'] == \core\enums\PatientStatus::waiting
                                || $val['patient_status'] == \core\enums\PatientStatus::pending)
                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.cancel'))
                                        <a ref_id="{{$val['reservation_id']}}" title="Cancel"
                                           class="btn btn-sm btn-danger deleteReserveBtn"><i
                                                    class="fa fa-times"></i></a>
                                    @endif
                                @endif
                                @if($val['patient_status'] == \core\enums\PatientStatus::waiting
                                    || $val['patient_status'] == \core\enums\PatientStatus::pending
                                    || $val['patient_status'] == \core\enums\PatientStatus::cancel
                                    || $val['patient_status'] == \core\enums\PatientStatus::patient_in
                                    || $val['patient_status'] == \core\enums\PatientStatus::patient_out
                                    || $val['patient_status'] == \core\enums\PatientStatus::archive)
                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.show'))
                                        <a ref_id="{{$val['reservation_id']}}" title="Patient Info"
                                           class="btn btn-sm btn-info showReserveBtn"><i
                                                    class="fa fa-search"></i></a>
                                    @endif
                                @endif
                                @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.note'))
                                        <a ref_id="{{$val['reservation_id']}}" title="Add Notes"
                                           class="btn btn-sm btn-default noteReserveBtn"><i
                                                    class="fa fa-sticky-note-o"></i></a>
                                    @endif
                                @endif
                                @if($val['patient_status'] == \core\enums\PatientStatus::pending ||
                                $val['patient_status'] == \core\enums\PatientStatus::waiting)
                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.edit'))
                                        <a reservation_id="{{$val['reservation_id']}}" title="Edit"
                                           class="btn btn-sm btn-warning editReserveBtn"><i
                                                    class="fa fa-pencil"></i></a>
                                    @endif
                                @endif
                                @if($val['patient_status'] == \core\enums\PatientStatus::archive)
                                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.unArchive'))
                                        <a reservation_id="{{$val['reservation_id']}}" title="UnArchive"
                                           class="btn btn-sm btn-default unArchiveReserveBtn">UN</a>
                                    @endif
                                @endif
                            @else
                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.add'))
                                    <a time="{{$val['time']}}" to_time="@if(isset($val['time_to']))
                                    {{$val['time_to']}}
                                    @else
                                    <?php
                                    $seconds = Functions::hoursToSeconds($val['time']);
                                    $newSeconds = $seconds + ($slots * 60);
                                    $time = Functions::timeFromSeconds($newSeconds);
                                    ?>
                                    {{$time}}
                                    @endif" title="Reserve"
                                       class="btn btn-sm btn-default reserveBtn"><i
                                                class="fa fa-plus"></i></a>
                                @endif
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
</div>