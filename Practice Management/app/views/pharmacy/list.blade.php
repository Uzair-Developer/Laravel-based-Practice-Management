@extends('layout/main')

@section('title')
    - Pharmacy
@stop

@section('footer')
    <script>
        $(function () {
            $("#pharmacyGetActivity").click(function (e) {
                $.ajax({
                    url: '{{route('pharmacyGetActivity')}}',
                    method: 'POST',
                    data: {},
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (data) {
                            var pharmacy_ticket_types = data.pharmacy_ticket_type_id.split(",");
                            $("#pharmacy_ticket_type_id").val(pharmacy_ticket_types).select2();
                            $("#pharmacyChangeActivityModal").modal('show');
                        } else {
                            alert('Error in getting data, please contact to administrator');
                        }
                    }
                });
            });

            setInterval(function () {
                $.ajax({
                    url: '{{route('getQueuePharmacyCounts')}}',
                    method: 'POST',
                    data: {},
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (data) {
                            $("#queue_count").html(data.queue_count);
                            $("#queue_count_served").html(data.count_served);
                            $("#queue_count_pass").html(data.count_pass);
                        }
                    }
                });
            }, 10000);

            $("#tab1_li").click(function (e) {
                $.ajax({
                    url: '{{route('refreshQueuePass')}}',
                    method: 'POST',
                    data: {},
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (data) {
                            $("#tab1_table").html(data);
                        }
                    }
                });
            });
            $(document).on("click", '#getNextQueuePharmacyBtn, #callFromPass', function (e) {
                $(".oldPatientCode").html('');
                $("#oldPatientId").val('');
                $("#typeOfModel").val('');
                $("#callFromPassVal").val('');
                var idOfAction = $(this);
                if (idOfAction.attr('id') == 'getNextQueuePharmacyBtn') {
                    $("#typeOfModel").val(1);
                } else {
                    $("#typeOfModel").val(2);
                    $("#callFromPassVal").val(idOfAction.attr('ref_id'));
                }
                $.ajax({
                    url: '{{route('checkNextQueuePharmacy')}}',
                    method: 'POST',
                    data: {},
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        if (data) {
                            if (data.success == 'yes') {
                                if (idOfAction.attr('id') == 'getNextQueuePharmacyBtn') {
                                    window.location.replace('{{route('getNextQueuePharmacy')}}');
                                } else {
                                    window.location.replace('{{route('callFromPassPharmacyQueue')}}?id=' + idOfAction.attr('ref_id'));
                                }
                            } else {
                                $(".oldPatientCode").html(data.queue['queue_code']);
                                $("#oldPatientId").val(data.queue['id']);
                                if (data.queue['pass_datetime']) {
                                    $("#passPatient").hide();
                                } else {
                                    $("#passPatient").show();
                                }
                                $("#getNextQueuePharmacyModal").modal('show');
                            }
                        }
                    }
                });
            });

            $("#callDonePatient").click(function (e) {
                if ($("#oldPatientId").val().length) {
                    if ($("#typeOfModel").val() == 1) {
                        window.location.replace('{{route('callDonePharmacyQueue', '')}}/' + $("#oldPatientId").val() + '?with_next=true');
                    } else {
                        window.location.replace('{{route('callDonePharmacyQueue', '')}}/' + $("#oldPatientId").val() + '?with_call_from_pass=true&id=' + $("#callFromPassVal").val());
                    }
                }
            });

            $("#passPatient").click(function (e) {
                if ($("#oldPatientId").val().length) {
                    if ($("#typeOfModel").val() == 1) {
                        window.location.replace('{{route('patientPassPharmacyQueue', '')}}/' + $("#oldPatientId").val() + '?with_next=true');
                    } else {
                        window.location.replace('{{route('patientPassPharmacyQueue', '')}}/' + $("#oldPatientId").val() + '?with_call_from_pass=true&id=' + $("#callFromPassVal").val());
                    }
                }
            });
        })
    </script>
@stop

