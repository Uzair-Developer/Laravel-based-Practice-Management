@extends('layout/main')

@section('title')
    - Pharmacy Reports
@stop

@section('header')
{{--    <link rel="stylesheet" href="{{asset('plugins/datepicker/datepicker3.css')}}">--}}
    <link rel="stylesheet" href="{{asset('plugins/datetimepicker/jquery.datetimepicker.css')}}">

    <link rel="stylesheet" href="{{asset('plugins/datatables/dataTables.bootstrap.css')}}">
{{--    <link rel="stylesheet" href="{{asset('plugins/datatables/fixedHeader.dataTables.min.css')}}">--}}
{{--    <link rel="stylesheet" href="{{asset('plugins/datatables/scroller.dataTables.min.css')}}">--}}
    <link rel="stylesheet" href="{{asset('plugins/loading_mask/waitMe.css')}}">
@stop

@section('footer')
{{--    <script src="{{asset('plugins/datepicker/bootstrap-datepicker.js')}}"></script>--}}
    <script src="{{asset('plugins/datetimepicker/jquery.datetimepicker.full.js')}}"></script>
    <script src="{{asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('plugins/datatables/dataTables.bootstrap.min.js')}}"></script>
{{--    <script src="{{asset('plugins/datatables/dataTables.fixedHeader.min.js')}}"></script>--}}
    {{--<script src="{{asset('plugins/datatables/dataTables.scroller.min.js')}}"></script>--}}
    <script src="{{asset('plugins/loading_mask/waitMe.js')}}"></script>
    <script>
        $(function () {

            $('.datetimepicker').datetimepicker({
                datepicker: true,
                format: 'Y-m-d H:i',
                step: 5
            });

            function getReport() {
                $("#download_excel_div").hide();
                withMe('#withMe');
                $.ajax({
                    url: '{{route('postPharmacyReport')}}',
                    method: 'POST',
                    data: {
                        from_datetime: $('#from_datetime').val(),
                        to_datetime: $('#to_datetime').val(),
                        pharmacy_id: $('#pharmacy_id').val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $('#withMe').waitMe('hide');
                        if (data.response == 'false') {
                            alert(data.message);
                            $("#report_html").html('');
                            $("#download_excel_div").hide();
                        } else {
                            $("#download_excel_div").show();
                            window.history.pushState('', '', '?' + $("#getPharmacyReportForm").serialize());
                            $("#report_html").html(data.html);
                            $('.showPopover').popover({
                                html: true
                            });
                        }
                    }
                });
            }

            $("#getReport").click(function (e) {
                e.preventDefault();
                getReport();
            });

            $("#download_excel").click(function (e) {
                window.location.href = '{{route('excelPharmacyReport')}}?' + $("#getPharmacyReportForm").serialize();
            });

            $(document).on('click', '.pharmacistLogsBtn', function (e) {
                $("#pharmacist_id_modal").val('');
                var pharmacist_id = $(this).attr('pharmacist_id');
                $("#pharmacist_id_modal").val(pharmacist_id);
                withMe('#withMe');
                getPharmacistLog(pharmacist_id, null);
            });

            $(document).on('click','.pagination a', function(e) {
                e.preventDefault();
                //to get what page, when you click paging
                var page = $(this).attr('href').split('page=')[1];
                //console.log(page);
                getPharmacistLog($("#pharmacist_id_modal").val(), page);
            });
        });

        function getPharmacistLog(pharmacist_id, page) {
            var url = '{{route('postPharmacistLog')}}';
            if(page) {
                url = url + '?page=' + page;
            }
            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    from_datetime: $('#from_datetime').val(),
                    to_datetime: $('#to_datetime').val(),
                    pharmacist_id: pharmacist_id
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#pharmacistLogsBody").html('');
                    $('#withMe').waitMe('hide');
                    if (data.response == 'false') {
                        alert(data.message);
                    } else {
                        $("#pharmacistLogsBody").html(data.html);
                        if(!page) {
                            $("#pharmacistLogsModal").modal('show');
                        }
                    }
                }
            });
        }
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Pharmacy Reports
        </h1>
    </section>
    <section class="content">
        <div class="row" id="withMe">
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-header">
                        Criteria
                        <button type="button" class="btn btn-box-tool pull-right" data-widget="collapse">
                            <i class="fa fa-minus"></i></button>
                    </div>
                    {{Form::open(array('method' => 'GET', 'id' => 'getPharmacyReportForm'))}}
                    <div class="box-body">
                        <div class="form-group col-md-4">
                            <label>From Date Time *</label>
                            <div class="bootstrap-timepicker">
                                <input required autocomplete="off" type="text" value="{{Input::get('from_datetime')}}"
                                       name="from_datetime" id="from_datetime"
                                       class="form-control datetimepicker">
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label>To Date Time *</label>
                            <div class="bootstrap-timepicker">
                                <input required autocomplete="off" type="text" value="{{Input::get('to_datetime')}}"
                                       name="to_datetime" id="to_datetime"
                                       class="form-control datetimepicker">
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Pharmacists</label>
                            <select autocomplete="off" name="pharmacy_id" id="pharmacy_id"
                                    class="form-control select2">
                                <option value="">Choose</option>
                                @foreach($pharmacists as $val)
                                    <option value="{{$val['id']}}" @if(Input::get('pharmacy_id') == $val['id'])
                                    selected @endif>{{ucwords(strtolower($val['full_name']))}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="box-footer">
                        <button class="btn btn-primary" id="getReport">Get</button>
                    </div>
                    {{Form::close()}}
                </div>
            </div>
            @if($c_user->user_type_id == 1 || $c_user->hasAccess('reports.pharmacy_report_excel'))
                <div class="col-md-12" id="download_excel_div" style="display: none;margin-bottom: 20px;">
                    <button id="download_excel" class="btn btn-primary">Download Excel</button>
                </div>
            @endif
            <div class="col-md-12">
                <div id="report_html">

                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="pharmacistLogsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span
                                aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Pharmacist Logs</h4>
                </div>
                <input type="hidden" id="pharmacist_id_modal">
                <div class="modal-body col-md-12" id="pharmacistLogsBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop