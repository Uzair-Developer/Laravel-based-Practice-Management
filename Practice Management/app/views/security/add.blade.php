@extends('layout/main')

@section('title')
    - Add permissions to {{$user['user_name']}}
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
        })
    </script>
@stop


@section('content')
    <section class="content-header">
        <h1>
            Add permissions to "{{$user['user_name']}}"
        </h1>
    </section>

    <section class="content">
        <div class="row">
            {{Form::open(array('role'=>"form"))}}
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

                        <div class="form-group col-md-4">
                            <label>PMS Attributes</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="AttributePms[]"
                                           @if($user->hasAccess('AttributePms.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="AttributePms[]"
                                           @if($user->hasAccess('AttributePms.add')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="AttributePms[]"
                                           @if($user->hasAccess('AttributePms.edit')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="AttributePms[]"
                                           @if($user->hasAccess('AttributePms.delete')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Public Holiday</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="publicHoliday[]"
                                           @if($user->hasAccess('publicHoliday.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Announcements</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="agentComment[]"
                                           @if($user->hasAccess('agentComment.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="agentComment[]"
                                           @if($user->hasAccess('agentComment.delete')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-4">
                            <label>Clinics</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinic_pms[]"
                                           @if($user->hasAccess('clinic_pms.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinic_pms[]"
                                           @if($user->hasAccess('clinic_pms.add')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinic_pms[]"
                                           @if($user->hasAccess('clinic_pms.edit')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinic_pms[]"
                                           @if($user->hasAccess('clinic_pms.delete')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Users</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if($user->hasAccess('user.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if($user->hasAccess('user.add')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if($user->hasAccess('user.edit')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if($user->hasAccess('user.delete')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if($user->hasAccess('user.changeStatus')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="changeStatus"> Change Status
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if($user->hasAccess('user.resetPassword')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="resetPassword"> Reset Password
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="user[]"
                                           @if($user->hasAccess('user.changePassword')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="changePassword"> Change Password
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Clinic Schedule</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if($user->hasAccess('clinicSchedule.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if($user->hasAccess('clinicSchedule.add')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if($user->hasAccess('clinicSchedule.edit')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if($user->hasAccess('clinicSchedule.delete')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if($user->hasAccess('clinicSchedule.duplicate')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Duplicate
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="clinicSchedule[]"
                                           @if($user->hasAccess('clinicSchedule.importExcel')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="importExcel"> Import Excel
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Physician</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physician[]"
                                           @if($user->hasAccess('physician.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physician[]"
                                           @if($user->hasAccess('physician.edit')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Physician Calendar</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianCalendar[]"
                                           @if($user->hasAccess('physicianCalendar.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Physician Exceptions</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianException[]"
                                           @if($user->hasAccess('physicianException.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>

                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianException[]"
                                           @if($user->hasAccess('physicianException.add')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>

                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianException[]"
                                           @if($user->hasAccess('physicianException.edit')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>

                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianException[]"
                                           @if($user->hasAccess('physicianException.delete')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>

                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianException[]"
                                           @if($user->hasAccess('physicianException.changeStatus')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="changeStatus"> Change Status
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Physician Schedule</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if($user->hasAccess('physicianSchedule.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if($user->hasAccess('physicianSchedule.add')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="add"> Add
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if($user->hasAccess('physicianSchedule.edit')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="edit"> Edit
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if($user->hasAccess('physicianSchedule.delete')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="delete"> Delete
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="physicianSchedule[]"
                                           @if($user->hasAccess('physicianSchedule.importExcel')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="importExcel"> Import Excel
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Reservations</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="reservation[]"
                                           @if($user->hasAccess('reservation.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Supervisor</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="supervisor[]"
                                           @if($user->hasAccess('supervisor.access')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="access"> Access
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label>Queue</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="queue[]"
                                           @if($user->hasAccess('queue.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Manage Reservations</label>

                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if($user->hasAccess('manageReservation.list')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="list"> List
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if($user->hasAccess('manageReservation.open_close')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="open_close"> Clinic Open/Close
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if($user->hasAccess('manageReservation.patient_in_out')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="patient_in_out"> Patient In/Out
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if($user->hasAccess('manageReservation.patient_pending_resume')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="patient_pending_resume"> Patient Pending/Resume
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if($user->hasAccess('manageReservation.patient_cancel')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="patient_cancel"> Patient Cancel
                                </label>
                                <label class="checkbox-inline">
                                    <input class="checkbox-inline checkbox1" name="manageReservation[]"
                                           @if($user->hasAccess('manageReservation.walkIn_add')) checked
                                           @endif
                                           type="checkbox" autocomplete="off" value="walkIn_add"> Walk In Add
                                </label>
                            </div>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
            </div>
            @if($c_user->user_type_id == 1)
                <div class="col-md-12">
                    <div class="box box-primary">
                        <!-- form start -->
                        <div class="box-header">
                            Diagnosis Permissions
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                    <i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="checkbox-list">
                                <label class="checkbox-inline">
                                    <input id="diagnosis_select_all" class="checkbox-inline" type="checkbox"
                                           autocomplete="off"> Select All
                                </label>
                            </div>
                            <br>

                            <div class="form-group col-md-6">
                                <label>Country</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="country[]"
                                               @if($user->hasAccess('country.list')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="country[]"
                                               @if($user->hasAccess('country.add')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="country[]"
                                               @if($user->hasAccess('country.edit')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="country[]"
                                               @if($user->hasAccess('country.delete')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Organs</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="organ[]"
                                               @if($user->hasAccess('organ.list')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="organ[]"
                                               @if($user->hasAccess('organ.add')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="organ[]"
                                               @if($user->hasAccess('organ.edit')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="organ[]"
                                               @if($user->hasAccess('organ.delete')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Clinics</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinic[]"
                                               @if($user->hasAccess('clinic.list')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinic[]"
                                               @if($user->hasAccess('clinic.add')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinic[]"
                                               @if($user->hasAccess('clinic.edit')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinic[]"
                                               @if($user->hasAccess('clinic.delete')) checked @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Clinic Specialties</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinicSpecialty[]"
                                               @if($user->hasAccess('clinicSpecialty.list')) checked @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinicSpecialty[]"
                                               @if($user->hasAccess('clinicSpecialty.add')) checked @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinicSpecialty[]"
                                               @if($user->hasAccess('clinicSpecialty.edit')) checked @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="clinicSpecialty[]"
                                               @if($user->hasAccess('clinicSpecialty.delete')) checked @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Symptoms</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="symptom[]"
                                               @if($user->hasAccess('symptom.list')) checked @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="symptom[]"
                                               @if($user->hasAccess('symptom.add')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="symptom[]"
                                               @if($user->hasAccess('symptom.edit')) checked @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="symptom[]"
                                               @if($user->hasAccess('symptom.delete')) checked @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label>Diseases</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="disease[]"
                                               @if($user->hasAccess('disease.list')) checked @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="disease[]"
                                               @if($user->hasAccess('disease.add')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="disease[]"
                                               @if($user->hasAccess('disease.edit')) checked @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="disease[]"
                                               @if($user->hasAccess('disease.delete')) checked @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>References</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="reference[]"
                                               @if($user->hasAccess('reference.list')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="reference[]"
                                               @if($user->hasAccess('reference.add')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="add"> Add
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="reference[]"
                                               @if($user->hasAccess('reference.edit')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="reference[]"
                                               @if($user->hasAccess('reference.delete')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Pending Relation</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="pendingRelation[]"
                                               @if($user->hasAccess('pendingRelation.list')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="pendingRelation[]"
                                               @if($user->hasAccess('pendingRelation.approval')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="approval"> Approval
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="pendingRelation[]"
                                               @if($user->hasAccess('pendingRelation.cancel')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="cancel"> Cancel
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="pendingRelation[]"
                                               @if($user->hasAccess('pendingRelation.edit')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Diagnosis</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="diagnosis[]"
                                               @if($user->hasAccess('diagnosis.list')) checked @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Symptom Comments</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="symptomComment[]"
                                               @if($user->hasAccess('symptomComment.list')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-3">
                                <label>Instructions</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="instruction[]"
                                               @if($user->hasAccess('instruction.list')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Patient</label>

                                <div class="checkbox-list">
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="patient[]"
                                               @if($user->hasAccess('patient.list')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="list"> List
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="patient[]"
                                               @if($user->hasAccess('patient.edit')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="edit"> Edit
                                    </label>
                                    <label class="checkbox-inline">
                                        <input class="checkbox-inline checkbox2" name="patient[]"
                                               @if($user->hasAccess('patient.delete')) checked
                                               @endif
                                               type="checkbox" autocomplete="off" value="delete"> Delete
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
            </div>
            {{Form::close()}}
        </div>
    </section>
@stop

