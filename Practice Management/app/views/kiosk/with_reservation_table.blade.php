<div class="box box-primary">
    <!-- /.box-header -->
    <div class="box-header">
        <div class="col-md-2" id="printAction" style="display: none;">
            <input type="hidden" id="printAllInput" name="ids">
            <button class="btn btn-block btn-info" id="printAll">Print All</button>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered" id="example1">
            <thead>
            <tr>
                <th>Select</th>
                <th>Options</th>
                <th>Clinic Name</th>
                <th>Physician Name</th>
                <th>Patient Name</th>
                <th>Date</th>
                <th>Time From</th>
                <th>Time To</th>
            </tr>
            </thead>
            <tbody style="font-size: 20px;">
            @foreach($reservations as $key => $val)
                <?php
                $physician = User::getById($val['physician_id']);
                $patient = Patient::getById($val['patient_id']);
                $time_from = '';
                ?>
                @if($val['type'] == 3)
                    <?php
                    $seconds = Functions::hoursToSeconds($val['revisit_time_from']);
                    $newSeconds = $seconds + (10 * 60);
                    $time_from = Functions::timeFromSeconds($newSeconds);
                    ?>
                @else
                    <?php $time_from = $val['time_from']; ?>
                @endif
                <tr style="text-align: center;">
                    <td>
                        @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                            @if($val['type'] == 2 || ($val['type'] != 2 && ($time_from > date('H:i:s')
                            && ((strtotime(date('Y-m-d H:i:s')) - strtotime(date('Y-m-d') . ' ' . $time_from )) / 60 ) <= 30) || ($time_from <= date('H:i:s'))))
                                <input reservation_id="{{$val['id']}}" class="checkbox-inline multiCheckbox checkbox1"
                                       type="checkbox" autocomplete="off">
                            @endif
                        @endif
                    </td>
                    <td>
                        @if($val['patient_status'] == \core\enums\PatientStatus::waiting)
                            <a reservation_id="{{$val['id']}}" title="Print" style="padding: 15px;font-size: 20px;"
                               class="btn btn-info with_reservation_print"><i class="fa fa-print"></i> Print</a>
                        @else
                            @if($val['patient_status'] == \core\enums\PatientStatus::cancel)
                                <span style="color: red">Canceled</span>
                            @elseif($val['patient_status'] == \core\enums\PatientStatus::no_show)
                                <span style="color: red">No Show</span>
                            @elseif($val['patient_status'] == \core\enums\PatientStatus::pending)
                                <span style="color: red">Pending</span>
                            @elseif($val['patient_status'] == \core\enums\PatientStatus::archive)
                                <span style="color: red">Archived</span>
                            @endif
                        @endif
                    </td>
                    <td>{{Clinic::getNameById($val['clinic_id'])}}</td>
                    <td>
                        <div>
                            {{ucwords(strtolower($physician['full_name']))}}
                        </div>
                    </td>
                    <td>
                        <div>
                            {{ucwords(strtolower($patient['name']))}}
                        </div>
                    </td>
                    <td>{{$val['date']}}</td>
                    <td>
                        {{$time_from}}
                    </td>
                    <td>
                        @if($val['type'] == 1)
                            {{$val['time_to']}}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    #example1 td {
        vertical-align: middle;
    }

    #example1 input[type=checkbox] {
        vertical-align: middle;
        /* Double-sized Checkboxes */
        -ms-transform: scale(3); /* IE */
        -moz-transform: scale(3); /* FF */
        -webkit-transform: scale(3); /* Safari and Chrome */
        -o-transform: scale(3); /* Opera */
        padding: 10px;

        /* Checkbox text */
        font-size: 110%;
        display: inline;
    }

</style>

<script>
    var checkedArray = new Array();
    $(document).on('change', '.multiCheckbox', function () {
        if ($(".multiCheckbox:checked").length > 0) {
            $("#printAction").show();
        }
        else {
            $("#printAction").hide();
        }
        var checkedValues = $('.multiCheckbox:checked').map(function () {
            return $(this).attr('reservation_id');
        }).get();
        $('#printAllInput').val(checkedValues);
    });
</script>