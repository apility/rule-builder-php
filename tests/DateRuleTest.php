<?php
use PHPUnit\Framework\TestCase;

final class DateRuleTest extends TestCase {

    public function testResolvingGroup(): void {

        $data = [
            'name' => "test1",
            'type' => "group",
            'count' => "any",
            'children' => []
        ];

        $this->assertInstanceOf(
            \Netflex\RuleBuilder\DateRules\GroupDateRule::class,
            \Netflex\RuleBuilder\DateRules\DateRule::parse($data)
        );
    }

    public function testResolvingDateRange(): void {
        $rule = \Netflex\RuleBuilder\DateRules\DateRule::parse([
            'name' => "dateRule",
            'type' => "dateRange",
            'from' => "2021-01-01",
            'to' => "2022-01-01"
        ]);

        $this->assertInstanceOf(
            \Netflex\RuleBuilder\DateRules\DateRangeRule::class,
            $rule);

        $this->assertInstanceOf(
            \Carbon\Carbon::class,
            $rule->from,
        );

        $this->assertTrue($rule->from->isSameDay(\Carbon\Carbon::parse("2021-01-01")));
    }

    public function testResolvingDayOfWeek(): void {
        $rule = \Netflex\RuleBuilder\DateRules\DateRule::parse([
            'name' => "dateRule",
            'type' => "dayOfWeek",
            'days' => [0,1,2,3,4,5],
        ]);

        $this->assertInstanceOf(
            \Netflex\RuleBuilder\DateRules\DayOfWeekDateRule::class,
            $rule
        );
    }
}
