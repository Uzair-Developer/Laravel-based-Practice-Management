<table class="table table-bordered">
    <thead>
    <tr>
        <th style="text-align: center;">Hospital</th>
        <th style="text-align: center;">Clinic</th>
        <th style="text-align: center;">Doctor Name</th>
        <th style="text-align: center;">Date</th>
        <th style="text-align: center;">Scheduled T(H:M)</th>
        <th style="text-align: center;">Exceptions T(H:M)</th>
        <th style="text-align: center;">Actual T(H:M)</th>
        <th style="text-align: center;">Estimated Visits</th>
        <th style="text-align: center;">Current Reservations</th>
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
        <tr style="text-align: center;">
            <td>{{$val['hospital_name']}}</td>
            <td>{{$val['clinic_name']}}</td>
            <td>{{$val['physician_name']}}</td>
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
    <tr style="text-align: center;background: #a59d9d;">
        <td>{{$report['total_hospitals']}}</td>
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
    </tbody>
</table>