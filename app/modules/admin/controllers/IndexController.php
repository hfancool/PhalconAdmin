<?php

namespace Phalcon\Modules\Admin\Controllers;
use Phalcon\Modules\Admin\Models\Admin;
use Phalcon\Modules\Admin\Models\Robots;
use Phalcon\Modules\Admin\Models\RobotsParts;

class IndexController extends ControllerBase
{

    public function indexAction()
    {
        $res = Admin::find()->toArray();

        print_r($res);
    }

    public function testAction(){
        $RobotsParts = Robots::findFirst() ;

        $sss= $RobotsParts->getRelated('eeeeee')->toArray();
        var_dump($sss);

    }

}

