#!/usr/bin/php
<?php

/**
 * SugarCRM SOAP client command line entry point
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
require __DIR__ . '/../vendor/autoload.php';

if (is_file(__DIR__ . '/../config.php')) {
    $options = include __DIR__ . '/../config.php';
} else {
    $options = include __DIR__ . '/../config.php.dist';
}

$args = $_SERVER['argv'];
$self = array_shift($args);

if (empty($args)) {
    echo 'Usage: ' . $self . ' command [arg1, [arg2, ...]]', PHP_EOL;
    exit(1);
}

$command = array_shift($args);

$method = preg_replace_callback(
    '/-([a-z])/',
    function ($matches) {
        return strtoupper($matches[1]);
    },
    $command
);

try {
    $client = new SugarCRM\Soap\Client($options);

    try {
        $result = $client->call($method, $args);
        if (!is_string($result)) {
            $result = json_encode($result);
        }
        echo $result, PHP_EOL;
    } catch (BadFunctionCallException $e) {
        $signature = $client->getMethodSignature($method);
        file_put_contents(
            'php://stderr',
            'Usage: ' . $self . ' ' . $command . ' ' . $signature
        );
        exit(1);
    }
} catch (Exception $e) {
    file_put_contents('php://stderr', $e->getMessage() . PHP_EOL);
    exit(1);
}
