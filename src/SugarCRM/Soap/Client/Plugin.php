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

use SugarCRM\Soap\Client;

/**
 * SugarCRM SOAP client plugin interface
 *
 * @category  SugarCRM
 * @package   SugarCRM\Soap
 * @author    Sergei Morozov <morozov@tut.by>
 * @copyright 2012 Sergei Morozov
 * @license   http://mit-license.org/ MIT Licence
 * @link      http://github.com/morozov/sugarcrm-soap-client
 */
interface Plugin
{
    /**
     * Returns client
     *
     * @return Client
     */
    public function getClient();

    /**
     * Sets client
     *
     * @param Client $client Client instance
     *
     * @return static
     */
    public function setClient(Client $client);
}
