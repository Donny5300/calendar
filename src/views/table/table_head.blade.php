<thead>
<tr>
    <th colspan="2"><a href="{{url($path . '?' . $inputName . '=' . $prevUri)}}">{{$prevWeekLabel}}</a></th>

    <th colspan="4">

        {{$month}} {{$year}}

        @if($showSelectLocation == 'head')
            {{$showSelect}}
        @endif

    </th>
    <th colspan="2"><a href="{{url($path . '?' . $inputName . '=' . $nextUri)}}">{{$nextWeekLabel}}</a></th>
</tr>
</thead>
