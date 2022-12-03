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
                <div style="margin-left: 0%;text-align: center;">
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

    $(function () {

        $(document).on('click', ".print_pharmacy_ticket", function (e) {
            loading('#loading');
            var pharmacy_ticket_type_id = $(this).attr('pharmacy_ticket_type_id');
            $.ajax({
                url: '{{route('kioskPrintPharmacyTicket')}}',
                method: 'POST',
                data: {pharmacy_ticket_type_id: pharmacy_ticket_type_id},
                async: false,
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    if (data.success == 'no') {
                        alert(data.msg);
                    }
                    $('#loading').waitMe('hide');
                }
            });
            $('#loading').waitMe('hide')
        });
    });
</script>
</body>
</html>
