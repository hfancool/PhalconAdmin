<?php
// +----------------------------------------------------------------------
// | Startzc
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2020 http://www.startzc.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: zhuliang <1076317244@qq.com>
// +----------------------------------------------------------------------

/**
 * 数据库类
 * @package  library
 * @author   hfan <804667084@qq.com>
 * @version 1.0
 */
class StmtS{
	public $dbhost,$dbuser,$dbpwd,$dbname,$charset,$last_sql;
	public function __construct($dbhost='127.0.0.1:3306',$dbuser='root',$dbpwd='000000',$dbname='redismq',$charset='utf8'){
		$this->dbhost = $dbhost;
		$this->dbuser = $dbuser;
		$this->dbpwd = $dbpwd;
		$this->dbname = $dbname;
		$this->charset = $charset;
		$this->conn = $this->_connect($dbhost,$dbuser,$dbpwd);
		$this->_select_db($dbname);
		$this->_set_names($charset);
	}

	function __destruct(){}

	private function _connect($dbhost,$dbuser,$dbpwd){
		$conn = mysql_connect($dbhost,$dbuser,$dbpwd);
		if($conn){
			return $conn;
		}else{
			$this->error();
		}
	}

	private function _select_db($dbname){
		$res = mysql_select_db($dbname,$this->conn);
		if($res){
			return true;
		}else{
			$this->error();
		}
	}

	private function _set_names($charset){
		$sql = "set names {$charset} ";
		$this->exec($sql);
	}

	function exec($sql){
		$this->last_sql = $sql;
		$res = mysql_query($sql,$this->conn);
		if($res === false){
			if(mysql_errno($this->conn) == 1062){
				return -1062;
			}else{
				$this->error();
			}
		}else{
			return mysql_affected_rows();
		}
	}

	function findOne($sql){
		$this->last_sql = $sql;
		$res = mysql_query($sql,$this->conn);
		if(!$res){
			$this->error();
		}else{
			return mysql_fetch_assoc($res);
		}
	}

	function findMany($sql){
		$this->last_sql = $sql;
		$res = mysql_query($sql,$this->conn);
		$result = array();
		if($res){
			while($row = mysql_fetch_assoc($res)){
				$result[] = $row;
			}
		}
		return $result;
	}

	function findCount($tablename,$condition=''){
		$sql = "select count(*) as count from {$tablename} ";
		if($condition){
			$sql .= "where {$condition}";
		}
		$this->last_sql = $sql;
		return $this->findOne($sql);
	}

	function update($tablename,$dataArr){
		$dataArr['time_stamp'] = date('Y-m-d H:i:s');
		$sql = "show columns from {$tablename}";
		$fieldArr = $this->findMany($sql);
		$primaryKey = '';
		foreach($fieldArr as $value){
			if($value['Key'] == 'PRI'){
				$primaryKey = $value['Field'];
				break;
			}
		}
		$sql = "update {$tablename} set ";
		foreach($dataArr as $key=>$value){
			if($key == $primaryKey){
				continue;
			}else{
				$sql .= "{$key} = '{$value}' , ";
			}
		}
		$sql = substr($sql,0,-2);
		$sql .= " where {$primaryKey} = {$dataArr[$primaryKey]} ";
		return $this->exec($sql);
	}

	function add($tablename,$dataArr){
        if(!isset($dataArr['create_time'])){
            $dataArr['create_time'] = date('Y-m-d H:i:s');
        }
        if(!isset($dataArr['time_stamp'])){
            $dataArr['time_stamp'] = date(' Y-m-d H:i:s');
        }
		$sql = "insert into {$tablename} set ";
		foreach($dataArr as $key=>$value){
			$sql .= "{$key} = '{$value}' , ";
		}
		$sql = substr($sql,0,-2);
		$this->last_sql = $sql;
		return $this->exec($sql);
	}

	function delete($tablename, $id){
		$sql = "DELETE FROM {$tablename} WHERE id = {$id}";
		$this->last_sql = $sql;
		return $this->exec($sql);
	}

	function error(){
		$errno = mysql_errno($this->conn);
		$error = mysql_error($this->conn);
		$res =  "<h1>数据库错误</h1>错误代码  ".$errno."<br/>错误提示  ".$error."<br/>";
		if($this->last_sql){
			$res .= "最后一次sql语句".$this->last_sql;
		}
		die();
	}

	function getInsertId(){
		return mysql_insert_id($this->conn);
	}

	function beginTransaction(){
		$this->exec('BEGIN');
	}

	function commit(){
		$this->exec('COMMIT');
	}

	function rollBack(){
		$this->exec('ROLLBACK');
	}
}