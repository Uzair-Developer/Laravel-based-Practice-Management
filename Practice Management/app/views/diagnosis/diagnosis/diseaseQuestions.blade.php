<div class="col-md-6">
    {{Form::open(array('role'=>"form", 'id' => 'diseaseQuestionForm'))}}
    @foreach($diseases as $key => $val)
        @if(count($val['questions']))
            <div class="box box-primary">
                <div class="box-header">
                    {{$val['name']}}
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    @foreach($val['questions'] as $key => $val2)
                        <div class="form-group col-md-12">
                            <label>{{$val2['text']}}</label>

                            <div class="radio">
                                <label>
                                    <input type="radio" value="0"
                                           name="{{$val['name']}}[{{$val2['id']}}]">
                                    Yes
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" value="{{$val2['score']}}" name="{{$val['name']}}[{{$val2['id']}}]">
                                    No
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
    <div class="box-footer">
        <button class="btn btn-primary" type="submit">Check the final result</button>
    </div>
    {{Form::close()}}
</div>
<div class="col-md-6">
    <div class="box box-primary">
        <!-- form start -->
        <div class="box-body">
            <div class="form-group col-md-12">
                <label>Possible diseases</label>

                <div id="diseasesDiv">
                    {{$diseasesResult}}
                </div>
            </div>
        </div>
        <!-- /.box-body -->

    </div>
</div>

<script>
    $("#diseaseQuestionForm").submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{route('postStartDiagnosis4')}}",
            method: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                if (data == 0) {
                    alert('Your Session Has Expired');
                    $(".tab-pane , .tab-li").removeClass('active');
                    $("#tab_0 , #li_0").addClass('active');
                    $("#tab_2, #tab_3, #tab_4").html('');
                    $("#phone, #address, #allergy_environments, #patient_id, #national_id, #id, #name, #birthday, #email, #phone2, #health_insurance, #allergy_drug").val('');
                    $("#female, #male").removeAttr('checked');
                    $("#diseasesDiv, #notes, #family_history, #past_history, #social_history").html('');
                    $(".select2").val('').select2();
                } else {
                    $("#tab_4").html('').html(data);
                    alert('Questions saved successfully');
                    $(".tab-pane , .tab-li").removeClass('active');
                    $("#tab_4 , #li_4").addClass('active');
                }
            }
        });
    });
</script>
