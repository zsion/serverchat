<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

include('includes/welive.Core.php');

header_nocache();

$uid = ForceIncomingInt('uid');

//保存数据
if(isset($_POST['submitcomment'])) {
	$vvckey=ForceIncomingInt('vvckey');

	if(ForceIncomingCookie('safecookieC'.$vvckey.COOKIE_KEY) != md5($_CFG['cKillRobotCode'] . $vvckey)) exit();
	setcookie('safecookieC'.$vvckey.COOKIE_KEY, '', 0, '/');

	$gname=ForceIncomingString('gname');
	$username=ForceIncomingString('username');
	$content=ForceIncomingString('content');
	$vvc = ForceIncomingString('vvc');

	if(strlen($gname)<1){$error_u = 1;}
	if(strlen($content)<1){$error_c = 1;}
	if(strlen($vvc)<1){
		$error_v = 1;
	}else if(!CheckVVC($vvckey, $vvc)){
		$error_v = 1;
		$error_vvc = $lang['er_vvc'];
	}

	if(!$error_c AND !$error_u AND !$error_v){
		$sql = "SELECT u.userid FROM " . TABLE_PREFIX . "user u
					LEFT JOIN " . TABLE_PREFIX . "usergroup ug ON ug.usergroupid = u.usergroupid
					WHERE u.userid  = '$uid'
					AND   u.activated = 1
					AND   u.usergroupid <> 1
					AND   ug.activated = 1";

		$user = $DB->getOne($sql);

		if(!$user['userid']){
			$error = $lang['er_noaccess'];
		}else{
			$DB->exe("INSERT INTO ".TABLE_PREFIX."comment VALUES(NULL,'$uid','$gname','$content','".GetIP()."','".time()."')");
			$er_info = '<BR><BR><BR><BR><BR><BR><BR><center><font color=green>'.$lang['thanksfor'].'</font></center>';
			header_utf8();
			die($er_info);
		}
	}

}else{

	$vvckey = ForceIncomingString('vvckey');
	$code = authcode(base64_decode($_GET['code']), 'DECODE', $vvckey);

	if(!$uid OR !$code OR !$vvckey){
		$error = $lang['er_verify'];
	}elseif($code !== COOKIE_KEY . $uid){
		$error = $lang['er_verify'];
	}elseif(IsBannedIP(GetIP())){
		$error = $lang['er_bannedip'];
	}else{
		$sql = "SELECT u.userid, u.userfrontname, u.userfrontename FROM " . TABLE_PREFIX . "user u
					LEFT JOIN " . TABLE_PREFIX . "usergroup ug ON ug.usergroupid = u.usergroupid
					WHERE u.userid  = '$uid'
					AND   u.activated = 1
					AND   u.usergroupid <> 1
					AND   ug.activated = 1";

		$user = $DB->getOne($sql);

		if(!$user['userid'] OR $code !== COOKIE_KEY . $user['userid']){
			$error = $lang['er_verify'];
		}elseif($user['isonline']){
			//跳转到服务窗口
		}
	}
	//以上需要添加禁止IP的验证

	//根据语言选择客服的信息
	if(IS_CHINESE){
		$username = $user['userfrontname'];
	}else{
		$username = $user['userfrontename'];
	}
}

if(isset($error)){
	header_utf8();
	$er_info = '<BR><BR><BR><BR><BR><BR><BR><center><font color=red>//1</font></center>';
	die(str_replace('//1', $error, $er_info));
}

$welcome_info = preg_replace('/\/\/1/i', '<span class=spec>'.$username.'</span>', $lang['welcome2']);
$vvckey = CreateVVC();


//以下输出页面
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>'.SITE_TITLE.'</title>
<link rel="stylesheet" type="text/css" href="templates/styles.css">
<link rel="shortcut icon" href="favicon.ico" />
</head>
<body'.Iif(!$msg, ' onload="document.forms.commentform.gname.focus();"').'>

<div id="guest">
	<div id="guest_top">
		<div class="logo">'.SITE_TITLE.'</div>
		<div id="guestinfo">'.$welcome_info.'</div>
	</div>

	<div class="comment">
	<form action="comment.php"  name="commentform" method="post">
	<input type="hidden" name="uid" value="'.$uid.'" />
	<input type="hidden" name="username" value="'.$username.'" />
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td width="100"><b>'.$lang['msg_to'].':</b>&nbsp;&nbsp;</td>
	<td>'.$username.'&nbsp;</td>
	</tr>
	<tr>
	<td><b>'.$lang['fullname'].':</b>&nbsp;&nbsp;</td>
	<td><input name="gname" type="text" size="16" maxlength="48" class="input-text" value="'.$gname.'" '. Iif($error_u, ' style="border: 1px solid #FF0000;"') .'> <span class="red">*</span></td>
	</tr>
	<tr>
	<td width="100"><b>'.$lang['content'].':</b><br><span class=orange>'.$lang['content_info'].'</span></td>
	<td valign="top" style="padding-top:8px;" ><textarea name="content" class="content" '.Iif($error_c, ' style="border:1px solid #FF0000;"').'>'.Iif(isset($_POST['content']), $_POST['content']).'</textarea> <span style="color:red;vertical-align:top;">*</span></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td><img src="vvc.php?key='.$vvckey.'" onclick="ChangeCaptcha(this);" class="vvc" title="'.$lang['clickfornew'].'" width="196" height="42" /><input type="hidden" name="vvckey" value="'.$vvckey.'" /></td>
	</tr>
	<tr>
	<td><b>'.$lang['vvc'].':</b>&nbsp;&nbsp;</td>
	<td style="padding-top:4px;"><input name="vvc" type="text" size="16" class="input-text" maxlength="48" '.Iif($error_v, ' style="border: 1px solid #FF0000;"').'> <span class="red">*</span></td>
	</tr>
	<tr>
	<td>&nbsp;</td>
	<td><BR><input type="submit" onclick="setCookie(\'safecookieC'.$vvckey.COOKIE_KEY.'\', \''.md5($_CFG['cKillRobotCode'].$vvckey).'\'); return true;" value="'.$lang['submit'].'" name="submitcomment" /></td>
	</tr>

	</table>
	</form>
	</div>

	<div id="guest_bottom">
		<div class="sysinfo_div"></div>
		<div id="loading"></div>
		<div class="copyright" id="copyright">'.COPYRIGHT.'</div>
	</div>
</div>
<style type="text/css">html,body{overflow:hidden}</style>
<script type="text/javascript">
function ChangeCaptcha(i){
	var a = Math.random(); 
	var url = i.src;
	i.src= url.split("&")[0] + "&" + a; 
}

function setCookie(name,value) {
	document.cookie = name+"="+value+"; path=/";
}
'.Iif($error_vvc, 'alert("'.$error_vvc.'");').'
</script>
</body>
</html>';

?>
