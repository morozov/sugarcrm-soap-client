<?php

/**
 * Plugin that accepts bug number as a parameter and returns
 * commit message which should be used when the bug is fixed
 *
 * PHP version 5.3
 *
 * @category  SugarCRM
 * @package   SugarCRM\Soap\Client\Plugin
 * @author    Sergei Morozov <morozov@tut.by>
 * @copyright 2012 Sergei Morozov
 * @license   http://mit-license.org/ MIT Licence
 * @link      http://github.com/morozov/sugarcrm-soap-client
 */
namespace SugarCRM\Soap\Client\Plugin;

use SugarCRM\Soap\Client;

/**
 * Plugin that accepts bug number as a parameter and returns
 * commit message which should be used when the bug is fixed
 *
 * @category  SugarCRM
 * @package   SugarCRM\Soap\Client\Plugin
 * @author    Sergei Morozov <morozov@tut.by>
 * @copyright 2012 Sergei Morozov
 * @license   http://mit-license.org/ MIT Licence
 * @link      http://github.com/morozov/sugarcrm-soap-client
 */
class GetBugCommitMessage extends AbstractPlugin
{
    /**
     * Message format
     */
    const FORMAT = 'Bug %d - %s';

    /**
     * Plugin entry point
     *
     * @param int $number Bug number
     *
     * @return string
     * @throws \InvalidArgumentException
     * @throws \DomainException
     */
    public function __invoke($number)
    {
        $number = filter_var(
            $number,
            FILTER_VALIDATE_INT,
            array(
                'options' => array(
                    'min_range' => 0,
                ),
            )
        );

        if (false === $number) {
            throw new \InvalidArgumentException('Number must be a positive integer');
        }

        $bug = $this->getClient()->getEntry(
            'Bugs',
            'bug_number=' . (int) $number,
            array('name')
        );

        if (!is_array($bug)) {
            throw new \DomainException('Bug not found');
        }

        return sprintf(self::FORMAT, $number, $bug['name']);
    }
}
