<?php

namespace Netflex\RuleBuilder;

class ExplainerNode
{

    public bool $result;
    public array $children;
    public array $settings;

    function __construct(bool $result, array $settings = [], array $children = []) {
        $this->result = $result;
        $this->settings = $settings;
        $this->children = $children;
    }
}
