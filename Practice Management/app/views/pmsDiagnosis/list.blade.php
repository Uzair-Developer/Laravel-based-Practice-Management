@extends('layout/main')

@section('title')
    - PMS Diagnosis
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(function () {
            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('Are You Sure?')) {
                    window.location.replace($(this).attr('href'));
                }
            });

            $('#example1').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                'order': [[0, 'asc']],
                "sScrollY": "400px",
                "sScrollX": "100%",
                "sScrollXInner": "150%",
                "bScrollCollapse": true
            });


            $("#referred_to_parent_id").change(function (e) {
                $("#referred_to_child_id").attr('disabled', 'disabled');
                $.ajax({
                    url: "{{route('getChildReferredTo')}}",
                    method: 'POST',
                    data: {
                        id: $(this).val()
                    },
                    success: function (data) {
                        $("#referred_to_child_id").html(data).removeAttr('disabled').select2();
                    }
                });
            });

            @if(Input::get('referred_to_parent_id'))
            $("#referred_to_child_id").attr('disabled', 'disabled');
            $.ajax({
                url: "{{route('getChildReferredTo')}}",
                method: 'POST',
                data: {
                    id: '{{Input::get('referred_to_parent_id')}}'
                },
                success: function (data) {
                    $("#referred_to_child_id").html(data).val('{{Input::get('referred_to_child_id')}}')
                            .removeAttr('disabled').select2();
                }
            });
            @endif

        });
    </script>
@stop
@section('content')
    <section class="content-header">
        <h1>
            PMS Diagnosis
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
                            <label>Patient Id</label>
                            <input type="text" name="patient_pin" value="{{Input::get('patient_pin')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Phone</label>
                            <input type="text" maxlength="15" name="patient_phone"
                                   value="{{Input::get('patient_phone')}}"
                                   class="form-control">
                        </div>
                        <div class="form-group col-md-3">
                            <label>Patient Name</label>
                            <input type="text" name="patient_name" value="{{Input::get('patient_name')}}"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Speciality Referred To</label>
                            <br>
                            <select autocomplete="off" id="referred_to_parent_id" name="referred_to_parent_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($referred_to_parent as $val)
                                    <option value="{{$val['id']}}"
                                            @if(Input::get('referred_to_parent_id') == $val['id'])
                                            selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Sub Speciality Referred To</label>
                            <br>
                            <select autocomplete="off" id="referred_to_child_id" name="referred_to_child_id"
                                    class="form-control select2">
                                <option value="">Choose</option>

                            </select>
                        </div>


                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('pmsDiagnosis')}}" class="btn btn-info">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('pmsDiagnosis.list'))
                <div class="col-md-2">
                    <a href="{{route('addPmsDiagnosis')}}">
                        <button class="btn btn-block btn-default">Add PMS Diagnosis</button>
                    </a>
                    <br>
                </div>
            @endif
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <table class="table table-bordered" id="example1">
                            <thead>
                            <tr>
                                <th style="width: 15px">#</th>
                                <th>Patient Id</th>
                                <th>Patient Name</th>
                                <th>Patient Phone</th>
                                <th>Complain</th>
                                <th>Main System Affected</th>
                                <th>Organs</th>
                                <th>Primary Diagnosis</th>
                                <th>Speciality R To</th>
                                <th>Sub R To</th>
                                <th>Created By</th>
                                <th>Updated By</th>
                                <th>Options</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pmsDiagnosis as $val)
                                <tr>
                                    <td>{{$val['id']}}</td>
                                    <td>{{$val['patient_id']}}</td>
                                    <td>{{$val['patient_name']}}</td>
                                    <td>{{$val['patient_phone']}}</td>
                                    <td style="cursor: pointer;" class="showPopover" data-container="body"
                                        data-toggle="popover" data-placement="top"
                                        data-content="{{nl2br($val['complain'])}}">{{Functions::get_words($val['complain'])}}</td>
                                    <td>{{$val['main_system_affected_name']}}</td>
                                    <td style="cursor: pointer;" class="showPopover" data-container="body"
                                        data-toggle="popover" data-placement="top"
                                        data-content="{{($val['organ_name'])}}">{{Functions::get_words($val['organ_name'])}}</td>
                                    <td style="cursor: pointer;" class="showPopover" data-container="body"
                                        data-toggle="popover" data-placement="top"
                                        data-content="{{nl2br($val['primary_diagnosis'])}}">{{Functions::get_words($val['primary_diagnosis'])}}</td>
                                    <td>{{$val['referred_to_parent_name']}}</td>
                                    <td>{{$val['referred_to_child_name']}}</td>
                                    <td>{{$val['created_by_name']}}</td>
                                    <td>{{$val['updated_by_name']}}</td>
                                    <td>
                                        <div class="btn-group" style="width: 150px;">
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('pmsDiagnosis.edit'))
                                                <a class="btn btn-default"
                                                   href="{{route('editPmsDiagnosis', $val['id'])}}">Edit</a>

                                            @endif
                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('pmsDiagnosis.delete'))
                                                <a class="btn btn-danger ask-me"
                                                   href="{{route('deletePmsDiagnosis', $val['id'])}}">Delete</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{$pmsDiagnosis->appends(Input::except('_token'))->links()}}
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
        </div>
    </section>
@stop