<?php

require __DIR__ . '/../vendor/autoload.php';

if (is_file(__DIR__ . '/../config.php')) {
    $options = include __DIR__ . '/../config.php';
} else {
    $options = include __DIR__ . '/../config.php.dist';
}

$client = new SugarCRM\Soap\Client($options);
$soapClient = $client->getSoapClient();
$response = $soapClient->get_entries(
    $client->getSessionId(),
    'Reports',
    array('efa4d476-fe59-684a-e773-5135f7566f95')
);

var_dump($response);
