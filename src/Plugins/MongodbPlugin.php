<?php

namespace Plugins;
// https://github.com/yiisoft/yii2/blob/master/framework/db/Connection.php

use pinpoint\PluginsCore\Common\Candy;

///@hook:yii\mongodb\Connection::open
///@hook:yii\mongodb\Connection::close
///@hook:yii\mongodb\Command::execute
class MongodbPlugin extends Candy
{
    function onBefore()
    {
        pinpoint_add_clue(PP_SERVER_TYPE, PP_MONGODB);
        $db = ($this->who instanceof \yii\mongodb\Connection) ? $this->who : $this->who->db;
        pinpoint_add_clue(PP_DESTINATION, $db->dsn);

        if ($this->who instanceof \yii\mongodb\Command)
        {
            pinpoint_add_clues(PP_PHP_ARGS, sprintf("%s", isset($this->args[0]) ? $this->args[0] : ''));
            pinpoint_add_clues(PP_SQL, print_r($this->who->document, true));
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