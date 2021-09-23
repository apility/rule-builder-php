<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;

use Netflex\RuleBuilder\Contracts\Traversable;

class DateRuleRecurring extends DateRule
{
    /** @var string */
    public string $name = 'recurring';

    /** @var string */
    public string $interval;

    /** @var DateRule */
    public ?DateRule $child;

    public function validate(Carbon $date): bool
    {
        if (!$this->child) {
            return true;
        }

        $stack = [];

        $mutate = function (DateRule $rule) use (&$stack, $date) {
            switch (get_class($rule)) {
                case DateRangeRule::class:
                    $from = clone ($rule->from);
                    $to = clone ($rule->to);

                    $stack[] = function () use ($rule, $from, $to) {
                        $rule->from = $from;
                        $rule->to = $to;
                    };

                    $year = $date->year;
                    $yearDiff = $from && $to ? ($to->year - $from->year) : 0;
                    $month = $date->month;
                    $monthDiff = $from && $to ? ($to->month - $from->month) : 0;

                    switch ($this->interval) {
                        case 'monthly':
                            $rule->from = $rule->from ? $rule->from->setMonth($month) : null;
                            $rule->to = $rule->to ? $rule->to->setMonth($month + $monthDiff) : null;
                        case 'yearly':
                            $rule->from = $rule->from ? $rule->from->setYear($year) : null;
                            $rule->to = $rule->to ? $rule->to->setYear($year + $yearDiff) : null;
                            break;
                    }

                    break;
            }
        };

        if ($this->child instanceof Traversable) {
            /** @var Traversable $child */
            $child = $this->child;
            $child->traverse($mutate);
        } else {
            $mutate($this->child);
        }

        $validated = $this->child->validate($date);

        foreach (array_reverse($stack) as $revert) {
            $revert();
        }

        return $validated;
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
    public function settings(): array
    {
        return array_merge(parent::settings(), [
            'interval' => $this->count,
        ]);
    }
}
