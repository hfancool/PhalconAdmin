<?php
/**
 * Author: hfan
 * Contact:804667084@qq.com
 * Date: 2017/7/11
 * Time: 9:47
 */

/*链接redis*/

try{
    $redis = new Redis();

    $res = $redis->connect('127.0.0.1');

    if(!$res){
        throw new Exception('connect failed',400);
    }

    for($i=0;$i<100;$i++){
        $redis->rPush('mq','user_'+$i.'#'.rand(1,99999));
    }

    $redis->close();

}catch (Exception $e){
    die($e->getMessage());
}





