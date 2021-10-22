<?php

use Netflex\RuleBuilder\DateRules\GroupDateRule;

use PHPUnit\Framework\TestCase;
use Carbon\Carbon;
use Netflex\RuleBuilder\DateRules\DateRule;
use Netflex\RuleBuilder\RuleCollection;

trait MockDateRule
{
    public static function make(): self
    {
        return new static([], []);
    }
}

class AlwaysTrueDateRule extends DateRule
{
    use MockDateRule;

    function validate(Carbon $date): bool
    {
        return true;
    }
}

class AlwaysFalseDateRule extends DateRule
{
    use MockDateRule;

    public function validate(Carbon $date): bool
    {
        return false;
    }
}

class GroupDateRuleTest extends TestCase
{
    public function testEmptyCase()
    {
        $rule = new GroupDateRule(['children' => [], 'name' => "test", 'count' => 'any'], []);
        $this->assertFalse($rule->validate(Carbon::now()));
    }

    public function testAnyCase()
    {
        $rule = new GroupDateRule(['children' => [], 'name' => "test"], []);
        $rule->count = "any";
        $rule->children = RuleCollection::make([
            AlwaysTrueDateRule::make(),
            AlwaysFalseDateRule::make(),
            AlwaysFalseDateRule::make(),
        ]);
        $this->assertTrue($rule->validate(Carbon::today()));

        $rule->children = RuleCollection::make([
            AlwaysFalseDateRule::make(),
            AlwaysFalseDateRule::make(),
        ]);

        $this->assertFalse($rule->validate(Carbon::today()));
    }

    public function testAllCase()
    {
        $rule = new GroupDateRule(['children' => [], 'name' => "test"], []);
        $rule->count = "all";
        $rule->children = RuleCollection::make([
            AlwaysTrueDateRule::make(),
            AlwaysFalseDateRule::make(),
            AlwaysFalseDateRule::make(),
        ]);
        $this->assertFalse($rule->validate(Carbon::today()));

        $rule->children = RuleCollection::make([
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make()
        ]);

        $this->assertTrue($rule->validate(Carbon::today()));
    }

    public function testIntCase()
    {
        $rule = new GroupDateRule(['children' => [], 'name' => "test"], []);
        $rule->count = 2;
        $rule->children = RuleCollection::make([
            AlwaysTrueDateRule::make(),
            AlwaysFalseDateRule::make(),
            AlwaysFalseDateRule::make(),
        ]);
        $this->assertFalse($rule->validate(Carbon::today()));

        $rule->children = RuleCollection::make([
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make(),
            AlwaysFalseDateRule::make(),
        ]);

        $rule->count = 7;
        $this->assertFalse($rule->validate(Carbon::today()));
        $rule->count = 6;
        $this->assertTrue($rule->validate(Carbon::today()));
        $rule->count = 1;
        $this->assertTrue($rule->validate(Carbon::today()));
    }

    public function testIntCountLargerThanChildrenCountToStillPass()
    {
        $rule = new GroupDateRule(['children' => [], 'name' => "test"], []);
        $rule->count = 2;
        $rule->children = RuleCollection::make([
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make(),
            AlwaysTrueDateRule::make(),
        ]);
        $rule->count = 666;
        $this->assertTrue($rule->validate(Carbon::today()));
    }
}
