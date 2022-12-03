<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="width: 15px">#</th>
            <th>Clinic Name</th>
            <th>Physician Name</th>
            <th>Reason</th>
            <th>Effect?</th>
            <th>F Date</th>
            <th>T Date</th>
            <th>F Time</th>
            <th>T Time</th>
            <th>Schedule Times</th>
            <th>Notes</th>
            <th>Created By</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($exceptions as $key => $val)
            <tr>
                <td>{{$val['id']}}</td>
                <td>{{$val['clinic_name']}}</td>
                <td>{{$val['physician_name']}}</td>
                <td>{{$val['reason_name']}}</td>
                <td>{{$val['effect'] == 1 ? 'Yes' : 'No'}}</td>
                <td>{{$val['from_date']}}</td>
                <td>{{$val['to_date']}}</td>
                <td>{{$val['from_time']}}</td>
                <td>{{$val['to_time']}}</td>
                <td>{{str_replace(' ', ' to ', $val['schedule_times'])}}</td>
                <td>{{$val['notes']}}</td>
                <td>{{$val['create_name']}}</td>
                <td>
                    @if($val['status'] == 1)
                        Approved
                    @elseif($val['status'] == 2)
                        Rejected
                    @elseif($val['status'] == 0)
                        Pending
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

