@extends('layout/main')

@section('title')
    - Physicians
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop


@section('footer')
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
    <script>

        $(".ask-me").click(function (e) {
            e.preventDefault();
            msg = $(this).attr('show-msg');
            if (msg) {
                if (confirm('Are You Sure? (' + msg + ')')) {
                    window.location.replace($(this).attr('href'));
                }
            } else {
                if (confirm('Are You Sure?')) {
                    window.location.replace($(this).attr('href'));
                }
            }
        });

        $('#example1').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true
        });
        $(function () {
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

            $(".backToDrBtn, .upPublishBtn").click(function (e) {
                e.preventDefault();
                var href = $(this).attr('href');
                $("#headNotesForm").attr('action', href);
                $("#headNotesModal").modal('show');
            });

            @if(Input::get('hospital_id'))
            $.ajax({
                        url: '{{route('getClinicsByHospitalId')}}',
                        method: 'POST',
                        data: {
                            hospital_id: $('#selectHospital2').val()
                        },
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#selectClinic2").html(data).select2();
                            @if(Input::get('clinic_id'))
                            $("#selectClinic2").val('{{Input::get('clinic_id')}}').select2();
                            @endif


                        }
                    });
            @endif


        })
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            List Physicians
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
                            <select id="selectClinic2" name="clinic_id" class="form-control select2">
                                <option value="">Choose</option>
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label>Physician Name</label>
                            <input type="text" name="name" value="{{Input::get('name')}}"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Profile Status</label>
                            <select name="current_status" class="form-control select2">
                                <option value="">Choose</option>
                                <option value="0" @if(Input::get('current_status') === '0')
                                selected @endif>No Actions
                                </option>
                                <option value="1" @if(Input::get('current_status') == 1)
                                selected @endif>Need To Approved
                                </option>
                                <option value="2" @if(Input::get('current_status') == 2)
                                selected @endif>Published
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                        <a href="{{route('physicians')}}" class="btn btn-default">Clear</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="example1">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th>Full Name</th>
                                    <th>Activated?</th>
                                    <th>Clinics</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($physicians as $physician)
                                    <tr>
                                        <td>{{$physician['id']}}</td>
                                        <td>
                                            <div style="width: 250px;">{{$physician['full_name']}}</div>
                                        </td>
                                        <td>@if($physician['activated'] == 1)
                                                Yes
                                            @else
                                                No
                                            @endif
                                        </td>
                                        <td>{{$physician['clinic_name']}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 250px;">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('physician.edit'))
                                                    <a class="btn btn-default"
                                                       href="{{route('editPhysician', $physician['id'])}}">Edit</a>
                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('physician.changeStatus'))
                                                    @if($physician['activated'] == 1)
                                                        <a class="btn btn-danger ask-me"
                                                           @if($physician['user_type_id'] == 7)
                                                           show-msg="All Reservations Will Convert To
                                                                be Cancelled"
                                                           @endif
                                                           href="{{route('changeStatus', $physician['id'])}}">
                                                            Deactivate</a>
                                                    @else
                                                        <a class="btn btn-default ask-me"
                                                           href="{{route('changeStatus', $physician['id'])}}">
                                                            Activate</a>
                                                    @endif
                                                @endif
                                                <?php $userData = Sentry::findUserByID($physician['id']); ?>
                                                @if($c_user->user_type_id == 1
         /*if physician and head*/              || ($c_user->user_type_id == 7 && $c_user->hasAccess('head_dept.access') && !$userData->hasAccess('head_dept.access'))
         /*if clinic manger and head*/          || ($c_user->user_type_id == 3 && $c_user->hasAccess('head_dept.access') && $userData->hasAccess('head_dept.access')))
                                                    @if($c_user->id != $physician['id'])
                                                        @if(isset($physician['physicianData']['current_status']) &&
                                                        $physician['physicianData']['current_status'] == 1)
                                                            <a class="btn btn-default ask-me"
                                                               href="{{route('changeProfileStatus', array($physician['id'], 'publish'))}}">
                                                                Publish</a>
                                                        @endif
                                                        @if(isset($physician['physicianData']['current_status']) &&
                                                        $physician['physicianData']['current_status'] == 1)
                                                            <a class="btn btn-default backToDrBtn"
                                                               href="{{route('changeProfileStatus', array($physician['id'], 'back-to-dr'))}}">
                                                                Back To Dr</a>
                                                        @endif
                                                        @if(isset($physician['physicianData']['current_status']) &&
                                                        $physician['physicianData']['current_status'] == 2)
                                                            <a class="btn btn-warning upPublishBtn"
                                                               href="{{route('changeProfileStatus', array($physician['id'], 'un-publish'))}}">
                                                                Un Publish</a>
                                                        @endif
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="headNotesModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Add Notes</h4>
                </div>
                {{Form::open(array('role'=>"form", 'id' => 'headNotesForm', 'method' => 'GET'))}}
                <div class="modal-body col-md-12">
                    <div class="form-group col-md-12">
                        <label>Notes</label>
                        <textarea required name="head_notes" class="form-control"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop