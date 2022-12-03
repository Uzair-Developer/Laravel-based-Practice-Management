@extends('layout/main')

@section('title')
    - PMS Attributes
@stop

@section('footer')
    <script>
        $(function () {
            $("#effect").hide();
            $("#duration").hide();
            $("#edit_effect").hide();
            $("#referred_to_div").hide();

            $(".ask-me").click(function (e) {
                e.preventDefault();
                if (confirm('Confirm Delete?')) {
                    window.location.replace($(this).attr('href'));
                }
            });

            $("#type_id").change(function (e) {
                if ($(this).val() == 1) {
                    $("#effect").show();
                } else {
                    $("#effect").hide();
                }
                if ($(this).val() == 5) {
                    $("#duration").show();
                } else {
                    $("#duration").hide();
                }
                if ($(this).val() == 12) {
                    $("#referred_to_div").show();
                } else {
                    $("#referred_to_div").hide();
                }
            });

            $("#edit_type_id").change(function (e) {
                if ($(this).val() == 1) {
                    $("#edit_effect").show();
                } else {
                    $("#edit_effect").hide();
                }
                if ($(this).val() == 5) {
                    $("#edit_duration").show();
                } else {
                    $("#edit_duration").hide();
                }
                if ($(this).val() == 12) {
                    $("#edit_referred_to_div").show();
                } else {
                    $("#edit_referred_to_div").hide();
                }
            });

            $(".editPmsAttributeBtn").click(function (e) {
                var ref_id = $(this).attr('ref_id');
                $.ajax({
                    url: '{{route('getAttributePms')}}',
                    method: 'POST',
                    data: {
                        id: ref_id
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (data) {
                            $("#edit_id").val(data.id);
                            $("#edit_type_id").val(data.type_id).select2();
                            if (data.type_id == 1) {
                                $("#edit_effect").show();
                                $("#reason1, #reason2").attr('required', 'required');
                                if (data.effect == 2) {
                                    $("#reason2").prop("checked", true);
                                    $("#reason1").prop("checked", false);
                                } else if (data.effect == 1) {
                                    $("#reason1").prop("checked", true);
                                    $("#reason2").prop("checked", false);
                                } else {
                                    $("#reason1, #reason2").prop("checked", false);
                                }
                            } else {
                                $("#edit_effect").hide();
                                $("#edit_hospital_div").hide();
                                $("#reason1, #reason2").removeAttr('required');
                            }
                            if (data.type_id == 5) {
                                $("#edit_duration").show();
                                $("#input_duration").val(data.duration);
                            } else {
                                $("#edit_duration").hide();
                                $("#input_duration").val('');
                            }
                            if (data.type_id == 12) {
                                $("#edit_referred_to_div").show();
                                $("#edit_referred_to_div select").val(data.parent_id).select2();
                            } else {
                                $("#edit_referred_to_div").hide();
                                $("#edit_referred_to_div select").val('').select2();
                            }
                            $("#edit_name").val(data.name);
                            $("#edit_text").html(data.text);
                            $("#editAttributePms").modal('show');
                        }
                    }
                });
            });
        })
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            PMS Attributes
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
                            <label>Type</label>
                            <select name="type" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($attributePmsTypes as $key => $val)
                                    <option value="{{$key}}" @if(Input::get('type') == $key)
                                    selected @endif>{{$val}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <label>&nbsp;</label>
                            <br>
                            <button class="btn btn-primary" type="submit">Search</button>
                        </div>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('AttributePms.add'))
                <div class="col-md-3">
                    <a data-target="#addAttributePms" data-toggle="modal">
                        <button class="btn btn-block btn-default">Add PMS Attribute</button>
                    </a>
                    <br>
                </div>
            @endif
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                <tr>
                                    <th style="width: 15px">#</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Parent</th>
                                    <th>Notes</th>
                                    <th>Options</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($attributePms as $val)
                                    <tr>
                                        <td>{{$val['id']}}</td>
                                        <td>{{$val['type_name']}}</td>
                                        <td>{{$val['name']}}</td>
                                        <td>{{$val['parent_name']}}</td>
                                        <td>{{$val['text']}}</td>
                                        <td>
                                            <div class="btn-group" style="width: 150px;">
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('AttributePms.edit'))
                                                    <a ref_id="{{$val['id']}}"
                                                       class="editPmsAttributeBtn btn btn-default">Edit</a>

                                                @endif
                                                @if($c_user->user_type_id == 1 || $c_user->hasAccess('AttributePms.delete'))
                                                    <a class="btn btn-danger ask-me"
                                                       href="{{route('deleteAttributePms', $val['id'])}}"
                                                       style="cursor: pointer">Delete</a>

                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            {{$attributePms->appends(Input::except('_token'))->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="addAttributePms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Add PMS Attribute</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'createAttributePms'))}}
                <div class="modal-body col-md-12">

                    <div class="form-group col-md-6">
                        <label>Type</label>
                        <br>
                        <select autocomplete="off" id="type_id" required name="type_id" class="form-control select2">
                            <option value="">Choose</option>
                            @foreach($attributePmsTypes as $key => $val)
                                <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Name</label>
                        <br>
                        <input required name="name" class="form-control" type="text"/>
                    </div>

                    <div class="form-group col-md-12" id="duration">
                        <label>Duration Time In Minutes</label>
                        <br>
                        <input name="duration" class="form-control" type="number"/>
                    </div>

                    <div class="form-group col-md-12" id="effect">
                        <label>Effect Reason?</label>

                        <div class="checkbox-list">
                            <label class="checkbox-inline">
                                <input autocomplete="off" class="checkbox-inline" name="effect"
                                       checked type="radio" value="1"> Effect
                            </label>
                            <label class="checkbox-inline">
                                <input autocomplete="off" class="checkbox-inline" name="effect"
                                       type="radio" value="2"> Non Effect
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-12" id="referred_to_div">
                        <label>Parent</label>
                        <br>
                        <select autocomplete="off" name="parent_id" class="form-control select2">
                            <option value="">Choose</option>
                            @foreach($parentReferredTo as $key => $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Notes</label>
                        <textarea name="text" class="form-control"></textarea>
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

    <div class="modal fade" id="editAttributePms" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Edit PMS Attribute</h4>
                </div>
                {{Form::open(array('role'=>"form", 'route' => 'updateAttributePms'))}}
                <div class="modal-body col-md-12">

                    <div class="form-group col-md-6">
                        <label>Type</label>
                        <br>
                        <select autocomplete="off" required id="edit_type_id" name="type_id"
                                class="form-control select2"
                                style="width:250px">
                            <option value="">Choose</option>
                            @foreach($attributePmsTypes as $key => $val)
                                <option value="{{$key}}">{{$val}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Name</label>
                        <br>
                        <input id="edit_name" required name="name" class="form-control" type="text"/>
                        <input id="edit_id" name="id" type="hidden" value=""/>
                    </div>

                    <div class="form-group col-md-12" id="edit_duration">
                        <label>Duration Time In Minutes</label>
                        <br>
                        <input id="input_duration" name="duration" class="form-control" type="number"/>
                    </div>

                    <div class="form-group col-md-12" id="edit_effect">
                        <label>Effect Reason?</label>

                        <div class="checkbox-list">
                            <label class="checkbox-inline">
                                <input autocomplete="off" id="reason1" class="checkbox-inline" name="effect"
                                       type="radio" value="1"> Effect
                            </label>
                            <label class="checkbox-inline">
                                <input autocomplete="off" id="reason2" class="checkbox-inline" name="effect"
                                       type="radio" value="2"> Non Effect
                            </label>
                        </div>
                    </div>

                    <div class="form-group col-md-12" id="edit_referred_to_div">
                        <label>Parent</label>
                        <br>
                        <select autocomplete="off" id="parent_id" name="parent_id" class="form-control select2">
                            <option value="">Choose</option>
                            @foreach($parentReferredTo as $key => $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-12">
                        <label>Notes</label>
                        <textarea id="edit_text" name="text" class="form-control"></textarea>
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