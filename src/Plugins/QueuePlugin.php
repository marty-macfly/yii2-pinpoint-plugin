<?php

namespace Plugins;

use pinpoint\PluginsCore\Common\Candy;

///@hook:yii\queue\cli\Queue::handleMessage
///@hook:yii\queue\cli\SignalLoop::canContinue
class QueuePlugin extends Candy
{
    public function __construct($apId, $who, &...$args)
    {
        $argv = isset($_SERVER['argv']) ? implode(" ", $_SERVER['argv']) : '';
        // Special behavior for never ending worker
        if (preg_match('/\/listen.+--isolate\s+0/', $argv) === 1)
        {
            if ($apId == "yii\queue\cli\SignalLoop::canContinue")
            {
                PerRequestPlugins::instance()->sendTrace();
            }
            else
            {
                pinpoint_add_clue(PP_REQ_URI, sprintf("[id:%s] %s", $args[0], $argv));
            }
        }

        array_unshift($args, $apId, $who);
        call_user_func_array('parent::__construct', $args);
    }

    function onBefore()
    {
        pinpoint_add_clue(PP_SERVER_TYPE, PP_PHP_METHOD);
        if ($this->apId != "yii\queue\cli\SignalLoop::canContinue")
        {
            pinpoint_add_clues(PP_PHP_ARGS, sprintf("[id:%s][ttr:%s][attempt:%s]", $this->args[0], $this->args[2], $this->args[3]));
        }
    }

    function onEnd(&$ret)
    {
        pinpoint_add_clues(PP_PHP_RETURN, print_r($ret, true));
    }

    function onException($e)
    {
        pinpoint_add_clue(PP_ADD_EXCEPTION, $e->getMessage());
    }
}