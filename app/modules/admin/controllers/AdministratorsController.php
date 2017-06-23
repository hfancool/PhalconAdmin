<?php

namespace Phalcon\Modules\Admin\Controllers;
use Phalcon\Modules\Admin\Models\Admin;

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
        $this->view->totalPage = ceil(Admin::count()/$pageSize);
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
            if ($cur->delete() === false) {
                echo "Sorry, we can't delete the robot right now: \n";

                $messages = $cur->getMessages();

                foreach ($messages as $message) {
                    echo $message, "\n";
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



}