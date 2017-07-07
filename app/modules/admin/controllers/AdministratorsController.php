<?php

namespace Phalcon\Modules\Admin\Controllers;
use Phalcon\Modules\Admin\Models\Admin;
use Phalcon\Modules\Admin\Models\AdminMenu;
use Phalcon\Modules\Admin\Models\AdminPerm;

/**
 * 管理员管理类
 * Class IndexController
 * @package Phalcon\Modules\Admin\Controllers
 */
class AdministratorsController extends ControllerBase
{

    /**
     * 管理员列表
     */
    public function adminListAction(){
        /*是否有查询字段*/
        $pageSize  = $this->config->pageSize;
        $curPage   = $this->request->getQuery('page') ? $this->request->getQuery('page') : 1;

        $parameters = array(
            "columns" => "user_id , status, IF(last_login>0,FROM_UNIXTIME(last_login,'%Y-%m-%d %H:%i:%s'),'') as last_login ,user_name , logins ,last_ip ",
            "limit" => $pageSize,
            "offset" => $pageSize*($curPage - 1),
            "order"  => 'user_id'
        );

        if($this->request->getQuery('user_name')){
            $parameters['conditions'] =  'user_name LIKE "%'.$this->request->getQuery('user_name').'%"';
        }

        $AdminList = Admin::find($parameters) -> toArray();

        $this->view->lists = $AdminList;
        $this->view->totalPage = ceil(Admin::count([
                'conditions' => $parameters['conditions']
            ])/$pageSize);
        $this->view->sequence = $pageSize*($curPage - 1);

        return $this->view->render('administrators','adminList');
    }

    /**
     * 删除管理员
     */
    public function deleteAction($id){

        if(empty($id)){
            return $this->flash->error("非法请求");
        }

        $adminInfo = Admin::find([
            'conditions' => 'user_id in ('.$id.')'
        ]);


        if(empty($adminInfo)){
            return $this->flash->error('系统错误');
        }

        foreach($adminInfo as $cur){

            /*删除依赖关系*/
            $adminPerm = $cur->getRelated('admin_perm');

            if ($cur->delete() === false) {
                echo "Sorry, we can't delete the robot right now: \n";

                $messages = $cur->getMessages();

                foreach ($messages as $message) {
                    echo $message, "\n";
                }
            }
            if(empty($adminPerm)){
                foreach($adminPerm as $perm){
                    $perm->delete();
                }
            }
        }

        return $this->dispatcher->forward([
            'action'=>'adminList'
        ]);

    }

    /**
     * 禁止/启用 管理员登录
     */
    public function handleAction($id){

        if(empty($id)){
            return json_encode(array(
                'code'    => 400,
                'message' => '请求参数错误'
            ));
        }

        $adminInfo = Admin::findFirst($id);

        if($adminInfo->status == 1){
            $adminInfo->status = 0;
        }else{
            $adminInfo->status = 1;
        }

        $res = $adminInfo->save();

        if(!$res){
            return json_encode(array(
                'code'    => 400,
                'message' => '系统错误'
            ));
        }

        return json_encode(array(
            'code'  => 200,
            'status' => $adminInfo->status
        ));
    }

    /**
     * 批量处理管理员禁用、启用
     */
    public function bacthHandleAction(){

        /*获取id*/

        $str_id = $this->request->getPost('id');

        if(empty($str_id)){
            return json_encode(array(
                'code'    => 400,
                'message' => '请求参数错误'
            ));
        }

        $admin = new Admin();

        $phql_str = '('.$str_id.')';

        $res = $admin->batchHandle($phql_str);

        if(!$res){
            return json_encode([
                'code'    => 400,
                'message' => '系统错误'
            ]);
        }

        return json_encode([
            'code' => 200
        ]);
    }

    /**
     * 获取管理员权限
     */
    public function adminPermAction($adminId){


        if(empty($adminId)){
            return json_encode(array(
                'code'    => 400,
                'message' => '请求参数错误'
            ));
        }

        $returnData = array(
            'title' => array(),
            'perm'  => array(),
            'hadPerms' => array()
        );

        $parents = AdminMenu::find([
            'conditions' => 'pid = 0'
        ]);

        foreach($parents as $parent){
            array_push($returnData['title'],array(
                'text'  => $parent->title
            ));
            $children = $parent->getMenuList();
            $text =array();
            foreach($children as $child){
                array_push($text,array(
                    'id'   => $child['menu_id'],
                    'name' => $child['title']
                ));
            }
            array_push($returnData['perm'],array(
                'text' => $text
            ));
        }

        $hadPerms = AdminPerm::find([
            'columns'    => 'perm_id',
            'conditions' => 'admin_id = '.$adminId
        ])->toArray();

        foreach($hadPerms as $val){
            array_push($returnData['hadPerms'],$val['perm_id']);
        }

        return json_encode($returnData);

    }

    /**
     * 修改管理员的权限
     */
    public function changePermAction(){
        /*获取请求数据*/
        $adminId = $this->request->getQuery('admin_id');
        $permId  = $this->request->getQuery('perm_id');
        $flag    = $this->request->getQuery('flag');


        if(empty($adminId) || empty($permId) || empty($flag)){
            return json_encode(array(
                'code'    => 400
            ));
        }

        if($flag == 'true'){
            $adminPerm = new AdminPerm();
            $adminPerm->admin_id = $adminId;
            $adminPerm->perm_id  = $permId;
            $adminPerm->modify_time = time();
            $adminPerm->operator = $this->session->get('user_id');
            $adminPerm->save();
        }else{
            AdminPerm::findFirst([
                'conditions' => 'admin_id = '.intval($adminId).' and perm_id = '.intval($permId)
            ])->delete();
        }
    }

}