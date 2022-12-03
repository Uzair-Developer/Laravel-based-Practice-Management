@extends('layout/main')

@section('title')
    - Import Excel
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>
    <script>
        $(function () {
//            //Initialize Select2 Elements
//            $(".select2").select2();
            $('.datepicker2').datepicker({
                todayHighlight: true,
                autoclose: true
            });

            $("#selectHospital2").change(function (e) {
                $("#clinic_id").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#clinic_id").removeAttr('disabled').html(data).select2();
                    }
                });
            });

            $("#clinic_id").change(function () {
                $("#clinic_schedule_id").attr('disabled', true);
                $.ajax({
                    url: '{{route('getPhysicianByClinic')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $(this).val()
                    },
                    success: function (data) {
                        $("#clinic_schedule_id").html(data.schedulesHtml).attr('disabled', false).select2();
                    }
                });
            });

        });
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Import Excel
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        Download Template
                    </div>
                    <!-- form start -->
                    {{Form::open(array('role'=>"form",'files' => true, 'route' => 'downloadExcelPhysicianSchedule'))}}
                    <div class="box-body">

                        <div class="form-group col-md-4">
                            <label>Hospital</label>
                            <select autocomplete="off" id="selectHospital2" required name="hospital_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Clinics</label>
                            <br>
                            <select autocomplete="off" id="clinic_id" name="clinic_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label>Clinic Schedules</label>
                            <select id="clinic_schedule_id" name="clinic_schedule_id"
                                    class="form-control select2">

                            </select>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Download Excel</button>
                        <a href="{{route('physicianSchedules')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>

            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header">
                        Import Template
                    </div>
                    <!-- form start -->
                    {{Form::open(array('role'=>"form",'files' => true, 'route' => 'postImportExcelPhysicianSchedule'))}}
                    <div class="box-body">

                        <div class="form-group col-md-6">
                            <label>Import Template File</label>
                            <input required type="file" name="template" class="form-control">
                        </div>
                        <div class="form-group col-md-6">
                            <label>Split With Months?</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="split"
                                           type="radio" autocomplete="off" value="1"> Yes
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="split" checked
                                           type="radio" autocomplete="off" value="2"> No
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Import Excel</button>
                        <a href="{{route('physicianSchedules')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop