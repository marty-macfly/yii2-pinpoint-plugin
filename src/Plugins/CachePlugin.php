<?php

namespace Plugins;
// https://github.com/yiisoft/yii2/blob/master/framework/caching/Cache.php
// https://github.com/yiisoft/yii2/blob/master/framework/caching/CacheInterface.php

use pinpoint\PluginsCore\Common\Candy;

///@hook:yii\caching\Cache::add
///@hook:yii\caching\Cache::delete
///@hook:yii\caching\Cache::exists
///@hook:yii\caching\Cache::flush
///@hook:yii\caching\Cache::get
///@hook:yii\caching\Cache::getOrSet
///@hook:yii\caching\Cache::multiAdd
///@hook:yii\caching\Cache::multiGet
///@hook:yii\caching\Cache::multiSet
///@hook:yii\caching\Cache::set
///@hook:yii\caching\MemCache::init
class CachePlugin extends Candy
{
    function onBefore()
    {
        if ($this->apId == 'yii\caching\MemCache::init')
        {
            $host = $this->who->servers[0]->host;
            $port = $this->who->servers[0]->port;
            pinpoint_add_clue(PP_SERVER_TYPE,PP_MEMCACHED);
            pinpoint_add_clue(PP_DESTINATION,"$host:$port");
        }
        else
        {
            pinpoint_add_clue(PP_SERVER_TYPE, PP_PHP_METHOD);
            pinpoint_add_clues(PP_PHP_ARGS, isset($this->args[0]) ? \yii\helpers\VarDumper::dumpAsString($this->args[0], 1) : '');
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