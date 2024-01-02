<?php

use Carbon\Carbon;
use Netflex\RuleBuilder\DateRules\DateRangeRule;
use PHPUnit\Framework\TestCase;

class DateRangeRuleTest extends TestCase
{
    public function testIncludesStartDate()
    {
        $from = Carbon::parse('2021-02-01');
        $to = Carbon::parse('2021-03-01');

        $rule = $this->_bootstrapRule($from, $to);

        $this->assertTrue($rule->validate($from));
    }

    public function testNotIncludesEndDate()
    {

        $from = Carbon::parse('2021-02-01');
        $to = Carbon::parse('2021-03-01');
        $rule = $this->_bootstrapRule($from, $to);

        $this->assertFalse($rule->validate($to));
    }

    public function testMissingFromMeansAnyPreviousDate()
    {
        $to = Carbon::parse('2021-03-01');
        $rule = $this->_bootstrapRule(null, $to);

        $this->assertFalse($rule->validate($to->copy()->addMillennia()), '');
        $this->assertFalse($rule->validate($to->copy()), '');
        $this->assertTrue($rule->validate($to->copy()->subMillennia()), 'Date between 1970 and to-date passes');
    }

    public function testMissingToMeansAnyDateInFuture()
    {
        $from = Carbon::parse('2021-02-01');
        $rule = $this->_bootstrapRule($from, null);

        $this->assertTrue($rule->validate($from->copy()->addMillennia()), 'Any date in future passes');
        $this->assertTrue($rule->validate($from->copy()), 'Same date/time passes');
        $this->assertFalse($rule->validate($from->copy()->subMillennia()), 'Any date before to fails');
    }


    public function testNonInclusiveness()
    {

        $week2 = new DateRangeRule(["id" => "2622b2d4-d4ef-4c7b-baf0-787b50481475", "type" => "dateRange", "name" => "Uke 2", "from" => "2024-01-08", "to" => "2024-01-15"], []);
        $week3 = new DateRangeRule(["id" => "2622b2d4-d4ef-4c7b-baf0-787b50481475", "type" => "dateRange", "name" => "Uke 3", "from" => "2024-01-15", "to" => "2024-01-22"], []);

        $date = Carbon::parse('2024-01-15');
        $date2 = Carbon::parse('2024-01-15');

        $this->assertFalse($week2->validate($date));
        $this->assertTrue($week3->validate($date));

        $this->assertSame(
            $date->toIso8601ZuluString(),
            $date2->toIso8601ZuluString(),
        );
    }

    private function _bootstrapRule(?Carbon $from, ?Carbon $to): DateRangeRule
    {
        return new DateRangeRule(['name' => 'yay', 'from' => $from ? $from->toIso8601String() : null, 'to' => $to ? $to->toIso8601String() : null], []);
    }
}
