<?php
///////////////Setting of pinpoint php////////////////////

if (!function_exists('pinpoint_start_trace'))
{
    return;
}

//Cached class
define('AOP_CACHE_DIR', __DIR__ . '/../../../../runtime/pinpoint/cache/');
// where to load pinpoint plugins
define('PLUGINS_DIR', __DIR__ . '/plugins/');

if (defined('YII_ENV') && YII_ENV == "dev")
{
    define('PINPOINT_ENV', 'dev');
}

define('APPLICATION_ID', isset($config['id']) ? $config['id'] : 'yii' );
define('APPLICATION_NAME', isset($config['name']) ? $config['name'] : APPLICATION_ID);

/**
 * unregister Yii class loader
 * wrapper with PinpointYiiClassLoader
 */
function pinpoint_user_class_loader_hook()
{
    $loaders = spl_autoload_functions();
    foreach ($loaders as $loader)
    {
        if(is_array($loader) && is_string($loader[0]) && $loader[0] == 'Yii')
        {
            spl_autoload_unregister($loader);
            spl_autoload_register(['Plugins\PinpointYiiClassLoader', 'autoload'], true, false);
            break;
        }
    }
}

pinpoint_user_class_loader_hook();

require_once __DIR__ . '/../../../naver/pinpoint-php-aop/auto_pinpointed.php';