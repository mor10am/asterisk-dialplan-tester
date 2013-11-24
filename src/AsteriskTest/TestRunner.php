<?php

namespace AsteriskTest;

use AsteriskTest\Spec;
use AsteriskTest\Test;
use PAMI\Client\IClient;

class TestRunner
{
    public $pami;
    public $spec;
    public $messages = array();

    public function __construct(Spec $spec, IClient $pami)
    {
        $this->pami = $pami;
        $this->spec = $spec;
    }

    public function run()
    {
        foreach ($this->spec->tests as $test)
        {
            $status = Test::NOT_RUN;

            try {
                $status = $test->run();
            } catch (\Exception $e) {
                $this->messages[] = $test->name . ": ".$e->getMessage();
            }

            if ($status != Test::PASSED) {
                $this->messages = array_merge($this->messages, $test->messages);
            }
        }

        if (count($this->messages)) {
            echo "\n\n";
            echo implode("\n", $this->messages);
        }
    }

    public function getTestCount()
    {
        return count($this->spec->tests);
    }

    public function getFailedAsserts()
    {
        $count = 0;

        foreach ($this->spec->tests as $test) {
            $count = $count + $test->assert_failed;
        }

        return $count;
    }

    public function getSuccessAsserts()
    {
        $count = 0;

        foreach ($this->spec->tests as $test) {
            $count = $count + $test->assert_success;
        }

        return $count;
    }
}
