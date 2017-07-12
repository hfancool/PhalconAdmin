<?php
/**
 * Author: hfan
 * Contact:804667084@qq.com
 * Date: 2017/7/11
 * Time: 9:47
 */
include_once './Stmts.php';


try{
    $redis = new Redis();

    $res = $redis->connect('127.0.0.1');

    if(!$res){
        throw new Exception('connect failed',400);
    }

    $res = $redis->lPop('mq');

    if(!$res){
        throw new Exception('no data',400);
    }

    $res = explode('#',$res);

    $con = new StmtS();

    $result = $con->exec('insert into test VALUES ('.$res[0].','.$res[1].')');

    if(!$result){
        throw new Exception('sql error',400);
    }

    $redis->close();

}catch (Exception $e){
    die($e->getMessage());
}
