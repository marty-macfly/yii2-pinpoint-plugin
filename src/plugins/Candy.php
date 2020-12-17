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

/**
 * User: eeliu
 * Date: 1/4/19
 * Time: 3:23 PM
 */

namespace Plugins;
require_once "Common/PluginsDefines.php";

abstract class Candy
{
    protected $apId;
    protected $who;
    protected $args;
    protected $ret = null;

    // Partial trace variable
    protected $app_name;
    protected $app_id;
    protected $tid;
    protected $psid;

    public function __construct($apId, $who, &...$args)
    {
        /// todo start_this_aspect_trace
        $this->apId = $apId;
        $this->who =  $who;
        $this->args = &$args;

        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, $apId);
    }

    public function __destruct()
    {
        if (isset($this->tid))
        {
            while (pinpoint_end_trace() > 1);
        }

        pinpoint_end_trace();
    }

    protected function startPartialTrace()
    {
        $this->app_name = Yii2ReqPlugins::instance()->app_name;
        $this->app_id = Yii2ReqPlugins::instance()->app_id;
        $this->tid = Yii2ReqPlugins::instance()->tid;
        $this->psid = Yii2ReqPlugins::instance()->sid;

        // End all previous started tracing
        while (pinpoint_end_trace() > 0);

        pinpoint_start_trace();
        pinpoint_add_clue(PP_SERVER_TYPE, PHP);
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, "PHP Request: ". php_sapi_name());
        pinpoint_add_clue(PP_REQ_SERVER, isset($_SERVER['argv']) ? implode(" ", $_SERVER['argv']) : '');
        pinpoint_add_clue(PP_APP_NAME, $this->app_name);
        pinpoint_add_clue(PP_TRANSCATION_ID, Yii2ReqPlugins::instance()->generateTransactionID());
        pinpoint_add_clue(PP_SPAN_ID, Yii2ReqPlugins::instance()->generateSpanID());
        pinpoint_add_clue(PP_APP_ID, $this->app_id);
    }

    abstract function onBefore();

    abstract function onEnd(&$ret);

    abstract function onException($e);
}
