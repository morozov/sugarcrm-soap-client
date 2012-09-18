<?php

namespace SugarCRM\Test\Soap\Client\Plugin;

use SugarCRM\Soap\Client;
use SugarCRM\Soap\Client\Plugin\GetBugCommitMessage as Plugin;

class GetBugCommitMessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Plugin
     */
    private $plugin;

    public function setUp()
    {
        $this->plugin = new Plugin();
        $this->plugin->setClient(new Client());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testNonNumericNumber()
    {
        $plugin = $this->plugin;
        $plugin(-1);
    }

    /**
     * @expectedException \DomainException
     */
    public function testBugNotFound()
    {
        $client = $this->getMock('SugarCRM\Soap\Client', array('getEntry'));
        $client->expects($this->any())
            ->method('getEntry')
            ->will($this->returnValue(null));

        /** @var Client $client */
        $this->plugin->setClient($client);

        $plugin = $this->plugin;
        $plugin(1);
    }

    public function testResultMessage()
    {
        $client = $this->getMock('SugarCRM\Soap\Client', array('getEntry'));
        $client->expects($this->any())
            ->method('getEntry')
            ->will(
                $this->returnValue(
                    array(
                        'name' => 'Everything\'s not working',
                    )
                )
            );

        /** @var Client $client */
        $this->plugin->setClient($client);

        $plugin = $this->plugin;
        $result = $plugin(12345);

        $this->assertEquals('Bug 12345 - Everything\'s not working', $result);
    }
}
