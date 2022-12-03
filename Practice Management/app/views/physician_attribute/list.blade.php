@extends('layout/main')

@section('title')
    - Profile Settings
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/loading_mask/waitMe.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
@stop

@section('footer')
    <script src="{{asset('plugins/loading_mask/waitMe.js')}}"></script>
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
                "autoWidth": true,
                "order": [[0, "desc"]]
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
            $("#selectHospital3").change(function (e) {
                $("#selectClinic3").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic3").removeAttr('disabled').html(data).select2();
                    }
                });
            });
            $("#selectHospital4").change(function (e) {
                $("#selectClinic4").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic4").removeAttr('disabled').html(data).select2();
                    }
                });
            });
            $("#selectHospital5").change(function (e) {
                $("#selectClinic5").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic5").removeAttr('disabled').html(data).select2();
                    }
                });
            });
            $("#selectHospital6").change(function (e) {
                $("#selectClinic6").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic6").removeAttr('disabled').html(data).select2();
                    }
                });
            });
            $("#selectHospital7").change(function (e) {
                $("#selectClinic7").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getClinicsByHospitalId')}}',
                    method: 'POST',
                    data: {
                        hospital_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectClinic7").removeAttr('disabled').html(data).select2();
                    }
                });
            });
            $(document).on('click', '.editPhysicianAttributeBtn', function (e) {
                var ref_id = $(this).attr('ref_id');
                $.ajax({
                    url: '{{route('getPhysicianAttribute')}}',
                    method: 'POST',
                    data: {
                        id: ref_id
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (data) {
                            $("#selectHospital4").val(data.hospital_id).select2();
                            $.ajax({
                                url: '{{route('getClinicsByHospitalId')}}',
                                method: 'POST',
                                data: {
                                    hospital_id: data.hospital_id
                                },
                                headers: {token: '{{csrf_token()}}'},
                                success: function (data2) {
                                    $("#selectClinic4").html(data2).val(data.clinic_id).select2();
                                }
                            });
                            $("#edit_id").val(data.id);
                            $("#edit_type").val(data.type);
                            $("#edit_name").val(data.name);
                            $("#edit_name_ar").val(data.name_ar);
                            $("#edit_desc").val(data.desc);
                            $("#edit_desc_ar").val(data.desc_ar);
                            $("#editPhysicianAttribute").modal('show');
                        }
                    }
                });
            });
            $("#updatePhysicianAttribute").submit(function (e) {
                e.preventDefault();
                $("#editPhysicianAttribute").modal('hide');
                $('#WithMe').waitMe({
                    effect: 'ios',
                    text: 'Please wait...',
                    bg: 'rgba(255,255,255,0.7)',
                    color: '#000',
                    maxSize: '',
                    source: 'img.svg'
                });
                var type = $("#edit_type").val();
                var o = getFormData($(this).serializeArray());
                $.ajax({
                    url: '{{route('updatePhysicianAttribute')}}',
                    method: 'POST',
                    data: {
                        form: o
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (data.success == 'yes') {
                            if (type == 1) { // clinic services
                                getCustomTable('1', 'clinic_service_table');
                            } else if (type == 2) { // performed operations
                                getCustomTable('2', 'performed_operations_table');
                            } else if (type == 3) {
                                getCustomTable('3', 'equipments_table');
                            } else if (type == 4) {
                                getCustomTable('4', 'specialty_table');
                            }
                        }
                        $('#WithMe').waitMe('hide');
                        alert(data.message);
                    }
                });
            });
            $("#addPhysicianAttribute1, #addPhysicianAttribute2, #addPhysicianAttribute3, #addPhysicianAttribute4").submit(function (e) {
                e.preventDefault();
                var type = $(this).attr('ref_type');
                $('#WithMe').waitMe({
                    effect: 'ios',
                    text: 'Please wait...',
                    bg: 'rgba(255,255,255,0.7)',
                    color: '#000',
                    maxSize: '',
                    source: 'img.svg'
                });
                var o = getFormData($(this).serializeArray());
                $.ajax({
                    url: '{{route('createPhysicianAttribute')}}',
                    method: 'POST',
                    data: {
                        form: o
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (data.success == 'yes') {
                            if (type == 1) { // clinic services
                                getCustomTable('1', 'clinic_service_table');
                                $("#addPhysicianAttribute1 .select2").val('').select2();
                                $("#addPhysicianAttribute1 input:not([name='type'])").val('');
                                $("#addPhysicianAttribute1 textarea").val('');
                            } else if (type == 2) { // performed operations
                                getCustomTable('2', 'performed_operations_table');
                                $("#addPhysicianAttribute2 .select2").val('').select2();
                                $("#addPhysicianAttribute2 input:not([name='type'])").val('');
                                $("#addPhysicianAttribute2 textarea").val('');
                            } else if (type == 3) {
                                getCustomTable('3', 'equipments_table');
                                $("#addPhysicianAttribute3 .select2").val('').select2();
                                $("#addPhysicianAttribute3 input:not([name='type'])").val('');
                                $("#addPhysicianAttribute3 textarea").val('');
                            } else if (type == 4) {
                                getCustomTable('4', 'specialty_table');
                                $("#addPhysicianAttribute4 .select2").val('').select2();
                                $("#addPhysicianAttribute4 input:not([name='type'])").val('');
                                $("#addPhysicianAttribute4 textarea").val('');
                            }
                        }
                        $('#WithMe').waitMe('hide');
                        alert(data.message);
                    }
                });
            });
            $(document).on('click', '.deletePhysicianAttributeBtn', function (e) {
                var type = $(this).attr('ref_type');
                var id = $(this).attr('ref_id');
                $('#WithMe').waitMe({
                    effect: 'ios',
                    text: 'Please wait...',
                    bg: 'rgba(255,255,255,0.7)',
                    color: '#000',
                    maxSize: '',
                    source: 'img.svg'
                });
                $.ajax({
                    url: '{{route('deletePhysicianAttribute')}}',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (type == 1) { // clinic services
                            getCustomTable('1', 'clinic_service_table');
                        } else if (type == 2) { // performed operations
                            getCustomTable('2', 'performed_operations_table');
                        } else if (type == 3) {
                            getCustomTable('3', 'equipments_table');
                        } else if (type == 4) {
                            getCustomTable('4', 'specialty_table');
                        }
                        $('#WithMe').waitMe('hide');
                        alert(data);
                    }
                });
            });
            $("#tab-li_0, #tab-li_1, #tab-li_2, #tab-li_3").click(function (e) {
                if ($(this).attr('id') == 'tab-li_0') {
                    getCustomTable('1', 'clinic_service_table');
                } else if ($(this).attr('id') == 'tab-li_1') {
                    getCustomTable('2', 'performed_operations_table');
                } else if ($(this).attr('id') == 'tab-li_2') {
                    getCustomTable('3', 'equipments_table');
                } else if ($(this).attr('id') == 'tab-li_3') {
                    getCustomTable('4', 'specialty_table');
                }
            });
            function getCustomTable(type, div) {
                $.ajax({
                    url: '{{route('getPhysicianAttributeByType')}}',
                    method: 'POST',
                    data: {
                        type: type
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $('#' + div).html(data);
                    }
                });
            }
        })
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Profile Settings
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li id="tab-li_0" class="tab-li active"><a href="#tab_0" data-toggle="tab">Clinic Services</a>
                        </li>
                        <li id="tab-li_1" class="tab-li "><a href="#tab_1" data-toggle="tab">Performed Operations</a>
                        </li>
                        <li id="tab-li_2" class="tab-li "><a href="#tab_2" data-toggle="tab">Equipments</a></li>
                        <li id="tab-li_3" class="tab-li "><a href="#tab_3" data-toggle="tab">Specialty</a></li>
                    </ul>
                    <div class="tab-content col-md-12" id="WithMe">
                        <div class="tab-pane active" id="tab_0">
                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('physician_attribute.add'))
                                <div class="box box-primary">
                                    <div class="box-header">
                                        <button type="button" class="btn btn-box-tool pull-right"
                                                data-widget="collapse">
                                            <i class="fa fa-minus"></i></button>
                                    </div>
                                    {{Form::open(array('role'=>"form", 'id' => 'addPhysicianAttribute1', 'ref_type' => 1))}}
                                    <div class="box-body">
                                        <div class="form-group col-md-3">
                                            <label>Hospital *</label>
                                            <select required autocomplete="off" name="hospital_id" class="form-control select2"
                                                    id="selectHospital3">
                                                <option value="">Choose</option>
                                                @foreach($hospitals as $key => $val)
                                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Clinic *</label>
                                            <select required autocomplete="off" name="clinic_id" class="form-control select2"
                                                    id="selectClinic3">
                                                <option value="">Choose</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Name En *</label>
                                            <input required name="name" class="form-control" type="text"/>
                                            <input name="type" type="hidden" value="1"/>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Name Ar *</label>
                                            <input required name="name_ar" class="form-control" type="text"/>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>Description En</label>
                                            <textarea name="desc" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>Description Ar</label>
                                            <textarea name="desc_ar" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </div>
                                    {{Form::close()}}
                                </div>
                            @endif
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="table-responsive" id="clinic_service_table">
                                            <table id="example1" class="table table-bordered">
                                                <thead>
                                                <tr>
                                                    <th style="width: 15px">#</th>
                                                    <th>Hospital</th>
                                                    <th>Clinic</th>
                                                    <th>Name En</th>
                                                    <th>Name Ar</th>
                                                    <th>Description En</th>
                                                    <th>Description Ar</th>
                                                    <th>Options</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($clinic_services as $val)
                                                    <tr>
                                                        <td>{{$val['id']}}</td>
                                                        <td>{{$val['hospital_name']}}</td>
                                                        <td>{{$val['clinic_name']}}</td>
                                                        <td>{{$val['name']}}</td>
                                                        <td>{{$val['name_ar']}}</td>
                                                        <td>{{$val['desc']}}</td>
                                                        <td>{{$val['desc_ar']}}</td>
                                                        <td>
                                                            <div class="btn-group">
                                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('physician_attribute.edit'))
                                                                    <a ref_id="{{$val['id']}}"
                                                                       class="btn btn-default editPhysicianAttributeBtn">Edit</a>
                                                                @endif
                                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('physician_attribute.delete'))
                                                                    <a ref_id="{{$val['id']}}" ref_type="1"
                                                                       class="btn btn-danger deletePhysicianAttributeBtn">Delete</a>
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
                        <div class="tab-pane" id="tab_1">
                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('physician_attribute.add'))
                                <div class="box box-primary">
                                    <div class="box-header">
                                        <button type="button" class="btn btn-box-tool pull-right"
                                                data-widget="collapse">
                                            <i class="fa fa-minus"></i></button>
                                    </div>
                                    {{Form::open(array('role'=>"form", 'id' => 'addPhysicianAttribute2', 'ref_type' => 2))}}
                                    <div class="box-body">
                                        <div class="form-group col-md-3">
                                            <label>Hospital *</label>
                                            <br>
                                            <select required autocomplete="off" name="hospital_id" class="form-control select2"
                                                    id="selectHospital5" style="width:230px;">
                                                <option value="">Choose</option>
                                                @foreach($hospitals as $key => $val)
                                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Clinic *</label>
                                            <br>
                                            <select required autocomplete="off" name="clinic_id" class="form-control select2"
                                                    id="selectClinic5" style="width:230px;">
                                                <option value="">Choose</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Name En *</label>
                                            <input required name="name" class="form-control" type="text"/>
                                            <input name="type" type="hidden" value="2"/>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Name Ar *</label>
                                            <input required name="name_ar" class="form-control" type="text"/>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>Description En</label>
                                            <textarea name="desc" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>Description Ar</label>
                                            <textarea name="desc_ar" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </div>
                                    {{Form::close()}}
                                </div>
                            @endif
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="table-responsive" id="performed_operations_table">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_2">
                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('physician_attribute.add'))
                                <div class="box box-primary">
                                    <div class="box-header">
                                        <button type="button" class="btn btn-box-tool pull-right"
                                                data-widget="collapse">
                                            <i class="fa fa-minus"></i></button>
                                    </div>
                                    {{Form::open(array('role'=>"form", 'id' => 'addPhysicianAttribute3', 'ref_type' => 3))}}
                                    <div class="box-body">
                                        <div class="form-group col-md-3">
                                            <label>Hospital *</label>
                                            <br>
                                            <select required autocomplete="off" name="hospital_id" class="form-control select2"
                                                    id="selectHospital6" style="width:230px;">
                                                <option value="">Choose</option>
                                                @foreach($hospitals as $key => $val)
                                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Clinic *</label>
                                            <br>
                                            <select required autocomplete="off" name="clinic_id" class="form-control select2"
                                                    id="selectClinic6" style="width:230px;">
                                                <option value="">Choose</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Name En *</label>
                                            <input required name="name" class="form-control" type="text"/>
                                            <input name="type" type="hidden" value="3"/>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Name Ar *</label>
                                            <input required name="name_ar" class="form-control" type="text"/>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>Description En</label>
                                            <textarea name="desc" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>Description Ar</label>
                                            <textarea name="desc_ar" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </div>
                                    {{Form::close()}}
                                </div>
                            @endif
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="table-responsive" id="equipments_table">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab_3">
                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('physician_attribute.add'))
                                <div class="box box-primary">
                                    <div class="box-header">
                                        <button type="button" class="btn btn-box-tool pull-right"
                                                data-widget="collapse">
                                            <i class="fa fa-minus"></i></button>
                                    </div>
                                    {{Form::open(array('role'=>"form", 'id' => 'addPhysicianAttribute4', 'ref_type' => 4))}}
                                    <div class="box-body">
                                        <div class="form-group col-md-3">
                                            <label>Hospital *</label>
                                            <br>
                                            <select required autocomplete="off" name="hospital_id" class="form-control select2"
                                                    id="selectHospital7" style="width:230px;">
                                                <option value="">Choose</option>
                                                @foreach($hospitals as $key => $val)
                                                    <option value="{{$val['id']}}">{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Clinic *</label>
                                            <br>
                                            <select required autocomplete="off" name="clinic_id" class="form-control select2"
                                                    id="selectClinic7" style="width:230px;">
                                                <option value="">Choose</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Name En *</label>
                                            <input required name="name" class="form-control" type="text"/>
                                            <input name="type" type="hidden" value="4"/>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label>Name Ar *</label>
                                            <input required name="name_ar" class="form-control" type="text"/>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>Description En</label>
                                            <textarea name="desc" class="form-control"></textarea>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>Description Ar</label>
                                            <textarea name="desc_ar" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="box-footer">
                                        <button class="btn btn-primary" type="submit">Save</button>
                                    </div>
                                    {{Form::close()}}
                                </div>
                            @endif
                            <div class="col-md-12">
                                <div class="box box-primary">
                                    <!-- /.box-header -->
                                    <div class="box-body">
                                        <div class="table-responsive" id="specialty_table">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="editPhysicianAttribute" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Edit PMS Attribute</h4>
                </div>
                {{Form::open(array('role'=>"form", 'id' => 'updatePhysicianAttribute'))}}
                <div class="modal-body col-md-12">

                    <div class="form-group col-md-6">
                        <label>Hospital *</label>
                        <br>
                        <select required autocomplete="off" name="hospital_id" class="form-control select2"
                                id="selectHospital4" style="width: 250px;">
                            <option value="">Choose</option>
                            @foreach($hospitals as $key => $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Clinic *</label>
                        <br>
                        <select required autocomplete="off" name="clinic_id" class="form-control select2"
                                id="selectClinic4">
                            <option value="">Choose</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name En *</label>
                        <input id="edit_name" required name="name" class="form-control" type="text"/>
                        <input id="edit_id" name="id" type="hidden" value=""/>
                        <input id="edit_type" name="type" type="hidden" value=""/>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Name Ar *</label>
                        <input id="edit_name_ar" required name="name_ar" class="form-control" type="text"/>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Description En</label>
                        <textarea id="edit_desc" name="desc" class="form-control"></textarea>
                    </div>
                    <div class="form-group col-md-12">
                        <label>Description Ar</label>
                        <textarea id="edit_desc_ar" name="desc_ar" class="form-control"></textarea>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Confirm</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>
@stop