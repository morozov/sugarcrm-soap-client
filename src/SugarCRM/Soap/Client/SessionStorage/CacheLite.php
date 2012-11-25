<?php

/**
 * Cache_Lite session ID storage
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
use SugarCRM\Soap\Client\SessionStorage\Exception;
use Cache_Lite;

/**
 * Cache_Lite session ID storage
 *
 * @category  SugarCRM
 * @package   SugarCRM\Soap
 * @author    Sergei Morozov <morozov@tut.by>
 * @copyright 2012 Sergei Morozov
 * @license   http://mit-license.org/ MIT Licence
 * @link      http://github.com/morozov/sugarcrm-soap-client
 */
class CacheLite implements SessionStorage
{
    /**
     * Cache_Lite instance
     *
     * @var array
     */
    protected $cache;

    /**
     * Default Cache_Lite constructor options
     *
     * @var array
     */
    protected static $defaultOptions = array(
        'cacheDir' => '/tmp/sugarcrm-soap-client/',
        'lifeTime' => 1440,
    );

    /**
     * Constructor
     *
     * @param array $options Cache_Lite constructor options
     *
     * @throws Exception
     */
    public function __construct(array $options = array())
    {
        $options = array_merge(self::$defaultOptions, $options);

        $cacheDir = $options['cacheDir'];
        if (!is_dir($cacheDir) && !mkdir($cacheDir, 0777, true)) {
            throw new Exception('Unable to create cache directory');
        }

        $this->cache = new Cache_Lite($options);
    }

    /**
     * Returns session ID for the specified connection
     *
     * @param string $connectionKey Connection key
     *
     * @return string|boolean
     */
    public function getSessionId($connectionKey)
    {
        return $this->cache->get($connectionKey);
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
        $this->cache->save($sessionId, $connectionKey);

        return $this;
    }
}
