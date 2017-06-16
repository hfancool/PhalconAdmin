<?php
namespace Phalcon\Modules\Admin\Models;
use Phalcon\Mvc\Model;

class Admin extends Model
{

    protected $salt;
    protected $password;

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

    /**
     * 更新管理员密码
     */
    public function changePassword($newPassword){

        if(empty($newPassword)){
            return false;
        }

        $newSalt      = substr('abcdefghifasidj)*&^*(kjasdo((&&',4,4);
        $newPwd = md5($newSalt.'#'.$newPassword.'&'.strrev($newSalt));

        $this->salt = $newSalt;
        $this->password = $newPwd;

        if($this->save() === false){
            return false;
        }

        return true;
    }

}