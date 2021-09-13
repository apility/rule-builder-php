<?php

class DateRangeRuleTest extends \PHPUnit\Framework\TestCase
{

    public function testIncludesStartDate() {

        $from = \Carbon\Carbon::parse("2021-02-01");
        $to = \Carbon\Carbon::parse("2021-03-01");

        $rule = $this->_bootstrapRule($from, $to);

        $this->assertTrue($rule->validate($from));
    }

    public function testNotIncludesEndDate() {

        $from = \Carbon\Carbon::parse("2021-02-01");
        $to = \Carbon\Carbon::parse("2021-03-01");
        $rule = $this->_bootstrapRule($from, $to);

        $this->assertFalse($rule->validate($to));
    }

    public function testMissingFromMeansAnyPreviousDate() {

        $from = \Carbon\Carbon::parse("2021-02-01");
        $to = \Carbon\Carbon::parse("2021-03-01");
        $rule = $this->_bootstrapRule(null, $to);


        $this->assertFalse($rule->validate($to->copy()->addMillennia()), "");
        $this->assertFalse($rule->validate($to->copy()), "");
        $this->assertTrue($rule->validate($to->copy()->subMillennia()), "Date between 1970 and to-date passes");


    }

    public function testMissingToMeansAnyDateInFuture() {

        $from = \Carbon\Carbon::parse("2021-02-01");
        $to = \Carbon\Carbon::parse("2021-03-01");
        $rule = $this->_bootstrapRule($from, null);

        $this->assertTrue($rule->validate($from->copy()->addMillennia()), "Any date in future passes");
        $this->assertTrue($rule->validate($from->copy()), "Same date/time passes");
        $this->assertFalse($rule->validate($from->copy()->subMillennia()), "Any date before to fails");
    }

    private function _bootstrapRule(?\Carbon\Carbon $from, ?\Carbon\Carbon $to): \Netflex\RuleBuilder\DateRules\DateRangeRule {
        return new \Netflex\RuleBuilder\DateRules\DateRangeRule(['name' => "yay", 'from' => $from ? $from->toIso8601String() : null, 'to' => $to ? $to->toIso8601String() : null], []);
    }
}
