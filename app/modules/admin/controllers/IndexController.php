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

        return $this->view->render('index','main');

        echo 'hello world';
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

