<?php

namespace AsteriskTest\Command;

use AsteriskTest\BaseCommand;
use AsteriskTest\Test;

class assertcontextCommand extends BaseCommand
{
    public function execute(Test $test)
    {
        $this->is_assert = true;

        if (isset($test->contexts_seen[$this->param])) {
            return true;
        }

        $this->message = "Asserted that context is '{$this->param}', but current context is '{$this->currentcontext}'";

        return false;
    }
}
