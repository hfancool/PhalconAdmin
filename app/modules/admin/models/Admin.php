<?php
namespace Phalcon\Modules\Admin\Models;
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;
use Phalcon\Di;

class Admin extends Model
{
    const nsTable = 'Phalcon\Modules\Admin\Models\Admin';
    protected $salt;
    protected $create_time;
    protected $last_login;
    protected $logins;

    public function initialize()
    {
        /*定义数据库模型关系*/
        $this->hasMany(
            'user_id',
            "Phalcon\\Modules\\Admin\\Models\\AdminPerm",
            'admin_id',
            ['alias'=>'admin_perm']
        );
    }

    public function beforeCreate()
    {
        // Set the creation date
        $newSalt      = substr('abcdefghifasidj)*&^*(kjasdo((&&',rand(0,31),4);
        $newPwd = md5($newSalt.'#'.$this->password.'&'.strrev($newSalt));
        $this->create_time  = time();
        $this->password     = $newPwd;
        $this->salt         = $newSalt;
    }

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

        $newSalt      = substr('abcdefghifasidj)*&^*(kjasdo((&&',rand(0,31),4);
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

        $phql = 'update '.self::nsTable.' set status = case status when 1 then 0 when 0 then 1 end '.
                ' where user_id in '.$ids;
        $query = new Query($phql,$this->getDI());

         return $query ->execute();

    }

}