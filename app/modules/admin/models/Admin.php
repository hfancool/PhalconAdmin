<?php
namespace Phalcon\Modules\Admin\Models;
use Phalcon\Mvc\Model;

class Admin extends Model
{


    /**
     * 管理员登录信息验证
     */
    public function Authentication($password){

        /*根据用户名获取管理员的信息*/
        $salt      = $this->salt;
        $originPwd = $this->password;

        if(md5($salt.'#'.$password.'&'.strrev($salt)) != $originPwd){
            return false;
        }

        return true;

    }
}