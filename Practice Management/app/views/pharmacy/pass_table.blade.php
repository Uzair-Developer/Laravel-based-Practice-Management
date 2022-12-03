<table id="example1" class="table table-bordered">
    <thead>
    <tr>
        <th>Code</th>
        <th>Options</th>
    </tr>
    </thead>
    <tbody>

    @foreach($queue_pass as $val)
        <tr>
            <td>{{$val['queue_code']}}</td>
            <td>
                <div class="btn-group" style="width: 150px;">
                        <a id="callFromPass" ref_id="{{$val['id']}}"
                           class="btn btn-default">Call</a>
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>