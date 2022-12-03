<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PMS - Reception Queue</title>
    <style>
        .flip-clock-wrapper {
            width: auto;
            margin-left: 37%;
        }

        .flip {
            color: red;
        }
    </style>
</head>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body>
<div style="text-align: center;">
    <h1 style="font-size: 80px;" id="clock"></h1>
</div>

<script src="{{asset('plugins/jQuery/jQuery-2.1.4.min.js')}}"></script>
<script>
    @if($success == 'yes')
    $("#clock").html('{{$number}}');
    @else
    $("#clock").html('00000');
    @endif
    var audio = new Audio('{{asset('uploads/queue3.mp3')}}');
    audio.play();

    setInterval(
            function () {
                $.ajax({
                    url: '{{route('listReceptionQueue')}}',
                    method: 'POST',
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        old_number = $("#clock").html();
                        if (old_number != data) {
                            $("#clock").html(data);
                            var audio = new Audio('{{asset('uploads/queue3.mp3')}}');
                            audio.play();
                        }
                    }
                });
            }, 5000);
</script>
</body>
</html>