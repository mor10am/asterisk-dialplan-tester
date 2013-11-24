<?php

namespace AsteriskTest;

use PAMI\Client\IClient;
use PAMI\Message\Event\EventMessage;
use PAMI\Message\Event\NewextenEvent;
use PAMI\Message\Event\NewchannelEvent;
use PAMI\Message\Event\UnknownEvent;
use PAMI\Message\Action\ActionMessage;
use PAMI\Message\Action\HangupAction;
use PAMI\Message\Event\HangupEvent;

class Test
{
    const NOT_RUN = "?";
    const SKIPPED = "S";
    const PASSED = ".";
    const FAILED = "F";

    public $name;
    public $pami;
    public $commandlist;

    public $asserts = array();
    public $assert_success = 0;
    public $assert_failed = 0;
    public $status = self::NOT_RUN;

    public $currentcontext = false;
    public $currentexten = false;
    public $currentpri = false;
    public $currentsound = false;

    public $channel = false;
    public $contexts_seen = array();
    public $cep_seen = array();
    public $sound_seen = array();

    public $messages = array();

    public function __construct($name, IClient $pami)
    {
        $this->pami = $pami;
        $this->name = $name;
    }

    public function addCommandSet(array $set)
    {
        foreach ($set as $idx => $cmdarray) {

            $cmd = key($cmdarray);
            $value = current($cmdarray);

            $cmd = "AsteriskTest\\Command\\".str_replace("_", "", strtolower($cmd))."Command";

            $cmdobj = new $cmd($value, $this->pami);

            $this->commandlist[] = $cmdobj;
        }
    }

    public function eventHandler(EventMessage $message)
    {
        if ($message instanceof NewextenEvent) {
            $this->currentcontext = trim($message->getContext());
            $this->currentexten = trim($message->getExtension());
            $this->currentpri = trim($message->getPriority());

            $cep = $this->currentcontext.",".$this->currentexten.",".$this->currentpri;

            $now = microtime(true);

            $this->cep_seen[$cep] = $now;
            $this->contexts_seen[$this->currentcontext] = $now;

            switch (strtolower($message->getKey('Application'))) {
                case 'background':
                case 'playback':
                    $filename = $message->getKey('AppData');
                    $this->sound_seen[$filename] = $now;
                    $this->currentsound = $filename;
                    break;
                default:
                    break;
            }

        } elseif ($message instanceof UnknownEvent) {
            if (!$this->channel and $message->getKey('event') == 'LocalBridge') {
                $this->channel = $message->getKey('Channel2');
            }
        } elseif ($message instanceof HangupEvent) {
            $this->channel = false;
        }
    }

    public function run()
    {
        $this->currentcontext = false;
        $this->currentexten = false;
        $this->currentpri = false;
        $this->contexts_seen = array();

        $this->pami->registerEventListener(array($this, 'eventHandler'));

        foreach ($this->commandlist as $idx => $testcommand) {
            $step = $idx + 1;

            try {
                $cmdtime = microtime(true);

                //echo (string) $testcommand . "\n";

                do {
                    if (!$testcommand->is_assert) {
                        $this->pami->process();
                    }

                    $result = $testcommand->execute($this);

                    $delay = (microtime(true) - $cmdtime);

                    if ($delay >= $testcommand->timeout) {
                        throw new \Exception("Command timeout");
                    }

                } while ($result === false and !$testcommand->is_assert);

                if ($testcommand->is_assert) {
                    if ($result) {
                        $this->asserts[] = self::PASSED;
                        echo self::PASSED;
                    } else {
                        $this->asserts[] = self::FAILED;

                        if ($testcommand->message) {
                            $this->messages[] = $testcommand->message . " in test '".$this->name."'";
                        }

                        echo self::FAILED;
                    }
                }

            } catch (\Exception $e) {
                $this->status = self::FAILED;

                if ($this->channel) {
                    $response = $this->pami->send(new HangupAction($this->channel));

                    if ($response->isSuccess()) {
                        $this->channel = false;
                    }
                }

                throw new \Exception("(Step $step => " . get_class($testcommand) . ") ". $e->getMessage());
            }
        }

        $this->status = self::PASSED;

        if (count($this->asserts)) {
            foreach ($this->asserts as $assert) {
                if ($assert != self::PASSED) {
                    $this->assert_failed++;
                } else {
                    $this->assert_success++;
                }
            }
        }

        if ($this->channel) {
            $response = $this->pami->send(new HangupAction($this->channel));

            if ($response->isSuccess()) {
                $this->channel = false;
            }
        }

        if ($this->assert_failed) {
            $this->status = self::FAILED;
        } else {
            $this->status = self::PASSED;
        }

        return $this->status;
    }
}
