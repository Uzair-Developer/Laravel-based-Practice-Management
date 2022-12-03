<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>PMS @yield('title')</title>
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
    <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/select2/select2.min.css')}}">
    {{--<link rel="stylesheet" href="{{asset('plugins/autocomplete/jquery.autocomplete.css')}}">--}}
    <style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            color: black !important;
        }

        .select2 {
            width: 100%;
        }

        .line-through {
            text-decoration: line-through
        }
    </style>
    @yield('header')
    <style>
        .ui-widget.ui-widget-content {
            z-index: 2147483647;
        }
    </style>
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->

</head>
<!-- ADD THE CLASS fixed TO GET A FIXED HEADER AND SIDEBAR LAYOUT -->
<!-- the fixed layout is not compatible with sidebar-mini -->
<body class="hold-transition skin-blue sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a class="logo" href="{{route('home')}}">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>PMS</b></span>
            <!-- logo for regular state and mobile devices -->

            <span class="logo-lg">
                    <b><img src="{{asset('images/SGH.png')}}" height="45" width="155"></b>
                </span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    @if($c_user->user_type_id == 7)
                        <?php $physicianData = Physician::getByPhysicianId($c_user->id); ?>
                        <li class="">
                            <a class="
                            @if($physicianData)
                            @if ($physicianData['current_status'] == 0)
                                    bg-gray
                                    @elseif($physicianData['current_status'] == 1)
                                    bg-orange
                                    @elseif($physicianData['current_status'] == 2)
                                    bg-green
                                @endif
                            @else
                                    bg-gray
                                    @endif
                                    btn btn-block
                            " href="{{route('editProfile')}}" style="color:black;">
                                <i class="fa fa-user"></i> Profile</a>
                        </li>
                    @endif
                    <li class="">
                        <a style="cursor: pointer;" data-target="#myModalAgent" data-toggle="modal">
                            <i class="fa fa-edit"></i> Add Announcement
                        </a>
                    </li>
                    @if($c_user->user_type_id == 7)
                        @if($c_user->is_ready == 1)
                            <li class="">
                                <a style="cursor: pointer;" class="btn btn-block btn-success"
                                   data-target="#notReadyReasonModal"
                                   data-toggle="modal">
                                    Now: Ready
                                </a>
                            </li>
                        @else
                            <li class="">
                                <a style="cursor: pointer;" class="btn btn-block btn-danger"
                                   href="{{route('userReadyMonitor')}}">
                                    Now: Not Ready
                                </a>
                            </li>
                    @endif
                @endif
                <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            @if($user->image_url)
                                <img src="{{asset($user->image_url)}}" class="user-image"
                                     title="{{$user->image_url}}"
                                     alt="{{$user->image_url}}">
                            @else
                                <img src="{{asset('dist/img/avatar5.png')}}" class="user-image"
                                     title="User Image"
                                     alt="User Image">
                            @endif
                            <span class="hidden-xs">{{$user->user_name}}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                @if($user->image_url)
                                    <img src="{{asset($user->image_url)}}" class="img-circle"
                                         title="{{$user->image_url}}" alt="{{$user->image_url}}">
                                @else
                                    <img src="{{asset('dist/img/avatar5.png')}}" class="img-circle"
                                         title="User Image"
                                         alt="User Image">
                                @endif
                                <p>
                                    {{$user->full_name}}
                                    {{--<small>Member since Nov. 2012</small>--}}
                                </p>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="{{route('editProfile')}}"
                                       class="btn btn-default btn-flat">Profile</a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{route('logout')}}" class="btn btn-default btn-flat">Sign out</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                    <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- =============================================== -->

    <!-- Left side column. contains the sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <ul class="sidebar-menu">
                @if($user->user_type_id != 0)
                    @if($user->user_type_id == 1 || $user->hasAccess('user.list'))
                        <li><a href="{{route('users')}}"><i class="fa fa fa-users"></i><span>Users</span></a></li>
                    @endif
                    @if($user->hasAccess('patient.list') || $user->user_type_id == 1)
                        <li>
                            <a href="{{route('listPatient')}}">
                                <i class="fa fa-users"></i> <span>Patients</span>
                            </a>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->user_type_id == 2 || $user->hasAccess('permissions.list'))
                        <li>
                            <a href="{{route('listGroup')}}">
                                <i class="fa fa-gear"></i> <span>Permissions Group</span>
                            </a>
                        </li>
                    @endif
                    @if($user->hasAccess('agentComment.list') || $user->user_type_id == 1)
                        <li>
                            <a href="{{route('listAgentComment')}}">
                                <i class="fa fa-th"></i> <span>Announcements</span>
                                @if($agentComments)
                                    <small class="label pull-right bg-green">New</small>
                                @endif
                            </a>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('manageReservation.list'))
                        @if($user->user_type_id != \core\enums\UserRules::patientRelation)
                            <li>
                                <a href="{{route('manageClinicReservations')}}?date_from={{date('Y-m-d')}}&date_to={{date('Y-m-d')}}"><i
                                            class="fa fa fa-users"></i>
                                    <span>All Reservations</span>&nbsp;
                                    @if($c_user->user_type_id == \core\enums\UserRules::physician)
                                        @if($countNewWalkInReservation)
                                            <small class="label bg-orange">{{($countNewWalkInReservation)}}
                                                New
                                            </small>
                                        @endif
                                    @endif
                                    @if($c_user->user_type_id == \core\enums\UserRules::receptionPersonnel
                                    || $c_user->user_type_id == \core\enums\UserRules::clinicManager
                                    || $c_user->user_type_id == 1)
                                        @if($countNewWalkInReservation)
                                            <small class="label bg-orange">{{($countNewWalkInReservation)}}
                                                N
                                            </small>
                                        @endif
                                        @if($countApprovalWalkInReservation)
                                            <small class="label bg-green">{{($countApprovalWalkInReservation)}}
                                                A
                                            </small>
                                        @endif
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endif
                    @if($user->user_type_id == 1 || ($user->user_type_id == 7 && $user->hasAccess('manageReservation.list')))
                        <li>
                            <a href="{{route('manageClinicReservations')}}?today_only=1&date_from={{date('Y-m-d')}}&date_to={{date('Y-m-d')}}">
                                <i class="fa fa fa-users"></i> <span>Today Reservations
                                    </span>
                            </a>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('standAlonReservation.list'))
                        <li>
                            <a href="{{route('standAloneRevisit')}}">
                                <i class="fa fa fa-gears"></i> <span>Stand Alone Res</span>
                            </a>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('clinic_pms.times_availability'))
                        <li>
                            <a href="{{route('getClinicAvailability')}}">
                                <i class="fa fa fa-gears"></i> <span>Clinic Availability</span>
                            </a>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('complain.list'))
                        <li>
                            <a href="{{route('listComplain')}}">
                                <i class="fa fa fa-gears"></i> <span>Complaints</span>
                                @if($newComplains)
                                    <small class="label pull-right bg-green">{{count($newComplains)}} New</small>
                                @endif
                            </a>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('physician_attribute.list'))
                        <li><a href="{{route('listPhysicianAttribute')}}"><i class="fa fa-gear"></i>
                                <span>Profile Settings</span>
                            </a>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('physician.list'))
                        <li><a href="{{route('physicians')}}"><i class="fa fa-gear"></i>
                                <span>Doctors</span>&nbsp;
                                @if($physicianNoAction)
                                    <small class="label bg-gray">{{$physicianNoAction}}E</small>
                                @endif
                                @if($physicianNeedApprove)
                                    <small class="label bg-orange">{{$physicianNeedApprove}}P</small>
                                @endif
                                @if($physicianPublish)
                                    <small class="label bg-green">{{$physicianPublish}}A</small>
                                @endif
                            </a>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('physicianSchedule.list'))
                        <li><a href="{{route('physicianSchedules')}}"><i class="fa fa fa-gear"></i>
                                <span>Doctor Schedules</span>
                            </a></li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('physicianScheduleException.list'))
                        <li><a href="{{route('listPhysicianScheduleException')}}"><i class="fa fa fa-gear"></i>
                                <span>Dr Schedules Exception</span>
                            </a></li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('physicianException.list'))
                        <li><a href="{{route('physicianExceptions')}}"><i class="fa fa fa-gear"></i>
                                <span>Dr. Exceptions</span> &nbsp;&nbsp;&nbsp;&nbsp;
                                @if($countPendingException)
                                    <small class="label bg-orange">{{count($countPendingException)}}</small>
                                @endif
                                @if($user->user_type_id == 7)
                                    @if($countApprovedException)
                                        <small class="label bg-green">{{count($countApprovedException)}}</small>

                                    @endif
                                    @if($countNotApprovedException)
                                        <small class="label bg-red">{{count($countNotApprovedException)}}</small>
                                    @endif
                                @endif
                            </a>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('physicianCalendar.list'))
                        <li><a href="{{route('viewCalendar')}}"><i class="fa fa fa-calendar"></i>
                                <span>Doctor Calendar</span>
                            </a></li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('reservation.list'))
                        <li style="cursor: pointer;"><a data-target="#myModalReservation" data-toggle="modal"><i
                                        class="fa fa fa-gears"></i>
                                <span>Booking</span>
                            </a>
                        </li>
                    @endif

                    @if($user->user_type_id == 1 || $user->hasAccess('patientLapRadiology.list'))
                        <li><a href="{{route('listPatientLapRadiology')}}"><i class="fa fa fa-gears"></i>
                                <span>Lab Results</span>
                            </a></li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('patient_attend.list'))
                        <li><a href="{{route('listPatientAttend')}}"><i class="fa fa fa-gears"></i>
                                <span>Patient Attend</span>
                            </a></li>
                    @endif

                    @if($user->user_type_id == 1 || $user->hasAccess('pmsDiagnosis.list'))
                        <li>
                            <a href="{{route('pmsDiagnosis')}}"><i class="fa fa fa-gears"></i>
                                <span>PMS Diagnosis</span>
                            </a>
                        </li>
                    @endif

                    @if($user->user_type_id == 1 || $user->hasAccess('reports.physician_reports') || $user->hasAccess('reports.clinic_reports')
                    || $user->hasAccess('reports.physician_exception_reports'))
                        <li class="treeview" id="pms_menu">
                            <a href="#">
                                <i class="fa fa-dashboard"></i> <span>Reports</span> <i
                                        class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                @if($user->user_type_id == 1 || $user->hasAccess('reports.physician_reports'))
                                    <li><a href="{{route('getPhysicianReports')}}"><i class="fa fa-gear"></i>
                                            <span>Doctor Report</span>
                                        </a>
                                    </li>
                                @endif
                                @if($user->user_type_id == 1 || $user->hasAccess('reports.clinic_reports'))
                                    <li><a href="{{route('getClinicReports')}}"><i class="fa fa fa-gears"></i>Clinic
                                            Reports</a>
                                    </li>
                                @endif
                                @if($user->user_type_id == 1 || $user->hasAccess('reports.physician_exception_reports'))
                                    <li><a href="{{route('getPhysicianExceptionReports')}}"><i
                                                    class="fa fa fa-gears"></i>Dr Exception
                                            Reports</a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('answerType.list') || $user->hasAccess('question.list')
                    || $user->hasAccess('surveyGroup.list') || $user->hasAccess('survey.list')
                    || $user->hasAccess('patientSurvey.list') || $user->hasAccess('patientSurvey.report'))
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-tint"></i> <span>Survey</span> <i
                                        class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                @if($user->user_type_id == 1 || $user->hasAccess('answerType.list'))
                                    <li>
                                        <a href="{{route('listAnswerType')}}">
                                            <i class="fa fa fa-gears"></i> <span>Answer Types</span>
                                        </a>
                                    </li>
                                @endif
                                @if($user->user_type_id == 1 || $user->hasAccess('question.list'))
                                    <li>
                                        <a href="{{route('listQuestion')}}">
                                            <i class="fa fa fa-gears"></i> <span>Questions</span>
                                        </a>
                                    </li>
                                @endif
                                @if($user->user_type_id == 1 || $user->hasAccess('surveyGroup.list'))
                                    <li>
                                        <a href="{{route('listSurveyGroup')}}">
                                            <i class="fa fa fa-gears"></i> <span>Survey Groups</span>
                                        </a>
                                    </li>
                                @endif
                                @if($user->user_type_id == 1 || $user->hasAccess('survey.list'))
                                    <li>
                                        <a href="{{route('listSurvey')}}">
                                            <i class="fa fa fa-gears"></i> <span>Surveys</span>
                                        </a>
                                    </li>
                                @endif
                                @if($user->user_type_id == 1 || $user->hasAccess('patientSurvey.list'))
                                    <li>
                                        <a href="{{route('listPatientSurvey')}}">
                                            <i class="fa fa fa-gears"></i> <span>Patient Surveys</span>
                                        </a>
                                    </li>
                                @endif
                                @if($user->user_type_id == 1 || $user->hasAccess('patientSurvey.report'))
                                    <li>
                                        <a href="{{route('reportCountsPatientSurvey')}}">
                                            <i class="fa fa fa-gears"></i> <span>Report</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                    @endif
                    @if($user->user_type_id == 1 || $user->hasAccess('manageReservation.view_history'))
                        <li>
                            <a href="{{route('reservationHistory')}}?hospital_id=2&date_from={{date('Y-m-d')}}&date_to={{date('Y-m-d')}}"><i
                                        class="fa fa fa-eye"></i>
                                <span>Reservations History</span>
                            </a>
                        </li>
                    @endif

                    <?php $show_pms = false; ?>
                    <li class="treeview" id="pms_menu">
                        <a href="#">
                            <i class="fa fa-dashboard"></i> <span>PMS Menu</span> <i
                                    class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">
                            @if($user->user_type_id == 1 || $user->hasAccess('AttributePms.list'))
                                <?php $show_pms = true; ?>
                                <li><a href="{{route('listAttributePms')}}"><i class="fa fa fa-gears"></i>Pms Attribute</a>
                                </li>
                            @endif
                            @if($user->user_type_id == 1 || $user->hasAccess('publicHoliday.list'))
                                <?php $show_pms = true; ?>
                                <li class="treeview">
                                    <a href="#">
                                        <i class="fa fa-gears"></i> <span>System</span> <i
                                                class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        @if($user->user_type_id == 1)
                                            <li><a href="{{route('systemRoot')}}"><i class="fa fa-laptop"></i> System
                                                    Root</a>
                                            </li>
                                        @endif
                                        @if($user->user_type_id == 1 || $user->hasAccess('publicHoliday.list'))
                                            <li><a href="{{route('publicHoliday')}}"><i class="fa fa-globe"></i> Public
                                                    Holidays</a>
                                            </li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            @if($user->user_type_id == 1 || $user->hasAccess('hospital.list'))
                                <?php $show_pms = true; ?>
                                <li><a href="{{route('hospitals')}}"><i class="fa fa fa-heartbeat"></i>Hospitals</a>
                                </li>
                            @endif

                            @if($user->user_type_id == 1 || $user->hasAccess('smsCampaign.access'))
                                <?php $show_pms = true; ?>
                                <li><a href="{{route('smsCampaign')}}"><i class="fa fa fa-envelope"></i>SMS
                                        Campaign</a>
                                </li>
                            @endif
                            @if($user->user_type_id == 1 || $user->hasAccess('clinic_pms.list') || $user->hasAccess('clinicSchedule.list'))
                                <?php $show_pms = true; ?>
                                <li class="treeview">
                                    <a href="#">
                                        <i class="fa fa-tint"></i> <span>Clinics</span> <i
                                                class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        @if($user->user_type_id == 1 || $user->hasAccess('clinic_pms.list'))
                                            <li><a href="{{route('clinics')}}"><i class="fa fa fa-table"></i> Manage
                                                    Clinics</a>
                                            </li>
                                        @endif
                                        @if($user->user_type_id == 1 || $user->hasAccess('clinicSchedule.list'))
                                            <li><a href="{{route('clinicSchedules')}}"><i class="fa fa fa-calendar"></i>
                                                    Clinic
                                                    Schedules</a></li>
                                        @endif
                                    </ul>
                                </li>
                            @endif

                            @if($user->user_type_id == 1)
                                <?php $show_pms = true; ?>
                                <li class="treeview">
                                    <a href="#">
                                        <i class="fa fa-tint"></i> <span>Website</span> <i
                                                class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        <li>
                                            <a href="{{route('websiteSettings')}}"><i class="fa fa fa-table"></i>
                                                Website Settings</a>
                                        </li>
                                    </ul>
                                </li>
                            @endif

                            @if($user->user_type_id == 1 || $user->hasAccess('ipToScreen.list'))
                                <?php $show_pms = true; ?>
                                <li><a href="{{route('ipToScreen')}}"><i class="fa fa fa-gears"></i>IP To Screen</a>
                                </li>
                            @endif

                            @if($user->user_type_id == 1 || $user->hasAccess('ipToRoom.list'))
                                <?php $show_pms = true; ?>
                                <li><a href="{{route('ipToRoom')}}"><i class="fa fa fa-gears"></i>IP To Room</a>
                                </li>
                            @endif

                            @if($user->user_type_id == 1 || $user->hasAccess('manageReservation.open_close'))
                                <?php $show_pms = true; ?>
                                <li><a href="{{route('reservationManage')}}"><i class="fa fa fa-users"></i>
                                        Clinic Opening</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
                <li class="treeview" id="diagnosis_menu">
                    <?php $show_diagnosis = false; ?>
                    <a href="#">
                        <i class="fa fa-user"></i> <span>Diagnosis Menu</span> <i
                                class="fa fa-angle-left pull-right"></i>
                    </a>
                    <ul class="treeview-menu">
                        @if($user->hasAccess('country.list') || $user->user_type_id == 1)
                            <?php $show_diagnosis = true; ?>
                            <li class="treeview">
                                <a href="{{route('listCountry')}}">
                                    <i class="fa fa-gears"></i> <span>Countries</span>
                                </a>
                            </li>
                        @endif
                        @if($user->hasAccess('organ.list') || $user->user_type_id == 1)
                            <?php $show_diagnosis = true; ?>
                            <li><a href="{{route('listOrgan')}}"><i class="fa fa-gears "></i>Organs</a></li>
                        @endif
                        @if($user->hasAccess('clinic.list') || $user->hasAccess('clinicSpecialty.list')
                        || $user->user_type_id == 1)
                            <?php $show_diagnosis = true; ?>
                            <li class="treeview">
                                <a href="#">
                                    <i class="fa fa-tint"></i> <span>Clinics</span> <i
                                            class="fa fa-angle-left pull-right"></i>
                                </a>
                                <ul class="treeview-menu">
                                    @if($user->hasAccess('clinic.list') || $user->user_type_id == 1)
                                        <?php $show_diagnosis = true; ?>
                                        <li><a href="{{route('dListClinic')}}"><i class="fa fa fa-gears"></i>Manage
                                                Clinics</a>
                                        </li>
                                    @endif
                                    @if($user->hasAccess('clinicSpecialty.list') || $user->user_type_id == 1)
                                        <?php $show_diagnosis = true; ?>
                                        <li><a href="{{route('listSpecialty')}}"><i class="fa fa fa-gears"></i>Manage
                                                Specialties</a>
                                        </li>
                                    @endif
                                </ul>
                            </li>
                        @endif
                        @if($user->hasAccess('symptom.list') || $user->user_type_id == 1)
                            <?php $show_diagnosis = true; ?>
                            <li><a href="{{route('listSymptom')}}"><i class="fa fa-flash"></i>Symptoms</a></li>
                        @endif
                        @if($user->hasAccess('disease.list') || $user->user_type_id == 1)
                            <?php $show_diagnosis = true; ?>
                            <li><a href="{{route('listDisease')}}"><i class="fa fa-heartbeat"></i>Diseases</a></li>
                        @endif
                        @if($user->hasAccess('diagnosis.list') || $user->user_type_id == 1)
                            <?php $show_diagnosis = true; ?>
                            <li class="treeview">
                                <a href="{{route('startDiagnosis1')}}">
                                    <i class="fa fa-heartbeat"></i> <span>Start Diagnosis</span>
                                </a>
                            </li>
                        @endif
                        @if($user->hasAccess('pendingRelation.list') || $user->user_type_id == 1)
                            <?php $show_diagnosis = true; ?>
                            <li>
                                <a href="{{route('diseaseSymptomsPending')}}">
                                    <i class="fa fa-th"></i> <span>Pending Relations</span>
                                    @if($diseaseSymptomsPending)
                                        <small class="label pull-right bg-green">new</small>
                                    @endif
                                </a>
                            </li>
                        @endif
                        @if($user->hasAccess('symptomComment.list') || $user->user_type_id == 1)
                            <?php $show_diagnosis = true; ?>
                            <li class="treeview">
                                <a href="{{route('listComment', 'symptom')}}">
                                    <i class="fa fa-heartbeat"></i> <span>Symptom Comments</span>
                                </a>
                            </li>
                        @endif
                        @if($user->hasAccess('instruction.list') || $user->user_type_id == 1)
                            <?php $show_diagnosis = true; ?>
                            <li>
                                <a href="{{route('editInstruction' ,1)}}">
                                    <i class="fa fa-gear"></i> <span>Instructions</span>
                                </a>
                            </li>
                        @endif
                    </ul>
                </li>
            </ul>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- =============================================== -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
    @include('layout/flashMessages')

    <!-- Main content -->
    @yield('content')
    <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> 1.0
        </div>
        <strong>Copyright &copy; 2016-2017 <a>JTech</a>.</strong> All rights
        reserved.
    </footer>
