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
                    {{Form::open(array('role'=>"form",'files' => true, 'route' => 'downloadExcelClinicSchedule'))}}
                    <div class="box-body">

                        <div class="form-group col-md-4">
                            <label>Hospital</label>
                            <select required name="hospital_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($hospitals as $val)
                                    <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Download Excel</button>
                        <a href="{{route('clinicSchedules')}}" class="btn btn-info" type="submit">Back</a>
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
                    {{Form::open(array('role'=>"form",'files' => true, 'route' => 'postImportExcelClinicSchedule'))}}
                    <div class="box-body">

                        <div class="form-group col-md-6">
                            <label>Import Template File</label>
                            <input required type="file" name="template" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Start Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('start_date')}}"
                                   name="start_date" class="form-control datepicker2">
                        </div>

                        <div class="form-group col-md-3">
                            <label>End Date</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('end_date')}}"
                                   name="end_date" class="form-control datepicker2">
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Import Excel</button>
                        <a href="{{route('clinicSchedules')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop