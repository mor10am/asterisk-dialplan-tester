<?php

namespace AsteriskTest\Command;

use AsteriskTest\BaseCommand;
use AsteriskTest\Test;
use PAMI\Message\Action\GetVarAction;

class assertvariableCommand extends BaseCommand
{
    public function execute(Test $test)
    {
        $this->is_assert = true;

        $parts = explode('=', $this->param);
        $var = $parts[0];
        unset($parts[0]);
        $data = implode('=', $parts);

        $action = new GetVarAction($var, $test->channel);

        $response = $this->pami->send($action);

        if (!$response->isSuccess()) {
            throw new \Exception($response->getMessage());
        }

        $value = $response->getKey('value');

        if ($value == $data) {
            return true;
        }

        $this->message = "Asserted that {$var} = '{$data}', but value was '$value'";

        return false;
    }
}
