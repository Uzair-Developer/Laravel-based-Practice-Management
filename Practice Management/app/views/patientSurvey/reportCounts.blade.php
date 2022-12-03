@extends('layout/main')

@section('title')
    - Patient Survey Report
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(function () {
            $('#example1').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true
            });

            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });

            $("#survey_id").change(function (e) {
                $("#question_id").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getQuestionBySurvey')}}',
                    method: 'POST',
                    data: {
                        survey_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#question_id").html('').removeAttr('disabled').html(data).select2();
                    }
                });
            });

            $("#selectHospital2").change(function (e) {
                $("#selectClinic2").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic2").removeAttr('disabled').html(data).select2();
                    }
                });
            });

            $("#selectClinic2").change(function (e) {
                $("#selectPhysician2").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinicId')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician2").removeAttr('disabled').html(data).select2();
                    }
                });
            });

            @if(Input::get('survey_id'))
            $("#question_id").attr('disabled', 'disabled');
            $.ajax({
                url: '{{route('getQuestionBySurvey')}}',
                method: 'POST',
                data: {
                    survey_id: '{{Input::get('survey_id')}}'
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#question_id").html('').removeAttr('disabled').html(data).select2();
                    @if(Input::get('question_id'))
                    $("#question_id").val('{{Input::get('question_id')}}').select2();
                    @endif



                }
            });

            @if(Input::get('hospital_id'))
            $.ajax({
                        url: '{{route('getClinicsByHospitalId')}}',
                        method: 'POST',
                        async: false,
                        data: {
                            hospital_id: '{{Input::get('hospital_id')}}'
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#selectClinic2").html(data).select2();
                            @if(Input::get('clinic_id'))
                            $("#selectClinic2").val('{{Input::get('clinic_id')}}').select2();
                            $.ajax({
                                url: '{{route('getPhysicianByClinicId')}}',
                                method: 'POST',
                                async: false,
                                data: {
                                    clinic_id: '{{Input::get('clinic_id')}}'
                                },
                                headers: {token: '{{csrf_token()}}'},
                                success: function (data) {
                                    $("#selectPhysician2").html(data).select2();
                                    @if(Input::get('physician_id'))
                                        $("#selectPhysician2").val('{{Input::get('physician_id')}}').select2();
                                    @endif



                                }
                            });
                            @endif



                        }
                    });
            @endif
            @endif



        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>
            Patient Survey Report
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        Search
                        <button type="button" class="btn btn-box-tool pull-right"
                                data-widget="collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                    <!-- /.box-header -->
                    {{Form::open(array('role'=>"form",'method' => 'GET'))}}
                    <div class="box-body">
                        <div class="form-group col-md-3">
                            <label>Survey *</label>
                            <select required autocomplete="off" name="survey_id" id="survey_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($surveys as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('survey_id') == $val['id'])
                                    selected @endif>{{$val['header_en']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Questions</label>
                            <select autocomplete="off" name="question_id" id="question_id" class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="clearfix"></div>

                        <div class="form-group col-md-3">
                            <label>Hospital</label>
                            <select id="selectHospital2" name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Clinic</label>
                            <br>
                            <select autocomplete="off" id="selectClinic2" name="clinic_id"
                                    class="form-control select2" style="width: 100%;">
                                <option value="">Choose</option>

                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Physician</label>
                            <select autocomplete="off" id="selectPhysician2" name="physician_id"
                                    class="form-control select2" style="width: 100%;">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="clearfix"></div>
                        <div class="form-group col-md-3">
                            <label>Res From Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('res_date_from')}}"
                                   name="res_date_from" class="form-control datepicker2">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Res To Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('res_date_to')}}"
                                   name="res_date_to" class="form-control datepicker2">
                        </div>

                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('reportCountsPatientSurvey')}}" class="btn btn-danger" type="button">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($inputs)
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-body">
                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('patientSurvey.print_excel'))
                                <div class="pull-right">
                                    {{Form::open(array('route' => 'printExcelPatientSurveyReportCounts'))}}
                                    @if(Input::except('_token'))
                                        @foreach(Input::except('_token') as $key => $val)
                                            <input type="hidden" name="{{$key}}" value="{{$val}}">
                                        @endforeach
                                    @endif
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-download"></i> Excel
                                    </button>
                                    {{Form::close()}}
                                </div>
                            @endif
                            <div class="col-md-12">
                                <label>Survey</label>

                                <div>
                                    {{$survey['header_en']}}
                                </div>
                            </div>
                            <br><br><br>
                            @foreach($report as $val)
                                <div class="col-md-12" style="padding-bottom: 20px;">
                                    <label>{{$val['title_en']}}</label>
                                    <?php
                                    $answer_type_data = $val['answer_type_data'];
                                    $answers = explode(',', $answer_type_data['answers_en']);
                                    ?>
                                    @foreach($answers as $k => $v)
                                        <div>
                                            {{$v}} [{{$val['patientCount'][$k]}}]
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
@stop