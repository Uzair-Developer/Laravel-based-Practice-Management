<div class="col-md-12">
    <div class="box box-primary">
        <div class="box-header">
            <h4>{{$survey['header_en']}}</h4>

            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="box-body">
            @foreach($patientSurveys['groups'] as $k => $group)
                    <div class="form-group col-md-12">
                        <strong style="color: black;font-size: 20px;">
                                <label>{{$group['title']}}</label>
                        </strong>
                        @foreach($group['questions'] as $question)
                            <div>
                                <label style="font-weight: bold;">
                                        {{$question['title']}}
                                </label>
                                <div>
                                    <span style="color: blue"> &rightarrow; {{ $question['answer'] }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
            @endforeach
        </div>
    </div>
</div>