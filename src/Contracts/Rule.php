<?php

namespace Netflex\RuleBuilder\Contracts;

use Carbon\Carbon;

use Netflex\RuleBuilder\ExplainerNode;
use Netflex\RuleBuilder\InvalidConfigurationException;

interface Rule
{
    /**
     * Resolves itself then resolves its childrens explain function to get a tree to see rule behaviour
     *
     * @param Carbon $date
     * @return ExplainerNode
     * @throws InvalidConfigurationException
     */
    public function explain(Carbon $date): ExplainerNode;

    /**
     * Validates the date against the current rule
     *
     * @param Carbon $date
     * @return bool
     * @throws InvalidConfigurationException
     */
    public function validate(Carbon $date): bool;

    /**
     * @return array
     */
    public function settings(Carbon $date): array;

    /**
     * @param string $json
     * @param array|null $rules
     * @throws UnknownNodeType
     * @return DateRule
     */
    public static function fromJson(string $json, ?array $rules = null): self;

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0);

    /**
     * @return array
     */
    public function jsonSerialize();

    /**
     * @return array
     */
    public function toArray();
}
