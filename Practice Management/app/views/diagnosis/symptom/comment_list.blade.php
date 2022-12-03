<table class="table table-bordered">
    <tbody>
    <tr>
        <th style="width: 15px">#</th>
        <th>Symptom Name</th>
        <th>User Name</th>
        <th>Status</th>
        <th>Comment</th>
        <th>Actions</th>
    </tr>
    @foreach($comments as $val)
        <tr id="tr_comment_{{$val['id']}}">
            <td>{{$val['id']}}</td>
            <td>{{$val['symptom_name']}}</td>
            <td>{{$val['user_name']}}</td>
            <td>
                @if($val['status'] == 1)
                    New
                @elseif($val['status'] == 2)
                    Pending
                @elseif($val['status'] == 3)
                    Read
                @endif
            </td>
            <td>{{$val['comment']}}</td>
            <td>
                <button ref_id="{{$val['id']}}" class="btn btn-danger deleteComment"><i class="fa fa-times"></i></button>
            </td>
        </tr>
    @endforeach
    @if(empty($comments->toArray()['data']))
        <td colspan="7">
            <center>No Records!</center>
        </td>
    @endif
    </tbody>
</table>
{{$comments->appends(Input::except('_token'))->links()}}