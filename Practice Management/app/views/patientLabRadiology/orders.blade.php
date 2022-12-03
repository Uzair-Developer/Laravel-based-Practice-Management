<table class="table table-bordered">
    <thead>
    <tr>
        <th>#</th>
        <th>Type</th>
        <th>Test Name</th>
        <th>Created At</th>
        <th>Finished?</th>
    </tr>
    </thead>
    <tbody>
    @foreach($patientLapRadiology as $val)
        <tr>
            <td>{{$val['id']}}</td>
            <td>{{$val['station']}}</td>
            <td>{{$val['test_name']}}</td>
            <td>{{$val['datetime']}}</td>
            <td>{{$val['verifieddatetime'] ?
                                        '<span style="color: green">' . $val['verifieddatetime'] . '</span>'
                                        : '<span style="color: red">Not Yet</span>'}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>