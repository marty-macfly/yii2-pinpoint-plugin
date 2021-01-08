<?php

namespace Plugins;
// https://github.com/yiisoft/yii2-httpclient/blob/master/src/CurlTransport.php
// https://github.com/yiisoft/yii2-httpclient/blob/master/src/StreamTransport.php

use pinpoint\PluginsCore\Common\Candy;

///@hook:yii\httpclient\CurlTransport::batchSend
///@hook:yii\httpclient\CurlTransport::send
///@hook:yii\httpclient\StreamTransport::send
class HttpClientPlugin extends Candy
{
    function onBefore()
    {
        if (!isset($this->args[0]))
        {
            return;
        }

        $requests = is_array($this->args[0]) ? $this->args[0] : [$this->args[0]];
        foreach ($requests as $request)
        {         
            // Add tracking header
            $request->addHeaders($this->getNextSpanHeaders($this->getHostFromURL($request->getFullUrl())));
        }
    }

    function onEnd(&$ret)
    {
        if (!isset($this->args[0]))
        {
            return;
        }

        $requests = is_array($this->args[0]) ? $this->args[0] : [$this->args[0]];
        foreach ($requests as $id => $request)
        {
            pinpoint_start_trace();
            pinpoint_add_clue(PP_INTERCEPTOR_NAME, strtoupper($request->getMethod()));
            pinpoint_add_clue(PP_SERVER_TYPE, PP_PHP_REMOTE);
            pinpoint_add_clue(PP_DESTINATION, $this->getHostFromURL($request->getFullUrl()));
            pinpoint_add_clues(PP_PHP_ARGS, $request->getFullUrl());
            pinpoint_add_clues(PP_HTTP_URL, $request->getFullUrl());
            pinpoint_add_clues(PP_HTTP_STATUS_CODE, is_array($ret) ? (string)($ret[$id]->getStatusCode()) : $ret->getStatusCode());

            if ($request->hasHeaders())
            {
                $headers = [];
                foreach ($request->getHeaders() as $name => $values)
                {
                    // Skipped header added for tracking
                    if (stripos($name, 'pinpoint') === 0) continue;
                    $name = str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
                    foreach ($values as $value)
                    {
                        $headers[] = "$name: $value";
                    }
                }
                if (!empty($headers))
                {
                    pinpoint_add_clues(PP_HTTP_IO, implode(';', $headers));
                }
            }

            if ($request->hasCookies())
            {
                $cookies = [];
                foreach ($request->getCookies() as $cookie)
                {
                    $cookies[] = $cookie->name . '=' . $cookie->value;
                }
                pinpoint_add_clues(PP_HTTP_COOKIE, implode(';', $parts));
            }

            if (($content = $request->getContent()) !== null)
            {
                pinpoint_add_clues(PP_HTTP_PARAM, $content);
            }
            
            pinpoint_end_trace();
        }
    }

    function onException($e)
    {
        pinpoint_add_clue(PP_ADD_EXCEPTION,$e->getMessage());
    }

    protected function getHostFromURL(string $url)
    {
        $urlAr = parse_url($url);
        $retUrl = '';

        if(isset($urlAr['host']))
        {
            $retUrl .= $urlAr['host'];
        }

        if(isset($urlAr['port']))
        {
            $retUrl .= ":".$urlAr['port'];
        }

        return $retUrl;
    }

    protected function getNextSpanHeaders($host)
    {
        if(pinpoint_tracelimit())
        {
            return [
                'Pinpoint-Sampled' => 's0',
            ];
        }

        $nsid = PerRequestPlugins::instance()->generateSpanID();
        return [
            'Pinpoint-Sampled' => 's1',
            'Pinpoint-Flags' => 0,
            'Pinpoint-Papptype' => PP_PHP,
            'Pinpoint-Pappname' => PerRequestPlugins::instance()->app_name,
            'Pinpoint-Host' => $host,
            'Pinpoint-Traceid' => PerRequestPlugins::instance()->tid,
            'Pinpoint-Pspanid' => PerRequestPlugins::instance()->sid,
            'Pinpoint-Spanid' => $nsid,
        ];
    }
}