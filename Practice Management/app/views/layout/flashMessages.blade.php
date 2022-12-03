@if ($message)
    <section style="margin: 5px 5px;">
        <div id="alertMessages" class="alert alert-{{ Session::get('flash_notification.level') }}">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <span id="alertBody">
            @if(is_array($message))
                @foreach($message as $key => $val)
                    @foreach($val as $key2 => $val2)
                        <ul>
                            <li>{{$val2}}</li>
                        </ul>
                    @endforeach
                @endforeach
            @else
                {{$message}}
            @endif
        </span>
        </div>
    </section>
@endif
