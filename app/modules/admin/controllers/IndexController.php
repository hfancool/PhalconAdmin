<?php

namespace Phalcon\Modules\Admin\Controllers;
use Phalcon\Modules\Admin\Models\Admin;
use Phalcon\Modules\Admin\Models\AdminMenu;

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
            }else{
                $this->view->user_name = '';
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

        /*判断管理员是否被禁用*/
        if($adminInfo->status == 0){
            return json_encode(array(
                'code'    => 400,
                'message' => '账号已停用'
            ));
        }

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
     * 管理员登录成功页面
     */
    public function welcomeAction(){
        return '<h1 style="text-align: center;margin-top: 20%">Welcome Admin</h1>';
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

    /**
     * 根据管理员权限分配管理员可操作的目录
     */
    public function navAction(){

        if(!$this->common->checkLogin()) return;

        $adminInfo = Admin::findFirst($this->session->get('user_id'));

        if($adminInfo->user_name == "admin"){
            $adminPerm = AdminMenu::find([
                'columns'=> 'menu_id as perm_id',
                'conditions' => 'pid > 0'
            ])->toArray();
        }else{
            $adminPerm = $adminInfo->getRelated('admin_perm')->toArray();
        }

        $children = array();
        foreach($adminPerm as $key => $value){
            $menu_item = AdminMenu::findFirst([
                'conditions' => 'menu_id = '.intval($value['perm_id'])
            ])->toArray();

            if(!array_key_exists($menu_item['pid'],$children)){
                $children[$menu_item['pid']] = array(
                    array(
                        'title'  => $menu_item['title'],
                        'icon'   => $menu_item['icon'],
                        'href'   => $menu_item['href']
                    )
                );
            }else{
                array_push($children[$menu_item['pid']],array(
                    'title'  => $menu_item['title'],
                    'icon'   => $menu_item['icon'],
                    'href'   => $menu_item['href']
                ));
            }
        }

        /*封装nav 数据*/
        $returnData = array();

        foreach($children as $key => $value){
            /*获取父节点*/
            $parentInfo = AdminMenu::findFirst($key)->toArray();

            array_push($returnData,array(
                'title'  => $parentInfo['title'],
                'icon'   => $parentInfo['icon'],
                'spread' => $parentInfo['spread'] ==  1 ?  true :false,
                'children' => $children[$key]
            ));

        }

        return json_encode($returnData);
    }

    public function testAction()
    {

        $res = AdminMenu::find([
            'conditions' => 'pid = 0 '
        ]);

        foreach($res as $p){
            var_dump($p->getMenuList());
        }

        exit;
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

