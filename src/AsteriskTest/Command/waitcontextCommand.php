<?php

namespace AsteriskTest\Command;

use AsteriskTest\BaseCommand;
use AsteriskTest\Test;

class waitcontextCommand extends BaseCommand
{
    public function execute(Test $test)
    {
        if (is_array($this->param)) {
            $context = $this->param['context'];
        } else {
            $context = $this->param;
        }

        if (isset($test->contexts_seen[$context])) {
            return true;
        }

        return false;
    }
}
