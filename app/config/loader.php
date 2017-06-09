<?php

use Phalcon\Loader;

$loader = new Loader();

/**
 * Register Namespaces
 */
$loader->registerNamespaces([
    'Phalcon\Models' => APP_PATH . '/common/models/',
    'Phalcon'        => APP_PATH . '/common/library/',
]);

/**
 * Register module classes
 */
$loader->registerClasses([
    'Phalcon\Modules\Frontend\Module' => APP_PATH . '/modules/frontend/Module.php',
    'Phalcon\Modules\Admin\Module' => APP_PATH . '/modules/admin/Module.php',
]);

$loader->register();
