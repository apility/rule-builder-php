<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;

use Netflex\RuleBuilder\RuleCollection;
use Netflex\RuleBuilder\Exceptions\InvalidConfigurationException;

use Netflex\RuleBuilder\Contracts\Traversable;

/**
 * Validates a group of rules against the same date and returns a unified answer
 *
 * $count is required, can be 'all', 'any' or an integer that determines how many rules must pass the check for the
 * group value to be true
 */
class GroupDateRule extends DateRule implements Traversable
{
    /** @var string|int */
    public $count;

    /** @var RuleCollection */
    public ?RuleCollection $children;

    /** @var string|null */
    public ?string $name = 'name';

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
            $children = collect($this->children);
            $minLevel = min($children->count(), $this->count);

            return $children->filter(function (DateRule $rule) use ($date) {
                return $rule->validate($date);
            })->count() >= $minLevel;
        }

        throw new InvalidConfigurationException("[count] is not a valid value on rule with name {$this->name}. Must be 'any', 'all' or int");
    }

    /**
     * @inheritDoc
     */
    public function traverse(callable $callback)
    {
        foreach ($this->children as $child) {
            if ($child instanceof Traversable) {
                /** @var Traversable $child */
                $child->traverse($callback);
                return;
            }

            $callback($child);
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'count' => $this->count,
            'children' => isset($this->children)
                ? $this->children->map(fn (DateRule $child) => $child->toArray())
                ->toArray()
                : [],
        ]);
    }

    /**
     * @return array
     */
    public function settings(Carbon $date): array
    {
        return array_merge(parent::settings($date), [
            'count' => $this->count
        ]);
    }
}
