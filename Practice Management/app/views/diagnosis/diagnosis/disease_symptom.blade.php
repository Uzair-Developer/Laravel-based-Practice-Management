<table class="table table-bordered">
    <thead>
    <tr>
        <th style="width: 15px">#</th>
        <th>Disease name</th>
        <th>Symptom name</th>
        <th>Rate</th>
        <th>Entry by</th>
        <th>Status</th>
        <th>Cancel Note</th>
        <th>Options</th>
    </tr>
    </thead>
    <tbody>
    @foreach($diseaseSymptom as $val)
            <tr id="tr_diseaseSymptom_{{$val['id']}}">
                <td>{{$val['id']}}</td>
                <td>{{$val['disease_name']}}</td>
                <td>{{$val['symptom_name']}}</td>
                <td>{{$val['rate']}}</td>
                <td>{{$val['user_name']}}</td>
                <td>
                    @if($val['status'] == 1)
                        Approval
                    @elseif($val['status'] == 0)
                        Pending
                    @elseif($val['status'] == 2)
                        Cancel
                    @endif
                </td>
                <td>{{nl2br($val['cancel_note'])}}</td>
                <td>
                    <button ref_id="{{$val['id']}}"
                            class="btn btn-danger deleteDiseaseSymptom"><i
                                class="fa fa-times"></i></button>
                </td>
            </tr>
    @endforeach
    </tbody>
</table>
{{$diseaseSymptom->appends(Input::except('_token'))->links()}}