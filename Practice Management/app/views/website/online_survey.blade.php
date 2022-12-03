<!DOCTYPE html>
<html>
<head @if($inputs['lang'] == 'ar') dir="rtl" @endif>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        @if($inputs['lang'] == 'ar')
            إستبيان
        @else
            Online Survey
        @endif
    </title>
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
    <link rel="stylesheet" type="text/css" href="//www.fontstatic.com/f=dubai"/>

    <style>
        .arabic-section {
            font-family: 'dubai';
            font-size: 17px;
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
<body class="hold-transition skin-blue fixed sidebar-mini @if($inputs['lang'] == 'ar') arabic-section @endif"
      @if($inputs['lang'] == 'ar') style="direction: rtl" @endif>
<!-- Site wrapper -->
<div class="wrapper">
    <div class="content-wrapper">
        <div class="clearfix"></div>
        <section class="content">
            <div class="row">
                @if($reservation)
                    {{$html}}
                @endif
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
<script>
    $(function () {
        $(document).ready(function () {
            $.ajax({
                url: '{{route('webSiteAPIGetReservationData')}}',
                method: 'POST',
                data: {
                    lang: "{{$lang}}",
                    c: "{{$reservation_id}}}"
                },
                dataType: "json",
                crossDomain: true,
                async: true,
                success: function (data) {
                    if (data.status == 1) {
                        $('#survey_wrapper').html(data.response);
                    }
                }
            });
            $(document).on("change", "#patient_id", function () {
                if ($(this).val().length) {
                    $.ajax({
                        url: '{{route('websiteAPICheckPatientWithReservation')}}',
                        method: 'POST',
                        data: {
                            lang: "{{$lang}}",
                            reservation_id: "{{$reservation_id}}}",
                            patient_id: $(this).val()
                        },
                        dataType: "json",
                        crossDomain: true,
                        async: true,
                        success: function (data) {
                            if (data.status == 1) {
                                $(".surveyDivs").show();
                                $("#checkForm").val(1);
                            } else {
                                alert(data.response);
                                $(".surveyDivs").hide();
                                $("#checkForm").val(2);
                            }
                        }
                    });
                } else {
                    $(".surveyDivs").hide();
                    $("#checkForm").val(2);
                }
            });
            $(document).on("click", "#saveSurvey", function () {
                if ($("#checkForm").val() == 1) {
                    $.ajax({
                        url: '{{route('webSiteAPISaveSurvey')}}',
                        method: 'POST',
                        data: {
                            lang: "{{$lang}}",
                            reservation_id: "{{$reservation_id}}}",
                            form: $("#survey_form").serialize()
                        },
                        dataType: "json",
                        crossDomain: true,
                        async: true,
                        success: function (data) {
                            if (data.status == 1) {
                                alert(data.response);
                                $(".surveyDivs").hide();
                                $("#checkForm").val(2);
                                window.location.replace('http://sghcairo.com');
                            }
                        }
                    });
                }
            });
        });
    });
</script>
</body>
</html>
