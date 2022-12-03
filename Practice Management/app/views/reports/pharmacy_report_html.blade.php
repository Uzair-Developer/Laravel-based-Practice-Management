<div class="box">
    <!-- /.box-header -->
    <div class="box-body">
        <table class="table table-bordered" id="example1" cellspacing="0">
            <thead>
            <tr style="background: lightslategray;">
                <th style="text-align: center;">Pharmacist</th>
                <th style="text-align: center;">No. Calls</th>
                <th style="text-align: center;">No. Pass</th>
                <th style="text-align: center;">No. Call Done</th>
                <th style="text-align: center;">No. Cancel</th>
                <th style="text-align: center;">Options</th>
            </tr>
            </thead>
            <tbody>
            @foreach($report as $val)
                <tr>
                    <td>{{ucwords(strtolower($val['pharmacist']['full_name']))}}</td>
                    <td>{{$val['numCalls']}}</td>
                    <td>{{$val['numPass']}}</td>
                    <td>{{$val['numCallsDone']}}</td>
                    <td>{{$val['numCancel']}}</td>
                    <td>
                        <a pharmacist_id="{{$val['pharmacist']['id']}}"
                           title="Logs"
                           class="btn btn-info pharmacistLogsBtn"><i
                                    class="fa fa-eye"></i> Logs</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#example1').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "autoWidth": true,
        "pageLength": 100,
        "order": [[0, "asc"]],
        "sScrollY": "400",
        "sScrollX": "100%",
        "sScrollXInner": "100%",
        "bScrollCollapse": true
    });
</script>