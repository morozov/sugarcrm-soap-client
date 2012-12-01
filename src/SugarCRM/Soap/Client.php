<?php

/**
 * SugarCRM SOAP client
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
namespace SugarCRM\Soap;

use SoapClient;
use StdClass;
use Exception;
use SugarCRM\Soap\Client\Plugin\PluginInterface;
use SugarCRM\Soap\Client\SessionStorage;

/**
 * SugarCRM SOAP client
 *
 * @category  SugarCRM
 * @package   SugarCRM\Soap
 * @author    Sergei Morozov <morozov@tut.by>
 * @copyright 2012 Sergei Morozov
 * @license   http://mit-license.org/ MIT Licence
 * @link      http://github.com/morozov/sugarcrm-soap-client
 */
class Client
{
    /**
     * Error response code
     */
    const ERROR_RESULT = -1;

    /**
     * @var SoapClient
     */
    protected $soapClient;

    /**
     * @var SessionStorage
     */
    protected $sessionStorage;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * Constructor
     *
     * @param array $options Client options
     */
    public function __construct(array $options = array())
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set client options
     *
     * @param array $options Client options
     *
     * @return static
     */
    public function setOptions(array $options)
    {
        foreach ($options as $param => $value) {
            $this->setOption($param, $value);
        }

        return $this;
    }

