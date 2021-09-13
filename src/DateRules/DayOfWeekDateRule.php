<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;

class DayOfWeekDateRule extends DateRule
{
    public array $days;

    /**
     * @inheritDoc
     */
    function validate(Carbon $date): bool
    {
        return collect($this->days)->some(function (int $day) use ($date) {
            return $date->isDayOfWeek($day);
        });
    }
}
