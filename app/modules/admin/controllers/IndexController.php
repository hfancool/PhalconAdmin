<?php

namespace Phalcon\Modules\Admin\Controllers;
use Phalcon\Modules\Admin\Models\Admin;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $res = Admin::find()->toArray();

        echo "<pre>";
        print_r($res);
        echo "/<pre>";
    }

    public function testAction(){
        var_dump('asdfasdfssssss');
    }

}

