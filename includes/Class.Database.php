<?php
// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

//if(!defined('WELIVE')) die('File not found!');

/*
	使用原生SQL语句查询:
	1. 获取select查询结果集, 使用getAll($query)或getOne($query)函数
	2. 获取select查询资源id, 使用query($query)函数, 使用fetch函数遍历
	3. insert|delete|update|replace查询使用exe($query)函数
 */

class MySQL{
	var $dbname = ''; //保存当前数据库名, 用于多数据操作时回选上一个数据库为当前数据库
	var $dbcharset = 'utf8';
	var $conn = 0; //当前连接资源id
	var $insert_id = 0; //insert|replace语句最后插入的id
	var $query_id = 0; //最后查询id
	var $query_nums = 0; //总计查询次数
	var $result_nums = 0; //查询结果数或查询影响的记录数
	var $printerror = true; //是否打印查询错误信息
	var $errno = 0; //数据库访问错误代码

	/*
	 * 构造函数 - 建立数据库服务器连接, 并选择数据库
	 */
	function MySQL($dbuser='', $dbpassword='', $dbname='', $dbhost='localhost', $pconnect=false, $printerror=true) {
		$this->printerror = $printerror;
		$this->conn = $pconnect ? @mysql_pconnect($dbhost, $dbuser, $dbpassword) : @mysql_connect($dbhost, $dbuser, $dbpassword, true);

		if (!$this->conn)	{
			$this->error('Connect database failed! The dbuser, dbpassoword or dbhost not correct.');
		}

		$dbVersion = @mysql_get_server_info($this->conn);
		if ($dbVersion >= "4.1") {
			@mysql_query("SET NAMES '".$this->dbcharset."'", $this->conn); //使用UTF8存取数据库, mysql 4.1以上支持
		}
		
		if($dbVersion > '5.0.1'){
			@mysql_query("SET sql_mode=''", $this->conn); //设置sql_model
		}

		$this->select_db($dbname);
	}

	/*
	 * 选择数据库, 用于选择不同的数据库或未选择数据库进行多库操作, 不需要任何返回值, 如果有错误, 在查询语句中将输出
	 */
	function select_db($dbname)	{
		$this->dbname = $dbname;
		@mysql_select_db($dbname, $this->conn);
	}

	/*
	 * 只能是"insert|delete|update|replace", select查询使用getAll或getOne或query
	 * @return 返回受影响行数, 在"insert|replace"的情况下, 用 $this->insert_id 记录新插入的ID
	 */
	function exe($query)	{
		$this->query_nums++;

		$this->query_id = @mysql_query($query, $this->conn);
		if (!$this->query_id){
			$this->error("Invalid SQL: ".$query); //查询失败输出错误
		}

		if (preg_match("/^(insert|replace)\s+/i", $query)){
			$this->insert_id = @mysql_insert_id($this->conn); //记录新插入的ID
		}

		$this->result_nums = @mysql_affected_rows($this->conn); //记录影响的行数
		return $this->result_nums; //返回影响的行数
	}

	/*
	 * 只能是"select"查询, 用$this->result_nums记录查询结果数
	 * @return  query_id
	 */
	function query($query)	{
		$this->query_nums++;

		$this->query_id = @mysql_query($query, $this->conn);
		if(!$this->query_id){
			$this->error("Invalid SQL: ".$query); //查询失败输出错误
		}

		$this->result_nums = @mysql_num_rows($this->query_id); //记录查询结果数

		return $this->query_id; //返回查询资源
	}

	/*
	 * 对查询资源ID进行fetch
	 * @return  query_id
	 */
	function fetch($queryId)	{
		return @mysql_fetch_array($queryId, MYSQL_ASSOC); //返回二维数组
	}

	/*
	 * 查询结果集
	 * @return 默认返回对象数组, $out_array=1时返回二维数组
	 */
	function getAll($query){
		$results = array(); //没有查询记录时返回空数组, 使数组遍历时不产生错误
		$query_id = $this->query($query);
		while ($row = $this->fetch($query_id)){
			$results[] = $row;
		}

		return $results;
	}

	/*
	 * 查询一条数据
	 * @return 对象或一维数组
	 */
	function getOne($query){
		return @mysql_fetch_assoc($this->query($query));
	}

	/*
	 * 获取最后一次select查询的字段数
	 * @return number
	 */
	function getFields(){
		return @mysql_num_fields($this->query_id);
	}

	/*
	 * 获取最后一次insert查询插入的ID值
	 * @return number
	 */
	function insert_id(){
		return $this->insert_id;
	}

	/*
	 * 关闭当前数据库连接, 一般无需使用. 连接会随php脚本结束自动关闭
	 */
	function close(){
		return @mysql_close($this->conn);
	}

	/*
	 * 释放查询结果及内存, PHP程序会在结束时自动释放, 一般不调用
	 */
	function free_result() {
		@mysql_free_result($this->query_id);
		$this->query_id = 0;
	}

	/*
	 * @return 错误代码
	 */
	function geterrno() {
		return $this->errno;
	}

	/*
	 * 输出错误
	 */
	function error($msg = ''){
		$this->errno = @mysql_errno($this->conn);

		if($this->printerror){
			$error_desc = @mysql_error($this->conn);

			$message  = "Database Query Error Info:\r\n\r\n";
			$message .= $msg."\r\n\r\n";
			$message .= "Error: ". $error_desc ."\r\n";
			$message .= "Error No: ".$errno."\r\n";
			$message .= "File: ". $_SERVER['PHP_SELF'] . "\r\n";

			echo '<center><br /><br /><br /><br /><b>Database Query Error Info</b><br /><textarea rows="22" style="width:480px;font-size:12px;">'.$message.'</textarea></center>';

			exit();
		}
	}
}

?>