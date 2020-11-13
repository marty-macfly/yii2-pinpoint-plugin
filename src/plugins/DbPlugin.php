<?php

namespace Plugins;
// https://github.com/yiisoft/yii2/blob/master/framework/db/Connection.php

///@hook:yii\db\Connection::open
///@hook:yii\db\Connection::close
///@hook:yii\db\Command::queryInternal
///@hook:yii\db\Command::internalExecute
class DbPlugin extends Candy
{
    function onBefore()
    {
        $db = ($this->who instanceof \yii\db\Connection) ? $this->who : $this->who->db;
        pinpoint_add_clue("dst", $db->dsn);

        switch(substr($db->dsn, 0, strpos($db->dsn, ':')))
        {
            case 'cubrid':
                pinpoint_add_clue("stp", CUBRID);
                break;
            case 'mssql':
                pinpoint_add_clue("stp", MSSQL_SERVER);
                break;
            case 'mysql':
                pinpoint_add_clue("stp", MYSQL);
                break;
            case 'oci':
                pinpoint_add_clue("stp", ORACLE);
                break;
            case 'pgsql':
                pinpoint_add_clue("stp", POSTGRESQL);
                break;
            case 'sqlsrv': 
                pinpoint_add_clue("stp", MSSQL_SERVER);
                break;
        }

        if ($this->who instanceof \yii\db\Command)
        {
            pinpoint_add_clues(PHP_ARGS, sprintf("%s", isset($this->args[0]) ? $this->args[0] : ''));
            pinpoint_add_clues(SQL, $this->who->getRawSql());
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