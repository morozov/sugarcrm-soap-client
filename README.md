SugarCRM SOAP Client
====================

This is a command line tool intended to automate any interactions with SugarCRM available via its SOAP API. Each action may be implemented as a plugin and then accessible as a command line action.

Your own plugin may look like this:

```php
<?php

namespace SugarCRM\Soap\Client\Plugin;

use SugarCRM\Soap\Client;

class FindEmployees extends AbstractPlugin
{
    public function __invoke($pattern)
    {
        $pattern = trim($pattern);
        if ('' === $pattern) {
            throw new \InvalidArgumentException('Query must be a non empty string');
        }

        return implode(
            PHP_EOL,
            array_map(
                function($row) {
                    return $row['name'];
                },
                $this->getClient()->getEntries(
                    'Employees',
                    'first_name LIKE \'' . addslashes($pattern) . '%\' OR last_name LIKE \'' . addslashes($pattern) . '%\'',
                    array(
                        'name',
                    )
                )
            )
        );
    }
}

```

…and you may invoke it in the following way:
```
$ ssc find-employees jim
Jim Brennan
```
