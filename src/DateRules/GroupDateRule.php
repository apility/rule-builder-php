<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;
use Netflex\RuleBuilder\RuleCollection;
use Netflex\RuleBuilder\InvalidConfigurationException;

/**
 * Validates a group of rules against the same date and returns a unified answer
 *
 * $count is required, can be 'all', 'any' or an integer that determines how many rules must pass the check for the
 * group value to be true
 */
class GroupDateRule extends DateRule
{
    /** @var string|int */
    public $count;

    /** @var RuleCollection */
    public ?RuleCollection $children;

    /** @var string */
    public string $name = 'name';

    /**
     * @inheritDoc
     * @throws InvalidConfigurationException
     */
    public function validate(Carbon $date): bool
    {
        if ($this->count === 'all') {
            return collect($this->children)
                ->every(function (DateRule $rule) use ($date) {
                    return $rule->validate($date);
                });
        }

        if ($this->count === 'any') {
            return collect($this->children)
                ->first(function (DateRule $rule) use ($date) {
                    return $rule->validate($date);
                }) != null;
        }

        if (is_int($this->count)) {
            $ch = collect($this->children);
            $minLevel = min($ch->count(), $this->count);

            return $ch->filter(function (DateRule $rule) use ($date) {
                return $rule->validate($date);
            })->count() >= $minLevel;
        }

        throw new InvalidConfigurationException("[count] is not a valid value on rule with name {$this->name}. Must be 'any', 'all' or int");
    }

    /**
     * @return array
     */
    public function settings(): array
    {
        return array_merge(parent::settings(), [
            'count' => $this->count
        ]);
    }
}
