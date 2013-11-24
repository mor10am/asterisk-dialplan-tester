<?php

namespace AsteriskTest\Command;

use AsteriskTest\BaseCommand;
use AsteriskTest\Test;
use PAMI\Message\Action\SetVarAction;

class setCommand extends BaseCommand
{
    public function execute(Test $test)
    {
        $parts = explode('=', $this->param);
        $var = $parts[0];
        unset($parts[0]);
        $data = implode('=', $parts);

        $action = new SetVarAction($var, $data, $test->channel);
        $response = $this->pami->send($action);

        if (!$response->isSuccess()) {
            throw new \Exception($response->getMessage());
        }

        return true;
    }
}
