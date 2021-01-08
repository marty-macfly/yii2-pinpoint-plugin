<?php
///////////////Setting of pinpoint php////////////////////

if (!function_exists('pinpoint_start_trace'))
{
    return;
}

// Enable memory usage collection
define('PP_REPORT_MEMORY_USAGE', '1');

// Class cache is enabled
define('PINPOINT_USE_CACHE', (defined('YII_ENV') && YII_ENV == "dev") ? 'NO' : 'YES');
// Where cached class will be stored
define('AOP_CACHE_DIR', __DIR__ . '/../../../../runtime/pinpoint/cache/');

// Plugins directory
define('PLUGINS_DIR', __DIR__ . '/Plugins/');

// Application id and name to appear in pinpoint UI
define('APPLICATION_ID', isset($config['id']) ? $config['id'] : 'yii' );
define('APPLICATION_NAME', isset($config['name']) ? $config['name'] : APPLICATION_ID);

define('PP_REQ_PLUGINS', '\Plugins\PerRequestPlugins');

require_once __DIR__ . '/../../../pinpoint-apm/pinpoint-php-aop/auto_pinpointed.php';