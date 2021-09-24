# Rule Builder PHP

## Installation

```bash
composer require apility/rule-builder
```

## Example usage

```php
<?php

use Carbon\Carbon;
use Netflex\RuleBuilder\DateRules\DateRule;

// Example of a rule that matches any dates in the year 2021 except the 17th of May, 24th of December and the month of June.
$rule = DateRule::parse([
    'type' => 'group',
    'count' => 'all',
    'children' => [
        [
            'type' => 'group',
            'count' => 'any',
            'children' => [
                [
                    'type' => 'dateRange',
                    'from' => '2021-01-01',
                    'to' => '2022-01-01'
                ]
            ]
        ],
        [
            'type' => 'not',
            'child' => [
                'type' => 'group',
                'count' => 'any',
                'children' => [
                    [
                        'name' => '17th of May',
                        'type' => 'dateRange',
                        'from' => '2021-05-17',
                        'to' => '2021-05-18'
                    ]
                    [
                        'name' => 'Christmas',
                        'type' => 'dateRange',
                        'from' => '2021-12-24',
                        'to' => '2021-12-25'
                    ],
                    [
                        'name' => 'Closed for the summer',
                        'type' => 'dateRange',
                        'from' => '2021-06-01',
                        'to' => '2021-07-01'
                    ]
                ]
            ]
        ]
    ]
]);

$validated = $rule->validate(Carbon::parse('2021-11-20'));

if ($validated) {
    // The rule matched the given date
}
```

```php
<?php

use Carbon\Carbon;
use Netflex\RuleBuilder\DateRules\DateRule;

// Example of a rule that matches every saturday and sunday in the month of September in the year 2021.
$rule = DateRule::parse([
    'type' => 'group',
    'count' => 'all',
    'children' => [
        [
            'type' => 'dateRange',
            'from' => '2021-09-01',
            'to' => '2022-10-01'
        ],
        [
            'type' => 'dayOfWeek',
            'days' => [6, 0]
        ]
    ]
]);

$validated = $rule->validate(Carbon::parse('2021-09-22'));

if ($validated) {
    // The rule matched the given date
}
```