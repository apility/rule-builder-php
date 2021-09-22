<?php

namespace Netflex\RuleBuilder\DateRules;

use ReflectionClass;
use ReflectionException;

use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Netflex\RuleBuilder\RuleCollection;
use Netflex\RuleBuilder\ExplainerNode;
use Netflex\RuleBuilder\InvalidConfigurationException;
use Netflex\RuleBuilder\UnknownNodeType;

abstract class DateRule
{
    /**
     * @var DateRule[]
     */
    public static array $rules = [
        'group' => GroupDateRule::class,
        'dayOfWeek' => DayOfWeekDateRule::class,
        'dateRange' => DateRangeRule::class,
        'not' => NotDateRule::class
    ];

    /** @var string */
    public string $name;

    /** @var RuleCollection|null */
    public ?RuleCollection $children;

    /**
     * @param array $nodeData
     * @param DateRule[] $rules
     * @throws UnknownNodeType
     */
    public function __construct(array $nodeData, array $rules)
    {
        $reflectionClass = new ReflectionClass($this);

        foreach ($nodeData as $key => $value) {
            try {
                $property = $reflectionClass->getProperty($key);

                if ($property->hasType()) {
                    $type = $property->getType()->getName();

                    if (is_subclass_of($type, Carbon::class) || $type === Carbon::class) {
                        $value = $value ? Carbon::parse($value) : null;
                    }

                    if (is_subclass_of($type, DateRule::class) || $type === DateRule::class) {
                        $value = $value ? DateRule::parse($value, $rules) : null;
                    }

                    if (is_subclass_of($type, RuleCollection::class) || $type === RuleCollection::class) {
                        $value = ($value ? RuleCollection::make($value) : RuleCollection::make([]))->map(function ($child) use ($rules) {
                            if (!($child instanceof DateRule)) {
                                return static::parse($child, $rules);
                            }

                            return $child;
                        });
                    }
                }
            } catch (ReflectionException $e) {
                // Unknown property, ignore it
                continue;
            }

            $this->{$key} = $value;
        }
    }

    /**
     * @param array $node
     * @param DateRule[]|null $rules
     * @throws UnknownNodeType
     * @return DateRule
     */
    public static function parse(array $node, ?array $rules = null): self
    {
        $rules = $rules ?? static::$rules;

        if ($rule = $rules[$node['type']] ?: null) {
            return new $rule($node, $rules);
        }

        throw new UnknownNodeType("DateRule of type {$node['type']} is not known");
    }

    /**
     * Resolves itself then resolves its childrens explain function to get a tree to see rule behaviour
     *
     * @param Carbon $date
     * @return ExplainerNode
     * @throws InvalidConfigurationException
     */
    public function explain(Carbon $date): ExplainerNode
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
    abstract public function validate(Carbon $date): bool;

    /**
     * @return array
     */
    public function settings(): array
    {
        return [
            'name' => $this->name
        ];
    }
}
