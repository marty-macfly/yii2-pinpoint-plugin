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
///@hook:yii\base\Controller::afterAction
///@hook:yii\base\Controller::beforeAction
///@hook:yii\base\Controller::runAction
class ControllerPlugin extends Candy
{
    public function onBefore()
    {
        pinpoint_add_clue(PP_SERVER_TYPE, PP_PHP_METHOD);
        if ($this->apId != 'yii\base\Controller::runAction'
            && isset($this->args[0])
            && $this->args[0] instanceof \yii\base\Action)
        {
            pinpoint_add_clues(PP_PHP_ARGS, $this->args[0]->getUniqueId());
        }
        else
        {
            pinpoint_add_clues(PP_PHP_ARGS, $this->who->getRoute());
        }
    }

    public function onEnd(&$ret)
    {
        if ($this->apId == 'yii\base\Controller::beforeAction'
            || ($this->apId == 'yii\base\Controller::runAction'
                && $this->who->request instanceof \yii\console\Request))
        {
            pinpoint_add_clues(PP_PHP_RETURN, var_export($ret, true));
        }
    }

    public function onException($e)
    {
        pinpoint_add_clue(PP_ADD_EXCEPTION, $e->getMessage());
    }
}
