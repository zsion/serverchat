<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

define('AUTH', true);

include('includes/welive.Core.php');

if($userinfo['usergroupid'] == 1){
	header("Location: admin.home.php");
	exit();
}

//添加快速回复短语, 默认显示40条
$getmsgs = $DB->query("SELECT msg FROM " . TABLE_PREFIX . "automsg WHERE activated =1 ORDER BY ordernum ASC LIMIT 40");
$automsgs = ''; 
$automsgs_line = 1; 
while($getmsg = $DB->fetch($getmsgs)){
	$automsgs .= '<div class="msgs_line" onmouseover="chClass(this);"><div class="msgs_line_n">'.$automsgs_line.'.</div><div class="msgs_line_m" onclick="insertMsgs(this);">'.html($getmsg['msg']).'</div></div>';

	++$automsgs_line; 
}

header_nocache();

$uid = $userinfo['userid'];
$gid = 0;
$realtime = time();

setcookie('weliveU'.COOKIE_KEY, md5(WEBSITE_KEY.$uid.$_CFG['cKillRobotCode']), 0, "/");         //用于AJAX验证
$ajaxpending = 'uid=' . $uid;        //需要动态变化, 用于将客服ID附加到AJAX URL

$smilies = ''; //添加表情图标
for($i = 0; $i < 24; $i++){
	$smilies .= '<img src="'.TURL.'smilies/' . $i . '.gif" onclick="insertSmilies(guestid, \\\'[:' . $i . ':]\\\');">';
}

//添加颜色
$colors = array('000000','6C6C6C','969696','FF0000','FF6600','FFCC00','916200','CD8447','2B8400','2FEA00','999900','0000CC','0066FF','35A2C1','701B76','C531D0'); 
$color_squares = '';
foreach($colors as $key => $value){
	$color_squares .= '<div class="color_squares" style="background:#' . $value . '" onclick="insertColors(guestid, \\\'' . $value . '\\\');"></div>';
}

$js_var = "pagetitle=\"".SITE_TITLE."\",soundon=\"$lang[soundon]\",soundoff=\"$lang[soundoff]\",newguest=\"$lang[newguest]\",ban=\"$lang[ban]\",baninfo=\"$lang[baninfo]\",unban=\"$lang[unban]\",unbaninfo=\"$lang[unbaninfo]\",reonline=\"$lang[reonlineg]\",er_system=\"$lang[er_system]\",er_goffline=\"$lang[er_goffline]\",sender_sys=\"$lang[system]\",guestname=\"$lang[guest]\",username=\"$lang[isay]\", t_url=\"".TURL."\"";

//窗口内容
$x_win_content = '<div class="guest"><div class="guest_top"><div class="ico_history"><img src="'.TURL.'images/history.gif"></div><div id="history_guestid" class="history"></div></div><div class="guest_tools"><div id="colors_guestid" class="colors_div" style="display:none">'.$color_squares.'</div><div id="smilies_guestid" class="smilies_div" style="display:none">'.$smilies.'</div><div id="tools_sound_guestid" class="tools_sound_on" onmouseover="chClassname(this, \\\'sound\\\');chSoundTitle(this);" onclick="toggleTools(guestid, \\\'sound\\\');"></div><div id="tools_smile_guestid" class="tools_smile_off" onclick="showSmilies(guestid, 0);" onmouseover="showSmilies(guestid);" title="'.$lang['smilies'].'"></div><div id="tools_color_guestid" class="tools_color_off" onclick="showColors(guestid, 0);" onmouseover="showColors(guestid);" title="'.$lang['fontcolor'].'"></div><div id="tools_bold_guestid" class="tools_bold_off" onmouseover="chClassname(this, \\\'bold\\\');" onclick="toggleTools(guestid, \\\'bold\\\');" title="'.$lang['bold'].'"></div><div id="tools_italic_guestid" class="tools_italic_off" onmouseover="chClassname(this, \\\'italic\\\');" onclick="toggleTools(guestid, \\\'italic\\\');" title="'.$lang['italic'].'"></div><div id="tools_underline_guestid" class="tools_underline_off" onmouseover="chClassname(this, \\\'underline\\\');" onclick="toggleTools(guestid, \\\'underline\\\');" title="'.$lang['underline'].'"></div><div id="tools_reset_guestid" class="tools_reset_off" onmouseover="chClassname(this, \\\'reset\\\');" onclick="ResetInput(guestid);" title="'.$lang['reset'].'"></div><div id="tools_msg_guestid" class="tools_msg_off" onmouseover="chClassname(this, \\\'msg\\\');" onclick="showMsgs(guestid, 0);"></div></div><div class="guest_bottom"><div class="ico_message"><img src="'.TURL. 'images/message.gif"></div><div class="message_div"><textarea id="message_guestid" class="message"></textarea></div><div class="tools_send_div"><div class="tools_send" onmouseover="chClassname(this, \\\'send\\\');" onclick="sending(guestid);return false;">发送</div></div></div></div>';


echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>' . SITE_TITLE . '</title>
<script type="text/javascript" src="includes/javascript/Ajax.js"></script>
<script type="text/javascript" src="includes/javascript/Support.js"></script>
<script type="text/javascript" src="includes/javascript/x-win.js"></script>
<link rel="stylesheet" type="text/css" href="templates/support.css">
</head>
<body>
<a href="#" name="top"></a>
<div id="msgs_div" class="msgs_div" style="display:none">'.$automsgs.'</div>
<div id="maindiv">
	<div id="header">
		<div class="logo"><img src="'.TURL.'images/logo.gif" align="absmiddle"> ' . SITE_TITLE . '</div>
		<div id="userinfo">'. preg_replace('/\/\/1/i', '<span class=spec>'.$userinfo['userfrontname'].'</span>', $lang['welcome_user']) .'&nbsp;&nbsp;'.Iif($userinfo['comments'], '<a href="admin.mycomments.php" target="_blank">您有'.$userinfo['comments'].'条留言</a>', '暂无给您的留言').'.&nbsp;&nbsp;&nbsp;&nbsp;[ <a href="index.php?logout=1" onclick="return confirm(\''.$lang['logoutinfo'].'\');"><span style="color:#FF3300;font-weight:700;">'.$lang['logout'].'</span></a> ]&nbsp;&nbsp;&nbsp;&nbsp;[ <span id="setbusy"><a href="javascript:;" onclick="setbusy();return false;"><b>挂起</b></a> </span>]</div>
		<div class="timer_div"><span id="timer">00:00</span></div>
	</div>
	<div class="contentdiv">
		<div class="welive_div">
			<table id="welive_list" border="0" cellpadding="0" cellspacing="0" class="waiting">
				<thead>
					<tr>
						<th class="first">访客</th>
						<th>上线时间</th>
						<th>IP地址</th>
						<th>浏览器</th>
						<th>来自页面</th>
						<th>操作</th>
					</tr>
				</thead>
				<tbody id="welive">
				<tr><th colspan="7" class="last"></th></tr>
				</tbody>
			</table>
		</div>
		<div id="noguest">'.$lang['noguest'].'</div>
	</div>
	<div class="blank40"></div>
</div>
<div id="footer">
	<div class="copyright">' . COPYRIGHT . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#top" title="TOP"><img src="'.TURL.'images/btn_top.gif" align="absmiddle" /></a></div>
	<div class="sysinfo_div"><span id="status_ok" class="status_ok"><img src="'.TURL.'images/status_ok.gif" align="top">&nbsp;&nbsp;'.$lang['status_ok'].'</span><span id="status_err" class="status_err"><img src="'.TURL.'images/status_err.gif" align="top">&nbsp;&nbsp;'.$lang['er_system'].'</span><span id="status_err2" class="status_err"><img src="'.TURL.'images/status_err.gif" align="top">&nbsp;&nbsp;'.$lang['er_database2'].'</span></div>
	<div class="loading_div"><span id="loading"><img src="'.TURL.'images/waitt.gif" align="top"></span>&nbsp;</div>
	<div id="sounder" style="width:0;height:0;visibility:hidden;overflow:hidden;"></div>
</div>

<script type="text/javascript">
var seconds=0, minutes=0, hours =0, guest=new Array();
var sys_status=1, ajaxpending = "'. $ajaxpending .'",'. $js_var .';
var lock = 0, tt = 0, ttt = 0, tttt = 0, ttttt = 0, msgId = 0, flashtitle_step = 0, allow_sound=1, response_tout = 0, ajax_last = 0, ajaxB="0", ajaxI="0", ajaxU="0", ajaxC="0";
var refresh_time = "'. $_CFG['cUpdate'] .'";
var sound=\'<object data="'. TURL .'sound.swf" type="application/x-shockwave-flash" width="1" height="1" style="visibility:hidden"><param name="movie" value="'. TURL .'sound.swf" /><param name="menu" value="false" /><param name="quality" value="high" /></object>\';
var x_win_content =\''.$x_win_content.'\';

_attachEvent(window, "load", timer_start, document);
_attachEvent(window, "beforeunload", setOffline);
_attachEvent(window, "unload", setOffline);
initObj();
setOnline();

var eWelive_list = $("welive_list");
if(eWelive_list){
	eWelive_list.onmouseover = function(e){
		var obj = GetObjByE(e);
		if (obj){
			if (obj.parentNode.tagName.toLowerCase() == "tr") {
				var row = obj.parentNode;
			} else { return false;}

			for (i = 0; i < row.cells.length; i++){
				if (row.cells[i].tagName.toLowerCase() != "th") row.cells[i].style.backgroundColor = \'#EEEEEE\';
			}
		}
	}

	eWelive_list.onmouseout = function(e){
		var obj = GetObjByE(e);
		if (obj){
			if (obj.parentNode.tagName.toLowerCase() == "tr") {
				var row = obj.parentNode;
			} else { return false;}

			for (i = 0; i < row.cells.length; i++){
				if (row.cells[i].tagName.toLowerCase() != "th") row.cells[i].style.backgroundColor = \'#FFF\';
			}
		}
	}
}
</script>
</body>
</html>';

?>