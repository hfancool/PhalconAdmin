<?php

namespace Phalcon\Modules\Admin\Controllers;
use Phalcon\Modules\Admin\Models\Admin;

class IndexController extends ControllerBase
{
    /**
     * @return mixed
     * 后台登录页面
     */
    public function indexAction()
    {
        if ($this->request->isAjax() == false) {

            if($this->cookies->has('user_name')){
                $this->view->user_name = $this->cookies->get('user_name');
            }

            return $this->view->render('index','index');

        }
        // 获取POST数据
        $username = $this->request->getPost("userName");
        $password = $this->request->getPost("password");
        $rememberMe = $this->request->getPost('rememberMe');

        if($rememberMe){
            $this->cookies->set('user_name',$username, time() + 15 * 86400);
        }

        $adminInfo =  Admin::findFirst(["conditions" => "user_name = '".$username."'"]);

        if(!$adminInfo){
            return json_encode(array(
                'code'    => 400,
                'message' => '管理员不存在'
            ));
        }

        $authInfo = $adminInfo->Authentication($password);

        if(!$authInfo){
            return json_encode(array(
                'code'    => 400,
                'message' => '用户名或密码错误'
            ));
        }
        $this->session->set('user_id',$adminInfo->user_id);
        $this->session->set('user_name',$adminInfo->user_name);

        return json_encode(array(
            'code'    => 200,
        ));

    }

    /**
     * 管理员修改密码
     */
    public function chpwdAction(){

        if(!$this->common->checkLogin()) return;

        if($this->request->isAjax()){
            $password   = $this->request->getPost('password');
            $rePassword = $this->request->getPost('rePassword');

            if($password != $rePassword){
                return json_encode(array(
                    'code'    => 400,
                    'message' => '两次密码输入不一致'
                ));
            }
            $adminInfo = Admin::findFirst($this->session->get('user_id'));
            if(!$adminInfo){
                return json_encode(array(
                    'code'    => 400,
                    'message' => '系统错误'
                ));
            }

            if($adminInfo->changePassword($password) === false) {
                return json_encode(array(
                    'code'    => 400,
                    'message' => '密码更新失败'
                ));
            }

            return json_encode(array(
                'code'    => 200,
            ));
        }
        $this->view->user_name = $this->session->get('user_name');
        return $this->view->render('index','chpwd');

    }

    /**
     * 管理员身份校验
     */
    public function checkAuthAction(){

        if(!$this->common->checkLogin()) return;

        $password = $this->request->getPost('oldPassword');

        $adminInfo = Admin::findFirst($this->session->get('user_id'));

        if(!$adminInfo){
            return json_encode(array(
                'code'    => 400,
                'message' => '系统错误'
            ));
        }

        if(!$adminInfo->Authentication($password)){
            return json_encode(array(
                'code'    => 400,
                'message' => '身份验证失败'
            ));
        }

        return json_encode(array(
            'code'    => 200,
        ));
    }

    /**
     * 获取管理员信息
     */
    public function adminInfoAction(){
        var_dump('asdf');
    }

    /**
     * 管理员登录成功页面
     */
    public function welcomeAction(){
        var_dump('ssss');
    }

    /**
     * 后台退出登录功能
     */
    public function logoutAction(){

        if($this->session->destroy()){
            return json_encode(array(
                'code'    => 200
            ));
        }else{
            return json_encode(array(
                'code'    => 400,
                'message' => '退出登录失败'
            ));
        }

    }


    /**
     * 后台登录成功页面
     */

    public function loginSuccessAction(){

        if(!$this->common->checkLogin()) return;

        /*获取管理员的信息*/
        $adminInfo = Admin::findFirst([
            'conditions' => $this->session->get('user_id'),
            'columns'    => 'create_time,user_name,last_login,last_ip,logins'
        ])->toArray();

        $this->view->adminInfo = $adminInfo;

        return $this->view->render('index','main');

    }

    public function testAction()
    {
//        $this->session->destroy();exit;
        $this->common->checkLogin();
        echo $this->common->test();
//        $this->session->destroy();
        echo md5('adfs#000000&'.strrev('adfs'));
//        $RobotsParts = Robots::findFirst();
//
//        $sss= $RobotsParts->getRelated('eeeeee')->toArray();
//        var_dump($sss);


    }

}

