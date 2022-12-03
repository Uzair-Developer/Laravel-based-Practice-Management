<div class="box">
    <!-- /.box-header -->
    <div class="box-header">
        <b>Totals Summary</b>
        <button type="button" class="btn btn-box-tool pull-right" data-widget="collapse">
            <i class="fa fa-minus"></i></button>
    </div>
    <div class="box-body">
        <div>
            <div class="form-group col-md-3">
                <label>Scheduled T(H:M): {{Functions::convertToHoursMins($report['total_schedule_time'])}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>Exceptions T(H:M): {{Functions::convertToHoursMins($report['total_exception_time'])}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>Actual T(H:M): {{Functions::convertToHoursMins($report['total_work_time'])}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>Estimated Visits: {{$report['total_estimate']}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>Total Reservations: {{$report['total_all_visits']}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>Attended Patients: {{$report['total_visits']}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>No Show: {{$report['total_no_show']}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>No Show Rate: {{$report['total_no_show_rate']}} %</label>
            </div>
            <div class="form-group col-md-3">
                <label>Booked Visit: {{$report['totalBooked']}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>On Wait List: {{$report['totalWaitingList']}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>Revisits: {{$report['totalRevisits']}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>Revisits Attended: {{$report['totalRevisitAttend']}}</label>
            </div>
            <div class="form-group col-md-3">
                <label>Revisits No Show: {{$report['totalRevisitNoShow']}}</label>
            </div>
            @if (app('production'))
                <div class="form-group col-md-3">
                    <label>Patient Paid: {{$report['total_patient_paid']}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label>Paid - Attended: {{$report['total_attend_sub_paid']}}</label>
                </div>
                <div class="form-group col-md-3">
                    <label style="cursor: pointer;" class="showPopover" data-container="body"
                           data-toggle="popover" data-placement="top"
                           data-content="Patient Paid / Actual Time">PT Seen/Hour: {{$report['total_PTSeenPerHour']}}</label>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="box">
    <!-- /.box-header -->
    <div class="box-body">
        <table class="table table-bordered" id="example1" cellspacing="0">
            <thead>
            <tr style="background: lightslategray;">
                {{--<th style="text-align: center;">Hospital</th>--}}
                <th style="text-align: center;">Clinic</th>
                <th style="text-align: center;">Doctor Name</th>
                <th style="text-align: center;">Date</th>
                <th style="text-align: center;">Scheduled T(H:M)</th>
                <th style="text-align: center;">Exceptions T(H:M)</th>
                <th style="text-align: center;">Actual T(H:M)</th>
                <th style="text-align: center;">Estimated Visits</th>
                <th style="text-align: center;">Total Reservations</th>
                <th style="text-align: center;">Attended Patients</th>
                <th style="text-align: center;">No Show</th>
                <th style="text-align: center;">No Show Rate</th>
                <th style="text-align: center;">Booked Visit</th>
                <th style="text-align: center;">On Wait List</th>
                <th style="text-align: center;">Revisits</th>
                <th style="text-align: center;">Revisits Attended</th>
                <th style="text-align: center;">Revisits No Show</th>
                @if (app('production'))
                    <th style="text-align: center;">Patient Paid</th>
                    <th style="text-align: center;">Paid - Attended</th>
                    <th style="text-align: center;">PT Seen/Hour</th>
                @endif
            </tr>
            </thead>
            <tbody>
            @foreach($report['data'] as $val)
                <tr>
                    {{--                        <td>{{$val['hospital_name']}}</td>--}}
                    <td>{{$val['clinic_name']}}</td>
                    <td>
                        {{$val['physician_name']}}
                    </td>
                    <td>{{$val['date']}}</td>
                    <td>{{Functions::convertToHoursMins($val['schedule_time'])}}</td>
                    <td>{{Functions::convertToHoursMins($val['exception_time'])}}</td>
                    <td>{{Functions::convertToHoursMins($val['work_time'])}}</td>
                    <td>{{$val['estimate_visits']}}</td>
                    <td>{{$val['allVisits']}}</td>
                    <td>{{$val['patientVisits']}}</td>
                    <td>{{$val['noShow']}}</td>
                    <td>{{$val['noShowRate']}}</td>
                    <td>{{$val['countBooking']}}</td>
                    <td>{{$val['countWaitingList']}}</td>
                    <td>{{$val['allRevisit']}}</td>
                    <td>{{$val['revisitAttend']}}</td>
                    <td>{{$val['revisitNoShow']}}</td>
                    @if (app('production'))
                        <td>{{$val['patientPaid']}}</td>
                        <td>{{$val['attendSubPaid']}}</td>
                        <td>{{$val['PTSeenPerHour']}}</td>
                    @endif
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr class="no-sort"
                style="background: lightslategray; font-weight: bold; font-size: 16px;text-align: center;">
                {{--                    <td>{{$report['total_hospitals']}}</td>--}}
                <td>{{$report['total_clinics']}}</td>
                <td>{{$report['total_physicians']}}</td>
                <td>{{$report['days_count'] + 1}}</td>
                <td>{{Functions::convertToHoursMins($report['total_schedule_time'])}}</td>
                <td>{{Functions::convertToHoursMins($report['total_exception_time'])}}</td>
                <td>{{Functions::convertToHoursMins($report['total_work_time'])}}</td>
                <td>{{$report['total_estimate']}}</td>
                <td>{{$report['total_all_visits']}}</td>
                <td>{{$report['total_visits']}}</td>
                <td>{{$report['total_no_show']}}</td>
                <td>{{$report['total_no_show_rate']}}</td>
                <td>{{$report['totalBooked']}}</td>
                <td>{{$report['totalWaitingList']}}</td>
                <td>{{$report['totalRevisits']}}</td>
                <td>{{$report['totalRevisitAttend']}}</td>
                <td>{{$report['totalRevisitNoShow']}}</td>
                @if (app('production'))
                    <td>{{$report['total_patient_paid']}}</td>
                    <td>{{$report['total_attend_sub_paid']}}</td>
                    <td>{{$report['total_PTSeenPerHour']}}</td>
                @endif
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    $('#example1').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "pageLength": 100,
        "sScrollY": "400",
        "sScrollX": "100%",
        "sScrollXInner": "200%",
        "bScrollCollapse": true
//        "fixedHeader": true
    });
</script>