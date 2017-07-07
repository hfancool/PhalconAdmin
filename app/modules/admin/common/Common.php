<?php
namespace Phalcon\Modules\Admin\Common;

use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;

class Common implements InjectionAwareInterface{

    protected $_di;

    public function setDi(DiInterface $di)
    {
        $this->_di = $di;
    }

    public function getDi()
    {
        return $this->_di;
    }

    public function checkLogin(){
        $session = $this->_di->get('session');
//        $dispatcher = $this->_di->get('dispatcher');
        if(!$session->has('user_id')){
            echo("<script language='javascript'>window.top.location.href='/admin/index/index'</script>");
//            $dispatcher->forward([
//                'controller' => 'index',
//                'action'     => 'index'
//            ]);

            return false;
        }

        return true;
    }

    public function test(){
        return 'test';
    }

}