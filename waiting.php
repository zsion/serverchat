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

//获取最新信息
$getmsgs = $DB->query("SELECT msg, biu, color FROM " . TABLE_PREFIX . "msg WHERE toid = '$gid' AND type = 1 AND fromid ='$uid' AND (created + minitime) > $ajax_last ORDER BY msgid ASC");

while($msg = $DB->fetch($getmsgs)){
	$lines[] = WeLive(1, html($msg['msg']), 2, $msg['biu'], $msg['color']);
}

$sql = "SELECT u.userid, u.activated, u.isonline AS uisonline, g.guestid, g.isonline AS gisonline, g.isbanned FROM " . TABLE_PREFIX . "user u
			LEFT JOIN " . TABLE_PREFIX . "guest g ON g.serverid = u.userid AND g.guestid = '$gid'
			WHERE u.userid  = '$uid'
			AND   u.usergroupid <> 1";

$uginfo = $DB->getOne($sql);

//验证客服和访客的状态
if(!$uginfo['userid'] OR !$uginfo['activated'] OR !$uginfo['uisonline']){
	$lines[] = 'offline';
}elseif(!$uginfo['guestid']){
	$lines[] = 'kickout';
}elseif($uginfo['isbanned']){
	$lines[] = 'banned';
}elseif($uginfo['guestid'] AND $act == 'sending'){ //发表信息
	$ajaxline = ForceIncomingString('ajaxline');
	$ajaxbiu = ForceIncomingString('ajaxbiu', '000');
	$ajaxcolor = ForceIncomingString('ajaxcolor', '0');

	$DB->exe("INSERT INTO " . TABLE_PREFIX . "msg (fromid, toid, msg, biu, color, created, minitime, type) VALUES ('$gid', '$uid', '$ajaxline', '$ajaxbiu', '$ajaxcolor', '$realtime', '$minitime', 0)");

	$lines[] = WeLive(2, html($ajaxline), 2, $ajaxbiu, $ajaxcolor);
}

if(!$uginfo['gisonline'] AND $uginfo['guestid']){ //客人如果不在线, 更新为在线状态
	$DB->exe("UPDATE " . TABLE_PREFIX . "guest SET isonline = 1, created = '$realtime' WHERE guestid = '$gid'");
}


WeLiveSend($realtime + $minitime, $lines, $ajax_last, $DB->errno);

?>

