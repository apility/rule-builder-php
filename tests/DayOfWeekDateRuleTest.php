<?php

use PHPUnit\Framework\TestCase;

use Carbon\CarbonPeriod;
use Carbon\Carbon;

use Netflex\RuleBuilder\DateRules\DayOfWeekDateRule;

final class DayOfWeekDateRuleTest extends TestCase
{
    public function testDayFilter()
    {

        $today = Carbon::today();
        for ($i = 0; $i < 365; $i++) {
            $in = $today->addDay()->copy();

            $period = new CarbonPeriod(
                $in,
                $in->copy()->addWeek()->subDay(),
                '1 day'
            );

            $rule = $this->_bootstrapRule();

            $this->assertEquals(1, collect($period->toArray())->filter(function (Carbon $date) use ($rule) {
                return $rule->validate($date);
            })->count());
        }
    }

    private function _bootstrapRule(): DayOfWeekDateRule
    {
        return new DayOfWeekDateRule([
            'name' => 'Day of week',
            'days' => [0]
        ], []);
    }
}
