<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PMS - Queue System</title>
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
{{--    <link rel="stylesheet" href="{{asset('plugins/jssor-slider/jssor-slider.css')}}">--}}

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
    </style>

</head>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">
    <div class="content-wrapper">
        <div class="clearfix"></div>
        <section class="content" style="min-height: 450px;">
            <div class="row" id="reservationTable">
                {{$tables}}
            </div>
        </section>
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">

                        <div class="box-body">
                            <div class="col-md-4">
                                <img src="{{asset('plugins/jssor-slider/img/1.jpg')}}">
                            </div>
                            <div class="col-md-4">
                                <img src="{{asset('plugins/jssor-slider/img/2.jpg')}}">
                            </div>
                            <div class="col-md-4">
                                <img src="{{asset('plugins/jssor-slider/img/3.jpg')}}">
                            </div>
                            {{--<div id="jssor_1"--}}
                                 {{--style="position:relative;margin:0 auto;top:0px;left:0px;width:980px;height:250px;overflow:hidden;visibility:hidden;">--}}
                                {{--<!-- Loading Screen -->--}}
                                {{--<div data-u="loading"--}}
                                     {{--style="position:absolute;top:0px;left:0px;background-color:rgba(0,0,0,0.7);">--}}
                                    {{--<div style="filter: alpha(opacity=70); opacity: 0.7; position: absolute; display: block; top: 0px; left: 0px; width: 100%; height: 100%;"></div>--}}
                                    {{--<div style="position:absolute;display:block;background:url('{{asset('plugins/jssor-slider/img/loading.gif')}}') no-repeat center center;top:0px;left:0px;width:100%;height:100%;"></div>--}}
                                {{--</div>--}}
                                {{--<div data-u="slides"--}}
                                     {{--style="cursor:default;position:relative;top:0px;left:0px;width:980px;height:250px;overflow:hidden;">--}}
                                    {{--<div data-b="1">--}}
                                        {{--<img data-u="image"--}}
                                             {{--data-src2="{{asset('plugins/jssor-slider/img/1.jpg')}}"/>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                                {{--<!-- Bullet Navigator -->--}}
                                {{--<div data-u="navigator" class="jssorb01" style="bottom:16px;right:16px;"--}}
                                     {{--data-autocenter="1">--}}
                                    {{--<div data-u="prototype" style="width:12px;height:12px;"></div>--}}
                                {{--</div>--}}
                                {{--<!-- Arrow Navigator -->--}}
                {{--<span data-u="arrowleft" class="jssora03l" style="top:0px;left:8px;width:55px;height:55px;"--}}
                      {{--data-autocenter="2"></span>--}}
                {{--<span data-u="arrowright" class="jssora03r" style="top:0px;right:8px;width:55px;height:55px;"--}}
                      {{--data-autocenter="2"></span>--}}
                            {{--</div>--}}

                        </div>
                    </div>
                </div>

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
{{--<script src="{{asset('plugins/jssor-slider/jssor-slider.js')}}"></script>--}}
<script>
    $(function () {
//        jssor_1_slider_init();
        setInterval(
                function () {
                    $.ajax({
                        url: '{{route('getNextQueue')}}',
                        method: 'POST',
                        headers: {token: '{{csrf_token()}}'},
                        success: function (data) {
                            $("#reservationTable").html(data);
                        }
                    });
                }, 10000);

        setInterval(
                function () {
                    $(".highlight").css('background', 'lightgreen');
                    setTimeout(function () {
                        $(".highlight").css('background', 'darkgray');
                    }, 200)
                }, 500);
    });
</script>
</body>
</html>
