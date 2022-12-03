@foreach($events as $key => $val)
    <div class="box box-primary collapsed-box">
        <div class="box-header">
            Event #{{$val['id']}}: {{$val['created_at']}}
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-plus"></i></button>
            </div>
        </div>
        <div class="box-body" style="display: none;">
            <div class="form-group">
                <label>
                    @if($val['status'] == \core\diagnosis\enums\EventStatus::success)
                        <span style="color: green;" class="pull-right">Status: Success </span>
                    @else
                        <span style="color: red;" class="pull-right">Status: Pending </span> @endif
                </label>
                @if($val['event_details'])

                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th>Event Type</th>
                            <th>Reference</th>
                            <th>Response</th>
                        </tr>
                        @foreach($val['event_details'] as $key2 => $val2)
                            <tr>
                                <td>
                                    @if($val2['event_type'] == \core\diagnosis\enums\EventDetailsTypes::symptoms)
                                        <span style="color: red">Symptom</span>
                                    @elseif($val2['event_type'] == \core\diagnosis\enums\EventDetailsTypes::diseases)
                                        <span style="color: #db0ead">Disease</span>
                                    @elseif($val2['event_type'] == \core\diagnosis\enums\EventDetailsTypes::questions)
                                        <span style="color: green">Question</span>
                                    @endif
                                </td>
                                <td>
                                    @if($val2['event_type'] == \core\diagnosis\enums\EventDetailsTypes::symptoms)
                                        {{ Symptom::getName($val2['reference_id']) }}
                                    @elseif($val2['event_type'] == \core\diagnosis\enums\EventDetailsTypes::diseases)
                                        {{ Disease::getName($val2['reference_id']) }}
                                    @elseif($val2['event_type'] == \core\diagnosis\enums\EventDetailsTypes::questions)
                                        {{ DiseaseQuestions::getName($val2['reference_id']) }}
                                    @endif
                                </td>
                                <td>
                                    @if($val2['event_type'] == \core\diagnosis\enums\EventDetailsTypes::questions)
                                        @if($val2['response'] == 1) Yes @else No @endif
                                    @elseif($val2['event_type'] == \core\diagnosis\enums\EventDetailsTypes::diseases)
                                        {{$val2['response']}}%
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
            @if($val['agent_comment'])
                <div class="form-group">
                    <label>Agent Comment</label>

                    <div>{{$val['agent_comment']}}</div>
                </div>
            @endif
        </div>
    </div>
@endforeach
