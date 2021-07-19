<?php

namespace Plugins;
// https://github.com/yiisoft/yii2/blob/master/framework/caching/Cache.php

use pinpoint\PluginsCore\Common\Candy;

///@hook:yii\caching\Cache::addValue
///@hook:yii\caching\Cache::deleteValue
///@hook:yii\caching\Cache::flushValues
///@hook:yii\caching\Cache::getValue
///@hook:yii\caching\Cache::getValues
///@hook:yii\caching\Cache::setValue
///@hook:yii\caching\Cache::setValues
///@hook:yii\caching\MemCache::init
class CachePlugin extends Candy
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