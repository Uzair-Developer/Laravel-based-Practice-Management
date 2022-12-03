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
        .surveyDivs {
            display: none;
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
                <div class="col-md-12">
                    <div class="box box-primary">
                        <!-- /.box-header -->
                        <div class="box-body">
                            <form id="survey_form">
                                <div class="form-group col-md-4 col-xs-12 @if($lang == 'en') pull-right @endif">
                                    <img style="width: 30vw;" src="{{asset('images/sgh-logo5.png')}}">
                                </div>
                                <div class="form-group col-md-8">
                                    <p style="text-align: justify;text-justify: inter-word;">
                                        @if($lang == 'ar')
                                            {{nl2br($survey['description_ar'])}}
                                        @else
                                            {{nl2br($survey['description_en'])}}
                                        @endif
                                    </p>
                                </div>

                                <div class="form-group col-md-5 col-xs-12 @if($lang == 'ar') pull-right @endif">
                                    @if($lang == 'ar')
                                        <label>فضلا ادخل رقم الكارت الطبى الخاص بك بالمستشفى PIN Number</label>
                                    @else
                                        <label>Please enter your PIN (The number in your card of the hospital)</label>
                                    @endif
                                    <input id="patient_id" class="form-control" autocomplete="off" name="id" type="text"/>
                                </div>
                                <div class="form-group col-md-2 col-xs-12 @if($lang == 'ar') pull-right @endif">
                                    <label>&nbsp;</label>

                                    <div>
                                        <button id="patient_id_btn" class="btn btn-info" type="button">
                                            @if($lang == 'ar')
                                                إبدأ
                                            @else
                                                Start
                                            @endif
                                        </button>
                                    </div>
                                </div>

                                <div class="form-group col-md-12 col-xs-12 surveyDivs @if($lang == 'ar') pull-right @endif">
                                    <label>
                                        @if($lang == 'ar')
                                            إسم المريض:
                                        @else
                                            Patient Name:
                                        @endif
                                    </label>
                                    <span id="patient_name"></span>

                                </div>
                                <div class="form-group col-md-12 col-xs-12 surveyDivs">
                                    <strong>
                                        @if($lang == 'ar')
                                            فضلا ضع تقييمك للتالى:
                                        @else
                                            Please rate the following:
                                        @endif
                                    </strong>
                                </div>

                                @foreach($surveyToGroups as $group)
                                    <div class="form-group col-md-12 col-xs-12 surveyDivs">
                                        <strong style="color: black;font-size: 20px;">
                                            @if($lang == 'ar')
                                                <label>{{$group['group']['title_ar']}}</label>
                                            @else
                                                <label>{{$group['group']['title_en']}}</label>
                                            @endif
                                        </strong>
                                        @foreach($group['group']['questions'] as $question)
                                            <div>
                                                <label style="font-weight: bold;">
                                                    @if($lang == 'ar')
                                                        {{$question['question']['title_ar']}}
                                                    @else
                                                        {{$question['question']['title_en']}}
                                                    @endif
                                                </label>

                                                <div>
                                                    @if($lang=='ar')
                                                        <?php $answers = explode(",", $question['question']['answerType']['answers_ar']) ?>
                                                    @else
                                                        <?php $answers = explode(",", $question['question']['answerType']['answers_en']) ?>

                                                    @endif
                                                    @foreach($answers as $key => $answer)
                                                        <div class="checkbox-list">
                                                            <label class="checkbox-inline">
                                                                <input required class="checkbox-inline"
                                                                       @if($question['question']['answerType']['type']==1)
                                                                       type="radio"
                                                                       name="answer[{{$group['group']['id']}}][{{$question['question']['id']}}]"
                                                                       @else
                                                                       type="checkbox"
                                                                       name="answer[{{$group['group']['id']}}][{{$question['question']['id']}}][]"
                                                                       @endif autocomplete="off" value="{{$key}}"> {{$answer}}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <br>

                                        @endforeach
                                    </div>
                                @endforeach
                                <div class="col-md-12 col-xs-12 surveyDivs">
                                    @if($lang == 'ar')
                                        {{$survey['footer_ar']}}
                                    @else
                                        {{$survey['footer_en']}}
                                    @endif
                                </div>
                                <div class="col-md-12 col-xs-12 surveyDivs">
                                    <input type="hidden" id="checkForm" value="2">
                                    <br>
                                    <button type="button" class="button subbutton btn btn-primary" id="saveSurvey">
                                        @if($lang == 'ar')
                                            إرسال
                                        @else
                                            Confirm
                                        @endif
                                    </button>
                                </div>
                            </form>
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
<script>
    $(function () {
        $(document).ready(function () {
            $(document).on("keyup", "#patient_id", function () {
                $(".surveyDivs").hide();
                $("#checkForm").val(2);
            });
            $(document).on("click", "#patient_id_btn", function () {
                if ($('#patient_id').val().length) {
                    $.ajax({
                        url: '{{route('websiteCheckInPatientDischarge')}}',
                        method: 'POST',
                        data: {
                            lang: "{{$lang}}",
                            patient_id: $('#patient_id').val()
                        },
                        dataType: "json",
                        crossDomain: true,
                        async: true,
                        success: function (data) {
                            if (data.status == 1) {
                                $(".surveyDivs").show();
                                $("#checkForm").val(1);
                                $("#patient_name").html(data.response);
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
                    var success = true;
                    $('#survey_form').find('input').each(function(){
                        if($(this).prop('required')){
                            var name = $(this).attr('name');
                            if(!$('input[name="'+name+'"]').is(':checked')){
                                console.log(name);
                                @if($lang == 'ar')
                                        alert('من فضلك إدخل كل الإجابات');
                                    @else
                                        alert('Please answer all questions');
                                    @endif
                                    success = false;
                                return false;
                            }
                        }
                    });
                    if(!success){
                        return false;
                    }
                    $.ajax({
                        url: '{{route('webSiteSaveInPatientSurvey')}}',
                        method: 'POST',
                        data: {
                            lang: "{{$lang}}",
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
