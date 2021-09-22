<?php

use PHPUnit\Framework\TestCase;

use Carbon\Carbon;

use Netflex\RuleBuilder\DateRules\DateRangeRule;
use Netflex\RuleBuilder\DateRules\DateRule;
use Netflex\RuleBuilder\DateRules\DayOfWeekDateRule;
use Netflex\RuleBuilder\DateRules\GroupDateRule;

final class DateRuleTest extends TestCase
{
    public function testResolvingGroup(): void
    {
        $data = [
            'name' => 'test1',
            'type' => 'group',
            'count' => 'any',
            'children' => []
        ];

        $this->assertInstanceOf(
            GroupDateRule::class,
            DateRule::parse($data)
        );
    }

    public function testResolvingDateRange(): void
    {
        $rule = DateRule::parse([
            'name' => 'dateRule',
            'type' => 'dateRange',
            'from' => '2021-01-01',
            'to' => '2022-01-01'
        ]);

        $this->assertInstanceOf(
            DateRangeRule::class,
            $rule
        );

        $this->assertInstanceOf(
            Carbon::class,
            $rule->from,
        );

        $this->assertTrue($rule->from->isSameDay(Carbon::parse('2021-01-01')));
    }

    public function testResolvingDayOfWeek(): void
    {
        $rule = DateRule::parse([
            'name' => 'dateRule',
            'type' => 'dayOfWeek',
            'days' => [0, 1, 2, 3, 4, 5],
        ]);

        $this->assertInstanceOf(
            DayOfWeekDateRule::class,
            $rule
        );
    }
}
