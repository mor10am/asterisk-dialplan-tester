<?php

namespace AsteriskTest\Command;

use AsteriskTest\BaseCommand;
use AsteriskTest\Test;

class logCommand extends BaseCommand
{
    public function execute(Test $test)
    {
        return true;
    }
}
