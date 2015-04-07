<?php namespace Donny5300\Calendar;

use Carbon\Carbon;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\View;
use DateTime;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;

class Calendar
{
    private $configLoad = ['views', 'times', 'input', 'path', 'labels', 'interval', 'today-class','week-select', 'table-class', 'view-mode'];
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
    private $week;
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
    private $tableClass;
    private $nextLabel = 'Next';
    private $prevLabel = 'Prev';
    private $events = [];
    private $todayClass = 'date-time-today';
    private $setWeekSelect;
    private $weekSelectLocation = 'week-replace';

    /**
     * Default views
     */
    private $tableHeadView  = 'calendar::table.table_head';
    private $tableCellView  = 'calendar::events.table_cell';
    private $eventView      = 'calendar::events.render';


    public function __construct()
    {
        $now = Carbon::now();
        $now = $now->addDay()->toDateString();
        $this->date = Input::get($this->inputName, $now);

//        dd($this->date);

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
        return $this->todayClass = 'class="' . $class . '"';
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
            'showSelect' => $this->setWeekSelect,
            'showSelectLocation' => $this->weekSelectLocation,

        ];

        return View::make($this->tableHeadView, $data)->render();
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
        $this->weekNumber = $date->format('W');
        $this->week = 'Week ' . $date->format('W');
    }

    public function events($data, $set_key = false)
    {

        if ($set_key !== false) {
            $data = $data->toArray();

            $events = [];
            foreach ($data as $key => $item) {
                $new_key = Carbon::parse($item[$set_key])->toDateTimeString();
                $events[$new_key] = $item;
            }

            return $this->events = $events;
        }


        foreach ($data as $key => $event) {
            $key = Carbon::parse($key)->toDateTimeString();
            $this->events[$key->toDateTimeString()] = $event;
        }

        return $this->events;
    }

    public function renderWeek()
    {
        $endDay = Carbon::parse($this->first_day_of_week)->addDays(6);
        $head_dates = Carbon::parse($this->first_day_of_week);
        $now = Carbon::now()->toDateString();
        $html = '<tr>';

        for ($i = 0; $i <= 7; $i++) {
            if ($i == 0) {
                $html .= '<td>';
                $html .= $this->weekSelectLocation == 'week-replace' ? $this->setWeekSelect : $this->week;

                $html .= '</td>';
                continue;
            }

            if ($head_dates->toDateString() == $now) {
                $html .= '<td class="date-today">' . $this->dayLabels[$i] . ' ' . $head_dates->day . '</td>';
            } else {
                $html .= '<td>' . $this->dayLabels[$i] . ' ' . $head_dates->day . '</td>';
            }

            $head_dates->addDay();
        }

        $html .= '</tr>';

        for ($times = $this->startTime; $times <= $this->endTime; $times->addMinutes($this->interval)) {
            $hour = strlen($times->hour) == 1 ? '0' . $times->hour : $times->hour;
            $minute = strlen($times->minute) == 1 ? '0' . $times->minute : $times->minute;
            $startDay = Carbon::parse($this->first_day_of_week);
            $first_monday = Carbon::parse($this->first_day_of_week);
            $html .= '<tr><td> ' . $hour . ':' . $minute . '</td>';

            for ($startDay; $startDay <= $endDay; $startDay->addDay()) {
                $tdData = [
                    'startDay' => $startDay,
                    'todayClass' => $this->todayClass,
                    'tdDate' => $first_monday->toDateString() . ' ' . $times->toTimeString(),
                    'events' => $this->events,
                    'now' => $now,
                    'eventView' => $this->eventView,
                ];


                $html .= View::make($this->tableCellView, $tdData)->render();

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
    public function setPath($path)
    {
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

    public function setWeekSelect($min = 5, $max = 5, $class = false)
    {

        $min = $min === false ? 5 : $min;
        $max = $max === false ? 5 : $max;

        $nWeeks = Carbon::parse($this->date)->addWeeks($max);
        $pWeeks = Carbon::parse($this->date)->subWeeks($min);
        $now = Carbon::parse($this->date);
        $currentWeek = $this->format_date($now->toDateString(),'W');

        $html = '<form method="get"><select name="'.Config::get('calendar::student-planning.input').'">';
        $counter = 0;
        for ($week = $pWeeks; $week <= $nWeeks; $week->addWeek()) {
            $weekNumber = $this->format_date($week->toDateString(), 'W');


            if($currentWeek == $weekNumber){
//
//                echo ' Hoi';
//                echo $weekNumber;
                $html .= '<option value="'.$week->toDateString().'" selected="selected">Week '.$weekNumber.' (Geselecteerde week)</option>';
            } else{
                $html .= '<option value="'.$week->toDateString().'">Week '.$weekNumber.'</option>';
            }
//            echo '<hr />';
        }



        $html .= '</select></form>';
//
//        echo $html;
//die();
        return $this->setWeekSelect = $html;

    }

    public function setWeekSelectLocation($location)
    {
        $this->weekSelectLocation = $location;
    }

    public function loadTranslations(){
        $config = Config::get('calendar::files.dayLabels');
        $this->dayLabels = Lang::get($config);

//        dd($config);
    }


    public function loadConfig($options = true, $option_type = 'student-planning', $file = null){
        $package = $file === null ? 'calendar::'.$option_type.'.' : $file.'.'.$option_type.'.';

        if($options === true){
            $options = $this->configLoad;
        }

        if(is_array($options)){
            foreach($options as $option){
                switch($option){
                    case 'views';
                        $this->tableHeadView = Config::get($package.'views.table_head');
                        $this->tableCellView = Config::get($package.'views.table_cell');
                        $this->eventView = Config::get($package.'views.render_event');
                        break;

                    case 'times':
                        $time = $this->startTime(Config::get($package.'times.start'));
                        $this->startTime = $time;

                        $time = $this->endTime(Config::get($package.'times.end'));
                        $this->endTime = $time;
                    break;

                    case 'input':
                        $this->inputName = Config::get($package.'input');
                    break;

                    case 'path':
                        $this->path = Config::get($package.'path');
                    break;

                    case 'labels':
                        $this->nextLabel = Config::get($package.'labels.next');
                        $this->prevLabel = Config::get($package.'labels.prev');
                    break;

                    case 'interval':
                        $this->interval = Config::get($package.'interval');
                    break;

                    case 'today-class':
                        $this->todayClass = 'class="' . Config::get($package.'styling.classes.today') . '"';
                    break;

                    case 'table-class':
                        $this->tableClass = Config::get($package.'styling.classes.table');
                        break;

                    case 'table-id':
                        $this->tableId = Config::get($package.'styling.id.table');
                    break;

                    case 'view-mode':
                        $this->viewType = Config::get($package.'view_mode.mode');
                    break;

                    case 'week-select':
                        $next = Config::get($package.'view_mode.week-select.next');
                        $prev = Config::get($package.'view_mode.week-select.prev');
                        $input = Config::get($package.'view_mode.week-select.input-name');
                        $location = Config::get($package.'view_mode.week-select.location');

                        $this->setWeekSelect($prev, $next, $input);
                        $this->setWeekSelectLocation($location); //Head, week-replace

                        break;

                }
            }
        }
    }
}