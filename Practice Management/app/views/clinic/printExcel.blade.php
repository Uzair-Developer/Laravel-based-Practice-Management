<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<table>
    <tbody>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Code</th>
    </tr>
    @foreach($clinics as $clinic)
        <tr>
            <td>{{$clinic['id']}}</td>
            <td>{{$clinic['name']}}</td>
            <td>{{$clinic['code']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>