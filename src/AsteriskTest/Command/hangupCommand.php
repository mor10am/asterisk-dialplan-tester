<?php

namespace AsteriskTest\Command;

use AsteriskTest\BaseCommand;
use AsteriskTest\Test;
use PAMI\Message\Action\HangupAction;

class hangupCommand extends BaseCommand
{
    public function execute(Test $test)
    {
        if ($test->channel) {
            $action = new HangupAction($test->channel);
            $response = $this->pami->send($action);

            if (!$response->isSuccess()) {
                throw new \Exception($response->getMessage());
            }

            $test->channel = false;
        }

        return true;
    }
}
