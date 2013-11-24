<?php

namespace AsteriskTest;

use AsteriskTest\Test;
use PAMI\Client\IClient;

class Spec
{
    public $tests = array();

    public function __construct(array $spec, IClient $pami)
    {
        $before = array();
        $after = array();

        foreach ($spec as $key => $testcommandset) {

            if (substr($key, 0, 5) == 'test_') {
                $test = new Test($key, $pami);

                if (isset($spec['before'])) {
                    $test->addCommandSet($spec['before']);
                }

                $test->addCommandSet($testcommandset);

                if (isset($spec['after'])) {
                    $test->addCommandSet($spec['after']);
                }

                $this->tests[] = $test;
            }
        }
    }
}
