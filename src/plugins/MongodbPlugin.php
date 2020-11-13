<?php

namespace Plugins;
// https://github.com/yiisoft/yii2/blob/master/framework/db/Connection.php

///@hook:yii\mongodb\Connection::open
///@hook:yii\mongodb\Connection::close
///@hook:yii\mongodb\Command::execute
class MongodbPlugin extends Candy
{
    function onBefore()
    {
        pinpoint_add_clue("stp", MONGODB);
        $db = ($this->who instanceof \yii\mongodb\Connection) ? $this->who : $this->who->db;
        pinpoint_add_clue("dst", $db->dsn);

        if ($this->who instanceof \yii\mongodb\Command)
        {
            pinpoint_add_clues(PHP_ARGS, sprintf("%s", isset($this->args[0]) ? $this->args[0] : ''));
            pinpoint_add_clues(SQL, print_r($this->document, true));
        }
    }

    function onEnd(&$ret)
    {
//        pinpoint_add_clues(PHP_RETURN, print_r($ret, true));
    }

    function onException($e)
    {
        pinpoint_add_clue("EXP",$e->getMessage());
    }
}