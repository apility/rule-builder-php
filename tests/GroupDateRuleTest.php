<?php

use Netflex\RuleBuilder\DateRules\GroupDateRule;

class _AlwaysTrueDateRule extends \Netflex\RuleBuilder\DateRules\DateRule {

    function __construct()
    {
        parent::__construct([], []);
    }

    function validate(\Carbon\Carbon $date): bool
    {
        return true;
    }
}

class _AlwaysFalseDateRule extends \Netflex\RuleBuilder\DateRules\DateRule {

    function __construct()
    {
        parent::__construct([], []);
    }

    function validate(\Carbon\Carbon $date): bool
    {
        return false;
    }
}

class GroupDateRuleTest extends \PHPUnit\Framework\TestCase
{

    public function testAnyCase() {
        $rule = new GroupDateRule(['children' => [], 'name' => "test"], []);
        $rule->count = "any";
        $rule->children = [
            new _AlwaysTrueDateRule(),
            new _AlwaysFalseDateRule(),
            new _AlwaysFalseDateRule(),
        ];
        $this->assertTrue($rule->validate(\Carbon\Carbon::today()));

        $rule->children = [
            new _AlwaysFalseDateRule(),
            new _AlwaysFalseDateRule(),
        ];

        $this->assertFalse($rule->validate(\Carbon\Carbon::today()));

    }

    public function testAllCase() {
        $rule = new GroupDateRule(['children' => [], 'name' => "test"], []);
        $rule->count = "all";
        $rule->children = [
            new _AlwaysTrueDateRule(),
            new _AlwaysFalseDateRule(),
            new _AlwaysFalseDateRule(),
        ];
        $this->assertFalse($rule->validate(\Carbon\Carbon::today()));

        $rule->children = [
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule()
        ];

        $this->assertTrue($rule->validate(\Carbon\Carbon::today()));

    }

    public function testIntCase() {
        $rule = new GroupDateRule(['children' => [], 'name' => "test"], []);
        $rule->count = 2;
        $rule->children = [
            new _AlwaysTrueDateRule(),
            new _AlwaysFalseDateRule(),
            new _AlwaysFalseDateRule(),
        ];
        $this->assertFalse($rule->validate(\Carbon\Carbon::today()));

        $rule->children = [
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule(),
            new _AlwaysFalseDateRule(),
        ];

        $rule->count = 7;
        $this->assertFalse($rule->validate(\Carbon\Carbon::today()));
        $rule->count = 6;
        $this->assertTrue($rule->validate(\Carbon\Carbon::today()));
        $rule->count = 1;
        $this->assertTrue($rule->validate(\Carbon\Carbon::today()));

    }

    public function testIntCountLargerThanChildrenCountToStillPass() {
        $rule = new GroupDateRule(['children' => [], 'name' => "test"], []);
        $rule->count = 2;
        $rule->children = [
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule(),
            new _AlwaysTrueDateRule(),
        ];
        $rule->count = 666;
        $this->assertTrue($rule->validate(\Carbon\Carbon::today()));
    }


}
