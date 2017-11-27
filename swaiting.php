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
$gid = ForceIncomingInt('gid', 0);
$act = ForceIncomingString('act');
$ajax_last = ForceIncomingFloat('ajax_last');
$lastlogin = ForceInt(ForceIncomingCookie('last'.COOKIE_KEY));

if(!$uid){
	die('Hacking!');
}elseif(ForceIncomingCookie('weliveU'.COOKIE_KEY) != md5(WEBSITE_KEY.$uid.$_CFG['cKillRobotCode'])){
	setcookie('weliveU'.COOKIE_KEY, '', 0, '/');
	die('Hacking!');
}

$mktime = explode(' ', microtime());
$realtime = $mktime[1];
$minitime =  $mktime[0];

$lastlogin = Iif($lastlogin, $lastlogin-3600, $realtime - 3600*12);
$guests = '';
$msgs = '';

$getguests = $DB->query("SELECT guestid, guestip, browser, lang, isonline, isbanned, fromurl FROM " . TABLE_PREFIX . "guest WHERE serverid = '$uid' AND created > $lastlogin ORDER BY created ASC");

while($guest=$DB->fetch($getguests)){
	$guests .= $guest['guestid'] .'|||'.$guest['guestip'] .'|||'.$guest['browser'] .'|||'.$guest['lang'] .'|||'.$guest['isonline'] .'|||'.$guest['isbanned'] .'|||'.$guest['fromurl'] .'^^^';
}

$getmsgs = $DB->query("SELECT fromid, msg, biu, color FROM " . TABLE_PREFIX . "msg WHERE toid = '$uid' AND type = 0 AND (created + minitime) > $ajax_last ORDER BY msgid ASC");

while($msg = $DB->fetch($getmsgs)){
	$msgs .= $msg['fromid'] . '|||2|||'.html($msg['msg']) .'|||2|||'.$msg['biu'] .'|||'.$msg['color'] .'^^^';
}

if($gid AND $act == 'sending'){ //发表信息
	$ajaxline = ForceIncomingString('ajaxline');
	$ajaxbiu = ForceIncomingString('ajaxbiu', '000');
	$ajaxcolor = ForceIncomingString('ajaxcolor', '0');

	$DB->exe("INSERT INTO " . TABLE_PREFIX . "msg (fromid, toid, msg, biu, color, created, minitime, type) VALUES ('$uid', '$gid', '$ajaxline', '$ajaxbiu', '$ajaxcolor', '$realtime', '$minitime', 1)");

	$msgs .= $gid . '|||1|||'.html($ajaxline) .'|||2|||'.$ajaxbiu .'|||'.$ajaxcolor .'^^^';
}


WeLiveSend($realtime + $minitime, $guests, $msgs, $ajax_last, $DB->errno);

?>

