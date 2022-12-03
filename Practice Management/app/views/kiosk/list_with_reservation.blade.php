<div class="col-md-12">
    <div class="box box-primary">
        <!-- /.box-header -->
        <div class="box-body" style="font-size: 20px;">
            <div class="form-group col-md-1">

            </div>
            <div class="form-group col-md-3" style="display: none;">
                <label>Reservation Code </label>
                <input style="padding: 8%!important;height: 100%;font-size:28px;" id="reservation_code" type="text" name="code" class="form-control" placeholder="رقم الحجز">
            </div>
            <div class="form-group col-md-3">
                <label>Phone Number</label>
                <input style="padding: 8%!important;height: 100%;font-size:28px;" id="phone" type="text" name="phone" class="form-control" placeholder="رقم الموبايل">
            </div>
            <div class="form-group col-md-3">
                <label>Pin Number</label>
                <input style="padding: 8%!important;height: 100%;font-size:28px;" id="registration_no" type="text" name="registration_no" class="form-control" placeholder="رقم الملف الطبى">
            </div>
            <div class="form-group col-md-12" style="text-align: center;">
                {{--<h3 class="arabic-section">--}}
                    {{--برجاء إدخال  رقم الحجز <span style="color: #245490">أو</span> رقم الموبايل <span style="color: #245490">أو</span> رقم الملف الطبى--}}
                {{--</h3>--}}
                <h3 class="arabic-section">
                    برجاء إدخال  رقم الموبايل <span style="color: #245490">أو</span> رقم الملف الطبى
                </h3>
                {{--<h3>--}}
                    {{--Please enter your reservation code <span style="color: #245490">OR</span> phone number <span style="color: #245490">OR</span> pin number--}}
                {{--</h3>--}}
                <h3>
                    Please enter your phone number <span style="color: #245490">OR</span> pin number
                </h3>
            </div>
        </div>
    </div>
</div>
<div class="col-md-12" id="with_reservation_table">

</div>

<script>
    $("#reservation_code").inputmask("99-999");
    $('#reservation_code').keypad();
    $('#phone').keypad();
    $('#registration_no').keypad();
</script>