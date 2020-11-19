<?php

namespace Plugins;

///@hook:yii\queue\cli\Queue::handleMessage
class QueuePlugin extends Candy
{
    protected $app_name;
    protected $app_id;
    protected $tid;
    protected $psid;

    public function __construct($apId, $who, &...$args)
    {
        $argv = isset($_SERVER['argv']) ? implode(" ", $_SERVER['argv']) : '';

        // Special behavior for never ending worker
        if (preg_match('/\/listen.+--isolate\s+0/', $argv) === 1)
        {
            $this->app_name = PerRequestPlugins::instance()->app_name;
            $this->app_id = PerRequestPlugins::instance()->app_id;
            $this->tid = PerRequestPlugins::instance()->tid;
            $this->psid = PerRequestPlugins::instance()->sid;

            // End all previous started tracing
            while (pinpoint_end_trace() > 0);

            pinpoint_start_trace();
            pinpoint_add_clue("stp", PHP);
            pinpoint_add_clue("name", "PHP Request: ". php_sapi_name());
            pinpoint_add_clue("server", $argv);
            pinpoint_add_clue("uri", sprintf("[id:%s] %s", $args[0], $argv));
            pinpoint_add_clue("appname", $this->app_name);
            pinpoint_add_clue("tid", PerRequestPlugins::instance()->generateTransactionID());
            pinpoint_add_clue("sid", PerRequestPlugins::instance()->generateSpanID());
            pinpoint_add_clue('appid', $this->app_id);
        }

        array_unshift($args, $apId, $who);
        call_user_func_array('parent::__construct', $args);
    }

    public function __destruct()
    {
        if (isset($this->tid))
        {
            while (pinpoint_end_trace() > 1);
        }
        parent::__destruct();
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