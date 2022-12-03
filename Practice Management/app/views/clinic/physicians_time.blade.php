<div class="box box-primary">
    <div class="box-header">
        Available Time In: {{$selectedDate}}
    </div>
    <!-- /.box-header -->
    <div class="box-body table-responsive no-padding">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Physician Name</th>
                <th>From</th>
                <th>To</th>
                <th>Reservation Status</th>
                <th>Patient Status</th>
                <th>Physician Status</th>
                <th>Options</th>
            </tr>
            </thead>
            <tbody>
            @foreach($allData as $key2 => $val2)
                @if($val2['times'])
                    <?php $is_empty = true; ?>
                    @foreach($val2['times'] as $key => $val)
                        @if(!isset($val['reserved']))
                            @if($selectedDate == date('Y-m-d') && $val['time'] < date('H:i:s', strtotime("-30 minutes")))
                                <?php continue; ?>
                            @endif
                            <?php $is_empty = false; ?>
                            <tr id="{{$val2['physician_id']}}_{{str_replace(':',"_", $val['time'])}}">
                                <td>{{$val2['physician_name']}}</td>
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
                                    <?php
                                    $seconds = Functions::hoursToSeconds($val['time']);
                                    $newSeconds = $seconds + ($val2['slots'] * 60);
                                    $time_to = Functions::timeFromSeconds($newSeconds);
                                    if ($time_to > '23:59:00') {
                                        $seconds = Functions::hoursToSeconds($time_to);
                                        $newSeconds = $seconds - (24 * 60 * 60);
                                        $time_to = Functions::timeFromSeconds($newSeconds);
                                    }
                                    ?>
                                    {{$time_to}}
                                </td>
                                <td>
                                    <span style="color: green">Available</span>
                                </td>
                                <td>
                                    <span style="color: green">Available</span>
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
                                    <div class="btn-group">
                                        @if($c_user->user_type_id == 1 || $c_user->hasAccess('reservation.add'))
                                            <a time="{{$val['time']}}"
                                               time_for_id="{{str_replace(':',"_", $val['time'])}}"
                                               physician_id="{{$val2['physician_id']}}"
                                               clinic_schedule_id="{{$val2['clinic_schedule_id']}}" title="Reserve"
                                               class="btn btn-default reserveBtn"><i
                                                        class="fa fa-plus"></i></a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    @if($is_empty)
                        <tr>
                            <td>
                                <span style="color: red">{{$val2['physician_name']}}</span>
                            </td>
                            <td colspan="6">
                                <span style="color: red">Doctor Not Available</span>
                            </td>
                        </tr>
                    @endif
                @else
                    <tr>
                        <td>
                            <span style="color: red">{{$val2['physician_name']}}</span>
                        </td>
                        <td colspan="6">
                            <span style="color: red">Doctor Not Available</span>
                        </td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    </div>
</div>
