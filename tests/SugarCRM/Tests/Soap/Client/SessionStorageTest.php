<?php

namespace SugarCRM\Test\Soap\Client;

use SugarCRM\Soap\Client;
use SugarCRM\Soap\Client\SessionStorage;

class SessionStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param SessionStorage $sessionStorage
     * @dataProvider getSessionStorages
     */
    public function testNonExistingKey(SessionStorage $sessionStorage = null)
    {
        if (!$sessionStorage) {
            $this->markTestSkipped('Optional storage dependencies are not met');
        }

        $connectionKey = 'non-existing-key';
        $sessionId = $sessionStorage->getSessionId($connectionKey);
        $this->assertFalse($sessionId);
    }

    /**
     * @param SessionStorage $sessionStorage
     * @dataProvider getSessionStorages
     */
    public function testSetKey(SessionStorage $sessionStorage = null)
    {
        if (!$sessionStorage) {
            $this->markTestSkipped('Optional storage dependencies are not met');
        }

        $connectionKey = 'connection1';
        $sessionId     = 'session1';
        $sessionStorage->setSessionId($connectionKey, $sessionId);

        $result = $sessionStorage->getSessionId($connectionKey);
        $this->assertEquals($sessionId, $result);
    }

    public static function getSessionStorages()
    {
        return array(
            'Memory' => array(
                new SessionStorage\Memory(),
            ),
            'Cache_Lite' => array(
                new SessionStorage\CacheLite(),
            ),
        );
    }
}
