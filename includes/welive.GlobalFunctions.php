<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+


//if(!defined('WELIVE')) die('File not found!');


// #####################

function DisplayDate($timestamp = 0, $dateformat = '', $time = 0){
	global $_CFG;

	if(!$dateformat){
		$dateformat = $_CFG['cDateFormat'] . Iif($time, ' H:i:s');
	}

	$timezoneoffset = ForceInt($_CFG['cTimezone']);

	return @gmdate($dateformat, Iif($timestamp, $timestamp, time()) + (3600 * $timezoneoffset));
}

// #####################

function DisplayTime($timestamp = 0, $timeformat = 'H:i:s'){
	global $_CFG;

	$timezoneoffset = ForceInt($_CFG['cTimezone']);

	return @gmdate($timeformat, Iif($timestamp, $timestamp, time()) + (3600 * $timezoneoffset));
}

// #####################

function Iif($expression, $returntrue, $returnfalse = ''){
	if($expression){
		return $returntrue;
	}else{
		return $returnfalse;
	}
}

// #####################

function SafeSql($source){
	$entities_match = array(',',';','$','!','@','#','%','^','&','*','_','(',')','{','}','|',':','"','<','>','?','[',']','\\',"'",'.','/','*','+','~','`','=');
	return str_replace($entities_match, '', trim($source));
}

// #####################

function SafeSearchSql($source){
	$entities_match = array('$','!','@','#','%','^','&','*','_','(',')','{','}','|',':','"','<','>','?','[',']','\\',"'",'.','/','*','~','`','=');
	return str_replace($entities_match, '', trim($source));
}


// #####################

function IsEmail($email){
	return preg_match("/^[a-z0-9]+[.a-z0-9_-]*@[a-z0-9]+[.a-z0-9_-]*\.[a-z0-9]+$/i", $email);
}

// #####################

function IsName($name){
	$entities_match = array(',',';','$','!','@','#','%','^','&','*','(',')','{','}','|',':','"','<','>','?','[',']','\\',"'",'/','*','+','~','`','=');
	for ($i = 0; $i<count($entities_match); $i++) {
	     if(strpos($name, $entities_match[$i])){
               return false;
		 }
	}
   return true;
}

// #####################

function IsPass($pass){
   return preg_match("/^[[:alnum:]]+$/i", $pass);
}

// #####################

function PassGen($length = 8){
	$str = 'abcdefghijkmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	for ($i = 0, $passwd = ''; $i < $length; $i++)
		$passwd .= substr($str, mt_rand(0, strlen($str) - 1), 1);
	return $passwd;
}

// #####################

function IsGet($VariableName) {
	if (isset($_GET[$VariableName])) {
		return true;
	} else {
		return false;
	}

}

// #####################

function IsPost($VariableName) {
	if (isset($_POST[$VariableName])) {
		return true;
	} else {
		return false;
	}

}

// #####################

function ForceIncomingString($VariableName, $DefaultValue = '') {
	if (isset($_GET[$VariableName])) {
		return ForceString($_GET[$VariableName], $DefaultValue);
	} elseif (isset($_POST[$VariableName])) {
		return ForceString($_POST[$VariableName], $DefaultValue);
	} else {
		return $DefaultValue;
	}
}

// #####################

function ForceIncomingInt($VariableName, $DefaultValue = 0) {
	if (isset($_GET[$VariableName])) {
		return ForceInt($_GET[$VariableName], $DefaultValue);
	} elseif (isset($_POST[$VariableName])) {
		return ForceInt($_POST[$VariableName], $DefaultValue);
	} else {
		return $DefaultValue;
	}
}

// #####################

function ForceIncomingFloat($VariableName, $DefaultValue = 0) {
	if (isset($_GET[$VariableName])) {
		return doubleval($_GET[$VariableName]);
	} elseif (isset($_POST[$VariableName])) {
		return doubleval($_POST[$VariableName]);
	} else {
		return $DefaultValue;
	}
}

// #####################

function ForceIncomingCookie($VariableName, $DefaultValue = '') {
	if (isset($_COOKIE[$VariableName])) {
		return ForceString($_COOKIE[$VariableName], $DefaultValue);
	} else {
		return $DefaultValue;
	}
}

// #####################

function EscapeSql($query_string) {

	if (get_magic_quotes_gpc()) {
		$query_string = stripslashes($query_string);
	}

	$query_string = htmlspecialchars(str_replace (array('\0', '　'), '', $query_string), ENT_QUOTES);
	
	if(function_exists('mysql_real_escape_string')) {
		$query_string = mysql_real_escape_string($query_string);
	}else if(function_exists('mysql_escape_string')){
		$query_string = mysql_escape_string($query_string);
	}else{
		$query_string = addslashes($query_string);
	}

	return $query_string;
}

// #####################

function html($String) {
	 return str_replace(array('&amp;','&#039;','&quot;','&lt;','&gt;'), array('&','\'','"','<','>'), $String);
}

// #####################

function ForceInt($InValue, $DefaultValue = 0) {
	$iReturn = intval($InValue);
	return ($iReturn == 0) ? $DefaultValue : $iReturn;
}

// #####################

function ForceString($InValue, $DefaultValue = '') {
	if (is_string($InValue)) {
		$sReturn = EscapeSql(trim($InValue));
		if (empty($sReturn) && strlen($sReturn) == 0) $sReturn = $DefaultValue;
	} else {
		$sReturn = EscapeSql($DefaultValue);
	}
	return $sReturn;
}

