@extends('layout/main')

@section('title')
    - Patient Survey
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

            $(".viewSurveyBtn").click(function (e) {
                e.preventDefault();
                $("#patientSurveyWrapper").html("");
                var ele = $(this);
                var ref_id = ele.attr("data-ref-id");
                $.ajax({
                    url: '{{route('viewPatientSurvey')}}',
                    method: 'GET',
                    data: {
                        id: ref_id
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (data) {
                            $("#patientSurveyWrapper").html(data);
                            $("#viewPatientSurvey").modal('show');
                        }
                    }
                });
            })
        });
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>
            Patient Survey
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
                            <label>Survey</label>
                            <select name="survey_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($surveys as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('survey_id') == $val['id'])
                                    selected @endif>{{$val['header_en']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Reservation Code</label>
                            <input type="text" name="code" value="{{Input::get('code')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Id</label>
                            <input type="text" name="id" value="{{Input::get('id')}}" class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Phone</label>
                            <input type="text" maxlength="15" name="phone" value="{{Input::get('phone')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Name</label>
                            <input type="text" name="name" value="{{Input::get('name')}}" class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>National Id</label>
                            <input type="text" name="national_id" value="{{Input::get('national_id')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3" style="margin-bottom: 5px">
                            <label>Date From</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('date_from')}}"
                                   name="date_from" class="form-control datepicker2">
                        </div>
                        <div class="form-group col-md-3" style="margin-bottom: 5px">
                            <label>Date To</label>
                            <input type="text" data-date-format="yyyy-mm-dd" value="{{Input::get('date_to')}}"
                                   name="date_to" class="form-control datepicker2">
                        </div>

                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th>Survey</th>
                                    <th>Reservation Code</th>
                                    <th>Patient Name</th>
                                    <th>Patient Phone</th>
                                    <th>Pin No.</th>
                                    <th>Date Created</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($patientSurvey as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['survey_title']}}</td>
                                        <td>{{$val['reservation_code']}}</td>
                                        <td>{{($val['patient_name'])}}</td>
                                        <td>{{$val['patient_phone']}}</td>
                                        <td>{{$val['patient']['registration_no']}}</td>
                                        <td>{{$val['created_at']}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 150px;">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('patientSurvey.view'))
                                                    <a class="btn btn-warning viewSurveyBtn"
                                                       data-ref-id="{{$val['id']}}" style="cursor: pointer">View</a>

                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$patientSurvey->appends(Input::except('_token'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="viewPatientSurvey" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">View Patient Survey</h4>
                </div>
                {{Form::open(array('role'=>"form"))}}
                <div class="modal-body col-md-12" id="patientSurveyWrapper">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop