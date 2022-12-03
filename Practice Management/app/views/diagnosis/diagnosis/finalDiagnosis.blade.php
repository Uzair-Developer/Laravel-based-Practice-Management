<div class="col-md-6">
    <div class="box box-primary">
        <div class="box-header">
            <h3>Patient information</h3>
        </div>
        <div class="box-body">
            <div class="form-group col-md-12">
                <label>Patient name</label>

                <div>{{$patientInfo['name']}}</div>
            </div>
            <div class="form-group col-md-6">
                <label>Patient phone</label>

                <div>{{$patientInfo['phone']}}</div>
            </div>
            <div class="form-group col-md-6">
                <label>Patient age</label>

                <div>{{$patientInfo['age']}} Years old</div>
            </div>
            <div class="form-group col-md-12">
                <label>Agent Comment</label>
                <textarea id="agent_comment" class="form-control" name="agent_comment"></textarea>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a class="btn btn-primary" id="startDiagnosis">Start diagnosis again?</a>
    </div>
</div>
<div class="col-md-6">
    <div class="box box-primary">
        <!-- form start -->
        <div class="box-body">
            <div class="form-group col-md-12">
                <label>Possible diseases</label>

                <div id="diseasesDiv">
                    {{$diseases}}
                </div>
            </div>
        </div>
        <!-- /.box-body -->

    </div>
</div>
<script>
    $("#startDiagnosis").click(function (e) {
        e.preventDefault();
        $.ajax({
            url: "{{route('diagnosisStartAgain')}}",
            method: 'POST',
            data: { agent_comment: $('#agent_comment').val() },
            success: function (data) {
                $('#patientEvents').hide();
                $('#eventsCount').html(0);
                $('#myModalBody').html('');
                $('#myModalLabel').html('');
                $(".tab-pane , .tab-li").removeClass('active');
                $("#tab_0 , #li_0").addClass('active');
                $("#tab_2, #tab_3, #tab_4").html('');
                $("#phone, #address, #allergy_environments, #patient_id, #national_id, #id, #name, #birthday, #email, #phone2, #health_insurance, #allergy_drug").val('');
                $("#female, #male").removeAttr('checked');
                $("#diseasesDiv, #notes, #family_history, #past_history, #social_history").html('');
                $(".select2").val('').select2();
            }
        });
    });
</script>