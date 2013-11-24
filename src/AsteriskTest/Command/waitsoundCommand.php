<?php

namespace AsteriskTest\Command;

use AsteriskTest\BaseCommand;
use AsteriskTest\Test;

class waitsoundCommand extends BaseCommand
{
    public function execute(Test $test)
    {
        if ($test->currentsound == trim($this->param)) {
            return true;
        }

        return false;
    }
}
