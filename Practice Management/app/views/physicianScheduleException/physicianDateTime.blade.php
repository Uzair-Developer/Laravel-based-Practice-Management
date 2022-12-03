<table class="table table-bordered" id="example2">
    <thead>
    <tr>
        <th>Physician</th>
        <th>Clinic</th>
        <th>Date</th>
        <th>Options</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $from_date = $physicianSchedule['start_date'];
    $to_date = $physicianSchedule['end_date'];
    ?>
    @while(true)
        <?php $dateName = lcfirst(date('l', strtotime($from_date))); ?>
            <tr>
                <td class="physiciansName">

                </td>
                <td class="clinicsName">

                </td>
                <td>{{date('l', strtotime($from_date)) . ' ' . $from_date}}</td>
                <td>
                    <a date="{{$from_date}}" class="btn btn-info manageException">Manage</a>
                </td>
            </tr>
        @if($from_date == $to_date)
            <?php break; ?>
        @else
            <?php $from_date = date('Y-m-d', strtotime("+1 day", strtotime($from_date))); ?>
        @endif
    @endwhile

    </tbody>
</table>

<script>
    $('#example2').DataTable({
        "paging": false,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true
    });
</script>