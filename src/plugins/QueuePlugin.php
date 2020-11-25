<?php

namespace Plugins;

///@hook:yii\queue\cli\Queue::handleMessage
class QueuePlugin extends Candy
{
    public function __construct($apId, $who, &...$args)
    {
        $argv = isset($_SERVER['argv']) ? implode(" ", $_SERVER['argv']) : '';

        // Special behavior for never ending worker
        if (preg_match('/\/listen.+--isolate\s+0/', $argv) === 1)
        {
            $this->startPartialTrace();
            pinpoint_add_clue("uri", sprintf("[id:%s] %s", $args[0], $argv));
        }

        array_unshift($args, $apId, $who);
        call_user_func_array('parent::__construct', $args);
    }

    function onBefore()
    {
        pinpoint_add_clue("stp", PHP_METHOD);
        pinpoint_add_clues(PHP_ARGS, sprintf("[id:%s][ttr:%s][attempt:%s]", $this->args[0], $this->args[2], $this->args[3]));
    }

    function onEnd(&$ret)
    {
        pinpoint_add_clues(PHP_RETURN, print_r($ret, true));
    }

    function onException($e)
    {
        pinpoint_add_clue("EXP", $e->getMessage());
    }
}