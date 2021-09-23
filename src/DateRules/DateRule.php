<?php

namespace Netflex\RuleBuilder\DateRules;

use ReflectionClass;
use ReflectionException;

use Carbon\Carbon;

use Netflex\RuleBuilder\Contracts\Rule;

use Netflex\RuleBuilder\RuleCollection;
use Netflex\RuleBuilder\ExplainerNode;
use Netflex\RuleBuilder\InvalidConfigurationException;
use Netflex\RuleBuilder\UnknownNodeType;

abstract class DateRule implements Rule
{
    const GROUP = 'group';
    const DAY_OF_WEEK = 'dayOfWeek';
    const RANGE = 'dateRange';
    const RECURRING = 'recurring';
    const NOT = 'not';

    /**
     * @var DateRule[]
     */
    public static array $rules = [
        DateRule::GROUP => GroupDateRule::class,
        DateRule::DAY_OF_WEEK => DayOfWeekDateRule::class,
        DateRule::RANGE => DateRangeRule::class,
        DateRule::RECURRING => DateRuleRecurring::class,
        DateRule::NOT => NotDateRule::class
    ];

    /** @var string|int|null */
    public $id = null;

    /** @var string */
    public string $name = 'dateRule';

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

        if (!$this->name) {
            $this->name = $this->type;
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

        if ($rule = $rules[$node['type']] ?? null) {
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

        if (isset($this->children)) {
            /** @var static $child */
            foreach ($this->children as $child) {
                array_push($children, $child->explain($date));
            }
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
