<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PMS - Choose Your Way</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{asset('bootstrap-files/css/bootstrap.min.css')}}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{asset('css/font-awesome/css/font-awesome.min.css')}}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{asset('css/ionicons/css/ionicons.min.css')}}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{asset('dist/css/AdminLTE.min.css')}}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{asset('dist/css/skins/_all-skins.min.css')}}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" type="text/css" href="//www.fontstatic.com/f=rsail-bold"/>
    <link rel="stylesheet" href="{{asset('plugins/loading_mask/waitMe.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/jquery.numpad/css/jquery.keypad.css')}}">

    <style>
        .arabic-section {
            font-family: 'rsail-bold';
        }

        .fixed .content-wrapper, .fixed .right-side {
            padding-top: 0px;
        }

        .content-wrapper, .right-side, .main-footer {
            margin-left: 0px;
            transition: transform 0.3s ease-in-out 0s, margin 0.3s ease-in-out 0s;
            z-index: 820;
        }

        body {
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
    </style>

</head>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<input type="hidden" id="kioskGetWithReservationCode" url="{{route('kioskGetWithReservationCode')}}">
<!-- Site wrapper -->
<div class="wrapper">
    <div class="content-wrapper" id="loading">
        <div class="clearfix"></div>
        <section class="content">
            <div class="col-lg-12 col-xs-12">
                <a id="btnHome" style="padding: 3% 3%;font-size: 30px;display: none;position: absolute;"
                   class="btn btn-warning kiosk_back pull-left">
                    <i class="fa fa-home"></i> Home
                </a>
                <div style="argin-left: 0%;text-align: center;">
                    <img width="250" height="100" style=""
                         src="{{asset('images/sgh-logo5.png')}}">
                    <h2 style="">Welcome To Saudi German Hospital</h2>
                </div>
            </div>
            <div id="content_body">
                {{$buttons}}
            </div>
        </section>
    </div>
</div>

<div class="modal fade" id="modalPatientLate" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Notification</h4>
            </div>
            <div class="modal-body col-md-12">
                <div class="form-group col-md-12">
                    <h2 class="arabic-section">
                        نأسف لإلغاء حجزكم نظرا لتأخركم عن الموعد المحدد برجاء مراجعة الموظف لعمل ما يمكن عمله لكم.
                    </h2>
                </div>
                <div class="form-group col-md-12">
                    <h2>
                        We are sorry to inform you that your reservation is cancelled because of your delay, please refer to the receptionist to find any alternative for you.
                    </h2>
                </div>
                <input type="hidden" id="lateReservation">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button id="saveConvert" type="submit" class="btn btn-primary">Ok</button>
            </div>
        </div>
    </div>
</div>


<!-- jQuery 2.1.4 -->
<script src="{{asset('plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<script src="{{asset('plugins/jQueryUI/jquery-ui.min.js')}}"></script>
<!-- Bootstrap 3.3.5 -->
<script src="{{asset('bootstrap-files/js/bootstrap.min.js')}}"></script>
<!-- SlimScroll -->
<script src="{{asset('plugins/slimScroll/jquery.slimscroll.min.js')}}"></script>
<!-- FastClick -->
<script src="{{asset('plugins/fastclick/fastclick.min.js')}}"></script>
<!-- iCheck 1.0.1 -->
<!-- AdminLTE App -->
<script src="{{asset('dist/js/app.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('dist/js/demo.js')}}"></script>
<script src="{{asset('plugins/loading_mask/waitMe.js')}}"></script>
<script src="{{asset('plugins/input-mask/jquery.inputmask.js')}}"></script>
<script src="{{asset('plugins/jquery.numpad/js/jquery.plugin.min.js')}}"></script>
<script src="{{asset('plugins/jquery.numpad/js/jquery.keypad.js')}}"></script>
<script>
    function loading(id) {
        $(id).waitMe({
            effect: 'ios',
            text: 'Please wait...',
            bg: 'rgba(255,255,255,0.7)',
            color: '#000',
            maxSize: '',
            source: 'img.svg'
        });
    }

    var idleTime = 0;
    function timerIncrement() {
        idleTime = idleTime + 1;
        if (idleTime > 20) { // 20 sec
            idleTime = 0;
            $.ajax({
                url: '{{route('kioskBack')}}',
                method: 'POST',
                data: {},
                async: false,
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#content_body").html(data);
                    $("#btnHome").hide();
                    $('.keypad-popup').hide();
                }
            });
        }
    }

    $(function () {
        //Increment the idle time counter.
        var idleInterval = setInterval(timerIncrement, 1000);

        //Zero the idle timer on mouse movement.
        $(this).mousemove(function (e) {
            idleTime = 0;
        });
        $(this).keypress(function (e) {
            idleTime = 0;
        });

        $(document).on('click', "#no_reservation_btn", function (e) {
            loading('#loading');
            $.ajax({
                url: '{{route('kioskNoReservation')}}',
                method: 'POST',
                data: {},
                async: false,
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#btnHome").hide();
                    if (data.success == 'no') {
                        alert(data.msg);
                    }
                    $('#loading').waitMe('hide');
                }
            });
            $('#loading').waitMe('hide')
        });

        $(document).on('click', "#with_reservation_btn", function (e) {
            loading('#loading');
            $.ajax({
                url: '{{route('kioskGetWithReservation')}}',
                method: 'POST',
                data: {},
                async: false,
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#content_body").html(data);
                    $("#btnHome").show();
                    $('#loading').waitMe('hide');
                }
            });
            $('#loading').waitMe('hide')
        });

        $(document).on('click', "#with_reservation_search", function (e) {
            loading('#loading');
            if (!$("#reservation_code").val().length && !$("#registration_no").val().length && !$("#phone").val().length) {
                $('#loading').waitMe('hide');
                return;
            }
            $.ajax({
                url: '{{route('kioskGetWithReservationCode')}}',
                method: 'POST',
                data: {
                    code: $("#reservation_code").val(),
                    phone: $("#phone").val(),
                    registration_no: $("#registration_no").val()
                },
                async: false,
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    if (data.success == 'yes') {
                        $("#with_reservation_table").html(data.return);
                    } else {
                        alert(data.msg);
                    }
                    $("#reservation_code, #registration_no, #phone").val('');
                    $("#btnHome").show();
                    $('#loading').waitMe('hide');
                }
            });
            $('#loading').waitMe('hide');
        });

        $(document).on('click', ".with_reservation_print", function (e) {
            loading('#loading');
            var reservation_id = $(this).attr('reservation_id');
            $.ajax({
                url: '{{route('kioskWithReservationPrint')}}',
                method: 'POST',
                data: {
                    reservation_id: reservation_id
                },
                async: false,
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    if (data.success == 'yes') {
                        $("#content_body").html(data.buttons);
                        $("#btnHome").hide();
                    } else {
                        if (data.msg == 'late') {
                            $("#lateReservation").val(data.return['id']);
                            $("#modalPatientLate").modal('show');
                            $("#btnHome").show();
                        } else {
                            alert(data.msg);
                            $("#btnHome").show();
                        }
                    }
                    $("#reservation_code").val('');
                    $('#loading').waitMe('hide');
                }
            });
            $('#loading').waitMe('hide');
        });

        $(document).on('click', "#saveConvert", function (e) {
            $("#modalPatientLate").modal('hide');
            loading('#loading');
            $.ajax({
                url: '{{route('kioskWithReservationConvertToWaiting')}}',
                method: 'POST',
                data: {
                    reservation_id: $("#lateReservation").val()
                },
                async: false,
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    if (data.success == 'yes') {
                        $("#content_body").html(data.buttons);
                        $("#btnHome").hide();
                        $("#lateReservation").val('');
                        $('#loading').waitMe('hide');
                    } else {
                        alert(data.msg);
                        $("#btnHome").show();
                    }
                }
            });
            $('#loading').waitMe('hide');
        });

        $(document).on('click', "#printAll", function (e) {
            loading('#loading');
            var reservation_id = $('#printAllInput').val();
            $.ajax({
                url: '{{route('kioskWithReservationPrint')}}',
                method: 'POST',
                data: {
                    reservation_id: reservation_id
                },
                async: false,
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    if (data.success == 'yes') {
                        $("#content_body").html(data.buttons);
                        $("#btnHome").hide();
                    } else {
                        alert(data.msg);
                        $("#btnHome").show();
                    }
                    $("#reservation_code").val('');
                    $('#loading').waitMe('hide');
                }
            });
            $('#loading').waitMe('hide');
        });

        $(document).on('click', ".kiosk_back", function (e) {
            loading('#loading');
            $.ajax({
                url: '{{route('kioskBack')}}',
                method: 'POST',
                data: {},
                async: false,
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#content_body").html(data);
                    $("#btnHome").hide();
                    $('#loading').waitMe('hide');
                }
            });
            $('#loading').waitMe('hide')
        });
    });
</script>
</body>
</html>
