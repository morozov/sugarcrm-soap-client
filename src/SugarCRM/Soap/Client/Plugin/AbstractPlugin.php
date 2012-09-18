<?php

/**
 * SugarCRM SOAP client plugin
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
namespace SugarCRM\Soap\Client\Plugin;

use SugarCRM\Soap\Client;

/**
 * SugarCRM SOAP client plugin
 *
 * @category  SugarCRM
 * @package   SugarCRM\Soap
 * @author    Sergei Morozov <morozov@tut.by>
 * @copyright 2012 Sergei Morozov
 * @license   http://mit-license.org/ MIT Licence
 * @link      http://github.com/morozov/sugarcrm-soap-client
 */
class AbstractPlugin implements PluginInterface
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * Returns client
     *
     * @return Client
     * @throws \LogicException
     */
    public function getClient()
    {
        if (!$this->client) {
            throw new \LogicException('Client is not set');
        }

        return $this->client;
    }

    /**
     * Sets client
     *
     * @param Client $client Client instance
     *
     * @return static
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
        return $this;
    }
}
