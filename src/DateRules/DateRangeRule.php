<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;

class DateRangeRule extends DateRule
{
    public ?Carbon $from;
    public ?Carbon $to;

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
}
