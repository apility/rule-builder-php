<?php

namespace Netflex\RuleBuilder\DateRules;

use ReflectionClass;
use ReflectionException;

use Carbon\Carbon;

use JsonSerializable;

use Illuminate\Support\Str;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Contracts\Support\Arrayable;

use Netflex\RuleBuilder\Contracts\Rule;

use Netflex\RuleBuilder\RuleCollection;
use Netflex\RuleBuilder\ExplainerNode;

use Netflex\RuleBuilder\Exceptions\InvalidConfigurationException;
use Netflex\RuleBuilder\Exceptions\UnknownNodeType;
use SebastianBergmann\Type\UnknownType;

abstract class DateRule implements Rule, JsonSerializable, Jsonable, Arrayable
{
    const GROUP = 'group';
    const DAY_OF_WEEK = 'dayOfWeek';
    const RANGE = 'dateRange';
    const RECURRING_RANGE = 'recurringDateRange';
    const NOT = 'not';

    /**
     * @var DateRule[]
     */
    public static array $rules = [
        DateRule::GROUP => GroupDateRule::class,
        DateRule::DAY_OF_WEEK => DayOfWeekDateRule::class,
        DateRule::RANGE => DateRangeRule::class,
        DateRule::RECURRING_RANGE => RecurringDateRangeRule::class,
        DateRule::NOT => NotDateRule::class
    ];

    /** @var string|int|null */
    public $id = null;

    /** @var string|null */
    public ?string $name = 'dateRule';

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

        if (!isset($this->id) || !$this->id) {
            $this->id = Str::uuid();
        }
    }

    /**
     * @param string $json
     * @param array|null $rules
     * @throws UnknownNodeType
     * @return DateRule
     */
    public static function fromJson(string $json, ?array $rules = null): self
    {
        $payload = json_decode($json, true);

        if ($payload && is_array($payload)) {
            return static::parse($payload, $rules);
        }

        throw new UnknownNodeType('Unexpected payload: ' . $json);
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => isset($this->id) ? $this->id : null,
            'name' => isset($this->name) ? $this->name : null,
            'type' => array_search(static::class, static::$rules),
        ];
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
    public function explain(Carbon $date = null): ExplainerNode
    {
        $date = $date ?? Carbon::now();
        $children = [];

        if (isset($this->children)) {
            /** @var static $child */
            foreach ($this->children as $child) {
                array_push($children, $child->explain($date));
            }
        }

        return new ExplainerNode($this->validate($date), $this->settings($date), $children);
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
    public function settings(Carbon $date): array
    {
        return [
            'name' => $this->name
        ];
    }
}
