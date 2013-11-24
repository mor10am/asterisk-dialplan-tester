#!/usr/bin/env php
<?php

require_once __DIR__."/vendor/autoload.php";

use AsteriskTest\TestRunner;
use AsteriskTest\Spec;
use PAMI\Client\Impl\ClientImpl;
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parse(__DIR__."/asterisktest.yml");

$options = array(
    'log4php.properties' => array(
        'threshold' => 'ALL',
        'rootLogger' => array(
            'level' => 'ERROR',
            'appenders' => array('default'),
        ),
        'appenders' => array(
            'default' => array(
                'class' => 'LoggerAppenderEcho'
            ),
        )),
    );

$options = array_merge($options, $config['options']);

echo "Asterisk Dialplan Tester by Morten Amundsen.\n\n";

$starttime = microtime(true);

try {
    $pami = new ClientImpl($options);

    $spec = new Spec($config, $pami);

    $tester = new TestRunner($spec, $pami);

    $pami->open();

    $tester->run();

} catch (Exception $e) {
    die($e->getMessage()."\n");
}

$timeused = round((microtime(true)-$starttime), 3);

$failed = $tester->getFailedAsserts();
$success = $tester->getSuccessAsserts();
$total = $failed+$success;
$tests = $tester->getTestCount();

echo "\n\n{$tests} test(s), {$success}/{$total} assert(s) succeeded, run in {$timeused} seconds.\n";
