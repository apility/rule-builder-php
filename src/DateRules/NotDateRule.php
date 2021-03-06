<?php

namespace Netflex\RuleBuilder\DateRules;

use Carbon\Carbon;

use Netflex\RuleBuilder\Contracts\Traversable;
use Netflex\RuleBuilder\ExplainerNode;

class NotDateRule extends DateRule implements Traversable
{
    /** @var DateRule */
    public ?DateRule $child;

    /** @var string|null */
    public ?string $name = 'not';

    /**
     * @inheritDoc
     */
    public function validate(Carbon $date): bool
    {
        return !$this->child->validate($date);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'child' => isset($this->child) ? $this->child->toArray() : null,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function traverse(callable $callback)
    {
        if ($this->child) {
            if ($this->child instanceof Traversable) {
                /** @var Traversable $child */
                $child = $this->child;
                $child->traverse($callback);
                return;
            }

            $callback($this->child);
        }
    }

    public function explain(Carbon $date = null): ExplainerNode
    {
        return new ExplainerNode($this->validate($date), [], [$this->child->explain($date)], get_class($this));
    }
}
