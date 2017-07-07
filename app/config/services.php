<?php

use Phalcon\Loader;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url as UrlResolver;
//use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Mvc\View;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Http\Response\Cookies;
use Phalcon\Db\Adapter\Pdo\Mysql;

/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    $connection = new $class($params);

    return $connection;
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Registering a router
 */
$di->setShared('router', function () {
    $router = new Router();

    $router->setDefaultModule('frontend');

    return $router;
});

/**
 * The URL component is used to generate all kinds of URLs in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});


/**
 * cookie
 */
$di->setShared(
    "cookies",
    function () {
        $cookies = new Cookies();

        $cookies->useEncryption(false);

        return $cookies;
    }
);

/**
 * Starts the session the first time some component requests the session service
 */
$di->setShared('session', function () {

    /**
     * Include adapter
     */
    require APP_PATH . '/adapter/session/Database.php';

    $connection = new Mysql([
        'host'     => 'localhost',
        'username' => 'root',
        'password' => 'root',
        'dbname'   => 'phalcon'
    ]);

    $session = new Phalcon\Session\Adapter\Database([
        'db'    => $connection,
        'table' => 'session_data'
    ]);

    $session->start();

    return $session;
    /*默认文件*/
//    $session = new SessionAdapter();
//    $session->start();
//
//    return $session;
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    return new Flash([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
});

/**
 * Set the default namespace for dispatcher
 */
$di->setShared('dispatcher', function() {
    $dispatcher = new Dispatcher();
    $dispatcher->setDefaultNamespace('Phalcon\Modules\Frontend\Controllers');
    return $dispatcher;
});


/**
 * Configure the Volt service for rendering .volt templates
 */
$di->setShared('voltShared', function ($view) {
    $config = $this->getConfig();

    $volt = new VoltEngine($view, $this);

    $volt->setOptions([
        'compiledPath' => function($templatePath) use ($config) {

            /*获取模块地址*/
            $pattern = '/modules.*/';
            preg_match($pattern, $templatePath, $matches, PREG_OFFSET_CAPTURE, 3);
            $moduleDir = $matches[0][0];

            $fileName = $config->application->cacheDir . 'volt/' . $moduleDir . '.php';

            $dirName = dirname($fileName);

            if(!is_dir($dirName)){
                mkdir($dirName,0777,true);
            }

            return $fileName;
        }
    ]);

    return $volt;
});
