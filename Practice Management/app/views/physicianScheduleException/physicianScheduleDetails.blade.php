<input type="hidden" value="{{$date}}" name="date">
<input type="hidden" name="num_of_shifts" value="{{$clinicSchedule['num_of_shifts']}}">
<div class="col-md-12">
    <center>
        <label style="font-size: 20px;">{{date('l', strtotime($date)) . '  ' . $date}}</label>
    </center>
</div>

<div class="form-group col-md-4">
    <label>Duration (minuets)</label>
    <input type="number" value="{{$schedule['slots']}}" name="slots" class="form-control">
</div>
@if($hasException)
    <div class="form-group col-md-12">
        <label>Shift 1</label>
    </div>

    <div class="form-group col-md-4">
        <label>From Time</label>

        <div class="bootstrap-timepicker">
            <input @if($schedule['shift1_dayoff'] == 1) disabled @endif type="text"
                   value="{{$schedule['shift1_time_from']}}"
                   name="shift1_time_from"
                   class="form-control timepicker">
        </div>
    </div>

    <div class="form-group col-md-4">
        <label>To Time</label>

        <div class="bootstrap-timepicker">
            <input @if($schedule['shift1_dayoff'] == 1) disabled @endif type="text"
                   value="{{$schedule['shift1_time_to']}}"
                   name="shift1_time_to"
                   class="form-control">
        </div>
    </div>

    <div class="form-group col-md-4">
        <label>Day Off</label>

        <select class="form-control" name="shift1_dayoff">
            <option @if($schedule['shift1_dayoff'] == 1) selected @endif value="1">Yes</option>
            <option @if($schedule['shift1_dayoff'] == 2) selected @endif value="2">No</option>
        </select>
    </div>

    @if($clinicSchedule['num_of_shifts'] == 2 || $clinicSchedule['num_of_shifts'] == 3)
        <div class="form-group col-md-12">
            <label>Shift 2</label>
        </div>

        <div class="form-group col-md-4">
            <label>From Time</label>

            <div class="bootstrap-timepicker">
                <input @if($schedule['shift2_dayoff'] == 1) disabled @endif type="text"
                       value="{{$schedule['shift2_time_from']}}"
                       name="shift2_time_from"
                       class="form-control timepicker">
            </div>
        </div>

        <div class="form-group col-md-4">
            <label>To Time</label>

            <div class="bootstrap-timepicker">
                <input @if($schedule['shift2_dayoff'] == 1) disabled @endif type="text"
                       value="{{$schedule['shift2_time_to']}}"
                       name="shift2_time_to"
                       class="form-control">
            </div>
        </div>

        <div class="form-group col-md-4">
            <label>Day Off</label>

            <select class="form-control" name="shift2_dayoff">
                <option @if($schedule['shift2_dayoff'] == 1) selected @endif value="1">Yes</option>
                <option @if($schedule['shift2_dayoff'] == 2) selected @endif value="2">No</option>
            </select>
        </div>

        @if($clinicSchedule['num_of_shifts'] == 3)
            <div class="form-group col-md-12">
                <label>Shift 3</label>
            </div>

            <div class="form-group col-md-4">
                <label>From Time</label>

                <div class="bootstrap-timepicker">
                    <input @if($schedule['shift3_dayoff'] == 1) disabled @endif type="text"
                           value="{{$schedule['shift3_time_from']}}"
                           name="shift3_time_from"
                           class="form-control timepicker">
                </div>
            </div>

            <div class="form-group col-md-4">
                <label>To Time</label>

                <div class="bootstrap-timepicker">
                    <input @if($schedule['shift3_dayoff'] == 1) disabled @endif type="text"
                           value="{{$schedule['shift3_time_to']}}"
                           name="shift3_time_to"
                           class="form-control">
                </div>
            </div>

            <div class="form-group col-md-4">
                <label>Day Off</label>

                <select class="form-control" name="shift3_dayoff">
                    <option @if($schedule['shift3_dayoff'] == 1) selected @endif value="1">Yes</option>
                    <option @if($schedule['shift3_dayoff'] == 2) selected @endif value="2">No</option>
                </select>
            </div>
        @endif
    @endif

