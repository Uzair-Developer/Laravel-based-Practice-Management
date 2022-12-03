<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-aqua">
        <div class="inner">
            <h3>{{$total_res}}</h3>

            <p>Total Reservations</p>
        </div>
        <div class="icon">
            <i class="ion ion-flag"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>
<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-gray">
        <div class="inner">
            <h3>{{$waiting_res}}</h3>

            <p>Reservations</p>
        </div>
        <div class="icon">
            <i class="ion ion-clock"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>
<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-green-gradient">
        <div class="inner">
            <h3>{{$attend_res}}</h3>

            <p>Patient Attended</p>
        </div>
        <div class="icon">
            <i class="ion ion-checkmark-circled"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>
@if (app('production'))
    <div class="col-lg-4 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green-gradient">
            <div class="inner">
                <h3>{{$total_paid}}</h3>

                <p>Total Paid</p>
            </div>
            <div class="icon">
                <i class="ion ion-cash"></i>
            </div>
            {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
        </div>
    </div>
@endif
<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-green-active">
        <div class="inner">
            <h3>{{$patient_in_res}}</h3>

            <p>Current Patient In Clinic</p>
        </div>
        <div class="icon">
            <i class="ion ion-arrow-down-a"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>
<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-gray-active">
        <div class="inner">
            <h3>{{$patient_out_res}}</h3>

            <p>Patient Out</p>
        </div>
        <div class="icon">
            <i class="ion ion-arrow-up-a"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>

<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-orange">
        <div class="inner">
            <h3>{{$pending_res + $pendingAndNotAttend}}</h3>

            <p>Pending</p>
            <p>Pending & Attend: {{$pending_res}}</p>
            <p>Pending & Not Attend: {{$pendingAndNotAttend}}</p>
        </div>
        <div class="icon">
            <i class="ion ion-clock"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>

<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-red">
        <div class="inner">
            <h3>{{$cancelled_res}}</h3>

            <p>Cancelled</p>
            @foreach($cancelled_reason_res as $val)
                <p>{{$val['name'] . ': ' . $val['count']}}</p>
            @endforeach
        </div>
        <div class="icon">
            <i class="ion ion-close"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>

<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-red">
        <div class="inner">
            <h3>{{$archive_res}}</h3>

            <p>Archive</p>
        </div>
        <div class="icon">
            <i class="ion ion-edit"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>

<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-red">
        <div class="inner">
            <h3>
                @if ($no_show_res > 0)
                    {{$no_show_res}}
                @else
                    0
                @endif
            </h3>

            <p>No Show</p>
        </div>
        <div class="icon">
            <i class="ion ion-eye-disabled"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>

<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-aqua">
        <div class="inner">
            <h3>{{$in_service_res}}</h3>

            <p>In Service</p>
        </div>
        <div class="icon">
            <i class="ion ion-arrow-down-a"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>

<div class="col-lg-4 col-xs-6">
    <!-- small box -->
    <div class="small-box bg-aqua">
        <div class="inner">
            <h3>{{$service_done_res}}</h3>

            <p>Service Done</p>
        </div>
        <div class="icon">
            <i class="ion ion-checkmark-circled"></i>
        </div>
        {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
    </div>
</div>
