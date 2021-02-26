<?php

namespace Plugins;
// https://github.com/yiisoft/yii2-redis/blob/master/src/Connection.php

use pinpoint\PluginsCore\Common\Candy;

///@hook:yii\caching\MemCache::init
///@hook:yii\caching\MemCache::getValue
///@hook:yii\caching\MemCache::getValues
///@hook:yii\caching\MemCache::setValue
///@hook:yii\caching\MemCache::setValues
///@hook:yii\caching\MemCache::addValue
///@hook:yii\caching\MemCache::deleteValue
///@hook:yii\caching\MemCache::flushValues
///@hook:yii\caching\MemCache::normalizeDuration
class MemcachedPlugin extends Candy
{
    function onBefore()
    {
        if ($this->apId == 'yii\caching\MemCache::init') {
            $host = $this->who->servers[0]->host;
            $port = $this->who->servers[0]->port;
            pinpoint_add_clue(PP_SERVER_TYPE,PP_MEMCACHED);
            pinpoint_add_clue(PP_DESTINATION,"$host:$port");
        }
        else
        {
            pinpoint_add_clue(PP_SERVER_TYPE, PP_PHP_METHOD);
            pinpoint_add_clues(PP_PHP_ARGS, sprintf("%s %s", isset($this->args[0]) ? $this->args[0] : '', isset($this->args[1][0]) ? $this->args[1][0] : ''));

        }

    }

    function onEnd(&$ret)
    {
//       pinpoint_add_clues(PP_PHP_RETURN, print_r($ret, true));
    }

    function onException($e)
    {
        pinpoint_add_clue(PP_ADD_EXCEPTION,$e->getMessage());
    }

}