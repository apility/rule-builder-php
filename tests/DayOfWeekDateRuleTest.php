<?php

use PHPUnit\Framework\TestCase;

final class DayOfWeekDateRuleTest extends TestCase
{

    public function testDayFilter()
    {

        $today = \Carbon\Carbon::today();
        for ($i = 0; $i < 365; $i++) {
            $in = $today->addDay()->copy();

            $period = new \Carbon\CarbonPeriod(
                $in,
                $in->copy()->addWeek()->subDay(),
                '1 day'
            );

            $rule = $this->_bootstrapRule();

            $this->assertEquals(1, collect($period->toArray())->filter(function(\Carbon\Carbon $date) use ($rule) {
                return $rule->validate($date);
            })->count());

        }

    }

    private function _bootstrapRule(): \Netflex\RuleBuilder\DateRules\DayOfWeekDateRule
    {
        return new \Netflex\RuleBuilder\DateRules\DayOfWeekDateRule([
            'name' => "Day of week",
            'days' => [0]
        ], []);
    }
}