    /**
     * Sets option
     *
     * @param string $name  Option name
     * @param mixed  $value Option value
     *
     * @return static
     */
    public function setOption($name, $value)
    {
        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method)) {
            $this->$method($value);
        }

        return $this;
    }

    /**
     * Sets SugarCRM instance URL
     *
     * @param string $url SugarCRM instance URL
     *
     * @return static
     * @throws Exception
     */
    public function setUrl($url)
    {
        if (!is_string($url)) {
            $this->error(
                'SOAP service URL must be a string, ' . gettype($url) . ' given'
            );
        }

        if ($url != $this->url) {
            $this->soapClient = null;
        }

        $this->url = rtrim($url, '/');
        return $this;
    }

    /**
     * Sets web service authentication username
     *
     * @param string $username SugarCRM API user name
     *
     * @return static
     * @throws Exception
     */
    public function setUsername($username)
    {
        if (!is_string($username)) {
            $this->error(
                'SOAP service username must be a string, '
                . gettype($username) . ' given'
            );
        }

        if ($username != $this->username) {
            $this->soapClient = null;
        }

        $this->username = $username;

        return $this;
    }

    /**
     * Sets web service authentication password
     *
     * @param string $password SugarCRM API user password
     *
     * @return static
     * @throws Exception
     */
    public function setPassword($password)
    {
        if (!is_string($password)) {
            $this->error(
                'SOAP service password must be a string, '
                . gettype($password) . ' given'
            );
        }

        if ($password != $this->password) {
            $this->soapClient = null;
        }

        $this->password = $password;

        return $this;
    }

    /**
     * Sets web service authentication credentials
     *
     * @param string $username SugarCRM API user name
     * @param string $password SugarCRM API user password
     *
     * @return static
     */
    public function setCredentials($username, $password)
    {
        return $this->setUsername($username)
            ->setPassword($password);
    }

    /**
     * Returns SOAP client
     *
     * @return SoapClient
     */
    public function getSoapClient()
    {
        if (!$this->soapClient instanceof SoapClient) {

            if ('' == $this->url) {
                $this->error('SugarCRM instance URL is not specified');
            }

            // instantiate the soap client
            $this->soapClient = new SoapClient(
                null,
                array(
                    'location' => $this->url . '/service/v3/soap.php',
                    'uri'      => $this->url . '/',
                    'trace'    => 1,
                )
            );

            $sessionId = $this->getSessionId();

            if (false === $sessionId) {
                // try to login to retrieve a session id
                $result = $this->soapClient->login(
                    array(
                        'user_name' => $this->username,
                        'password'  => md5($this->password),
                    )
                );

                // process authentication error
                if (self::ERROR_RESULT == $result->id) {
                    $this->error($result->error->description);
                }

                $this->setSessionId($result->id);
            }
        }

        return $this->soapClient;
    }

    /**
     * Sets SOAP client
     *
     * @param SoapClient $soapClient SOAP client instance
     *
     * @return static
     */
    protected function setSoapClient(SoapClient $soapClient)
    {
        $this->soapClient = $soapClient;

        return $this;
    }

    /**
     * Returns connection key
     *
     * @return string
     */
    protected function getConnectionKey()
    {
        $key = array($this->url, $this->username, $this->password);
        $key = serialize($key);
        $key = md5($key);
        return $key;
    }

    /**
     * Returns session ID storage
     *
     * @return SessionStorage
     */
    public function getSessionStorage()
    {
        if (!$this->sessionStorage) {
            $this->sessionStorage = new SessionStorage\CacheLite();
        }

        return $this->sessionStorage;
    }

    /**
     * Sets session ID storage
     *
     * @param SessionStorage $sessionStorage Session ID storage
     *
     * @return static
     */
    public function setSessionStorage(SessionStorage $sessionStorage)
    {
        $this->sessionStorage = $sessionStorage;

        return $this->sessionStorage;
    }

    /**
     * Returns session ID storage
     *
     * @return string|boolean Session ID or FALSE if session ID is not stored
     */
    public function getSessionId()
    {
        return $this->getSessionStorage()->getSessionId(
            $this->getConnectionKey()
        );
    }

    /**
     * Sets session ID storage
     *
     * @param string $sessionId Session ID
     *
     * @return static
     */
    public function setSessionId($sessionId)
    {
        $this->getSessionStorage()->setSessionId(
            $this->getConnectionKey(),
            $sessionId
        );

        return $this;
    }

    /**
     * Converts associative array to web service compatible array of parameters
     *
     * @param array $params Associative array of parameters
     *
     * @return array
     */
    protected function toSoapParams(array $params)
    {
        $result = array();
        foreach ($params as $name => $value) {
            $result[] = array(
                'name' => $name,
                'value' => $value,
            );
        }
        return $result;
    }

    /**
     * Retrieve SugarCRM entry
     *
     * @param string $module Module name
     * @param string $where  SQL WHERE expression
     * @param array  $fields Desired entry fields
     *
     * @return array|null
     */
    public function getEntry($module, $where, array $fields)
    {
        $response = $this->getEntryList($module, $where, $fields, 1);

        $entry = array_shift($response->entry_list);

        if (null !== $entry) {
            return $this->processEntry($entry);
        }

        return null;
    }

    /**
     * Retrieve list of SugarCRM entries
     *
     * @param string $module Module name
     * @param string $where  SQL WHERE expression
     * @param array  $fields Desired entry fields
     *
     * @return array|null
     */
    public function getEntries($module, $where, array $fields)
    {
        $response = $this->getEntryList($module, $where, $fields);

        $result = $index = array();
        foreach ($response->entry_list as $entry) {

            // filter unique entries as long web services returns duplicates.
            // this is caused by retrieving M2M-related entries
            if (isset($index[$entry->id])) {
                continue;
            }

            $result[] = $this->processEntry($entry);
            $index[$entry->id] = true;
        }

        return $result;
    }

    /**
     * Retrieve entry list form SOAP service
     *
     * @param string   $module      Module name
     * @param string   $where       SQL WHERE expression
     * @param array    $fields      Desired entry fields
     * @param int|null $max_results Max results number
     *
     * @internal
     * @return mixed
     */
    protected function getEntryList($module, $where, $fields, $max_results = null)
    {
        $soapClient = $this->getSoapClient();

        // perform SOAP function call
        $response = $soapClient->get_entry_list(
            $this->getSessionId(),
            $module,
            $where,
            null,
            0,
            $fields,
            $max_results,
            false
        );

        // validate service response
        $this->validateEntryListResponse($response);
        return $response;
    }

    /**
     * Process entry
     *
     * @param \StdClass $entry Entry retrieved from SOAP response
     *
     * @return array
     */
    protected function processEntry(stdClass $entry)
    {
        $this->validateEntry($entry);

        $data = array();
        foreach ($entry->name_value_list as $item) {
            $name = $item->name;
            $value = trim($item->value);

            // decode HTML entities received from web service
            $value = mb_convert_encoding($value, 'UTF-8', 'HTML-ENTITIES');

            $data[$name] = $value;
        }

        return array_merge(
            $data,
            array(
                'id' => $entry->id,
            )
        );
    }

    /**
     * Validates SOAP service response object
     *
     * @param mixed $response SOAP service response object
     *
     * @return void
     * @throws Exception
     */
    protected function validateEntryListResponse($response)
    {
        if (!is_object($response)) {
            $this->error('Response is not an object (' . gettype($response) . ')');
        }

        if (property_exists($response, 'result_count')
            && self::ERROR_RESULT == $response->result_count
        ) {
            $this->error($response->error->description);
        }

        if (!property_exists($response, 'entry_list')) {
            $this->error('Response object does not have "entry_list" property');
        }

        if (!is_array($response->entry_list)) {
            $this->error(
                'Response "entry_list" is not an array ('
                . gettype($response->entry_list) . ')'
            );
        }
    }

    /**
     * Validates resulting entry
     *
     * @param mixed $entry Entry retrieved from SOAP response
     *
     * @return void
     * @throws Exception
     */
    protected function validateEntry($entry)
    {
        if (!is_object($entry)) {
            $this->error('Entry is not an object (' . gettype($entry) . ')');
        }

        if (!property_exists($entry, 'name_value_list')) {
            $this->error('Entry object does not have "name_value_list" property');
        }

        if (!is_array($entry->name_value_list)) {
            $this->error(
                'Entry "name_value_list" is not an array ('
                . gettype($entry->entry_list) . ')'
            );
        }
    }

    /**
     * Call plugin method
     *
     * @param string $method    Method to call
     * @param array  $arguments Arguments to call the method with
     *
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $plugin = $this->getPlugin($method);

        /** @var callable $plugin */
        return call_user_func_array($plugin, $arguments);
    }

    /**
     * Call plugin method. Validation of arguments is performed before
     *
     * @param string $method    Method to call
     * @param array  $arguments Arguments to call the method with
     *
     * @return mixed
     * @throws \BadFunctionCallException()
     */
    public function call($method, array $arguments)
    {
        $plugin = $this->getPlugin($method);

        $re = $this->getPluginReflection($plugin);
        $length = $this->getPluginMinArguments($re);

        if (count($arguments) < $length) {
            throw new \BadFunctionCallException();
        }

        /** @var callable $plugin */
        return call_user_func_array($plugin, $arguments);
    }

    /**
     * Get plugin with specified name
     *
     * @param string $name Name of the plugin
     *
     * @return PluginInterface
     * @throws \DomainException()
     * @throws \RuntimeException()
     */
    public function getPlugin($name)
    {
        $class = 'SugarCRM\\Soap\\Client\\Plugin\\' . ucfirst($name);
        if (!class_exists($class)) {
            throw new \DomainException('Plugin "' . $name . '" not found');
        }

        /** @var $plugin PluginInterface */
        $plugin = new $class();
        if (!method_exists($plugin, '__invoke')) {
            throw new \RuntimeException('Plugin "' . $name . '" is not callable');
        }

        $plugin->setClient($this);

        return $plugin;
    }

    /**
     * Retrieve the minimum number arguments the plugin can be invoked with
     *
     * @param \ReflectionMethod $re Plugin reflection
     *
     * @return int
     */
    protected function getPluginMinArguments(\ReflectionMethod $re)
    {
        $parameters = $re->getParameters();
        for ($i = count($parameters); $i > 0; $i--) {
            /** @var \ReflectionParameter $param */
            $param = $parameters[$i - 1];
            if (!$param->isOptional()) {
                return $i;
            }
        }

        return 0;
    }

    /**
     * Retrieve plugged-in method signature
     *
     * @param string $method Method name
     *
     * @return string
     */
    public function getMethodSignature($method)
    {
        $plugin = $this->getPlugin($method);
        $re = $this->getPluginReflection($plugin);

        $signature = array();
        $parameters = $re->getParameters();
        for ($i = 0, $count = count($parameters); $i < $count; $i++) {
            /** @var \ReflectionParameter $param */
            $param = $parameters[$i];
            $name = $param->getName();
            if ($param->isOptional()) {
                $name = '[' . $name . ']';
            }
            $signature[] = $name;
        }

        return implode(' ', $signature);
    }

    /**
     * Retrieve reflection of plugin's __invoke() method
     *
     * @param PluginInterface $plugin Plugin object
     *
     * @return \ReflectionMethod
     */
    protected function getPluginReflection($plugin)
    {
        return new \ReflectionMethod($plugin, '__invoke');
    }

    /**
     * Triggers error with specified message
     *
     * @param string $message AN error message
     *
     * @return void
     * @throws Exception
     */
    protected function error($message)
    {
        throw new Exception($message);
    }
}
