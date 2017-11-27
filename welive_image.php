<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

include('includes/welive.Core.php');

function checkOnline($welive_onlines){
	foreach($welive_onlines AS $usergroup){
		foreach ($usergroup['user'] AS $user) {
			if($user['isonline']) return true;
		}
	}

	return false;
}


header_nocache();

$online_cache_file = BASEPATH . "cache/online_cache.php";
@include($online_cache_file);

if(isset($welive_onlines) && is_array($welive_onlines)){
	$getone = checkOnline($welive_onlines);
}else{
	$getone = $DB->getOne("SELECT u.userid FROM " . TABLE_PREFIX . "usergroup ug LEFT JOIN " . TABLE_PREFIX . "user u ON ug.usergroupid = u.usergroupid WHERE  ug.usergroupid <>1 AND ug.activated = 1 AND u.activated = 1 AND u.isonline =1");
}


if($_CFG['cActived']){
	$thisUrl = urlencode(base64_encode($_SERVER['HTTP_REFERER']));
	$iframe_height = $_CFG['cPanalHeight'];

	echo 'var welive_tt = 0;
	function welive_intval(v){
		v = parseInt(v);
		return isNaN(v) ? 0 : v;
	}

	function welive_getpos(e){
		var l = 0;
		var t  = 0;
		while (e.offsetParent){
			l += e.offsetLeft + (e.currentStyle?welive_intval(e.currentStyle.borderLeftWidth):0);
			t += e.offsetTop  + (e.currentStyle?welive_intval(e.currentStyle.borderTopWidth):0);
			e = e.offsetParent;
		}
		l += e.offsetLeft + (e.currentStyle?welive_intval(e.currentStyle.borderLeftWidth):0);
		t  += e.offsetTop  + (e.currentStyle?welive_intval(e.currentStyle.borderTopWidth):0);
		return {x:l, y:t};
	}

	function ShowWeLive(me) {
		clearTimeout(welive_tt);
		var ei = document.getElementById("welive-righDiv2");
		if(!ei) return;

		var me_pos = welive_getpos(me);

		ei.style.top  = me_pos.y + me.offsetHeight + "px";
		ei.style.left  = me_pos.x + "px";
		ei.style.display = "block";

		var welive_main2 = document.getElementById("welive_main2");
		if(welive_main2){
			welive_main2.innerHTML = "<iframe id=\"welive_main_frame2\" src=\"'.BASEURL.'online.php?url='.$thisUrl.'\" frameBorder=\"0\" style=\"margin:0;padding:0;width:100%;height:'.$iframe_height.'px;overflow:hidden;border:none;background:#FFF;\" scrolling=\"no\"></iframe>";
		}

		ei.onmouseover = function() {
			clearTimeout(welive_tt);
		};

		ei.onmouseout = function() {
			clearTimeout(welive_tt);
			welive_tt = setTimeout(function(){
				ei.style.display = "none";
			}, 200);
		};

		me.onmouseover = function() {
			clearTimeout(welive_tt);
		};

		me.onmouseout = function() {
			clearTimeout(welive_tt);
			welive_tt = setTimeout(function(){
				ei.style.display = "none";
			}, 800);
		};
	}

	var welive_panel_top = "<img src=\"'.BASEURL.'images/welive_' . Iif($getone, 'on_', 'off_') . Iif(IS_CHINESE, 'cn', 'en'). '.gif\" border=\"0\" onclick=\"ShowWeLive(this);\" style=\"cursor:pointer;\">" +

	"<div id=\"welive-righDiv2\" style=\"padding:0px;position:absolute;z-index:200008;width:168px;display:none;\">" +

	"<div style=\"height:30px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.TURL.'images/panel_top.png\', sizingMethod=\'scale\');background:url(\''.TURL.'images/panel_top.png\') !important;background:none;\"><div style=\"position:absolute;left:12px;top:9px;\"><img src=\"'.TURL.'images/' . Iif(IS_CHINESE, 'panel_title.png', 'panel_title_en.png'). '\" style=\"border:0;\"></div></div>";

	var welive_panel_main = "<div style=\"width:144px;height:100%;padding:0 12px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.TURL.'images/panel_main.png\', sizingMethod=\'scale\');background:url(\''.TURL.'images/panel_main.png\') !important;background:none;\"><div id=\"welive_main2\" style=\"position:relative;width:142px;height:100%;background:#fff;border:1px solid #666;padding:0;margin:0;\"></div></div>";

	var welive_panel_foot = "<div style=\"height:12px;overflow:hidden;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.TURL.'images/panel_foot.png\', sizingMethod=\'scale\');background:url(\''.TURL.'images/panel_foot.png\') !important;background:none;\"></div></div>";
	
	document.write(welive_panel_top);
	document.write(welive_panel_main);
	document.write(welive_panel_foot);';

}

?>

