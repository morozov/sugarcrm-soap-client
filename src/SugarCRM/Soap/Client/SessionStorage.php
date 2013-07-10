<?php

/**
 * SugarCRM SOAP client plugin interface
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
namespace SugarCRM\Soap\Client;

/**
 * Interface for sessionID storage
 *
 * @category  SugarCRM
 * @package   SugarCRM\Soap
 * @author    Sergei Morozov <morozov@tut.by>
 * @copyright 2012 Sergei Morozov
 * @license   http://mit-license.org/ MIT Licence
 * @link      http://github.com/morozov/sugarcrm-soap-client
 */
interface SessionStorage
{
    /**
     * Returns session ID for the specified connection
     *
     * @param string $connectionKey Connection key
     *
     * @return string|boolean
     */
    public function getSessionId($connectionKey);

    /**
     * Sets session ID for the specified connection
     *
     * @param string $connectionKey Connection key
     * @param string $sessionId     Session ID
     *
     * @return static
     */
    public function setSessionId($connectionKey, $sessionId);

    /**
     * Unsets session ID for the specified connection
     *
     * @param string $connectionKey Connection key
     *
     * @return static
     */
    public function unsetSessionId($connectionKey);
}
