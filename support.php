<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

define('AUTH', true);
define('AJAX', true);

include('includes/welive.Core.php');

$uid = ForceIncomingInt('uid');
$gid = ForceIncomingInt('gid');
$act = ForceIncomingString('act');
$ajax_last = ForceIncomingFloat('ajax_last');

if(!$uid){
	die('Hacking!');
}elseif(ForceIncomingCookie('weliveU'.COOKIE_KEY) != md5(WEBSITE_KEY.$uid.$_CFG['cKillRobotCode'])){
	setcookie('weliveU'.COOKIE_KEY, '', 0, '/');
	die('Hacking!');
}

$mktime = explode(' ', microtime());
$realtime = $mktime[1];
$minitime =  $mktime[0];

$guests = '';
$msgs = '';

switch($act){
	case 'offline': //离开页面时设置客服为离线状态
		$DB->exe("UPDATE " . TABLE_PREFIX . "user SET isonline = 0 WHERE userid = '$uid'");
		refreshCache($uid, 'isonline', '0'); //更新缓存
		break;

	case 'online':
		$DB->exe("UPDATE " . TABLE_PREFIX . "user SET isonline = 1 WHERE userid = '$uid'");
		refreshCache($uid, 'isonline', '1'); //更新缓存
		WeLiveSend($realtime + $minitime, $guests, $msgs, $ajax_last, $DB->errno);
		break;

	case 'banned':
		if($gid){
			$DB->exe("UPDATE " . TABLE_PREFIX . "guest SET isbanned = 1 WHERE guestid = '$gid'");
		}
		break;

	case 'unbanned':
		if($gid){
			$DB->exe("UPDATE " . TABLE_PREFIX . "guest SET isbanned = 0 WHERE guestid = '$gid'");
		}
		break;

	case 'kickout':
		if($gid){
			$DB->exe("DELETE FROM " . TABLE_PREFIX . "guest WHERE guestid = '$gid'");
		}
		break;

	case 'iplocation':
		$ip = ForceIncomingString('ip');
		echo convertip($ip);
		break;

	case 'setbusy': //设置为忙碌状态
		refreshCache($uid, 'isbusy', '1'); //更新缓存
		break;

	case 'unsetbusy': //解除忙碌状态
		refreshCache($uid, 'isbusy', '0'); //更新缓存
		break;
}

?>

