<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<table>
    <thead>
    <tr>
        <th>#</th>
        <th>Full Name</th>
        <th>User Name</th>
        <th>Role Name</th>
        <th>Group Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Mobile</th>
        <th>Address</th>
        <th>Extension No</th>
    </tr>
    </thead>
    <tbody>
    @foreach($users as $user)
        <tr>
            <td>{{$user['id']}}</td>
            <td>{{$user['full_name']}}</td>
            <td>{{$user['user_name']}}</td>
            <td>{{$user['role_name']}}</td>
            <td>{{$user['group_name']}}</td>
            <td>{{$user['email']}}</td>
            <td>{{$user['phone_number']}}</td>
            <td>{{$user['mobile1']}}</td>
            <td>{{$user['address']}}</td>
            <td>{{$user['extension_num']}}</td>
        </tr>
    @endforeach
    </tbody>
</table>