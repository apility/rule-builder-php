<?php

namespace Netflex\RuleBuilder\Contracts;

interface Traversable extends Rule
{
    /**
     * Traverse through the rule tree and apply the callback to visisted rules
     *
     * @param callable $callback
     * @return void
     */
    public function traverse(callable $callback);
}
