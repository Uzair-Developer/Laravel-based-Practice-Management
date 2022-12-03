@extends('layout/main')

@section('title')
    - Add Group
@stop

@section('footer')
    <script>
        $(function () {
            $('#pms_select_all').click(function (event) {  //on click
                if (this.checked) { // check select status
                    $('.checkbox1').each(function () { //loop through each checkbox
                        this.checked = true;  //select all checkboxes with class "checkbox1"
                    });
                } else {
                    $('.checkbox1').each(function () { //loop through each checkbox
                        this.checked = false; //deselect all checkboxes with class "checkbox1"
                    });
                }
            });

            $('#diagnosis_select_all').click(function (event) {  //on click
                if (this.checked) { // check select status
                    $('.checkbox2').each(function () { //loop through each checkbox
                        this.checked = true;  //select all checkboxes with class "checkbox1"
                    });
                } else {
                    $('.checkbox2').each(function () { //loop through each checkbox
                        this.checked = false; //deselect all checkboxes with class "checkbox1"
                    });
                }
            });

            @if($c_user->user_type_id != 1)
                $(".admin_only input").attr('disabled', 'disabled');
            @endif



        })
    </script>
@stop

@section('content')
    <section class="content-header">
        <h1>
            Add Group
        </h1>
    </section>

    <section class="content">
        <div class="row">
            {{Form::open(array('role'=>"form"))}}
            <div class="form-group col-md-12">
                <label>Group Name</label>
                <input required value="{{Input::old('name') ? Input::old('name') : $name}}" name="name"
                       class="form-control">
            </div>
            @if($c_user->user_type_id == 1)
                <div class="form-group col-md-12">
                    <div class="checkbox-list">
                        <label class="checkbox-inline">
                            <input autocomplete="off" name="system" @if($system == 1) checked
                                   @endif class="checkbox-inline" type="checkbox" >
                            System?
                        </label>

                        <label class="checkbox-inline">
                            <input class="checkbox-inline" type="checkbox" autocomplete="off" name="in_filter"
                                   @if($in_filter == 1) checked @endif>
                            Show In Reservations Filter
                        </label>
                    </div>
                </div>
            @endif
            <div class="col-md-12">
                <div class="box box-primary">
                    <!-- form start -->
                    <div class="box-header">
                        PMS Permissions
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="checkbox-list">
                            <label class="checkbox-inline">
                                <input id="pms_select_all" class="checkbox-inline" type="checkbox" autocomplete="off">
                                Select All
                            </label>
                        </div>
                        <br>

                        <div class="form-group col-md-12">
                            <label>Hospitals</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="hospital[]"
                                           @if(array_key_exists('hospital.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="hospital[]"
                                           @if(array_key_exists('hospital.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="hospital[]"
                                           @if(array_key_exists('hospital.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12 admin_only">
                            <label>Permissions Group</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="permissions[]"
                                           @if(array_key_exists('permissions.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="permissions[]"
                                           @if(array_key_exists('permissions.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="permissions[]"
                                           @if(array_key_exists('permissions.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="permissions[]"
                                           @if(array_key_exists('permissions.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Patients</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patient[]"
                                           @if(array_key_exists('patient.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patient[]"
                                           @if(array_key_exists('patient.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patient[]"
                                           @if(array_key_exists('patient.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>PMS Attributes</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="AttributePms[]"
                                           @if(array_key_exists('AttributePms.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="AttributePms[]"
                                           @if(array_key_exists('AttributePms.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="AttributePms[]"
                                           @if(array_key_exists('AttributePms.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="AttributePms[]"
                                           @if(array_key_exists('AttributePms.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-12 admin_only">
                            <label>Public Holiday</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="publicHoliday[]"
                                           @if(array_key_exists('publicHoliday.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Announcements</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="agentComment[]"
                                           @if(array_key_exists('agentComment.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="agentComment[]"
                                           @if(array_key_exists('agentComment.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12 admin_only">
                            <label>Users</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if(array_key_exists('user.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if(array_key_exists('user.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if(array_key_exists('user.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if(array_key_exists('user.changeStatus', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="changeStatus"> Change Status
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if(array_key_exists('user.resetPassword', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="resetPassword"> Reset Password
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if(array_key_exists('user.changePassword', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="changePassword"> Change
                                    Password
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if(array_key_exists('user.printExcel', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="printExcel"> Print Excel
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if(array_key_exists('user.his_import_physician', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="his_import_physician"> His Import
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Clinics</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinic_pms[]"
                                           @if(array_key_exists('clinic_pms.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinic_pms[]"
                                           @if(array_key_exists('clinic_pms.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinic_pms[]"
                                           @if(array_key_exists('clinic_pms.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinic_pms[]"
                                           @if(array_key_exists('clinic_pms.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinic_pms[]"
                                           @if(array_key_exists('clinic_pms.printExcel', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="printExcel"> Print Excel
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinic_pms[]"
                                           @if(array_key_exists('clinic_pms.times_availability', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="times_availability"> Times
                                    Availability
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Clinic Schedule</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if(array_key_exists('clinicSchedule.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if(array_key_exists('clinicSchedule.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if(array_key_exists('clinicSchedule.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if(array_key_exists('clinicSchedule.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if(array_key_exists('clinicSchedule.duplicate', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="duplicate"> Duplicate
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if(array_key_exists('clinicSchedule.importExcel', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="importExcel"> Import Excel
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Profile Settings</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physician_attribute[]"
                                           @if(array_key_exists('physician_attribute.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physician_attribute[]"
                                           @if(array_key_exists('physician_attribute.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physician_attribute[]"
                                           @if(array_key_exists('physician_attribute.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physician_attribute[]"
                                           @if(array_key_exists('physician_attribute.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Physician</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physician[]"
                                           @if(array_key_exists('physician.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physician[]"
                                           @if(array_key_exists('physician.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physician[]"
                                           @if(array_key_exists('physician.changeStatus', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="changeStatus"> Activate/Deactivate
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Physician Calendar</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianCalendar[]"
                                           @if(array_key_exists('physicianCalendar.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Physician Exceptions</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianException[]"
                                           @if(array_key_exists('physicianException.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>

                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianException[]"
                                           @if(array_key_exists('physicianException.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>

                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianException[]"
                                           @if(array_key_exists('physicianException.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>

                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianException[]"
                                           @if(array_key_exists('physicianException.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>

                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianException[]"
                                           @if(array_key_exists('physicianException.changeStatus', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="changeStatus"> Change Status
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Physician Schedule</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if(array_key_exists('physicianSchedule.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if(array_key_exists('physicianSchedule.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if(array_key_exists('physicianSchedule.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if(array_key_exists('physicianSchedule.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if(array_key_exists('physicianSchedule.importExcel', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="importExcel"> Import Excel
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if(array_key_exists('physicianSchedule.on-off', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="on-off"> [Un]Publish
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Physician Schedule Exception</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianScheduleException[]"
                                           @if(array_key_exists('physicianScheduleException.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianScheduleException[]"
                                           @if(array_key_exists('physicianScheduleException.manage', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="manage"> Manage
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianScheduleException[]"
                                           @if(array_key_exists('physicianScheduleException.save', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="save"> Save
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianScheduleException[]"
                                           @if(array_key_exists('physicianScheduleException.update', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="update"> Update
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianScheduleException[]"
                                           @if(array_key_exists('physicianScheduleException.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Patients Lab Radiology</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patientLapRadiology[]"
                                           @if(array_key_exists('patientLapRadiology.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patientLapRadiology[]"
                                           @if(array_key_exists('patientLapRadiology.order_details', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="order_details"> Order Details
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patientLapRadiology[]"
                                           @if(array_key_exists('patientLapRadiology.reset_password', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="reset_password"> Reset Password
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patientLapRadiology[]"
                                           @if(array_key_exists('patientLapRadiology.edit_phone', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit_phone"> Edit Phone
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patientLapRadiology[]"
                                           @if(array_key_exists('patientLapRadiology.on_off_lab_sms', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="on_off_lab_sms"> On/Off Lab SMS
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Booking</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.cancel', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="cancel"> Cancel
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.show', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="show"> Show
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.note', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="note"> Notes
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.patient_reservation_tab', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="patient_reservation_tab"> Patients
                                    Reservation Tab
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.unlockSlot', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="unlockSlot"> Unlock Slots
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.lockSlot', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="lockSlot"> Lock Slots
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.unArchive', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="unArchive"> Un Archive
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if(array_key_exists('reservation.patientUpdate', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="patientUpdate"> Patient Update
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Manage Reservations</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.listToday', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="listToday"> Today Res
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.open_close', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="open_close"> Clinic Open/Close
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.patient_in', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="patient_in"> Patient In
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.patient_out', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="patient_out"> Patient Out
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.patient_pending_resume', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="patient_pending_resume"> Patient
                                    Pending/Resume
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.patient_cancel', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="patient_cancel"> Cancel
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.patient_attend', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="patient_attend"> Patient Attend
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.walkIn_add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="walkIn_add"> Add Walk In
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.waitListApproval', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="waitListApproval"> Wait List
                                    Approval
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.revisit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="revisit"> Revisit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.resend_last_sms', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="resend_last_sms"> Resend SMS
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.nextPatientBtn', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="nextPatientBtn"> Next Patient Btn
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.printRes', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="printRes"> Print Res.
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if(array_key_exists('manageReservation.view_history', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="view_history"> View History
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>In Service Reservations</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="in_service[]"
                                           @if(array_key_exists('in_service.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="in_service[]"
                                           @if(array_key_exists('in_service.service_done', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="service_done"> Service Done
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Stand Alon Reservations</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="standAlonReservation[]"
                                           @if(array_key_exists('standAlonReservation.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="standAlonReservation[]"
                                           @if(array_key_exists('standAlonReservation.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="standAlonReservation[]"
                                           @if(array_key_exists('standAlonReservation.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Complains</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="complain[]"
                                           @if(array_key_exists('complain.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="complain[]"
                                           @if(array_key_exists('complain.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="complain[]"
                                           @if(array_key_exists('complain.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="complain[]"
                                           @if(array_key_exists('complain.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="complain[]"
                                           @if(array_key_exists('complain.read', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="read"> Read
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Patient Attend</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patient_attend[]"
                                           @if(array_key_exists('patient_attend.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patient_attend[]"
                                           @if(array_key_exists('patient_attend.view', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="view"> View
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="patient_attend[]"
                                           @if(array_key_exists('patient_attend.print', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="print"> Print
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Reports</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reports[]"
                                           @if(array_key_exists('reports.physician_reports', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="physician_reports"> Physician Report
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reports[]"
                                           @if(array_key_exists('reports.physician_report_excel', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="physician_report_excel">Download Physician Excel
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reports[]"
                                           @if(array_key_exists('reports.clinic_reports', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="clinic_reports"> Clinic Report
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reports[]"
                                           @if(array_key_exists('reports.clinic_report_excel', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="clinic_report_excel">Download Clinic Excel
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reports[]"
                                           @if(array_key_exists('reports.physician_exception_reports', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="physician_exception_reports">Physician Exception Report
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reports[]"
                                           @if(array_key_exists('reports.physician_exception_excel', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="physician_exception_excel">Download Physician Exception
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Supervisor</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="supervisor[]"
                                           @if(array_key_exists('supervisor.access', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="access"> Access
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Head Of Department</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="head_dept[]"
                                           @if(array_key_exists('head_dept.access', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="access"> Access
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Dashboard</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="dashboard[]"
                                           @if(array_key_exists('dashboard.reservationCounts', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="reservationCounts"> Count
                                    Reservations
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>SMS Campaign</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="smsCampaign[]"
                                           @if(array_key_exists('smsCampaign.access', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="access"> Access
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>PMS Diagnosis</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="pmsDiagnosis[]"
                                           @if(array_key_exists('pmsDiagnosis.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="pmsDiagnosis[]"
                                           @if(array_key_exists('pmsDiagnosis.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="pmsDiagnosis[]"
                                           @if(array_key_exists('pmsDiagnosis.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="pmsDiagnosis[]"
                                           @if(array_key_exists('pmsDiagnosis.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Ip To Room</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToRoom[]"
                                           @if(array_key_exists('ipToRoom.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToRoom[]"
                                           @if(array_key_exists('ipToRoom.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToRoom[]"
                                           @if(array_key_exists('ipToRoom.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToRoom[]"
                                           @if(array_key_exists('ipToRoom.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Ip To Screen</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToScreen[]"
                                           @if(array_key_exists('ipToScreen.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToScreen[]"
                                           @if(array_key_exists('ipToScreen.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToScreen[]"
                                           @if(array_key_exists('ipToScreen.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToScreen[]"
                                           @if(array_key_exists('ipToScreen.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Ip To Reception</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToReception[]"
                                           @if(array_key_exists('ipToReception.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToReception[]"
                                           @if(array_key_exists('ipToReception.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToReception[]"
                                           @if(array_key_exists('ipToReception.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToReception[]"
                                           @if(array_key_exists('ipToReception.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Ip To Printer</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToPrinter[]"
                                           @if(array_key_exists('ipToPrinter.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToPrinter[]"
                                           @if(array_key_exists('ipToPrinter.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToPrinter[]"
                                           @if(array_key_exists('ipToPrinter.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="ipToPrinter[]"
                                           @if(array_key_exists('ipToPrinter.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Answer Types</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="answerType[]"
                                           @if(array_key_exists('answerType.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="answerType[]"
                                           @if(array_key_exists('answerType.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="answerType[]"
                                           @if(array_key_exists('answerType.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="answerType[]"
                                           @if(array_key_exists('answerType.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Questions</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="question[]"
                                           @if(array_key_exists('question.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="question[]"
                                           @if(array_key_exists('question.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="question[]"
                                           @if(array_key_exists('question.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="question[]"
                                           @if(array_key_exists('question.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Survey</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="survey[]"
                                           @if(array_key_exists('survey.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="survey[]"
                                           @if(array_key_exists('survey.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="survey[]"
                                           @if(array_key_exists('survey.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="survey[]"
                                           @if(array_key_exists('survey.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Survey Group</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="surveyGroup[]"
                                           @if(array_key_exists('surveyGroup.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="surveyGroup[]"
                                           @if(array_key_exists('surveyGroup.add', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="surveyGroup[]"
                                           @if(array_key_exists('surveyGroup.edit', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="surveyGroup[]"
                                           @if(array_key_exists('surveyGroup.delete', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Patient Survey</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="patientSurvey[]"
                                           @if(array_key_exists('patientSurvey.list', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="patientSurvey[]"
                                           @if(array_key_exists('patientSurvey.view', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="view"> View
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="patientSurvey[]"
                                           @if(array_key_exists('patientSurvey.report', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="report"> Report
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox2" name="patientSurvey[]"
                                           @if(array_key_exists('patientSurvey.print_excel', $permissions)) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="print_excel"> Excel
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            @if($c_user->user_type_id == 1)
                <div class="col-md-12">
                    <div class="box box-primary collapsed-box">
                        <div class="box-header">
                            Diagnosis Permissions
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                    <i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="display: none;">
                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input id="diagnosis_select_all" class="checkbox-inline" type="checkbox"
                                           autocomplete="off"> Select All
                                </label>
                            </div>
                            <br>

                            <div class="form-group col-md-12">
                                <label>Country</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="country[]"
                                               @if(array_key_exists('country.list', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="country[]"
                                               @if(array_key_exists('country.add', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="country[]"
                                               @if(array_key_exists('country.edit', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="country[]"
                                               @if(array_key_exists('country.delete', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Organs</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="organ[]"
                                               @if(array_key_exists('organ.list', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="organ[]"
                                               @if(array_key_exists('organ.add', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="organ[]"
                                               @if(array_key_exists('organ.edit', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="organ[]"
                                               @if(array_key_exists('organ.delete', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Clinics</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinic[]"
                                               @if(array_key_exists('clinic.list', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinic[]"
                                               @if(array_key_exists('clinic.add', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinic[]"
                                               @if(array_key_exists('clinic.edit', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinic[]"
                                               @if(array_key_exists('clinic.delete', $permissions)) checked @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Clinic Specialties</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinicSpecialty[]"
                                               @if(array_key_exists('clinicSpecialty.list', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinicSpecialty[]"
                                               @if(array_key_exists('clinicSpecialty.add', $permissions)) checked @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinicSpecialty[]"
                                               @if(array_key_exists('clinicSpecialty.edit', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinicSpecialty[]"
                                               @if(array_key_exists('clinicSpecialty.delete', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Symptoms</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="symptom[]"
                                               @if(array_key_exists('symptom.list', $permissions)) checked @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="symptom[]"
                                               @if(array_key_exists('symptom.add', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="symptom[]"
                                               @if(array_key_exists('symptom.edit', $permissions)) checked @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="symptom[]"
                                               @if(array_key_exists('symptom.delete', $permissions)) checked @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Diseases</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="disease[]"
                                               @if(array_key_exists('disease.list', $permissions)) checked @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="disease[]"
                                               @if(array_key_exists('disease.add', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="disease[]"
                                               @if(array_key_exists('disease.edit', $permissions)) checked @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="disease[]"
                                               @if(array_key_exists('disease.delete', $permissions)) checked @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>References</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="reference[]"
                                               @if(array_key_exists('reference.list', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="reference[]"
                                               @if(array_key_exists('reference.add', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="reference[]"
                                               @if(array_key_exists('reference.edit', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="reference[]"
                                               @if(array_key_exists('reference.delete', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Pending Relation</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="pendingRelation[]"
                                               @if(array_key_exists('pendingRelation.list', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="pendingRelation[]"
                                               @if(array_key_exists('pendingRelation.approval', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="approval"> Approval
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="pendingRelation[]"
                                               @if(array_key_exists('pendingRelation.cancel', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="cancel"> Cancel
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="pendingRelation[]"
                                               @if(array_key_exists('pendingRelation.edit', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Diagnosis</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="diagnosis[]"
                                               @if(array_key_exists('diagnosis.list', $permissions)) checked @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Symptom Comments</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="symptomComment[]"
                                               @if(array_key_exists('symptomComment.list', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label>Instructions</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="instruction[]"
                                               @if(array_key_exists('instruction.list', $permissions)) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            @endif
            <div class="box-footer">
                <button class="btn btn-primary" type="submit">Save</button>
                <a class="btn btn-danger" href="{{route('listGroup')}}">Back</a>
            </div>
            {{Form::close()}}
        </div>
    </section>
@stop

