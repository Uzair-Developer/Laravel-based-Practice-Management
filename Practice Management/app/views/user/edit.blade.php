@extends('layout/main')

@section('title')
    - Edit {{$user['user_name']}}
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/fancytree/skin-win7/ui.fancytree.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Edit {{$user['user_name']}}
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-9">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('role'=>"form",'files' => true))}}
                    <div class="box-body">
                        <div class="form-group col-md-3">
                            <label>First name En</label>
                            <input required type="text" value="{{$user['first_name']}}" name="first_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Middle name En</label>
                            <input required type="text" value="{{$user['middle_name']}}" name="middle_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Last name En</label>
                            <input required type="text" value="{{$user['last_name']}}" name="last_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Family name En</label>
                            <input type="text" value="{{$user['family_name']}}" name="family_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>First name Ar</label>
                            <input type="text" value="{{$user['first_name_ar']}}" name="first_name_ar"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label>Last name Ar</label>
                            <input type="text" value="{{$user['last_name_ar']}}" name="last_name_ar"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>User name(For Login)</label>
                            <input required type="text" value="{{$user['user_name']}}" name="user_name"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" value="{{$user['email']}}" name="email" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Phone number</label>
                            <input type="text" value="{{$user['phone_number']}}"
                                   name="phone_number" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Mobile 1</label>
                            <input type="text" value="{{$user['mobile1']}}"
                                   name="mobile1" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Mobile 2</label>
                            <input type="text" value="{{$user['mobile2']}}"
                                   name="mobile2" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Address</label>
                            <input autocomplete="off" type="text" value="{{$user['address']}}"
                                   name="address" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Personal photo</label>
                            <input type="file" name="image_url">
                            @if(!empty($user['image_url']))
                                <img src="{{asset($user['image_url'])}}" width="100" height="100">
                            @endif
                        </div>

                        <div class="box box-default col-md-12">
                            <div class="box-header with-border">
                                <h3 class="box-title">Group Permissions</h3>

                                <div class="box-tools pull-right">
                                    <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body" style="display: block;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group col-md-6">
                                            <label>Group Name</label>
                                            <select required name="group_id[]" class="form-control select2" multiple>
                                                <option value="">Choose</option>
                                                @foreach($groups as $val)
                                                    <option value="{{$val['id']}}"
                                                            @if(in_array($val['id'], $user_groups))
                                                            selected @endif>{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.box-body -->
                        </div>

                        <div class="box box-default col-md-12">
                            <div class="box-header with-border">
                                <h3 class="box-title">Authority</h3>

                                <div class="box-tools pull-right">
                                    <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body" style="display: block;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group col-md-6">
                                            <label>Rule Name</label>
                                            <select id="user_type_id" name="user_type_id" class="form-control select2">
                                                <option value="">Choose</option>
                                                @foreach($userTypes as $val)
                                                    <option value="{{$val['id']}}"
                                                            @if($val['id'] == $user['user_type_id'])
                                                            selected @endif>{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6" id="user_experience_div">
                                            <label>Physician Experience</label>
                                            <select id="user_experience_id" name="user_experience_id"
                                                    class="form-control select2">
                                                <option value="">Choose</option>
                                                @foreach($experience as $val)
                                                    <option value="{{$val['id']}}"
                                                            @if($user['user_experience_id'] == $val['id'])
                                                            selected @endif>{{$val['name']}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.box-body -->
                        </div>

                        <div class="box box-default col-md-12">
                            <div class="box-header with-border">
                                <h3 class="box-title">Localization</h3>

                                <div class="box-tools pull-right">
                                    <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <!-- /.box-header -->
                            <div class="box-body" style="display: block;">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Organization List</label>

                                            <div id="tree1" class="fancytree-radio"></div>
                                            <input name="hospital_ids" id="hospital_ids"
                                                   value="{{implode(',', $user_hospitals)}}" style="display: none"/>
                                            <input name="clinic_ids" id="clinic_ids"
                                                   value="{{implode(',', $user_clinics)}}" style="display: none"/>
                                        </div>
                                    </div>
                                </div>
                                <!-- /.row -->
                            </div>
                            <!-- /.box-body -->
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('users')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </section>
@stop

@section('footer')
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('plugins/fancytree/jquery.fancytree.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
            @if($user['user_type_id'] == 7)
            $("#user_experience_div").show();
            @else
            $("#user_experience_div").hide();
            @endif

                $("#user_type_id").change(function (e) {
                        if ($(this).val() == 7) {
                            $("#user_experience_div").show();
                        } else {
                            $("#user_experience_div").hide();
                        }
                    })
        });
    </script>
    @if(!empty($hospitals))
        <script type="text/javascript">
            var treeData = [
                    @foreach($hospitals as $val)
                {
                    title: "{{$val['name']}}", key: "hospital_{{$val['id']}}" {{in_array($val['id'],$user_hospitals) ?
                    ', selected: true,' : ','}}
                expanded: true
                    @if(!empty($val['clinics']))
                    ,
                    children: [
                            @foreach($val['clinics'] as $val2)
                        {
                            title: "{{$val2['name']}}",
                            key: 'clinic_{{$val2['id']}}' {{in_array($val2['id'],$user_clinics) ? ', selected: true' : ''}}


                        },
                        @endforeach
                    ]
                    @endif



                },
                @endforeach
            ];
            $(function () {

                // --- Initialize sample trees
                $("#tree1").fancytree({
                    checkbox: true,
                    selectMode: 2,
                    source: treeData,
                    select: function (event, data) {
                        // Display list of selected nodes
                        var selNodes = data.tree.getSelectedNodes();
                        // convert to title/key array
                        var selKeys = $.map(selNodes, function (node) {
//                        return "[" + node.key + "]: '" + node.title + "'";
                            if (node.key.indexOf("hospital") > -1) {
                                partsArray = node.key.split('_');
                                console.log(partsArray[1]);
                                return partsArray[1];
                            }
                        });
                        var selKeys2 = $.map(selNodes, function (node) {
//                        return "[" + node.key + "]: '" + node.title + "'";
                            if (node.key.indexOf("clinic") > -1) {
                                partsArray = node.key.split('_');
                                console.log(partsArray[1]);
                                return partsArray[1];
                            }
                        });
                        $("#hospital_ids").val(selKeys.join(","));
                        $("#clinic_ids").val(selKeys2.join(","));
                    }
                });
            });
        </script>
    @endif
@stop
