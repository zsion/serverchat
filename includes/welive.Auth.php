<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

//if(!defined('WELIVE')) die('File not found!');


// #######################################################

$userinfo = array();
$userid = 0;

define('COOKIE_NAME', 'weliveF'.COOKIE_KEY);
$realtime = time();

$loginusername = ForceIncomingString('loginusername');
$loginpassword = ForceIncomingString('loginpassword');

if(strlen($loginusername) AND strlen($loginpassword)){
	$vvckey           = ForceIncomingString('vvckey');

	if(ForceIncomingCookie('safecookieF'.$vvckey.COOKIE_KEY) != md5($_CFG['cKillRobotCode'] . $vvckey)) exit();
	setcookie('safecookieF'.$vvckey.COOKIE_KEY, '', 0, '/');

	if(!IsName($loginusername) OR !IsPass($loginpassword)){
		$logininfo = $lang['login_error1'];
		LogIn();
	}else{
		$userid = LoginUser($loginusername, $loginpassword);
		if(!$userid){
			$logininfo = $lang['login_error2'];
			LogIn();
		}else	{
			CreateSession($userid);
			header("Location: ./");
			exit();
		}
	}

} else if (ForceIncomingInt('logout') == 1) {

	$sessionid = ForceIncomingCookie(COOKIE_NAME);

	if($sessionid AND IsPass($sessionid)){
		$user = $DB->getOne("SELECT u.userid, u.usergroupid FROM " . TABLE_PREFIX . "session s 
		LEFT JOIN " . TABLE_PREFIX . "user u ON u.userid = s.userid
		WHERE sessionid    = '$sessionid'");

		$DB->exe("UPDATE " . TABLE_PREFIX . "user SET isonline = 0 WHERE userid = '$user[userid]' ");
		$DB->exe("DELETE FROM " . TABLE_PREFIX . "session WHERE sessionid = '$sessionid' ");
		$DB->exe("DELETE FROM " . TABLE_PREFIX . "vvc WHERE date < " . ($realtime - 3600*8));
		$DB->exe("DELETE FROM " . TABLE_PREFIX . "session WHERE created < " . ($realtime - 3600*48));

		if($user['usergroupid'] != 1){
			refreshCache($user['userid'], 'isonline', '0'); //仅客服退出时更新缓存
		}
	}

	setcookie(COOKIE_NAME, "", 0, "/");
	LogIn();

} else {

	$sessionid = ForceIncomingCookie(COOKIE_NAME);

	if($sessionid AND IsPass($sessionid)){
		$sql = "SELECT u.*, (select COUNT(*)  FROM " . TABLE_PREFIX . "comment WHERE touserid = s.userid) AS comments FROM " . TABLE_PREFIX . "session s
					LEFT JOIN " . TABLE_PREFIX . "user u ON u.userid = s.userid
					WHERE s.sessionid    = '$sessionid'
					AND   s.ipaddress = '" . GetIP() . "'
					AND   u.activated = 1";

		$userinfo = $DB->getOne($sql);

		if(!$userinfo OR !$userinfo['userid']){
			unset($userinfo);
			setcookie(COOKIE_NAME, "", 0, "/");
			LogIn();
		}
	} else {
		LogIn();
	}

}


unset($userid, $loginusername, $loginpassword, $sessionid);

// ####################################################################

function LogIn(){
	global $logininfo, $DB, $_CFG, $lang;

	$vvckey = PassGen(8);

	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>' . SITE_TITLE . '</title>
	<link rel="stylesheet" type="text/css" href="templates/support.css">
	<link rel="shortcut icon" href="favicon.ico" />
	<script type="text/javascript">
	function setSafeCookie() {
		document.cookie = "safecookieF'.$vvckey.COOKIE_KEY.'='.md5($_CFG['cKillRobotCode'].$vvckey).'; path=/";
	}
	</script>
	</head>
	<body onLoad="document.forms.frontlogin.loginusername.focus();">
	<div class="login">
	<form action="index.php" method="post" name="frontlogin">
	<input type="hidden" name="vvckey" value="' . $vvckey . '">
	<h3 class="title">' . $lang['login_title'] . '</h3>
	' . Iif($logininfo, '<div class="info">'. $logininfo . '</div>') . '
	<ul>
	<li><label>' . $lang['name'] . '：</label><input type="text" class="input-text" name="loginusername"></li>
	<li><label>' . $lang['password'] . '：</label><input type="password" class="input-text" name="loginpassword"></li>
	</ul>
	<div class="submit">
	<input type="submit" onclick="setSafeCookie();return true;" value="' . $lang['login'] . '" class="button" />
	</div>
	<br/><br/>
	<div class="copyright" style="display:none;">&copy; '.date("Y") .' <a href="'.APP_URL.'" target="_blank">'. APP_NAME .'</a></div>
	</form>
	</div>
	</body>
	</html>';

	exit();
}

function LoginUser($loginusername, $loginpassword){
	global $DB, $_CFG;

	$loginpassword = md5($loginpassword);

	$user = $DB->getOne("SELECT userid FROM " . TABLE_PREFIX . "user WHERE username = '$loginusername' AND password = '$loginpassword' AND activated = 1 AND type = 1");

	return $user['userid'];
}


function CreateSession($userid){
	global $DB, $_CFG;

	$userip = GetIP();
	$timenow = time();
	$sessionid = md5(uniqid($userid . COOKIE_KEY));

	$DB->exe("INSERT INTO " . TABLE_PREFIX . "session (sessionid, userid, ipaddress, created)
			  VALUES ('$sessionid', '$userid', '$userip', '$timenow') ");
	$DB->exe("UPDATE " . TABLE_PREFIX . "user SET lastlogin = '$timenow' WHERE userid = '$userid' ");
	$DB->exe("DELETE FROM " . TABLE_PREFIX . "guest WHERE created < " . ($timenow - 3600*24));

	$deletehistory = ForceInt($_CFG['cDeleteHistory']);

	if($deletehistory){
		$DB->exe("DELETE FROM " . TABLE_PREFIX . "msg WHERE created < " . ($timenow - 3600*$deletehistory));
	}

	setcookie(COOKIE_NAME, $sessionid, 0, "/");
	setcookie('last'.COOKIE_KEY, $timenow, 0, "/");

	setcookie('weliveU'.COOKIE_KEY, md5(WEBSITE_KEY.$userid.$_CFG['cKillRobotCode']), 0, "/");         //用于AJAX验证
}


?>