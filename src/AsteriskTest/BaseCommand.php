<?php

namespace AsteriskTest;

use PAMI\Client\IClient;

abstract class BaseCommand
{
    public $param;
    public $pami;
    public $is_assert = false;
    public $timeout = 30;
    public $message;

    public function __construct($param, IClient $pami)
    {
        $this->pami = $pami;
        $this->param = $param;

        if (is_array($param)) {
            if (isset($param['timeout'])) {
                $this->timeout = $param['timeout'];
            }
        }
    }

    public function execute(Test $test)
    {
        return true;
    }

    public function __toString()
    {
        $string = get_class($this);
        $string .= "(";

        if (is_array($this->param)) {
            $string .= implode(",", $this->param);
        } else {
            $string .= $this->param;
        }

        $string .= ")";

        return $string;
    }
}
