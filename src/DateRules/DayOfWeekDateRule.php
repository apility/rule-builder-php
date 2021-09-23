<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;

class DayOfWeekDateRule extends DateRule
{
    /** @var int[] */
    public array $days;

    /** @var string */
    public string $name = 'dayOfWeek';

    /**
     * @inheritDoc
     */
    public function validate(Carbon $date): bool
    {
        return collect($this->days)->some(function (int $day) use ($date) {
            return $date->isDayOfWeek($day);
        });
    }

    /**
     * @return array
     */
    public function settings(): array
    {
        return array_merge(parent::settings(), [
            'days' => $this->days,
        ]);
    }
}
