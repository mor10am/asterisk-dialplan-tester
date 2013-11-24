<?php

namespace AsteriskTest\Command;

use AsteriskTest\BaseCommand;
use AsteriskTest\Test;

use PAMI\Message\Action\OriginateAction;

class callCommand extends BaseCommand
{
    public function execute(Test $test)
    {
        $channel = "Local/".$this->param['extension']."@".$this->param['context'];

        $action = new OriginateAction($channel);

        if (isset($this->param['callerid'])) {
            $action->setCallerid($this->param['callerid']);
        }

/*
        if (isset($this->param['context'])) {
            $action->setContext($this->param['context']);
        }

        if (isset($this->param['extension'])) {
            $action->setExtension($this->param['extension']);
        }

        if (isset($this->param['priority'])) {
            $action->setPriority($this->param['priority']);
        }
*/

        $action->setApplication('Wait');
        $action->setData(300);

        $action->setAsync(true);

        $response = $this->pami->send($action);

        if (!$response->isSuccess()) {
            throw new \Exception($response->getMessage());
        }

        return true;
    }
}
