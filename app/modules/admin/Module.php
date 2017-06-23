<?php
namespace Phalcon\Modules\Admin;

use Phalcon\DiInterface;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\ModuleDefinitionInterface;
use Phalcon\Modules\Admin\Common\Common;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers an autoloader related to the module
     *
     * @param DiInterface $di
     */
    public function registerAutoloaders(DiInterface $di = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces([
            'Phalcon\Modules\Admin\Controllers' => __DIR__ . '/controllers/',
            'Phalcon\Modules\Admin\Models' => __DIR__ . '/models/',
            'Phalcon\Modules\Admin\Common' => __DIR__ . '/common/',
        ]);

        $loader->register();
    }

    /**
     * Registers services related to the module
     *
     * @param DiInterface $di
     */
    public function registerServices(DiInterface $di)
    {

        /**
         * Setting up the view component
         */
        $di->set('view', function () use ($di) {
            $view = new View();
            $view->setDI($this);
            $view->setViewsDir(__DIR__ . '/views/');
            $view->title = '后台管理';
            $view->action     = $di->get('dispatcher')->getActionName();
            $view->controller = $di->get('dispatcher')->getControllerName();

            $view->registerEngines([
                '.volt'  => 'voltShared',
                '.phtml' => PhpEngine::class
            ]);

            return $view;
        });

        /**
         * 注册common组件
         */
        $di->set("common", function() use ($di){

            $common = new Common();

            $common->setDi($di);

            return $common;
        });
    }

}
