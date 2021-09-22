<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;
use Netflex\RuleBuilder\ExplainerNode;
use Netflex\RuleBuilder\InvalidConfigurationException;
use Netflex\RuleBuilder\UnknownNodeType;

abstract class DateRule
{
    public static array $rules = [
        'group' => GroupDateRule::class,
        'dayOfWeek' => DayOfWeekDateRule::class,
        'dateRange' => DateRangeRule::class,
        'not' => NotDateRule::class
    ];

    public string $name;
    public array $children = [];

    /**
     * @throws UnknownNodeType
     */
    public function __construct(array $nodeData, array $rules)
    {
        $r = new \ReflectionClass($this);

        foreach ($nodeData as $key => $value) {
            try {
                $prop = $r->getProperty($key);
                if ($prop->hasType() && $prop->getType()->getName() == Carbon::class) {
                    $value = $value ? Carbon::parse($value) : null;
                } else if($prop->hasType() && $prop->getType()->getName() == DateRule::class) {
                    $value = $value ? DateRule::parse($value, $rules) : null;
                }
            } catch (\ReflectionException $e) {
            }
            $this->{$key} = $value;
        }

        foreach ($this->children as $key => $child) {
            $this->children[$key] = static::parse($child, $rules);
        }
    }

    /**
     * @throws UnknownNodeType
     */
    public static function parse(array $node, ?array $rules = null): self
    {
        $rules = $rules ?? static::$rules;

        if ($rule = $rules[$node['type']]) {
            return new $rule($node, $rules);
        } else {
            throw new UnknownNodeType("DateRule of type {$node['type']} is not known");
        }
    }

    /**
     * Resolves itself then resolves its childrens explain function to get a tree to see rule behaviour
     *
     * @param Carbon $date
     * @return ExplainerNode
     * @throws InvalidConfigurationException
     */
    function explain(Carbon $date): ExplainerNode
    {
        $children = [];

        /** @var static $child */
        foreach ($this->children as $child) {
            array_push($children, $child->explain($date));
        }

        return new ExplainerNode($this->validate($date), $this->settings(), $children);
    }

    /**
     * Validates the date against the current rule
     *
     * @param Carbon $date
     * @return bool
     * @throws InvalidConfigurationException
     */
    abstract function validate(Carbon $date): bool;

    function settings(): array
    {
        return [
            'name' => $this->name
        ];
    }
}