</div>
<!-- ./wrapper -->


<div class="modal fade" id="myModalReservation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Reservation Menu</h4>
            </div>
            {{Form::open(array('role'=>"form", 'route' => 'reservationAdd'))}}
            <div class="modal-body">

                <div class="form-group">
                    <label>Hospital</label>
                    <br>
                    <select autocomplete="off" id="selectHospital" required name="hospital_id"
                            class="form-control select2" style="width:400px">
                        <option value="">Choose</option>
                        @foreach($hospitals as $val)
                            <option value="{{$val['id']}}" @if(Input::old('hospital_id') == $val['id'])
                            selected @endif>{{$val['name']}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Clinic/Specialty</label>
                    <br>
                    <select autocomplete="off" id="selectClinic" required name="clinic_id" class="form-control select2"
                            style="width:400px">
                        <option value="">Choose</option>
                    </select>
                </div>

                {{--<div class="form-group">--}}
                {{--<label>Physician Experience</label>--}}
                {{--<br>--}}
                {{--<select autocomplete="off" id="user_experience_id" name="user_experience_id"--}}
                {{--class="form-control select2" style="width:400px">--}}
                {{--<option value="">Choose</option>--}}
                {{--@foreach($experience as $val)--}}
                {{--<option value="{{$val['id']}}"--}}
                {{--@if(Input::old('user_experience_id') == $val['id'])--}}
                {{--selected @endif>{{$val['name']}}</option>--}}
                {{--@endforeach--}}
                {{--</select>--}}
                {{--</div>--}}

                {{--<div class="form-group">--}}
                {{--<label>Physician Specialty</label>--}}
                {{--<br>--}}
                {{--<select autocomplete="off" id="user_specialty_id" name="user_specialty_id"--}}
                {{--class="form-control select2" style="width:400px">--}}
                {{--<option value="">Choose</option>--}}
                {{--@foreach($specialty as $val)--}}
                {{--<option value="{{$val['id']}}">{{$val['name']}}</option>--}}
                {{--@endforeach--}}
                {{--</select>--}}
                {{--</div>--}}

                <div class="form-group">
                    <label>Physician</label>
                    <br>
                    <select autocomplete="off" id="selectPhysician" required name="physician_id"
                            class="form-control select2" style="width:400px">
                        <option value="">Choose</option>
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Confirm</button>
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>


<div class="modal fade" id="myModalAgent" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Add Announcement</h4>
            </div>
            {{Form::open(array('role'=>"form", 'route' => 'createAgentComment'))}}
            <div class="modal-body col-md-12">
                <div class="form-group col-md-12">
                    <label>All Users</label>
                    <input id="all_users"
                           autocomplete="off" class="icheckbox_flat-blue" name="all_users" type="checkbox">
                </div>

                <div class="form-group col-md-12 filter_announcement">
                    <label>Users</label>
                    <br>
                    <select name="user_id[]" class="form-control select2" multiple style="width: 400px">
                        <option value="">Choose</option>
                        @foreach($users as $val)
                            <option value="{{$val['id']}}">{{$val['full_name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-12 filter_announcement">
                    <label>Group Name</label>
                    <br>
                    <select name="group_id[]" class="form-control select2" multiple style="width: 400px">
                        <option value="">Choose</option>
                        @foreach($groups as $val)
                            <option value="{{$val['id']}}">{{$val['name']}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-12">
                    <label>Notes</label>
                    <textarea required name="notes" class="form-control"></textarea>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>

<div class="modal fade" id="notReadyReasonModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span
                            aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Not Ready Reason</h4>
            </div>
            {{Form::open(array('role'=>"form", 'route' => 'userNotReadyMonitor'))}}
            <div class="modal-body col-md-12">

                <div class="form-group col-md-12 filter_announcement">
                    <label>Reason</label>
                    <br>
                    <select required name="not_ready_reason_id" class="form-control select2" style="width: 400px">
                        <option value="">Choose</option>
                        @foreach($notReadyReason as $key => $val)
                            <option value="{{$val['id']}}">{{$val['name']}}</option>
                        @endforeach
                    </select>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            {{Form::close()}}
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
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('dist/js/app.min.js')}}"></script>
<!-- AdminLTE for demo purposes -->
<script src="{{asset('dist/js/demo.js')}}"></script>
<script src="{{asset('plugins/select2/select2.full.min.js')}}"></script>
<script>
    function getFormData(form) {
        var o = {};
        var a = form;
        $.each(a, function () {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    }

    $(function () {
        $(".select2").select2();

        $('#all_users').change(function () {
            if ($(this).is(':checked')) {
                $(".filter_announcement").hide();
            } else {
                $(".filter_announcement").show();
            }
        });

        $("#selectHospitalQ").change(function (e) {
            $("#selectClinicQ").attr('disabled', 'disabled');
            $.ajax({
                url: '{{route('getClinicsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: $(this).val()
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectClinicQ").removeAttr('disabled').html(data).select2();
                }
            });
        });

        $("#selectClinicQ").change(function (e) {
            $("#selectPhysicianQ").attr('disabled', 'disabled');
            $.ajax({
                url: '{{route('getPhysicianByClinicIds')}}',
                method: 'POST',
                data: {
                    clinic_id: $(this).val()
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectPhysicianQ").removeAttr('disabled').html(data).select2();
                }
            });
        });

        $("#selectHospital").change(function (e) {
            $("#selectClinic").attr('disabled', 'disabled');
            $.ajax({
                url: '{{route('getClinicsByHospitalId')}}',
                method: 'POST',
                data: {
                    hospital_id: $(this).val()
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectClinic").removeAttr('disabled').html(data).select2();
                }
            });
        });

        $("#selectClinic").change(function (e) {
            $("#selectPhysician").attr('disabled', 'disabled');
            $.ajax({
                url: '{{route('getPhysicianByClinicId')}}',
                method: 'POST',
                data: {
                    clinic_id: $(this).val(),
                    user_experience_id: $("#user_experience_id").val(),
                    user_specialty_id: $("#user_specialty_id").val(),
                    bookable: true
                },
                headers: {token: '{{csrf_token()}}'},
                success: function (data) {
                    $("#selectPhysician").removeAttr('disabled').html(data).select2();
                }
            });
        });

        $("#user_experience_id").change(function (e) {
            if ($("#selectClinic").val()) {
                $("#selectPhysician").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinicId')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $("#selectClinic").val(),
                        user_specialty_id: $("#user_specialty_id").val(),
                        user_experience_id: $(this).val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician").removeAttr('disabled').html(data).select2();
                    }
                });
            }
        });

        $("#user_specialty_id").change(function (e) {
            if ($("#selectClinic").val()) {
                $("#selectPhysician").attr('disabled', 'disabled');
                $.ajax({
                    url: '{{route('getPhysicianByClinicId')}}',
                    method: 'POST',
                    data: {
                        clinic_id: $("#selectClinic").val(),
                        user_specialty_id: $(this).val(),
                        user_experience_id: $("#user_experience_id").val()
                    },
                    headers: {token: '{{csrf_token()}}'},
                    success: function (data) {
                        $("#selectPhysician").removeAttr('disabled').html(data).select2();
                    }
                });
            }
        });

        @if(Input::old('hospital_id') != '')
        $.ajax({
            url: '{{route('getClinicsByHospitalId')}}',
            method: 'POST',
            data: {
                hospital_id: $("#selectHospital").val()
            },
            headers: {token: '{{csrf_token()}}'},
            success: function (data) {
                $("#selectClinic").removeAttr('disabled').html(data);
                        @if(!empty(Input::old('clinic_id')))
                var clinic_id = '{{Input::old('clinic_id')}}';
                $("#selectClinic option[value=" + clinic_id + "]").attr('selected', 'selected');
                @endif
                $("#selectClinic").select2()
            }
        });
        @endif
        @if(isset($show_diagnosis) && !$show_diagnosis)
        $("#diagnosis_menu").remove();
        @endif

        @if(isset($show_pms) && !$show_pms)
        $("#pms_menu").remove();
        @endif

        @if($user->user_type_id == 7 && Session::has('edit_profile'))
        //            alert('Please Doctor, Your Profile Need To Update!');
        @endif
        ///////////////////////////////////sidebar-collapse//////////////////////////////////////
                @if($c_user->user_type_id == \core\enums\UserRules::patientRelation)
        var screenSizes = $.AdminLTE.options.screenSizes;
        //Enable sidebar push menu
        if ($(window).width() > (screenSizes.sm - 1)) {
            if ($("body").hasClass('sidebar-collapse')) {
                $("body").removeClass('sidebar-collapse').trigger('expanded.pushMenu');
            } else {
                $("body").addClass('sidebar-collapse').trigger('collapsed.pushMenu');
            }
        }
        //Handle sidebar push menu for small screens
        else {
            if ($("body").hasClass('sidebar-open')) {
                $("body").removeClass('sidebar-open').removeClass('sidebar-collapse').trigger('collapsed.pushMenu');
            } else {
                $("body").addClass('sidebar-open').trigger('expanded.pushMenu');
            }
        }
        @endif
        /////////////////////////////////////////////////////////////////////////
        //iCheck for checkbox and radio inputs
        $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });
        //Red color scheme for iCheck
        $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
            checkboxClass: 'icheckbox_minimal-red',
            radioClass: 'iradio_minimal-red'
        });
        //Flat red color scheme for iCheck
        $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
            checkboxClass: 'icheckbox_flat-green',
            radioClass: 'iradio_flat-green'
        });
        $.fn.modal.Constructor.prototype.enforceFocus = function () {
        }; // very important for select2 in popup
    });

    function withMe(id) {
        $(id).waitMe({
            effect: 'ios',
            text: 'Please wait...',
            bg: 'rgba(255,255,255,0.7)',
            color: '#000',
            maxSize: '',
            source: 'img.svg'
        });
    }
</script>
@yield('footer')
<script>
    $(function (e) {
        $(".select2").css('width', '100%');
        $('.showPopover').popover({
            html: true
        });

        $('.datepicker, .datepicker2').on('show', function (e) {
            if (e.date) {
                $(this).data('stickyDate', e.date);
            }
            else {
                $(this).data('stickyDate', null);
            }
        });

        $('.datepicker, .datepicker2').on('hide', function (e) {
            var stickyDate = $(this).data('stickyDate');

            if (!e.date && stickyDate) {
                $(this).datepicker('setDate', stickyDate);
                $(this).data('stickyDate', null);
            }
        });
    });
</script>
</body>
</html>
