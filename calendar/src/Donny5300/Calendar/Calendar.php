<?php namespace Donny5300\Calendar;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use DateTime;
use Illuminate\Routing\Route;

class Calendar
{

    private $inputName = 'date';
    private $dayLabels = [1 => 'Maandag', 2 => 'Dinsdag', 3 => 'Woensdag', 4 => 'Donderdag', 5 => 'Vrijdag', 6 => 'Zaterdag', 7 => 'Zondag'];
    private $monthLabels = [1 => 'Januari', 2 => 'Februari', 3 => 'Maart', 4 => 'April', 5 => 'Mei',
        6 => 'Juni', 7 => 'Juli', 8 => 'Augustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'December'];
    private $startTime;
    private $endTime;
    private $nextWeek;
    private $prevWeek;
    private $date;
    private $weekNumber;
    private $path = 'instructeur/beschikbaarheid/';
    private $interval;
    private $first_day_of_week;
    private $viewType;
    private $showWeek;
    private $showYear;
    private $showMonth;
    private $yearNumber;
    private $monthName;
    private $tableId;
    private $tableClass = 'table table-responsive';
    private $nextLabel = 'Next';
    private $prevLabel = 'Prev';
    private $events;
    private $todayClass = 'date-time-today';

    public function __construct()
    {
        $this->date = Input::get($this->inputName, date('d-m-Y'));

        $nextWeek = Carbon::parse($this->date)->addWeek();
        $prevWeek = Carbon::parse($this->date)->subDays(7);
        $this->nextWeek = $this->format_date($nextWeek, 'd-m-Y');
        $this->prevWeek = $this->format_date($prevWeek, 'd-m-Y');

        $monday = strtotime('last monday', strtotime($this->date));
        $this->first_day_of_week = Carbon::createFromTimestamp($monday);
    }

    public function setDate($value)
    {
        $this->date = Input::get($value, date('Y-m-d H:i:s'));
        $this->inputName = $value;
    }

    public function setTodayClass($class)
    {
        return $this->todayClass = 'class="'.$class.'"';
    }

    public function startTime($value)
    {
        $date = Carbon::parse($this->date);


        return $this->startTime = Carbon::create($date->year, $date->month, $date->day, $value, 00, 00);
    }

    public function endTime($value)
    {
        $date = Carbon::parse($this->date);

        return $this->endTime = Carbon::create($date->year, $date->month, $date->day, $value, 00, 00);
    }

    public function dayLabels()
    {

    }

    public function nextWeek()
    {
        $this->nextWeek = Carbon::parse($this->date)->addWeek();
    }

    public function prevWeek()
    {
        $this->prevWeek = Carbon::parse($this->date)->addWeek();
    }

    public function showDate($w = true, $m = true, $y = true)
    {
        $this->showWeek = (bool)$w;
        $this->showMonth = (bool)$m;
        $this->showYear = (bool)$y;
    }

    public function setHeader()
    {
        $data = [
            'path' => $this->path,
            'nextUri' => $this->nextWeek,
            'year' => $this->yearNumber,
            'prevUri' => $this->prevWeek,
            'inputName' => $this->inputName,
            'month' => $this->monthName,
            'nextWeekLabel' => $this->nextLabel,
            'prevWeekLabel' => $this->prevLabel,

        ];

        return View::make('calendar::table.table_head', $data)->render();
    }

    private function format_date($date, $format)
    {
        $date = new DateTime($date);
        return $date->format($format);
    }

    public function viewType($type)
    {
        return $this->viewType = $type;
    }

    public function setInterval($value)
    {
        return $this->interval = $value;
    }

    public function render()
    {

        $html = '<table border="0" class="' . $this->tableClass . '">';

        $html .= $this->setHeader();
        switch ($this->viewType) {
            case 'week':
                $html .= $this->renderWeek();
                break;
        }

        $html .= '</table>';

        return $html;
    }


    public function setMonthName()
    {
        return $this->monthName = $this->monthLabels[Carbon::parse($this->date)->month];
    }

    public function showYear()
    {
        return $this->yearNumber = Carbon::parse($this->date)->year;
    }

    public function showWeek()
    {
        $date = new DateTime($this->date);
        $this->weekNumber = 'Week ' . $date->format('W');
    }

    public function events($data)
    {
        foreach ($data as $key => $event) {
            $key = Carbon::parse($key);
            $this->events[$key->toDateTimeString()] = $event;
        }

        return;
    }

    public function renderWeek()
    {
        $endDay = Carbon::parse($this->first_day_of_week)->addDays(6);
        $head_dates = Carbon::parse($this->first_day_of_week);
        $now = Carbon::now()->toDateString();
        $html = '<tr>';

        for ($i = 0; $i <= 7; $i++) {
            if ($i == 0) {
                $html .= '<td>' . $this->weekNumber . '</td>';
                continue;
            }

            if ($head_dates->toDateString() == $now) {
                $html .= '<td class="date-today">' . $this->dayLabels[$i] . ' ' . $head_dates->day . '</td>';
            } else {
                $html .= '<td class="">' . $this->dayLabels[$i] . ' ' . $head_dates->day . '</td>';
            }

            $head_dates->addDay();
        }

        $html .= '</tr>';

        for ($times = $this->startTime; $times <= $this->endTime; $times->addMinutes($this->interval)) {
            $hour = strlen($times->hour) == 1 ? '0' . $times->hour : $times->hour;
            $minute = strlen($times->minute) == 1 ? '0' . $times->minute : $times->minute;
            $startDay = Carbon::parse($this->first_day_of_week);
            $first_monday = Carbon::parse($this->first_day_of_week);
            $html .= '<tr><td> ' . $hour . ':'.$minute.'</td>';

            for ($startDay; $startDay <= $endDay; $startDay->addDay()) {
                $tdData = [
                    'startDay' => $startDay,
                    'todayClass' => $this->todayClass,
                    'tdDate' => $first_monday->toDateString() . ' ' . $times->toTimeString(),
                    'events' => $this->events,
                    'now' => $now,
                ];

                $html .= View::make('calendar::events.table_cell', $tdData)->render();


                $first_monday->addDay();
            }

            $html .= '</tr>';
        }

        return $html . '</table>';
    }

    /**
     * @param $path
     * @param $auto
     *
     * Set path based at current route
     */
    public function setPath($path){
        return $this->path = $path;
    }

    /************* OPTIONAL VALUES ********************/
    public function setNextLabel($value)
    {
        return $this->nextLabel = $value;
    }

    public function setPrevLabel($value)
    {
        return $this->prevLabel = $value;
    }

    public function setTableClass($string)
    {
        return $this->tableClass = $string;
    }

    public function setTableId($id)
    {
        return $this->tableId = $id;
    }
}