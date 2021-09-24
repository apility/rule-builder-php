<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;

class DateRangeRule extends DateRule
{
    public ?Carbon $from;
    public ?Carbon $to;

    /** @var string|null */
    public ?string $name = 'dateRange';

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
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'from' => $this->from ? $this->from->toDateString() : null,
            'to' => $this->to ? $this->to->toDateString() : null,
        ]);
    }

    /**
     * @return array
     */
    public function settings(Carbon $date): array
    {
        return array_merge(parent::settings($date), [
            'from' => $this->from ? $this->from->toDateString() : 'Infinity',
            'to' => $this->to ? $this->to->toDateString() : 'Infinity',
        ]);
    }
}
