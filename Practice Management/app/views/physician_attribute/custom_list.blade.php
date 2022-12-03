<?php $time = time(); ?>
<table id="{{$time}}" class="table table-bordered">
    <thead>
    <tr>
        <th style="width: 15px">#</th>
        <th>Hospital</th>
        <th>Clinic</th>
        <th>Name En</th>
        <th>Name Ar</th>
        <th>Description En</th>
        <th>Description Ar</th>
        <th>Options</th>
    </tr>
    </thead>
    <tbody>
    @foreach($physicianAttribute as $val)
        <tr>
            <td>{{$val['id']}}</td>
            <td>{{$val['hospital_name']}}</td>
            <td>{{$val['clinic_name']}}</td>
            <td>{{$val['name']}}</td>
            <td>{{$val['name_ar']}}</td>
            <td>{{$val['desc']}}</td>
            <td>{{$val['desc_ar']}}</td>
            <td>
                <div class="btn-group">
                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('physician_attribute.edit'))
                        <a ref_id="{{$val['id']}}"
                           class="btn btn-default editPhysicianAttributeBtn">Edit</a>
                    @endif
                    @if($c_user->user_type_id == 1 || $c_user->hasAccess('physician_attribute.delete'))
                        <a ref_id="{{$val['id']}}" ref_type="{{$val['type']}}"
                           class="btn btn-danger deletePhysicianAttributeBtn">Delete</a>
                    @endif
                </div>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@if($physicianAttribute)
    <script>
        $(function () {
            $('#{{$time}}').DataTable({
                "paging": false,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": true,
                "order": [[0, "desc"]]
            });
        });
    </script>
@endif