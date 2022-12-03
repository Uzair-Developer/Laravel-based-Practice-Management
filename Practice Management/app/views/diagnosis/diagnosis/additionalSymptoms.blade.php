<div class="col-md-6">
    {{Form::open(array('role'=>"form", 'id' => 'additionalSymptomForm'))}}
    <?php
    $isFound = false;
    $symptomsView = array();
    ?>
    @foreach($diseases as $key => $val)
        @if(count($val['symptoms']))
            <?php $isFound = true; ?>
            <div class="box box-primary">
                <div class="box-header">
                    {{$val['name']}}
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                    class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    @foreach($val['symptoms'] as $key => $val2)
                        @if(!in_array($val2['id'], $symptomsView))
                            <?php $symptomsView[] = $val2['id']; ?>
                            <div class="form-group col-md-12">
                                <label>Do you have "{{$val2['name']}}"?</label>

                                <div class="radio">
                                    <label>
                                        <input type="radio" value="{{$val2['pivot']['rate']}}"
                                               name="{{$val['name']}}[{{$val2['id']}}]">
                                        Yes
                                    </label>
                                </div>
                                <div class="radio">
                                    <label>
                                        <input type="radio" value="0" name="{{$val['name']}}[{{$val2['id']}}]">
                                        No
                                    </label>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
    @if(!$isFound)
        <div class="box box-primary">
            <div class="box-header">
                No Questions
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <h4>No Questions To show!</h4>
            </div>
        </div>
    @endif
    <div class="">
        <button class="btn btn-primary" type="submit">Continue to: step4</button>
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
    $("#additionalSymptomForm").submit(function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{route('postStartDiagnosis3')}}",
            method: 'POST',
            data: $(this).serialize(),
            success: function (data) {
                if (data == 0) {
                    alert('Your Session Has Expired');
                } else if (data == 1) {
                    $.ajax({
                        url: "{{route('startDiagnosis4')}}",
                        method: 'GET',
                        success: function (data) {
                            $("#tab_3").html('').html(data);
                            alert('Additional symptoms saved successfully');
                            $(".tab-pane , .tab-li").removeClass('active');
                            $("#tab_3 , #li_3").addClass('active');
                        }
                    });
                }
            }
        });
    });
</script>
