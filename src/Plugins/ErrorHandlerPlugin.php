<?php
#-------------------------------------------------------------------------------
# Copyright 2019 NAVER Corp
# 
# Licensed under the Apache License, Version 2.0 (the "License"); you may not
# use this file except in compliance with the License.  You may obtain a copy
# of the License at
# 
#   http://www.apache.org/licenses/LICENSE-2.0
# 
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
# WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the
# License for the specific language governing permissions and limitations under
# the License.
#-------------------------------------------------------------------------------

namespace Plugins;

use pinpoint\PluginsCore\Common\Candy;

///@hook:yii\base\ErrorHandler::handleException
class ErrorHandlerPlugin extends Candy
{
    public function onBefore()
    {
        if ($this->who instanceof \yii\console\ErrorHandler
            && !$this->who->silentExitOnException
            && function_exists('pinpoint_mark_as_error'))
        {
            if(isset($this->args[0])
                && $this->args[0] instanceof \Exception)
            {
                pinpoint_mark_as_error(nl2br($this->args[0]->__toString()), $this->args[0]->getFile(), $this->args[0]->getLine());
            }
            else
            {
                pinpoint_mark_as_error('CLI Exception', __FILE__);
            }
        }
    }

    public function onEnd(&$ret)
    {
        //pinpoint_add_clues(PP_PHP_RETURN, var_export($ret, true));
    }

    public function onException($e)
    {
        pinpoint_add_clue(PP_ADD_EXCEPTION, $e->getMessage());
    }
}
