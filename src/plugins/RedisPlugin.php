<?php

namespace Plugins;
// https://github.com/yiisoft/yii2-redis/blob/master/src/Connection.php

///@hook:yii\redis\Connection::open
///@hook:yii\redis\Connection::sendCommandInternal
///@hook:yii\redis\Connection::executeCommand
///@hook:yii\redis\Connection::close
class RedisPlugin extends Candy
{
    function onBefore()
    {
        if ($this->apId == 'yii\redis\Connection::open')
        {
            if ($this->who->socket !== false)
            {
                return;
            }
            pinpoint_add_clue(PP_SERVER_TYPE, REDIS);
            pinpoint_add_clue(PP_DESTINATION, $this->who->connectionString . ', database=' . $this->who->database);
        }
        else
        {
            pinpoint_add_clue(PP_SERVER_TYPE, PHP_METHOD);
            if ($this->apId !== 'yii\redis\Connection::sendCommandInternal')
            {
                pinpoint_add_clues(PP_PHP_ARGS, sprintf("%s %s", isset($this->args[0]) ? $this->args[0] : '', isset($this->args[1][0]) ? $this->args[1][0] : ''));
            }
        }
    }

    function onEnd(&$ret)
    {
//        pinpoint_add_clues(PP_PHP_RETURN, print_r($ret, true));
    }

    function onException($e)
    {
        pinpoint_add_clue(PP_ADD_EXCEPTION,$e->getMessage());
    }
}