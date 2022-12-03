@foreach($ticketType as $key => $val)
    <a class="print_pharmacy_ticket" pharmacy_ticket_type_id="{{$val['id']}}">
        <div class="col-lg-3 col-xs-3"></div>
        <div class="col-lg-6 col-xs-6" style="margin-top: 70px;cursor: pointer">
            <!-- small box -->
            <div class="small-box bg-orange">
                <div class="inner" style="text-align: center">
                    <h3>Print {{$val['name']}}</h3>
                    <br>
                    <h3 class="arabic-section">طــباعــة {{$val['name_ar']}}</h3>
                </div>
                <div class="icon">
                    <i class="ion ion-printer"></i>
                </div>
                {{--<a class="small-box-footer" >More info <i class="fa fa-arrow-circle-right"></i></a>--}}
            </div>
        </div>
    </a>
    <div class="clearfix"></div>
@endforeach
