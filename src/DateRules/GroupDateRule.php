<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\DataAwareRule;
use Netflex\RuleBuilder\ExplainerNode;
use Netflex\RuleBuilder\InvalidConfigurationException;

/**
 * Validates a group of rules against the same date and returns a unified answer
 *
 * $count is required, can be "all", "any" or an integer that determines how many rules must pass the check for the
 * group value to be true
 */
class GroupDateRule extends DateRule
{

    public $count;
    public array $children;


    /**
     * @inheritDoc
     * @throws InvalidConfigurationException
     */
    function validate(Carbon $date): bool
    {
        if($this->count == "all") {
            return collect($this->children)
                ->every(function(DateRule $rule) use ($date) {
                    return $rule->validate($date);
                });
        }

        if($this->count == "any") {
            return collect($this->children)
                ->first(function(DateRule $rule) use ($date) {
                    return $rule->validate($date);
                }) != null;
        }

        if(is_int($this->count)) {
            $ch = collect($this->children);
            $minLevel = min($ch->count(), $this->count);

            return $ch->filter(function(DateRule $rule) use ($date) {
                return $rule->validate($date);
            })->count() >= $minLevel;
        }

        throw new InvalidConfigurationException("[count] is not a valid value on rule with name {$this->name}. Must be 'any', 'all' or int");
    }

    function settings(): array
    {
        return array_merge(parent::settings(), [
            'count' => $this->count
        ]);
    }
}
