<?php
$receptionsArray = [];
$countReception = count($receptions);
$firstSegment = ceil($countReception / 2);
$screen = 1;
?>
@if($screen == 1)
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th style="padding: 50px 0;font-size: 80px;text-align: center;">Desk No.</th>
                            <th style="padding: 50px 0;font-size: 80px;text-align: center;">Ticket No.</th>
                        </tr>
                        @for($i = 0; $i < $firstSegment; $i++)
                            <?php
                            if (in_array($receptions[$i]['ip'], $receptionsArray)) {
                                continue;
                            } else {
                                $receptionsArray[] = $receptions[$i]['ip'];
                            }
                            ?>
                            @if($receptions[$i]['no_reservation'] == 1)
                                <?php
                                $queue = PharmacyQueue::getAll([
                                    'pharmacy_ip' => $receptions[$i]['ip'],
                                    'date' => date('Y-m-d'),
                                    'hospital_id' => 1,
                                    'call_flag' => 1,
                                    'orderByCall' => true,
                                    'getFirst' => true,
                                ]);
                                ?>
                                <tr>
                                    <td style="padding: 50px 0;font-size: 80px;text-align: center;">Desk <span
                                                style="font-weight: bold;">{{$receptions[$i]['name']}}</span></td>
                                    @if(isset($queue['queue_code']))
                                        <td class="queue_code"
                                            style="padding: 50px 0;font-size: 90px;text-align: center;">{{$queue['queue_code']}}</td>
                                    @else
                                        <?php
                                        $monitor = Monitor::getBy([
                                            'ip' => $receptions[$i]['ip'],
                                            'date' => date('Y-m-d'),
                                            'order_by' => ['id', 'desc'],
                                            'getFirst' => true,
                                        ]);
                                        ?>
                                        @if($monitor && isset($monitor['status']))
                                            @if($monitor['status'] == '0')
                                                <td class="queue_code"
                                                    style="padding: 50px 0;font-size: 90px;text-align: center;color: red">
                                                    X
                                                </td>
                                            @else
                                                <td class="queue_code"
                                                    style="padding: 50px 0;font-size: 90px;text-align: center;">
                                                    ---
                                                </td>
                                            @endif
                                        @else
                                            <td class="queue_code"
                                                style="padding: 50px 0;font-size: 90px;text-align: center;color: red">
                                                X
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            @endif
                        @endfor
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th style="padding: 50px 0;font-size: 80px;text-align: center;">Desk No.</th>
                            <th style="padding: 50px 0;font-size: 80px;text-align: center;">Ticket No.</th>
                        </tr>
                        @for($i = $firstSegment; $i < $countReception; $i++)
                            <?php
                            if (in_array($receptions[$i]['ip'], $receptionsArray)) {
                                continue;
                            } else {
                                $receptionsArray[] = $receptions[$i]['ip'];
                            }
                            ?>
                            @if($receptions[$i]['no_reservation'] == 1)
                                <?php
                                $queue = PharmacyQueue::getAll([
                                    'pharmacy_ip' => $receptions[$i]['ip'],
                                    'date' => date('Y-m-d'),
                                    'hospital_id' => 1,
                                    'call_flag' => 1,
                                    'orderByCall' => true,
                                    'getFirst' => true,
                                ]);
                                ?>

                                <tr>
                                    <td style="padding: 50px 0;font-size: 80px;text-align: center;">Desk <span
                                                style="font-weight: bold;">{{$receptions[$i]['name']}}</span></td>
                                    @if(isset($queue['queue_code']))
                                        <td class="queue_code"
                                            style="padding: 50px 0;font-size: 90px;text-align: center;">{{$queue['queue_code']}}</td>
                                    @else
                                        <?php
                                        $monitor = Monitor::getBy([
                                            'ip' => $receptions[$i]['ip'],
                                            'date' => date('Y-m-d'),
                                            'order_by' => ['id', 'desc'],
                                            'getFirst' => true,
                                        ]);
                                        ?>
                                        @if($monitor && isset($monitor['status']))
                                            @if($monitor['status'] == '0')
                                                <td class="queue_code"
                                                    style="padding: 50px 0;font-size: 90px;text-align: center;color: red">
                                                    X
                                                </td>
                                            @else
                                                <td class="queue_code"
                                                    style="padding: 50px 0;font-size: 90px;text-align: center;">
                                                    ---
                                                </td>
                                            @endif
                                        @else
                                            <td class="queue_code"
                                                style="padding: 50px 0;font-size: 90px;text-align: center;color: red">
                                                X
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            @endif
                        @endfor
                    </table>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th style="padding: 50px 0;font-size: 80px;text-align: center;">Desk No.</th>
                            <th style="padding: 50px 0;font-size: 80px;text-align: center;">Ticket No.</th>
                        </tr>
                        @foreach($receptions as $key => $val)
                            <?php
                            if (in_array($val['ip'], $receptionsArray)) {
                                continue;
                            } else {
                                $receptionsArray[] = $val['ip'];
                            }
                            ?>
                            @if($val['no_reservation'] == 1)
                                <?php
                                $queue = PharmacyQueue::getAll([
                                    'pharmacy_ip' => $val['ip'],
                                    'date' => date('Y-m-d'),
                                    'hospital_id' => 1,
                                    'call_flag' => 1,
                                    'orderByCall' => true,
                                    'getFirst' => true,
                                ]);
                                ?>

                                <tr>
                                    <td style="padding: 50px 0;font-size: 80px;text-align: center;">Desk <span
                                                style="font-weight: bold;">{{$val['name']}}</span></td>
                                    @if(isset($queue['queue_code']))
                                        <td class="queue_code"
                                            style="padding: 50px 0;font-size: 90px;text-align: center;">{{$queue['queue_code']}}</td>
                                    @else
                                        <?php
                                        $monitor = Monitor::getBy([
                                            'ip' => $val['ip'],
                                            'date' => date('Y-m-d'),
                                            'order_by' => ['id', 'desc'],
                                            'getFirst' => true,
                                        ]);
                                        ?>
                                        @if($monitor && isset($monitor['status']))
                                            @if($monitor['status'] == '0')
                                                <td class="queue_code"
                                                    style="padding: 50px 0;font-size: 90px;text-align: center;color: red">
                                                    X
                                                </td>
                                            @else
                                                <td class="queue_code"
                                                    style="padding: 50px 0;font-size: 90px;text-align: center;">
                                                    ---
                                                </td>
                                            @endif
                                        @else
                                            <td class="queue_code"
                                                style="padding: 50px 0;font-size: 90px;text-align: center;color: red">
                                                X
                                            </td>
                                        @endif
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endif