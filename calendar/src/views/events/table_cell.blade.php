@if ($startDay->toDateString() == $now)

    <td {{$todayClass}}>

@else
    <td>
@endif
    @if (array_key_exists($tdDate, $events))
        <?php $data['event'] = $events[$tdDate] ?>
        {{View::make($eventView, $data)->render()}}
    @endif
</td>