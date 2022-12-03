@extends('layout/main')

@section('title')
    - Add Hospital
@stop

@section('header')
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
@stop

@section('content')
    <section class="content-header">
        <h1>
            Add Hospital
        </h1>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    {{Form::open(array('route' => 'createHospital','role'=>"form",'files' => true))}}
                    <div class="box-body">
                        <div class="form-group col-md-6">
                            <label>Name</label>
                            <input required type="text"
                                   value="{{Input::old('name')}}" name="name" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Issue Authority Code</label>
                            <input required type="text"
                                   value="{{Input::old('issue_authority_code')}}" name="issue_authority_code"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Country</label>
                            <select autocomplete="off" id="country_id" class="form-control select2" name="country_id">
                                <option value="">Country</option>
                                @foreach($countries as $val)
                                    <option @if(Input::get('country_id') == $val['id']) selected
                                            @endif value="{{$val['id']}}">{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label>City</label>
                            <select autocomplete="off" id="city_id" class="form-control select2" name="city_id">
                                <option value="">Cities</option>

                            </select>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Address</label>
                            <input required type="text" value="{{Input::old('address')}}" name="address"
                                   class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>TimeZone</label>
                            <select required name="time_zone" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($zones as $val)
                                    <option value="{{$val['name']}}" @if(Input::old('time_zone') == $val['name'])
                                    selected @endif>{{$val['name']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Currency</label>
                            <select required name="currency_id" class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($currency as $val)
                                    <option value="{{$val['id']}}" @if(Input::old('currency_id') == $val['id'])
                                    selected @endif>{{$val['label']}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label>Logo</label>
                            <input type="file" name="logo">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Website</label>
                            <input type="text" value="{{Input::old('website')}}" name="website" class="form-control">
                        </div>

                        <div class="form-group col-md-6">
                            <label>Email</label>
                            <input type="email" value="{{Input::old('email')}}" name="email" class="form-control">
                        </div>
                    </div>
                    {{--/////////////////////////////////////////////////--}}
                    <div class="box box-default col-md-12">
                        <div class="box-header with-border">
                            <h3 class="box-title">Contacts</h3>

                            <div class="box-tools pull-right">
                                <button data-widget="collapse" class="btn btn-box-tool"><i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" style="display: block;">
                            <div class="row">
                                <div class="col-md-12">
                                    <table id="tableContacts" class="table" style="display: none">
                                        <tbody>
                                        <tr>
                                            <th>Department Name</th>
                                            <th>Phone Number</th>
                                            <th>Extension</th>
                                            <th>Show in reservation SMS</th>
                                            <th>Options</th>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="col-md-12">
                                    <a id="addContact">
                                        <button style="width: 100px;" class="btn btn-block btn-default">Add Contact
                                        </button>
                                    </a>
                                    <br>
                                </div>
                                <div class="col-md-6" id="divAddContact" style="display: none;">
                                    <div class="form-group">
                                        <label>Department Name</label>
                                        <input id="deptName" type="text" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input id="phoneNumber" type="number" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Extension </label>
                                        <input id="extension" type="number" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label>Show in reservation SMS</label><br>
                                        <input id="showInSMS" type="checkbox" class="minimal ">
                                    </div>
                                    <div class="form-group">
                                        <button id="saveContact" style="width: 100px;float: left;margin-right: 10px;"
                                                class="btn btn-block btn-default">Add
                                        </button>
                                        <button id="closeContact" style="width: 100px;"
                                                class="btn btn-block btn-default">Close
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- /.row -->
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                        <button class="btn btn-primary" type="submit">Save</button>
                        <a href="{{route('hospitals')}}" class="btn btn-info" type="submit">Back</a>
                    </div>
                    {{Form::close()}}
                </div>

            </div>
        </div>
    </section>
@stop
@section('footer')
    <script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
    <script src="{{asset('plugins/underscore/underscore.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();
            $("#addContact").click(function (e) {
                e.preventDefault();
                $("#divAddContact").toggle(100);
            });
            $("#closeContact").click(function (e) {
                e.preventDefault();
                $("#divAddContact").hide(100);
            });
            var countContacts = '0';
            $("#saveContact").click(function (e) {
                e.preventDefault();
                var deptName = $("#deptName").val();
                var phoneNumber = $("#phoneNumber").val();
                var extension = $("#extension").val();
                var showInSMS = $("#showInSMS").is(':checked');
                var template = _.template($('#scriptContact').html(), {
                    deptName: deptName,
                    phoneNumber: phoneNumber,
                    extension: extension,
                    showInSMS: showInSMS,
                    countContacts: countContacts
                });
                $('#tableContacts tbody').append(template);
                $('#tableContacts').show();
                ++countContacts;
            });
            $(document).on('click', '.deleteTr', function (e) {
                e.preventDefault();
                if (confirm('Confirm?')) {
                    var trId = $(this).attr('deleteTr');
                    $("#contactNum_" + trId).remove();
                    if ($("#tableContacts tr").length == 1) {
                        $('#tableContacts').hide();
                    }
                }
            });
            $("#country_id").change(function (e) {
                $("#city_id").attr('disabled', 'disabled');
                $.ajax({
                    url: "{{route('getCitiesOfCountry')}}",
                    method: 'POST',
                    data: {country_id: $(this).val()},
                    success: function (data) {
                        $("#city_id").html(data).select2().removeAttr('disabled');
                    }
                });
            });
        });
    </script>
    <script id="scriptContact" type="text/html">
        <tr id="contactNum_<%= countContacts %>">
            <td><%= deptName %>
                <input type="hidden" name="contact[]"
                       value="<%= deptName %>--<%= phoneNumber %>--<%= extension %>--<%= showInSMS ? '1' : '0' %>">
            </td>
            <td><%= phoneNumber %></td>
            <td><%= extension %></td>
            <td><%= showInSMS ? 'Yes' : 'No' %></td>
            <td>
                <button deleteTr="<%= countContacts %>" class="btn btn-box-tool deleteTr">
                    <i class="fa fa-times"></i></button>
            </td>
        </tr>
    </script>
@stop