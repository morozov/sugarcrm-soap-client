<?php

/**
 * In-memory session ID storage
 *
 * PHP version 5.3
 *
 * @category  SugarCRM
 * @package   SugarCRM\Soap
 * @author    Sergei Morozov <morozov@tut.by>
 * @copyright 2012 Sergei Morozov
 * @license   http://mit-license.org/ MIT Licence
 * @link      http://github.com/morozov/sugarcrm-soap-client
 */
namespace SugarCRM\Soap\Client\SessionStorage;

use SugarCRM\Soap\Client\SessionStorage;

/**
 * In-memory session ID storage
 *
 * @category  SugarCRM
 * @package   SugarCRM\Soap
 * @author    Sergei Morozov <morozov@tut.by>
 * @copyright 2012 Sergei Morozov
 * @license   http://mit-license.org/ MIT Licence
 * @link      http://github.com/morozov/sugarcrm-soap-client
 */
class Memory implements SessionStorage
{
    /** @var array */
    protected $data;

    /**
     * Returns session ID for the specified connection
     *
     * @param string $connectionKey Connection key
     *
     * @return string|boolean
     */
    public function getSessionId($connectionKey)
    {
        if (isset($this->data[$connectionKey])) {
            return $this->data[$connectionKey];
        }

        return false;
    }

    /**
     * Sets session ID for the specified connection
     *
     * @param string $connectionKey Connection key
     * @param string $sessionId     Session ID
     *
     * @return static
     */
    public function setSessionId($connectionKey, $sessionId)
    {
        $this->data[$connectionKey] = $sessionId;

        return $this;
    }
}
