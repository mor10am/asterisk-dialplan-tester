<?php

namespace AsteriskTest\Command;

use AsteriskTest\BaseCommand;
use AsteriskTest\Test;
use PAMI\Message\Action\PlayDTMFAction;

class dtmfCommand extends BaseCommand
{
    public function execute(Test $test)
    {
        if ($test->channel) {
            $action = new PlayDTMFAction($test->channel, $this->param);

            $response = $this->pami->send($action);

            if (!$response->isSuccess()) {
                throw new \Exception($response->getMessage());
            }
        }

        return true;
    }
}
