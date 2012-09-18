<?php

namespace SugarCRM\Test\Soap\Client\Plugin;

use SugarCRM\Soap\Client;
use SugarCRM\Soap\Client\Plugin\AbstractPlugin as Plugin;

class AbstractPluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Plugin
     */
    private $plugin;

    public function setUp()
    {
        $this->plugin = $this->getMockForAbstractClass(
            'SugarCRM\Soap\Client\Plugin\AbstractPlugin'
        );
    }

    /**
     * @expectedException \LogicException
     */
    public function testClientNotSet()
    {
        $this->plugin->getClient();
    }

    public function testClientSetAndRetrieved()
    {
        $client = new Client();
        $this->plugin->setClient($client);

        $this->assertEquals($client, $this->plugin->getClient());
    }
}
