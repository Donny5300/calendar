@if ($startDay->toDateString() == $now)
    <td {{$todayClass}}>
@else
    <td>
        @endif

        {{--{{dd($events)}}--}}
        @if (array_key_exists($tdDate, $events))
            <?php $data['event'] = $events[$tdDate] ?>
            {{View::make('calendar::events.render', $data)->render()}}
        @endif
    </td>