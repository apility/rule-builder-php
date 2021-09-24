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

    /** @var string */
    public string $name = 'recurrringDateRange';

    /** @var string */
    public string $interval;

    /** @var Carbon */
    public Carbon $from;

    /** @var Carbon */
    public Carbon $to;

    protected function getDatesForDate(Carbon $date): array
    {
        $from = clone ($this->from);
        $to = clone ($this->to);

        $year = $date->year;
        $yearDiff = $from && $to ? ($to->year - $from->year) : 0;
        $month = $date->month;
        $monthDiff = $from && $to ? ($to->month - $from->month) : 0;

        switch ($this->interval) {
            case 'monthly':
                $from->setMonth($month);
                $to->setMonth($month + $monthDiff);
            case 'yearly':
                $from->setYear($year);
                $to->setYear($year + $yearDiff);
                break;
        }

        return ['from' => $from, 'to' => $to];
    }

    /**
     * @inheritDoc
     * @throws IllegalInterval
     */
    public function validate(Carbon $date): bool
    {
        if (!isset($this->interval) || !in_array($this->interval, [static::YEARLY, static::MONTHLY])) {
            throw new IllegalInterval;
        }

        if (!isset($this->from) || !isset($this->to)) {
            throw new InvalidConfigurationException('from or to fields cannot be NULL');
        }

        $dates = $this->getDatesForDate($date);
        $from = $dates['from'];
        $to = $dates['to'];

        return $date->isSameDay($from) || $date->between($from, $to);
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
    public function settings(Carbon $date): array
    {
        $dates = $this->getDatesForDate($date);
        $from = $dates['from'];
        $to = $dates['to'];

        return array_merge(parent::settings($date), [
            'interval' => $this->interval,
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
        ]);
    }
}
