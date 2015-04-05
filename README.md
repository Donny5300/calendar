EasyCalendar

Extended documentation comming soon!

Place this line in your controller: use Donny5300\Calendar\Calendar as Cal;

or add it to your config/app.php

use Donny5300\Calendar\Calendar as Cal;

```
$calendar = new Cal;

//Set to show current month name
$calendar->setMonthName();
//Set to show current year
$calendar->showYear();
//Set to show week
$calendar->showWeek();
//Set for input name, this will result in Input::get('date')
$calendar->setDate('date');
//Set day labels, currently in dutch
$calendar->dayLabels(null); //Day labels format: key => value
//Start time
$calendar->startTime(8); //Start time
//End time
$calendar->endTime(23); //End time
//Set interval for calendar
$calendar->setInterval(30); //Interval
//Set next label
$calendar->setNextLabel('Next'); //NextLabel
//Set previous label
$calendar->setPrevLabel('Previous'); //Previous Label
//Set TableClass
$calendar->setTableClass('table table-responsive'); //TableClass
//Set Table ID
$calendar->setTableId('test-id'); //Table ID
//Class to append to all td's matching that date
$calendar->setTodayClass('TestClass');
//View type (currently only supporting week view)
$calendar->viewType('week');
//Set the path for the next and previous button
$calendar->setPath('test/test2');
//Calendar array
$calendar->events($events);

$cal = $calendar->render();
```

How to render Events Array:

```
$events = [
            '2015-04-04 10:00:00' => [
                'title' => 'test 1'
            ],

            '05-04-2015 10:00' => [
                'title' => 'test 1'
            ],

            '06-04-2015 12:00' => [
                'title' => 'test 1'
            ],

            '07-04-2015 16:00' => [
                'title' => 'test 1'
            ],

            '08-04-2015 19:00' => [
                'title' => 'test 1'
            ],
        ];
```