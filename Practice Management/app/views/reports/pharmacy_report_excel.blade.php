<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table class="table table-bordered">
    <thead>
    <tr>
        <th style="text-align: center;">Pharmacist</th>
        <th style="text-align: center;">No. Calls</th>
        <th style="text-align: center;">No. Pass</th>
        <th style="text-align: center;">No. Call Done</th>
        <th style="text-align: center;">No. Cancel</th>
    </tr>
    </thead>
    <tbody>
    @foreach($report as $val)
        <tr>
            <td>{{ucwords(strtolower($val['pharmacist']['full_name']))}}</td>
            <td>{{$val['numCalls']}}</td>
            <td>{{$val['numPass']}}</td>
            <td>{{$val['numCallsDone']}}</td>
            <td>{{$val['numCancel']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>