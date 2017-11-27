<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

define('AJAX', true);

include('includes/welive.Core.php');

$uid = ForceIncomingInt('uid');
$gid = ForceIncomingInt('gid');
$act = ForceIncomingString('act');
$ajax_last = ForceIncomingFloat('ajax_last');

if(!$uid OR !$gid){
	die('Hacking!');
}elseif(ForceIncomingCookie('weliveG'.COOKIE_KEY) != md5($gid.WEBSITE_KEY.$uid.$_CFG['cKillRobotCode'])){
	setcookie('weliveG'.COOKIE_KEY, '', 0, '/');
	die('Hacking!');
}

$mktime = explode(' ', microtime());
$realtime = $mktime[1];
$minitime =  $mktime[0];

$lines = array();

if($act == 'offline'){ //离开页面时设置客人为离线状态
	$DB->exe("UPDATE " . TABLE_PREFIX . "guest SET isonline = 0 WHERE guestid = '$gid'");

}elseif($act == 'online'){
	$DB->exe("UPDATE " . TABLE_PREFIX . "guest SET isonline = 1, created = '$realtime', serverid = '$uid' WHERE guestid = '$gid'");

	$welcome = Iif(IS_CHINESE, html($_CFG['cWelcome']), html($_CFG['cWelcome_en']));

	$lines[] = WeLive(0, Iif($welcome, $welcome, $lang['guest_login']), 1);

	WeLiveSend($realtime + $minitime, $lines, $ajax_last, $DB->errno);
}


?>

