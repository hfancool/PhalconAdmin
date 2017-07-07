<?php
namespace Phalcon\Modules\Admin\Controllers;

use Phalcon\Mvc\Controller;

class ControllerBase extends Controller
{
    public function initialize()
    {
        if(!$this->session->has('user_id') && $this->dispatcher->getControllerName() != 'index' && $this->dispatcher->getActionName() != 'index'){
            echo("<script language='javascript'>window.top.location.href='/admin/index/index'</script>");exit;
        }
    }
}