@else
    <?php
    $daysName = array(
            'saturday' => 'sat',
            'sunday' => 'sun',
            'monday' => 'mon',
            'tuesday' => 'tues',
            'wednesday' => 'wed',
            'thursday' => 'thurs',
            'friday' => 'fri',
    );
    $inputDayName = lcfirst(date('l', strtotime($date)));
    ?>

    <div class="form-group col-md-12">
        <label>Shift 1</label>
    </div>
    <?php $dayoff_1 = explode(',', $schedule['dayoff_1']); ?>

    <div class="form-group col-md-4">
        <label>From Time</label>

        <div class="bootstrap-timepicker">
            <input @if(in_array($inputDayName, $dayoff_1)) disabled @endif type="text"
                   value="{{$schedule[$daysName[$inputDayName] . '_start_time_1']}}"
                   name="shift1_time_from"
                   class="form-control timepicker">
        </div>
    </div>

    <div class="form-group col-md-4">
        <label>To Time</label>

        <div class="bootstrap-timepicker">
            <input @if(in_array($inputDayName, $dayoff_1)) disabled @endif type="text"
                   value="{{$schedule[$daysName[$inputDayName] . '_end_time_1']}}"
                   name="shift1_time_to"
                   class="form-control">
        </div>
    </div>

    <div class="form-group col-md-4">
        <label>Day Off</label>

        <select class="form-control" name="shift1_dayoff">
            <option @if(in_array($inputDayName, $dayoff_1)) selected @endif value="1">Yes</option>
            <option @if(!in_array($inputDayName, $dayoff_1)) selected @endif value="2">No</option>
        </select>
    </div>

    @if($clinicSchedule['num_of_shifts'] == 2 || $clinicSchedule['num_of_shifts'] == 3)
        <div class="form-group col-md-12">
            <label>Shift 2</label>
        </div>
        <?php $dayoff_2 = explode(',', $schedule['dayoff_2']); ?>

        <div class="form-group col-md-4">
            <label>From Time</label>

            <div class="bootstrap-timepicker">
                <input @if(in_array($inputDayName, $dayoff_2)) disabled @endif type="text"
                       value="{{$schedule[$daysName[$inputDayName] . '_start_time_2']}}"
                       name="shift2_time_from"
                       class="form-control timepicker">
            </div>
        </div>

        <div class="form-group col-md-4">
            <label>To Time</label>

            <div class="bootstrap-timepicker">
                <input @if(in_array($inputDayName, $dayoff_2)) disabled @endif type="text"
                       value="{{$schedule[$daysName[$inputDayName] . '_end_time_2']}}"
                       name="shift2_time_to"
                       class="form-control">
            </div>
        </div>

        <div class="form-group col-md-4">
            <label>Day Off</label>

            <select class="form-control" name="shift2_dayoff">
                <option @if(in_array($inputDayName, $dayoff_2)) selected @endif value="1">Yes</option>
                <option @if(!in_array($inputDayName, $dayoff_2)) selected @endif value="2">No</option>
            </select>
        </div>

        @if($clinicSchedule['num_of_shifts'] == 3)
            <div class="form-group col-md-12">
                <label>Shift 3</label>
            </div>
            <?php $dayoff_3 = explode(',', $schedule['dayoff_3']); ?>

            <div class="form-group col-md-4">
                <label>From Time</label>

                <div class="bootstrap-timepicker">
                    <input @if(in_array($inputDayName, $dayoff_3)) disabled @endif type="text"
                           value="{{$schedule[$daysName[$inputDayName] . '_start_time_3']}}"
                           name="shift3_time_from"
                           class="form-control timepicker">
                </div>
            </div>

            <div class="form-group col-md-4">
                <label>To Time</label>

                <div class="bootstrap-timepicker">
                    <input @if(in_array($inputDayName, $dayoff_3)) disabled @endif type="text"
                           value="{{$schedule[$daysName[$inputDayName] . '_end_time_3']}}"
                           name="shift3_time_to"
                           class="form-control">
                </div>
            </div>

            <div class="form-group col-md-4">
                <label>Day Off</label>

                <select class="form-control" name="shift3_dayoff">
                    <option @if(in_array($inputDayName, $dayoff_3)) selected @endif value="1">Yes</option>
                    <option @if(!in_array($inputDayName, $dayoff_3)) selected @endif value="2">No</option>
                </select>
            </div>
        @endif
    @endif

@endif

<script>
    $('.timepicker').datetimepicker({
        datepicker: false,
        format: 'H:i',
        step: 5,
        {{--minDate: '{{date('Y-m-d')}}'--}}
    });
</script>