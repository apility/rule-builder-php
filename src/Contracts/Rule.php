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
    public function settings(): array;
}
