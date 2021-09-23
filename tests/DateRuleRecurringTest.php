<?php

use Carbon\Carbon;

use Netflex\RuleBuilder\DateRules\DateRangeRule;
use Netflex\RuleBuilder\DateRules\DateRuleRecurring;
use Netflex\RuleBuilder\Exceptions\IllegalInterval;
use PHPUnit\Framework\TestCase;

class DateRuleRecurringTest extends TestCase
{
    public function testCanRecurDateRangeYearly()
    {
        $from = Carbon::parse('2021-02-28');
        $to = Carbon::parse('2021-03-03');

        $rule = $this->_bootstrapRule($from, $to, DateRuleRecurring::YEARLY);

        $date = Carbon::parse('2021-03-01');
        $this->assertTrue($rule->validate($date));

        $date = Carbon::parse('2021-02-27');
        $this->assertFalse($rule->validate($date));

        $date = Carbon::parse('2022-03-01');
        $this->assertTrue($rule->validate($date));

        $date = Carbon::parse('2022-02-27');
        $this->assertFalse($rule->validate($date));
    }

    public function testCanRecurDateRangeMonthly()
    {
        $from = Carbon::parse('2021-01-02');
        $to = Carbon::parse('2021-01-11');

        $rule = $this->_bootstrapRule($from, $to, DateRuleRecurring::MONTHLY);

        $date = Carbon::parse('2021-01-03');
        $this->assertTrue($rule->validate($date));

        $date = Carbon::parse('2021-01-12');
        $this->assertFalse($rule->validate($date));

        $date = Carbon::parse('2021-02-03');
        $this->assertTrue($rule->validate($date));

        $date = Carbon::parse('2021-02-12');
        $this->assertFalse($rule->validate($date));

        $date = Carbon::parse('2022-02-03');
        $this->assertTrue($rule->validate($date));

        $date = Carbon::parse('2022-02-12');
        $this->assertFalse($rule->validate($date));
    }

    public function testThrowsExceptionForIllegalInterval()
    {
        $from = Carbon::parse('2021-01-02');
        $to = Carbon::parse('2021-01-11');
        $date = Carbon::parse('2021-01-03');

        $rule = $this->_bootstrapRule($from, $to, 'potato');

        $this->expectException(IllegalInterval::class);
        $rule->validate($date, true);

        $rule = $this->_bootstrapRule($from, $to, null);

        $this->expectException(IllegalInterval::class);
        $rule->validate($date, true);
    }

    private function _bootstrapRule(?Carbon $from, ?Carbon $to, $interval): DateRuleRecurring
    {
        $dateRange = ['type' => 'dateRange', 'from' => $from ? $from->toIso8601String() : null, 'to' => $to ? $to->toIso8601String() : null];

        return new DateRuleRecurring(['name' => 'Recurring', 'interval' => $interval, 'child' => $dateRange], [
            'dateRange' => DateRangeRule::class
        ]);
    }
}
