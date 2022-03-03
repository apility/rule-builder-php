<?php

use Carbon\Carbon;

use Netflex\RuleBuilder\DateRules\RecurringDateRangeRule;
use Netflex\RuleBuilder\Exceptions\IllegalInterval;
use PHPUnit\Framework\TestCase;

class RecurringDateRangeRuleTest extends TestCase
{
    public function testCanRecurDateRangeYearly()
    {
        $from = Carbon::parse('2021-02-28');
        $to = Carbon::parse('2021-03-03');

        $rule = $this->_bootstrapRule($from, $to, RecurringDateRangeRule::YEARLY);

        $date = Carbon::parse('2021-03-01');
        $this->assertTrue($rule->validate($date));
        $date = Carbon::parse('2021-03-03');
        $this->assertFalse($rule->validate($date));

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

        $rule = $this->_bootstrapRule($from, $to, RecurringDateRangeRule::MONTHLY);

        $date = Carbon::parse('2021-01-03');
        $this->assertTrue($rule->validate($date));
        $date = Carbon::parse('2021-01-11');
        $this->assertFalse($rule->validate($date));

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

    public function testDateOverlappingYears()
    {
        $from = Carbon::parse('2021-10-01');
        $to = Carbon::parse('2022-02-28');

        $rule = $this->_bootstrapRule($from, $to, RecurringDateRangeRule::YEARLY);

        $date = Carbon::parse('2022-11-11');
        $this->assertTrue($rule->validate($date));

        $date = Carbon::parse('2023-01-01');
        $this->assertTrue($rule->validate($date));

        $date = Carbon::parse('2022-03-01');
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

    private function _bootstrapRule(Carbon $from, Carbon $to, $interval): RecurringDateRangeRule
    {
        return new RecurringDateRangeRule([
            'name' => 'Recurring',
            'interval' => $interval,
            'from' => $from->toIso8601String(),
            'to' => $to->toIso8601String()
        ], []);
    }
}
