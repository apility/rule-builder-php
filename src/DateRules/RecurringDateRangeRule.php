<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;
use Netflex\RuleBuilder\Contracts\Traversable;
use Netflex\RuleBuilder\Exceptions\IllegalInterval;
use Netflex\RuleBuilder\Exceptions\InvalidConfigurationException;

class RecurringDateRangeRule extends DateRule implements Traversable
{
    /** @var string */
    const YEARLY = 'yearly';

    /** @var string */
    const MONTHLY = 'monthly';

    /** @var string|null */
    public ?string $name = 'recurrringDateRange';

    /** @var string */
    public string $interval;

    /** @var Carbon */
    public Carbon $from;

    /** @var Carbon */
    public Carbon $to;

    /**
     * @inheritDoc
     * @throws IllegalInterval
     */
    public function validate(Carbon $date): bool
    {
        if (!isset($this->from) || !isset($this->to)) {
            throw new InvalidConfigurationException('from or to fields cannot be NULL');
        }

        $to = $this->to->copy();
        if($to->isBefore($this->from)) {
            $to->addYear();
        }

        $period = $this->from->toPeriod($to->copy()->subDay());

        $dayFilter = fn(Carbon $carbon) => $date->day === $carbon->day;
        $monthFilter = fn(Carbon $carbon) => $date->month === $carbon->month && $dayFilter($carbon);

        switch ($this->interval) {
            case 'monthly':
                return $period->addFilter($dayFilter)->count() > 0;
            case 'yearly':
                return $period->addFilter($monthFilter)->count() > 0;
            default:
                throw new IllegalInterval;
        }
    }

    /**
     * @inheritDoc
     */
    public function traverse(callable $callback)
    {
        if ($this->child) {
            if ($this->child instanceof Traversable) {
                /** @var Traversable $child */
                $child = $this->child;
                $child->traverse($callback);
                return;
            }

            $callback($this->child);
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'interval' => $this->interval,
            'from' => $this->from->toDateString(),
            'to' => $this->to->toDateString(),
        ]);
    }

    /**
     * @return array
     */
    public function settings(Carbon $date): array
    {
        return array_merge(parent::settings($date), [
            'interval' => $this->interval,
            'from' => $this->from->toDateString(),
            'to' => $this->to->toDateString(),
        ]);
    }
}
