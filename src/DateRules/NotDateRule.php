<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;
use Netflex\RuleBuilder\InvalidConfigurationException;

class NotDateRule extends DateRule
{

    public DateRule $child;
    /**
     * @inheritDoc
     */
    function validate(Carbon $date): bool
    {
        return !$this->child->validate($date);
    }
}