@section('content')

    <section class="content-header">
        <h1>
            Pharmacy <span>{{$pharmacy_desk_num}}</span>
        </h1>
    </section>

    <section class="content">
        <div class="row">

            @if($c_user->user_type_id == 1 || $c_user->hasAccess('pharmacy.next_patient'))
                <div class="col-md-3">
                    <a id="getNextQueuePharmacyBtn">
                        <button class="btn btn-block btn-default">Next Patient (<span style="color: green"
                                                                                      id="queue_count">{{$queue_count}}</span>)
                        </button>
                    </a>
                    <br>
                </div>
                <div class="col-md-3">
                    <a id="pharmacyGetActivity">
                        <button class="btn btn-block btn-info">Change Your Activity</button>
                    </a>
                    <br>
                </div>
                <div class="col-md-12">
                    <div>
                        Served Count: <span style="color: green" id="queue_count_served">{{$queue_count_served}}</span>
                    </div>
                    <br>
                </div>
            @endif
            <div class="col-md-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li id="tab0_li" class="tab-li active"><a href="#tab0" data-toggle="tab">Queue List
                                        (<span style="color: green">{{count($queue_call->toArray())}}</span>)</a></li>
                                <li id="tab1_li" class="tab-li"><a href="#tab1" data-toggle="tab">Pass List
                                        (<span id="queue_count_pass"
                                               style="color: green">{{$queue_pass}}</span>)</a></li>
                            </ul>
                            <div class="tab-content col-md-12" id="loading">
                                <div class="tab-pane active" id="tab0">
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th>Code</th>
                                                <th>Options</th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($queue_call as $val)
                                                <tr>
                                                    <td>{{$val['queue_code']}}</td>
                                                    <td>
                                                        <div class="btn-group" style="width: 150px;">
                                                            @if($c_user->user_type_id == 1 || $c_user->hasAccess('pharmacy.call_done'))
                                                                <a href="{{route('callDonePharmacyQueue', $val['id'])}}"
                                                                   class="btn btn-default bg-green">OK</a>
                                                            @endif
                                                            @if(($c_user->user_type_id == 1 || $c_user->hasAccess('pharmacy.pass')) && empty($val['pass_datetime']))
                                                                <a class="btn btn-danger"
                                                                   href="javascript:void(0);"
                                                                   onclick="if(confirm('Are you sure?')) { window.location.replace('{{route('patientPassPharmacyQueue', $val['id'])}}'); } return false"
                                                                   style="cursor: pointer">Pass</a>

                                                            @endif
                                                            @if(!empty($val['pass_datetime']))
                                                                <a class="btn btn-danger"
                                                                   href="javascript:void(0);"
                                                                   onclick="if(confirm('Are you sure?')) { window.location.replace('{{route('cancelPatientPharmacyQueue', $val['id'])}}'); } return false"
                                                                   style="cursor: pointer">Cancel</a>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab1">
                                    <div class="table-responsive" id="tab1_table">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="getNextQueuePharmacyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title text-bold" id="modalTitle">Warring</h4>
                </div>
                <div class="modal-body">
                    <h3 style="text-align: center !important;">Please Take Action First With Patient Code (<span
                                class="oldPatientCode"></span>)</h3>
                    <div class="form-group" style="text-align: center !important;">
                        <h3>Patient Code</h3>
                        <h2 class="oldPatientCode text-bold"></h2>
                        <input type="hidden" id="oldPatientId">
                        <input type="hidden" id="typeOfModel">
                        <input type="hidden" id="callFromPassVal">
                    </div>

                </div>
                <div class="modal-footer" style="text-align: center !important;">
                    <a id="callDonePatient" class="btn btn-default bg-green">OK</a>
                    <a id="passPatient" class="btn btn-danger">Pass</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pharmacyChangeActivityModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title ">Change Your Activity</h4>
                </div>
                {{Form::open(array('route' => 'pharmacyChangeActivity'))}}
                <div class="modal-body">
                    <div class="form-group">
                        <label>Your Activity</label>
                        <select required autocomplete="off" id="pharmacy_ticket_type_id"
                                name="pharmacy_ticket_type_id[]"
                                class="form-control select2" multiple>
                            <option value="">Choose</option>
                            @foreach($ticketType as $val)
                                <option value="{{$val['id']}}">{{$val['name']}}</option>
                            @endforeach
                        </select>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-default">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                {{Form::close()}}
            </div>
        </div>
    </div>

@stop