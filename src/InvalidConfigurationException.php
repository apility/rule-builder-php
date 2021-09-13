<?php

namespace Netflex\RuleBuilder;

use Throwable;

class InvalidConfigurationException extends \Exception
{
    function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, 0, null);
    }
}
