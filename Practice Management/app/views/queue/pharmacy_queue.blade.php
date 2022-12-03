
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PMS - Pharmacy Queue System</title>
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
    <style>
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #d8d8d8
        }
    </style>
</head>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue fixed sidebar-mini">
<div class="row" style="margin-top:1.5%;text-align: center;">
    <img width="400" height="150" src="{{asset('images/sgh-logo5.png')}}">
</div>
<br>
<div id="pharmacy_queue">
    {{$queue_num}}
</div>

<audio id="audiotag" src="{{asset('uploads/queue3.mp3')}}" preload="auto"></audio>

<script>
    function strpos(haystack, needle, offset) {
        var i = (haystack + '').indexOf(needle, (offset || 0));
        return i === -1 ? false : i;
    }

    $(function () {
        var countArray = [];
        // localStorage.setItem("countArray", JSON.stringify(countArray));
        setInterval(
            function () {
                $.ajax({
                    url: '{{route('listPharmacyQueueAjax')}}',
                    method: 'POST',
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        // localStorage.clear();
                        if(data.localStorageReset == 'yes') {
                            localStorage.clear();
                        }
                        $("#pharmacy_queue").html(data.html);
                        $(".queue_code").each(function (index) {
                            if(JSON.parse(localStorage.getItem("countArray"))) {
                                countArray = JSON.parse(localStorage.getItem("countArray"));
                            } else {
                                countArray = []
                            }
                            // console.log(countArray);
                            if (index in countArray) {
                                if (countArray[index] != $(this).html()) {
                                    if (!strpos($(this).html(), '---') && !strpos($(this).html(), 'X')) {
                                        makeColorSound($(this));
                                    }
                                    countArray[index] = $(this).html();
                                    localStorage.setItem("countArray", JSON.stringify(countArray));
                                }
                            } else {
                                if (!strpos($(this).html(), '---') && !strpos($(this).html(), 'X')) {
                                    makeColorSound($(this));
                                }
                                countArray[index] = $(this).html();
                                localStorage.setItem("countArray", JSON.stringify(countArray));
                            }
                        });
                    }
                });
            }, 10000);

        function makeColorSound(object) {
            document.getElementById('audiotag').play();
            object.css('color', 'green')
                .fadeIn(250).fadeOut(250)
                .fadeIn(250).fadeOut(250)
                .fadeIn(250).fadeOut(250)
                .fadeIn(250).fadeOut(250)
                .fadeIn(250).fadeOut(250)
                .fadeIn(250).fadeOut(250)
                .fadeIn(250).fadeOut(250)
                .fadeIn(250);
            setTimeout(function () {
                object.css('color', '#333');
            }, 3750);
        }
    });
</script>
</body>
</html>