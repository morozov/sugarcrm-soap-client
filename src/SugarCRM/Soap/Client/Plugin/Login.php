<?php

/**
 * Login plugin
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
 * Login plugin
 *
 * @category  SugarCRM
 * @package   SugarCRM\Soap\Client\Plugin
 * @author    Sergei Morozov <morozov@tut.by>
 * @copyright 2012 Sergei Morozov
 * @license   http://mit-license.org/ MIT Licence
 * @link      http://github.com/morozov/sugarcrm-soap-client
 */
class Login extends AbstractPlugin
{
    /**
     * Plugin entry point
     *
     * @param string $username Username
     * @param string $password Password
     *
     * @return string
     */
    public function __invoke($username, $password)
    {
        return $this->getClient()->getSoapClient()->login(
            array(
                'user_name' => $username,
                'password'  => md5($password),
            )
        );
    }
}
