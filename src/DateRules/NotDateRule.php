<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;

class NotDateRule extends DateRule
{
    /** @var DateRule */
    public DateRule $child;

    /**
     * @inheritDoc
     */
    public function validate(Carbon $date): bool
    {
        return !$this->child->validate($date);
    }
}
