<div class="table-responsive">
    <table class="table table-bordered" id="example2" cellspacing="0">
        <thead>
        <tr style="background: lightslategray;">
            <th style="text-align: center;">Date Time</th>
            <th style="text-align: center;">Ticket Type</th>
            <th style="text-align: center;">Call Status</th>
            <th style="text-align: center;">Call By</th>
            <th style="text-align: center;">Call DT</th>
            <th style="text-align: center;">Pass DT</th>
            <th style="text-align: center;">Call From Pass By</th>
            <th style="text-align: center;">Call From Pass DT</th>
            <th style="text-align: center;">Call Done By</th>
            <th style="text-align: center;">Call Done DT</th>
            <th style="text-align: center;">Cancel By</th>
            <th style="text-align: center;">Cancel DT</th>
        </tr>
        </thead>
        <tbody>
        @foreach($report as $val)
            <tr>
                <td>{{$val['created_at']}}</td>
                <td>{{$val->ticketType['name']}}</td>
                <td>
                    @if($val['call_flag'] == 0)
                        Waiting
                    @elseif($val['call_flag'] == 1)
                        Call
                    @elseif($val['call_flag'] == 2)
                        Done
                    @elseif($val['call_flag'] == 3)
                        Pass
                    @elseif($val['call_flag'] == 4)
                        Cancel
                    @endif
                </td>
                <td>{{ucwords(strtolower($val->userCallBy['full_name']))}}</td>
                <td>{{$val['call_datetime']}}</td>
                <td>{{$val['pass_datetime']}}</td>
                <td>{{ucwords(strtolower($val->userCallFromPassBy['full_name']))}}</td>
                <td>{{$val['call_from_pass_datetime']}}</td>
                <td>{{ucwords(strtolower($val->userCallDoneBy['full_name']))}}</td>
                <td>{{$val['call_done_datetime']}}</td>
                <td>{{ucwords(strtolower($val->userCancelBy['full_name']))}}</td>
                <td>{{$val['cancel_datetime']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    {{ $report->links() }}
</div>

{{--<script>--}}
{{--$('#example2').DataTable({--}}
{{--"paging": false,--}}
{{--"lengthChange": true,--}}
{{--"searching": true,--}}
{{--"ordering": true,--}}
{{--"info": true,--}}
{{--"autoWidth": true,--}}
{{--"pageLength": 25,--}}
{{--"order": [[0, "asc"]],--}}
{{--"sScrollY": "400",--}}
{{--"sScrollX": "100%",--}}
{{--"sScrollXInner": "100%",--}}
{{--"bScrollCollapse": true--}}
{{--});--}}
{{--</script>--}}