<?php

use Carbon\Carbon;
use Netflex\RuleBuilder\DateRules\DateRangeRule;
use Netflex\RuleBuilder\DateRules\DateRule;
use Netflex\RuleBuilder\Exceptions\UnknownNodeType;
use PHPUnit\Framework\TestCase;

class FromJsonTest extends TestCase
{
    public function testCanParseJson()
    {
        $json = '{ "name": "test", "type": "group", "children": [], "count": "any" }';
        $parsed = DateRule::fromJson($json);
        $this->assertInstanceOf(DateRule::class, $parsed);
    }

    public function testThrowsExceptionOnInvalidPayload()
    {
        $json = 'null';

        $this->expectException(UnknownNodeType::class);

        DateRule::fromJson($json);
    }
}
