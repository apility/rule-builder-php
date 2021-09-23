<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;

class NotDateRule extends DateRule
{
    /** @var DateRule */
    public ?DateRule $child;

    /** @var string */
    public string $name = 'not';

    /**
     * @inheritDoc
     */
    public function validate(Carbon $date): bool
    {
        return !$this->child->validate($date);
    }
}