// #####################
function GetIP() {
	if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
		$thisip = getenv('HTTP_CLIENT_IP');
	} elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
		$thisip = getenv('HTTP_X_FORWARDED_FOR');
	} elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
		$thisip = getenv('REMOTE_ADDR');
	} elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
		$thisip = $_SERVER['REMOTE_ADDR'];
	}

	preg_match("/[\d\.]{7,15}/", $thisip, $thisips);
	$thisip = $thisips[0] ? $thisips[0] : 'unknown';
	return $thisip;
}

// #####################

function header_nocache() {
	header("Expires: Mon, 18 Jul 1988 01:08:08 GMT"); // Date in the past
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0"); // HTTP/1.1
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache"); // HTTP/1.0
}

// #####################

function header_utf8() {
	header("Content-type: text/html; charset=UTF-8");
}

// #####################

function CreateVVC() {
	global $DB;

	$DB->exe("INSERT INTO " . TABLE_PREFIX . "vvc VALUES (NULL, '', '".time()."')");

	return $DB->insert_id();
}

// #####################

function CheckVVC($vvcid, $code) {
	global $DB;

	if(!$code){return false;}

	if(is_numeric($vvcid)){
		$verifycode = $DB->getOne("SELECT code FROM " . TABLE_PREFIX . "vvc WHERE vvcid = '$vvcid'");

		if($verifycode['code'] == strtoupper($code)){
			$DB->exe("DELETE FROM " . TABLE_PREFIX . "vvc WHERE vvcid = '$vvcid'");
			return true;
		}
	}
	return false;
}

// #####################
function storeCache(){
	global $DB;

	$folder = BASEPATH . "cache/";
	@chmod($folder, 0777);

	if(!is_writeable($folder)) {
		return false;
	}

	$filename = $folder . "online_cache.php";

	$getsupporters = $DB->query("SELECT ug.usergroupid, ug.groupname, ug.groupename, ug.description, ug.descriptionen, u.type, u.userid, u.username, u.isonline, u.userfrontname, u.userfrontename FROM " . TABLE_PREFIX . "usergroup ug INNER JOIN " . TABLE_PREFIX . "user u ON ug.usergroupid = u.usergroupid WHERE  ug.usergroupid <>1 AND ug.activated = 1 AND u.activated = 1 ORDER BY ug.displayorder ASC, u.displayorder ASC");

    $arr = array();

	while($row=$DB->fetch($getsupporters)){
		$arr[$row['usergroupid']]['groupname']   = $row['groupname'];
		$arr[$row['usergroupid']]['groupename']   = $row['groupename'];
		$arr[$row['usergroupid']]['description']   = $row['description'];
		$arr[$row['usergroupid']]['descriptionen']   = $row['descriptionen'];
		$arr[$row['usergroupid']]['user'][$row['userid']]['username']  = $row['username'];
		$arr[$row['usergroupid']]['user'][$row['userid']]['type']  = $row['type'];
		$arr[$row['usergroupid']]['user'][$row['userid']]['isonline']  = $row['isonline'];
		$arr[$row['usergroupid']]['user'][$row['userid']]['isbusy']  = '0';
		$arr[$row['usergroupid']]['user'][$row['userid']]['userfrontname']  = $row['userfrontname'];
		$arr[$row['usergroupid']]['user'][$row['userid']]['userfrontename']  = $row['userfrontename'];
	}

	$online_cache = "<?php
if(!defined('WELIVE')) die('File not found!');

\$welive_onlines  = " . var_export($arr, true) . ";

?>";


	$fp = @fopen($filename, 'rb');
	$contents = @fread($fp, filesize($filename));
	@fclose($fp);
	$contents =  trim($contents);

	if($contents != $online_cache){
		$fp = @fopen($filename, 'wb');
		@fwrite($fp, $online_cache);
		@fclose($fp);
	}

	return $arr;
}

// #####################
function refreshCache($userid, $item, $new_value = '0'){
	$filename = BASEPATH . "cache/online_cache.php";

	if(file_exists($filename)){
		include($filename);

		foreach($welive_onlines AS $key => $value){
			if(array_key_exists($userid, $value['user'])){
				$welive_onlines[$key]['user'][$userid][$item] = $new_value;
				if($item == 'isonline'){
					$welive_onlines[$key]['user'][$userid]['isbusy'] = '0'; //更改了在线状态时, 重置忙碌状态为不忙
				}

				$online_cache = "<?php
if(!defined('WELIVE')) die('File not found!');

\$welive_onlines  = " . var_export($welive_onlines, true) . ";

?>";
				$fp = @fopen($filename, 'wb');
				@fwrite($fp, $online_cache);
				@fclose($fp);
			}
		}
	}else{
		storeCache(); //文件不存在时创建缓存
	}
}

// #####################
function checkbusy($userid){
	$filename = BASEPATH . "cache/online_cache.php";
	$groupid = 0;

	include($filename);

	//查询当前客服的用户组ID
	foreach($welive_onlines AS $key => $value){
		if(array_key_exists($userid, $value['user'])){
			if(!$value['user'][$userid]['isbusy']) return 0; //当前客服不忙
			$groupid = $key;
			break;
		}
	}

	$groupusers = $welive_onlines[$groupid]['user'];

	//查询当前用户组不忙客服的ID
	foreach($groupusers AS $key => $value){
		if($key != $userid && $value['isonline'] && !$value['isbusy']){
			return $key;  //返回一个在线且不忙的客服的ID
		}
	}

	return 0; //未找到不忙客服
}

?>