<?php

return array(
    'student-planning' => array(
        'views' => array(

            'table_head' => 'calendar::table.table_head',
            'table_cell' => 'calendar::events.table_cell',
            'render_event' => 'calendar::events.render',
        ),
        'times' => array(
            'start' => 8,
            'end' => 23,
        ),
        'input' => 'date',
        'path' => 'student/planning/3',
        'labels' => array(
            'next' => 'Next',
            'prev' => 'Previous'
        ),
        'interval' => 30,
        'view_mode' => array(
            'mode' => 'week',
            'week-select' => array(
                'location' => 'head',
                'prev' => 5,
                'next' => 5
            ),
        ),
        'styling' => array(
            'classes' => array(
                'today' => 'date-today',
                'table' => 'table table-responsive'
            ),
            'id' => array(),
        ),
    ),

    'files' => array(
        'dayLabels' => 'calendar::days',
        'monthLabels' => 'calendar::month'
    ),
);