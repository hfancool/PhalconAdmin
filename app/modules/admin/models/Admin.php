<?php
namespace Phalcon\Modules\Admin\Models;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;
use Phalcon\Di;

class Admin extends Model
{

    protected $salt;
    protected $password;
    protected $create_time;
    protected $last_login;
    protected $logins;

    /**
     * @return bool|string
     * 创建时间查询器
     */
    public function getCreateTime()
    {
        return empty($this->create_time) ? '' : date('%Y-%m-%d %H:%i:%s',$this->create_time);
    }

    /**
     * 上次登录时间查询器
     */

    public function getLastLogin(){
        return empty($this->last_login) ? '' : date('%Y-%m-%d %H:%i:%s',$this->last_login);
    }

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

        $dispatcher = Di::getDefault()->getDispatcher();

        if($dispatcher->getActionName() == 'index'){
            $this->last_login = time();
            $this->logins     = $this->logins + 1;
            $this->last_ip = Di::getDefault()->getRequest()->getClientAddress();
            $this->save();
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

    /**
     * 批量编辑管理员 禁用、启用
     */

    public function batchHandle($ids){
        if(empty($ids)){
            return false;
        }

        $phql = 'update '.__NAMESPACE__.'\Admin set status = case status when 1 then 0 when 0 then 1 end '.
                ' where user_id in '.$ids;
        $query = new Query($phql,$this->getDI());

         return $query ->execute();

    }
    /**
     * 获取管理员列表
     */
    public function getList(){

        return __NAMESPACE__ ;
    }

}