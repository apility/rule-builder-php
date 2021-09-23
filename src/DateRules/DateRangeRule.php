<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;

class DateRangeRule extends DateRule
{
    public ?Carbon $from;
    public ?Carbon $to;

    /** @var string */
    public string $name = 'dateRange';

    /**
     * @inheritDoc
     */
    public function validate(Carbon $date): bool
    {
        if (isset($this->from) && $date->lt($this->from)) {
            return false;
        }

        if (isset($this->to) && $date->gte($this->to)) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function settings(): array
    {
        return array_merge(parent::settings(), [
            'from' => $this->from ? $this->from->toDateString() : 'Infinity',
            'to' => $this->to ? $this->to->toDateString() : 'Infinity',
        ]);
    }
}
