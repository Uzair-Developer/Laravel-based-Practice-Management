<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<table>
    <thead>
    <tr style="background: #4c9bff;text-align: center;">
        <th>Clinic</th>
        <th>Doctor Name</th>
        @foreach($exceptions as $val)
            <th>{{$val['name']}}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($report as $key => $val)
        <?php //$count = 0; ?>
        @foreach($val['physicians'] as $key2 => $val2)
            <tr>
{{--                @if($count == 0)--}}
                    <td style="background: #62d6ff;vertical-align: middle;">
                        {{$val['name']}}
                    </td>
                {{--@endif--}}
                <td style="background: #FFB5F5;">{{$val2['physicianData']['full_name']}}</td>
                @foreach($exceptions as $val3)
                    @if(isset($val2['exceptions'][$val3['id']]))
                        <td>
                            {{Functions::convertToHoursMins($val2['exceptions'][$val3['id']])}}
                        </td>
                    @else
                        <td>
                            00:00
                        </td>
                    @endif
                @endforeach
            </tr>
            <?php //$count++; ?>
        @endforeach
    @endforeach
    </tbody>
</table>