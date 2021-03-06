<?php

namespace App\Renderers;

use App\Facades\EntityPermission;
use App\Models\Calendar;
use App\Models\CalendarEvent;
use App\Models\Event;
use Collective\Html\HtmlFacade;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CalendarRenderer
{
    /**
     * @var Calendar
     */
    protected $calendar;

    /**
     * Segments of the date
     * @var array
     */
    protected $segments = false;

    /**
     * Current month
     * @var integer
     */
    protected $month;

    /**
     * Current Year
     * @var integer
     */
    protected $year;

    /**
     * Full moons
     * @var array
     */
    protected $moons = [];

    /**
     * Season Changes
     * @var array
     */
    protected $seasons = [];

    /**
     * Layout option
     * @var string
     */
    protected $layout = 'year';
    /**
     * Initializer
     * @param Calendar $calendar
     */
    public function setCalendar(Calendar $calendar)
    {
        $this->calendar = $calendar;
        $this->buildCurrentSegments();
    }

    /**
     * Get previous month link
     * @return string
     */
    public function previous($title = false)
    {
        $month = $this->getMonth(-1);
        $year = $this->getYear();
        $months = $this->calendar->months();

        // Yearly navigation
        if ($this->isYearlyLayout()) {
            $year--;
            if ($title) {
                return $year;
            } else {
                return route(
                    'calendars.show',
                    ['calendar' => $this->calendar, 'layout' => 'year', 'year' => $year]
                );
            }
        }

        if ($month <= 0) {
            $year--;
            $month = count($months);
        }

        if ($title) {
            return $months[$month-1]['name'] . " $year";
        }

        return route(
            'calendars.show',
            ['calendar' => $this->calendar, 'month' => $month, 'year' => $year]
        );
    }

    /**
     * Get current year-month
     * @return string
     */
    public function current()
    {
        $month = $this->getMonth();
        $year = $this->getYear();
        $months = $this->calendar->months();
        $options = '';
        // Year name?
        $names = $this->calendar->years();
        $hasYearName = isset($names[$year]) ? $names[$year] : null;


        $monthName = $months[$this->getMonth(-1)]['name']
            . ($hasYearName ? ', ' : ' ');
        return ($this->isYearlyLayout() ? null : e($monthName))
            . '<a href="#" id="calendar-year-switcher">' . e(isset($names[$year]) ? $names[$year] : $year) . '</a>';
    }

    /**
     * Get next month link
     * @return string
     */
    public function next($title = false)
    {
        $month = $this->getMonth(1);
        $year = $this->getYear();
        $months = $this->calendar->months();

        // Yearly navigation
        if ($this->isYearlyLayout()) {
            $year++;
            if ($title) {
                return $year;
            } else {
                return route(
                    'calendars.show',
                    ['calendar' => $this->calendar, 'layout' => 'year', 'year' => $year]
                );
            }
        }

        if ($month > count($months)) {
            $year++;
            $month = 1;
        }

        if ($title) {
            return $months[$month-1]['name'] . " $year";
        }

        return route(
            'calendars.show',
            ['calendar' => $this->calendar, 'month' => $month, 'year' => $year]
        );
    }

    /**
     * Build the month with the weeks and days
     * @return array
     */
    public function month()
    {
        // Number of weeks in this month?
        $weekdays = $this->calendar->weekdays();
        $months = $this->calendar->months();
        $month = $months[$this->getMonth()-1];
        $data = [];

        $offset = $this->weekStartoffset();
        $events = $this->events();

        if (Arr::get($month, 'type') == 'intercalary') {
            $offset = 0;
        }

        // Check if this month is a leap month
        if ($this->calendar->has_leap_year) {
            if ($this->calendar->leap_year_month == $this->getMonth()) {
                // Is this the starting year, or an increment of the offset?
                $handle = $this->getYear() - $this->calendar->leap_year_start;
                if ($handle % $this->calendar->leap_year_offset === 0) {
                    $month['length'] += $this->calendar->leap_year_amount;
                }
            }
        }

        $monthLength = $month['length'];
        $weekLength = 0;
        $week = [];
        for ($day = 1; $day <= $monthLength; $day++) {
            if ($offset > 0) {
                $week[] = null;
                $offset--;
                $day--;
            } else {
                $exact = $this->getYear() . '-' . $this->getMonth() . '-' . $day;
                $dayData = [
                    'day' => $day,
                    'events' => [],
                    'date' => $exact,
                    'isToday' => false,
                ];

                if (isset($events[$exact])) {
                    $dayData['events'] = $events[$exact];
                }

                if ($exact == $this->calendar->date) {
                    $dayData['isToday'] = true;
                }

                if (isset($this->moons[$day])) {
                    $dayData['moons'] = $this->moons[$day];
                }

                $monthday = $this->getMonth() . '-' . $day;
                if (isset($this->seasons[$monthday])) {
                    $dayData['season'] = $this->seasons[$monthday];
                }
                $week[] = $dayData;
            }

            $weekLength++;

            if (count($week) >= count($weekdays)) {
                $data[] = $week;
                $week = [];
            }
        }

        // Fill in the last week?
        $lastWeekDiff = count($week) - count($weekdays);
        if ($lastWeekDiff < 0) {
            for ($day = $lastWeekDiff; $day < 0; $day++) {
                $week[] = null;
            }
        }

        $data[] = $week;

        return $data;
    }

    /**
     * @return array
     */
    public function weeks()
    {
        // Number of weeks in this month?
        $weekdays = $this->calendar->weekdays();
        $months = $this->calendar->months();
        $data = [];

        $events = $this->events();
        $offset = $this->weekStartoffset();

        // Add empty days for the beginning of the year
        for ($i = $offset; $i>0; $i--) {
            $data[] = null;
        }

        $weekLength = count($weekdays);
        $monthNumber = 1;
        $totalDay = 1;
        foreach ($months as $month) {
            $month = $months[$monthNumber-1];
            $this->month = $monthNumber;

            // Check if this month is a leap month
            if ($this->calendar->has_leap_year) {
                if ($this->calendar->leap_year_month == $monthNumber) {
                    // Is this the starting year, or an increment of the offset?
                    $handle = $this->getYear() - $this->calendar->leap_year_start;
                    if ($handle % $this->calendar->leap_year_offset === 0) {
                        $month['length'] += $this->calendar->leap_year_amount;

                        // If it also happens to add days on an intercalary day, we need to influence the number of days
                        // added at the end of the month.
                    }
                }
            }

            $monthLength = $month['length'];
            $week = [];

            // If the month is intercalary, we need to offset to the "beginning" of the week
            if (Arr::get($month, 'type') == 'intercalary') {
                $totalDays = count($data);
                $emptyDaysToFill = $weekLength - ($totalDays % $weekLength);
                $currentPosition = $weekLength - $emptyDaysToFill; // On which week day we currently are
                for ($d = 0; $d < $emptyDaysToFill; $d++) {
                    $data[] = [];
                }
            }

            // Add each day of the month to the day thing
            for ($day = 1; $day <= $monthLength; $day++) {
                $exact = $this->getYear() . '-' . $monthNumber . '-' . $day;
                $dayData = [
                    'day' => $day,
                    'events' => [],
                    'date' => $exact,
                    'isToday' => false,
                    'month' => $month['name'],
                ];

                if (isset($events[$exact])) {
                    $dayData['events'] = $events[$exact];
                }

                if ($exact == $this->calendar->date) {
                    $dayData['isToday'] = true;
                }

                if (isset($this->moons[$totalDay])) {
                    $dayData['moons'] = $this->moons[$totalDay];
                }

                $monthday = $monthNumber . '-' . $day;
                if (isset($this->seasons[$monthday])) {
                    $dayData['season'] = $this->seasons[$monthday];
                }
                $data[] = $dayData;

                $totalDay++;
            }


            // If the month is intercalary, we need to fill out the rest of the "week" until where it starts again
            if (Arr::get($month, 'type') == 'intercalary') {
                $totalDays = count($data);
                $emptyDaysToFill = $weekLength - ($totalDays % $weekLength);
                for ($d = 0; $d < $emptyDaysToFill; $d++) {
                    $data[] = [];
                }
                // Fill out the next month beginning if needed
                for ($d = 0; $d < $currentPosition; $d++) {
                    $data[] = [];
                }
            }

            // Fill in the last week?
//            $lastWeekDiff = count($week) - count($weekdays);
//            if ($lastWeekDiff < 0) {
//                for ($day = $lastWeekDiff; $day < 0; $day++) {
//                    $week[] = null;
//                }
//            }

//            $data[] = $week;
            $monthNumber++;
        }

        return $data;
    }

    /**
     * @return mixed
     */
    public function currentMonthId()
    {
        return $this->getMonth();
    }

    /**
     * Show a button for today if the current view isn't the current year-month.
     * @return string
     */
    public function todayButton()
    {
        $calendarYear = $this->calendar->currentDate('year');
        $calendarMonth = $this->calendar->currentDate('month');

        $options = ['class' => 'btn btn-default'];
        if ($this->year == $calendarYear && $this->month == $calendarMonth) {
            $options['disabled'] = 'disabled';
        }

        return link_to_route(
            'calendars.show',
            trans('calendars.actions.today'),
            [$this->calendar, 'month' => $calendarMonth, 'year' => $calendarYear],
            $options
        );
    }

    /**
     * @return bool
     */
    public function isIntercalaryMonth()
    {
        $month = $this->currentMonthId()-1;
        foreach ($this->calendar->months() as $k => $m) {
            if ($k == $month && Arr::get($m, 'type') == 'intercalary') {
                return true;
            }
        }

        return false;
    }

    /**
     * Build the current month and year segments
     */
    protected function buildCurrentSegments()
    {
        if ($this->segments === false) {
            $this->segments = true;

            $segments = $this->splitDate($this->calendar->date);
            $this->setMonth($segments[1]);
            $this->setYear($segments[0]);

            if (request()->has('month')) {
                $this->setMonth(request()->input('month'));
            }
            if (request()->has('year')) {
                $this->setYear(request()->input('year'));
            }

            if (empty($this->getMonth())) {
                $this->setMonth(1);
            }

            // If the month is too big? Then use the max
            if ($this->getMonth() > count($this->calendar->months())) {
                $this->setMonth(count($this->calendar->months()));
            }

            // Yearly layout does things a bit differently, reset month to first
            $this->layout = request()->get('layout', 'monthly');
            if ($this->isYearlyLayout()) {
                $this->setMonth(1);
            }

            $this->buildFullmoons();
            $this->buildSeasons();
        }
    }

    /**
     * Calculate the month starting offset
     * @return float
     */
    protected function weekStartOffset()
    {
        $totalDays = $this->daysToDate(false);
        $negativeYear = $totalDays < 0;
        $weekLength = count($this->calendar->weekdays());
        if ($weekLength == 0) {
            $weekLength = 1;
        }
        $totalDays = $negativeYear ? abs($totalDays) : $totalDays;
        $offset = floor($totalDays % $weekLength);

        // If we are in a negative year, we need to reduce the offset from the week length, as we want the last
        // month before the calendar really "starts" to end on the last day of the week.
        return $negativeYear ? $weekLength - $offset : $offset;
    }

    /**
     * Load events of the year and month
     * @return array
     */
    protected function events()
    {
        $events = [];
        $datePattern = $this->getYear() . (!$this->isYearlyLayout() ? '-' . $this->getMonth() : null) . '%';
        foreach ($this->calendar->calendarEvents()
                     ->has('entity')
                     ->with('entity')
                    ->where(function ($query) use ($datePattern) {
                        $query
                            // Where it's the current year , or current year and current month
                            ->where('date', 'like', $datePattern)
                            // Or where the event is recurring, or recurring on this month
                            ->orWhere(function ($sub) {
                                if ($this->isYearlyLayout()) {
                                    $sub->where('is_recurring', true);
                                } else {
                                    $sub->where('date', 'like', '%-' . $this->getMonth() . '-%')
                                        ->where('is_recurring', true);
                                }
                            })
                            // Events from previous month that spills over
                            ->orWhere(function ($sub) {
                                $sub->where('date', 'like', $this->subMonth($this->getYear(), $this->getMonth()) . '-%')
                                    ->where('length', '>', 1);
                            });
                    })
                     ->get() as $event) {
            $date = $event->date;

            // If the event is recurring, get the year to make sure it should start showing. This was previously
            // done in the query, but it didn't work on all systems.
            if ($event->is_recurring) {
                $blocks = $this->splitDate($date);
                if ($blocks[0] > $this->getYear()) {
                    continue;
                }
                // Over max reoccuring year?
                if (!empty($event->recurring_until) && $event->recurring_until < $this->getYear()) {
                    continue;
                }
                $date = $this->getYear() . '-' . $blocks[1] . '-' . $blocks[2];
            }
            if (!isset($events[$date])) {
                $events[$date] = [];
            }

            // Make sure the user can actually see the requested event
            if ($event->entity->child && EntityPermission::canView($event->entity, $this->calendar->campaign)) {
                $events[$date][] = $event;

                // Does the day go over a few days?
                if ($event->length > 1) {
                    $extraDate = $date;
                    for ($extra = 1; $extra < $event->length; $extra++) {
                        $extraDate = $this->addDay($extraDate);
                        $events[$extraDate][] = $event;
                    }
                }
            }
        }
        return $events;
    }

    /**
     * Add an extra day to a date.
     * @param $date string
     */
    protected function addDay($date)
    {
        list($year, $month, $day) = $this->splitDate($date);
        $day++;

        // Day longer than month?
        $months = $this->calendar->months();
        if ($day > $months[$month-1]['length']) {
            $day = 1;
            $month++;
        }

        // Month longer than max year?
        if ($month > count($months)) {
            $month = 1;
            $year++;
        }

        return "$year-$month-$day";
    }

    /**
     * @param $year
     * @param $month
     * @return string
     */
    protected function subMonth($year, $month)
    {
        $months = $this->calendar->months();
        $month--;

        if ($month <= 0) {
            $month = count($months);
            $year--;
        }
        return "$year-$month";
    }

    /**
     * Get the current year
     * @return mixed
     */
    protected function getYear($add = 0)
    {
        return (int) $this->year + (int) $add;
    }

    /**
     * Get the current month
     * @param int $add
     * @return int|mixed
     */
    protected function getMonth($add = 0)
    {
        return $this->month + $add;
    }

    /**
     * @param $year
     */
    protected function setYear($year)
    {
        $this->year = $year;
    }

    /**
     * @param $month
     */
    protected function setMonth($month)
    {
        $this->month = $month;
    }

    /**
     * Split the date into segments. Handle negative years
     * @param $date
     * @return array
     */
    protected function splitDate($date)
    {
        $segments = explode('-', ltrim($date, '-'));
        if (substr($date, 0, 1) === '-') {
            $segments[0] = '-' . $segments[0];
        }
        return $segments;
    }

    /**
     * @return bool
     */
    public function isYearlyLayout()
    {
        return $this->layout == 'year';
    }

    /**
     * @return int
     */
    public function currentYear()
    {
        return $this->year;
    }

    /**
     *
     */
    protected function buildFullmoons()
    {
        // Calculate the number of days since the 1.1.0
        $totalDays = $this->daysToDate();

        // We'll need this later to know how many full moons to add
        $daysInAYear = 0;
        foreach ($this->calendar->months() as $count => $month) {
            $length = $month['length'];
            $daysInAYear += $length;
        }
        foreach ($this->calendar->moons() as $moon) {
            $fullmoon = (int) $moon['fullmoon'];
            // Let's figure out how many full moons occurred until now
            $numberOfFullMoons = $totalDays / $fullmoon;

            // When was the last full moon?
            $lastFullMoon = floor($numberOfFullMoons) * $fullmoon;

            // Use that to see how many days it's been
            $daysSinceLastFullMoon = $totalDays - $lastFullMoon;

            // Next full moon? If it's 0, we want it today.
            $nextFullMoon = (1 + $moon['offset']) + ($fullmoon - ($daysSinceLastFullMoon == 0 ? $fullmoon : $daysSinceLastFullMoon));

//            if (true) {
//                dump("$name");
//                dump("number of full moons: $numberOfFullMoons");
//                dump("last full moon: $lastFullMoon");
//                dump("days since last full moon: $daysSinceLastFullMoon");
//                dump("next full moon: $nextFullMoon");
//            }

            $this->addFullMoon($nextFullMoon, $moon['name']);

            // Now the full moon will appear several times on this month/year.
            $fullMoonsPerYear = ceil($daysInAYear / $fullmoon);
            for ($i = 0; $i < $fullMoonsPerYear; $i++) {
                $nextFullMoon += $fullmoon;
                $this->addFullMoon($nextFullMoon, $moon['name']);
            }
        }
    }

    /**
     * Get the total amount of days since the beginning
     * @return float|int|mixed
     */
    protected function daysToDate($includeIntercalary = true)
    {
        // We assume that the 01 01 00 is a monday.
        // We need to know how many days elapsed since that day, to calculate the offset (total days / week length)

        $daysInAYear = $days = $leapDays = 0;
        foreach ($this->calendar->months() as $count => $month) {
            if (!$includeIntercalary && Arr::get($month, 'type') == 'intercalary') {
                continue;
            }
            $length = $month['length'];
            $daysInAYear += $length;

            // If the month has already passed, add it to the days for this year
            if ($count < $this->getMonth()-1) {
                $days += $length;
            }
        }

        if ($this->calendar->has_leap_year && $this->getYear() >= $this->calendar->leap_year_start) {
            // If the leap month is intercalary, we don't need to offset anything.
            $months = $this->calendar->months();
            $leapMonth = Arr::get($months, $this->calendar->leap_year_month-1, false);
            if ($leapMonth && Arr::get($leapMonth, 'type') == 'intercalary') {
                // Nothing
            } else {
                // Calc the number of years that were leap years
//            dump("the current year (" . $this->getYear() . ") is >= to when the calendar leap year starts
//               (" . $this->calendar->leap_year_start . ")");
                $yearDiffWithLeapStart = $this->getYear() - $this->calendar->leap_year_start;
                $amountOfYears = ceil($yearDiffWithLeapStart / $this->calendar->leap_year_offset);
//            dump ("the amount of leap years that has elapsed since the beginning is the following: $amountOfYears");
//            dump ("the value is ceil((" . $this->getYear() . "-" . $this->calendar->leap_year_start . ")
//               / " . $this->calendar->leap_year_offset . ")");
                if ($amountOfYears < 0) {
                    $amountOfYears = 0;
                }

                $leapDays = $amountOfYears * $this->calendar->leap_year_amount;

//            dump ("total leap days elapsed: $leapDays");

                // But if we are a leap year, we need to do the math
                if (($this->getYear() - $this->calendar->leap_year_start) % $this->calendar->leap_year_offset == 0) {
                    if ($this->getMonth() > $this->calendar->leap_year_month) {
                        // We've passed the leap month of the year
                        $leapDays += $this->calendar->leap_year_amount;
                    }
                }
            }
        }

        // Amount of days since the beginning of the year
        return ($daysInAYear * $this->getYear()) + $days + $leapDays;
    }

    /**
     * @param $nextFullMoon
     * @param $name
     */
    protected function addFullMoon($nextFullMoon, $name)
    {
        if (!isset($this->moons[$nextFullMoon])) {
            $this->moons[$nextFullMoon] = [];
        }
        $this->moons[$nextFullMoon][] = $name;
    }

    /**
     *
     */
    protected function buildSeasons()
    {
        foreach ($this->calendar->seasons() as $season) {
            $date = $season['month'] . '-' . $season['day'];
            $this->seasons[$date] = $season['name'];
        }
    }
}
