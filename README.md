# Pinpoint Yii2 framework plugin

[Pinpoint](https://pinpoint-apm.github.io/pinpoint/index.html) is an APM (Application Performance Management) tool for large-scale distributed systems written in Java / PHP. Inspired by Dapper, Pinpoint provides a solution to help analyze the overall structure of the system and how components within them are interconnected by tracing transactions across distributed applications.

[Pinpoint](https://pinpoint-apm.github.io/pinpoint/index.html) support Java, PHP and Python application introspection.

For PHP you need to install the [pinpoint-c-agent](https://github.com/pinpoint-apm/pinpoint-c-agent) and add plugin to instrument your application for [pinpoint-php-aop](https://github.com/pinpoint-apm/pinpoint-php-aop) to work.

This extension is providing an easy way to integrate [Pinpoint](https://pinpoint-apm.github.io/pinpoint/index.html) with your PHP application based on the [Yii2](https://www.yiiframework.com) framework.

To use that extension you need to do the following:

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist macfly/yii2-pinpoint-plugin
```

or add

```json
"macfly/yii2-pinpoint-plugin": "dev-main"
```

to the `require` section of your composer.json.

## Configuration

Update `web/index.php` in your Yii2 application to add `require_once __DIR__. '/../vendor/macfly/yii2-pinpoint-plugin/src/yii.php';`.

```php
<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

require_once __DIR__. '/../vendor/macfly/yii2-pinpoint-plugin/src/yii.php';

$app = new yii\web\Application($config);
$app->run();
```

Do the same for `yii` to add `require_once __DIR__. '/vendor/macfly/yii2-pinpoint-plugin/src/yii.php';`

```php
#!/usr/bin/env php
<?php
/**
 * Yii console bootstrap file.
 *
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/config/console.php';

require_once __DIR__. '/vendor/macfly/yii2-pinpoint-plugin/src/yii.php';

$application = new yii\console\Application($config);
$exitCode = $application->run();
exit($exitCode);
```