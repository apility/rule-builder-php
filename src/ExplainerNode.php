<?php

namespace Netflex\RuleBuilder;

class ExplainerNode
{
    /** @var bool */
    public bool $result;

    /** @var array */
    public array $children;

    /** @var array */
    public array $settings;

    public function __construct(bool $result, array $settings = [], array $children = [])
    {
        $this->result = $result;
        $this->settings = $settings;
        $this->children = $children;
    }
}
