<html moznomarginboxes mozdisallowselectionprint @if($lang == 'ar') lang="ar" @else lang="en" @endif>
<head>
    @if($lang == 'ar')
        <link rel="stylesheet" href="{{asset('css/reciept/style-ar.css')}}">
    @else
        <link rel="stylesheet" href="{{asset('css/reciept/style-en.css')}}">
    @endif
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <style>
        * {
            font-size: 20px;
        }
    </style>
    <style type="text/css" media="print">
        @page {
            size: auto;   /* auto is the initial value */
            margin: 0mm;  /* this affects the margin in the printer settings */
        }
    </style>
</head>
<body style="margin:0px; width:18cm; padding:0px; @if($lang == 'ar')float: right; @else float:left; @endif">
<div id="container">
    <div id="header">
        <!--<img src="reciept/header.png"/>-->
        <img src="{{asset('css/reciept/sgh-ryd2.jpg')}}" width="100" height="170">
    </div>
    <div class="vertical-line" style="height: 2px; width:18cm;margin: -10px 0 0 0;"></div>
    <!--end "vertical-line" div----->

    <div>
        <h2 align="center" style="font-size:20px; margin:5px">
            @if($lang == 'ar')
                <div class="arabic-word">
                    مستشفى السعودى الألمانى - الرياض
                </div>
            @else
                SAUDI GERMAN HOSPITAL - RIYADH
            @endif
        </h2>
    </div>
    <!--end "h" div----->

</div>
<!--end tab1 div--------------->
<div id="dataCustRec">

    <table border="2px" cellpadding="10px" cellspacing="5px" width="100%" @if($lang == 'ar') lang="ar" dir="rtl"
           @else lang="en" dir="ltr" @endif>

        <tr style="text-align: center;">
            <td>
                <label>
                    <h2 style="font-weight:bold;">
                        @if($lang == 'ar')
                            <div class="arabic-word">
                                ملف رقم:
                                {{Functions::enNumToAr($patient['registration_no'])}}
                            </div>
                        @else
                            File No.:
                            {{$patient['registration_no']}}
                        @endif
                    </h2>
                </label>
            </td>
        </tr>

        <tr>
            <td>
                <label>
                    <div style="font-weight:bold;">
                        @if($lang == 'ar')
                            <div class="arabic-word" style="float: right;margin-left: 7%;">
                                التاريخ:
                                <span>{{Functions::enNumToAr(date('d M Y', strtotime($reservation['date'])))}}</span>
                            </div>
                        @else
                            Date:
                            <span>{{date('d M Y', strtotime($reservation['date']))}}</span>
                        @endif

                        &nbsp;
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        &nbsp;

                        @if($reservation['type'] == 1 || $reservation['type'] == 3)
                            <?php $time = $reservation['time_from']; ?>
                            @if($reservation['type'] == 3)
                                <?php
                                $time = $reservation['revisit_time_from'];
                                $seconds = Functions::hoursToSeconds($time);
                                $newSeconds = $seconds + (10 * 60);
                                $time = Functions::timeFromSeconds($newSeconds);
                                ?>
                            @endif
                        @endif

                        @if($lang == 'ar')
                            <div class="arabic-word" style="float: right;margin-left: 7%;">
                                من:
                                @if($reservation['type'] == 1 || $reservation['type'] == 3)
                                    <span>{{Functions::enNumToAr(date('h:i A', strtotime($time)))}}</span>
                                @endif
                            </div>
                        @else
                            From:
                            @if($reservation['type'] == 1 || $reservation['type'] == 3)
                                <span>{{date('h:i A', strtotime($time))}}</span>
                            @endif
                        @endif

                        &nbsp;
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        &nbsp;
                        @if($lang == 'ar')
                            <div class="arabic-word" style="float: right;">
                                إلى:
                                @if($reservation['type'] == 1)
                                    <span dir="ltr">{{Functions::enNumToAr(date('h:i A', strtotime($reservation['time_to'])))}}</span>
                                @endif
                            </div>
                        @else
                            To:
                            @if($reservation['type'] == 1)
                                <span dir="ltr">{{date('h:i A', strtotime($reservation['time_to']))}}</span>
                            @endif
                        @endif
                    </div>
                </label>
            </td>
        </tr>

        <tr>
            <td>
                <label>
                    <div style="font-weight:bold;">
                        @if($lang == 'ar')
                            <div class="arabic-word">
                                إسم المريض:
                                {{ucwords(strtolower($reservation['patient_name']))}}
                            </div>
                        @else
                            Patient name:
                            {{ucwords(strtolower($reservation['patient_name']))}}
                        @endif
                    </div>
                </label>
            </td>
        </tr>

        <tr>
            <td>
                <label>
                    <div style="font-weight:bold;">
                        @if($lang == 'ar')
                            <div class="arabic-word">
                                العيادة:
                                {{$clinic['name_ar']}}
                            </div>
                        @else
                            Clinic:
                            {{$reservation['clinic_name']}}
                        @endif
                    </div>
                </label>
            </td>
        </tr>

        <tr>
            <td>
                <label>
                    <div style="font-weight:bold;">
                        @if($lang == 'ar')
                            <div class="arabic-word">
                                إسم الدكتور:
                                {{$physician['first_name_ar'] . ' ' . $physician['last_name_ar']}}
                            </div>
                        @else
                            Doctor name:
                            {{ucwords(strtolower($physician['full_name']))}}
                        @endif
                    </div>
                </label>
            </td>
        </tr>

        <tr>
            <td>
                <label>
                    <div style="font-weight:bold;">
                        @if ($reservation['create_by'])
                            <?php $create_by = User::getById($reservation['create_by']); ?>
                            @if($lang == 'ar')
                                <div class="arabic-word">
                                    موظف الحجز:
                                    {{ucwords(strtolower($create_by['full_name']))}}
                                </div>
                            @else
                                Reservation officer:
                                {{ucwords(strtolower($create_by['full_name']))}}
                            @endif
                        @endif
                    </div>
                </label>
            </td>
        </tr>
    </table>
</div>
<!----------------------------------------------->
<script>
    window.print();
</script>
</body>

</html>