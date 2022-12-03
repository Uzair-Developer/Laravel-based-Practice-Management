@foreach ($all as $key => $val)

    <div class="box collapsed-box">
        <div class="box-header">
            <p>{{str_replace('_', ' ', $key)}}: {{$val['rate']}}% @if($val['type'] == 1)
                    <span style="color: red"> [Most Common]</span> @elseif($val['type'] == 2)
                    <span style="color: red"> [Less Common]</span> @endif
            </p>

            <progress max="100" value="{{$val['rate']}}" class="html5">
            </progress>

            <div class="box-tools pull-right">
                <button data-widget="collapse" class="btn btn-box-tool" type="button">
                    <i class="fa fa-plus"></i></button>

            </div>
        </div>
        <div class="box-body" style="display: none;">
            <div class="form-group">
                <label>Symptoms</label>
                <br>
                <?php $countSymptoms = count($val['symptoms']); ?>
                @foreach($val['symptoms'] as $key2 => $val2)
                    @if($countSymptoms == $key2 + 1)
                        {{Symptom::getName($val2)}}
                    @else
                        {{Symptom::getName($val2)}} ,
                    @endif
                @endforeach
            </div>
        </div>
        <!-- /.box-body -->
    </div>
@endforeach
