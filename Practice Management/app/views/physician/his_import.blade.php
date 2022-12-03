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
            "ordering": false,
            "info": true,
            "autoWidth": true
        });
        $(function () {
            var checkedArray = new Array();
            $(document).on('change', '.getPhysicianCheckbox', function () {
                if ($(".getPhysicianCheckbox:checked").length > 0) {
                    $("#savePhysicians").show();
                }
                else {
                    $("#savePhysicians").hide();
                }
                var checkedValues = $('.getPhysicianCheckbox:checked').map(function () {
                    return $(this).attr('physician_his');
                }).get();
                $('#getPhysicianInput').val(checkedValues);
            });

            $("#savePhysicians").click(function (e) {
                if (confirm('Are You Sure?')) {
                    $.ajax({
                        url: "{{route('getPhysiciansFromHIS')}}",
                        method: 'POST',
                        data: {
                            physician_ids: $("#getPhysicianInput").val(),
                            hospital_id: $("#selectHospital2").val()
                        },
                        success: function (data) {
                            if (data.success == 'yes') {
                                $("#updateArabicLabel").html('').html(data.message);
                                $("#updateArabicBody").html('').html(data.modal_html);
                                $("#updateArabicModal").modal('show');
                            } else {
                                alert(data.message);
                            }
                        }
                    });
                }
            });

            $(document).on('click', '.activateBtn', function () {
                if (confirm('Are You Sure?')) {
                    var physician_id = $(this).attr('physician_id');
                    $.ajax({
                        url: "{{route('getActivatePhysicianFromHISForm')}}",
                        method: 'POST',
                        data: {
                            physician_id: physician_id
                        },
                        success: function (data) {
                            if (data.success == 'yes') {
                                $("#updateArabicLabel2").html('').html(data.message);
                                $("#updateArabicBody2").html('').html(data.modal_html);
                                $("#updateArabicModal2").modal('show');
                            } else {
                                alert(data.message);
                            }
                        }
                    });
                }
            });

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
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if(isset($inputs['hospital_id']) && $inputs['hospital_id'])
                <div class="col-md-12">
                    <div class="box">
                        <div class="box-header">
                            <button class="btn btn-info" id="savePhysicians" style="display: none">Save</button>
                            <input type="hidden" id="getPhysicianInput">
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <div class="table-responsive">
                                <table class="table table-bordered" id="example1">
                                    <thead>
                                    <tr>
                                        <th style="width: 15px"></th>
                                        <th>Full Name</th>
                                        <th>Clinics</th>
                                        <th>New?</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($his_physicians as $val)
                                        <tr>
                                            <td>
                                                <input autocomplete="off" type="checkbox" class="getPhysicianCheckbox"
                                                       physician_his="{{$val['HIS_Id']}}">
                                            </td>
                                            <td>
                                                <div style="width: 250px;">{{$val['FullName_EN']}}</div>
                                            </td>
                                            <td>{{$val['DepartmentName']}}</td>
                                            <td>
                                                <span style="color:green">Yes</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @foreach($deactivated_physicians as $val)
                                        <tr>
                                            <td></td>
                                            <td>
                                                <div style="width: 250px;">{{$val['full_name']}}</div>
                                            </td>
                                            <td>{{$val['clinic_name']}}</td>
                                            <td>
                                                <span style="color:red">No</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-default activateBtn"
                                                        physician_id="{{$val['id']}}">
                                                    Activate
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <div class="modal fade" id="updateArabicModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width: 85%;">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="updateArabicLabel"></h4>
                </div>
                <div class="modal-body col-md-12" id="updateArabicBody">

                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="updateArabicModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" style="width: 85%;">
            <div class="modal-content col-md-12">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="updateArabicLabel2"></h4>
                </div>
                <div class="modal-body col-md-12" id="updateArabicBody2">

                </div>
            </div>
        </div>
    </div>
@stop