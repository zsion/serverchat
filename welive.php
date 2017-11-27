<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

include('includes/welive.Core.php');

header_nocache();

$iframe_height = $_CFG['cPanalHeight'];
$thisUrl = base64_encode($_SERVER['HTTP_REFERER']);

if($_CFG['cActived']){

	echo 'var welive_lastScrollY = -108;
	var panel_status = welive_getCookie("PANEL' . COOKIE_KEY . '");

	function welive_setCookie (name,value,days) {
		if (days) {
			var date = new Date();
			date.setTime(date.getTime()+(days*24*3600*1000));
			var expires = "; expires="+date.toGMTString();
		}else{
			var expires = "";
		}

		document.cookie = name+"="+value+expires+"; path=/";
	}

	function welive_getCookie(name){
		var arr = document.cookie.match(new RegExp("(^| )"+name+"=([^;]*)(;|$)"));
		if(arr != null) return unescape(arr[2]); return 1;
	}

	function welive_close_panel(){
		document.getElementById("welive-righDiv").style.display="none";
		document.getElementById("welive-closeDiv").style.display="block";
		welive_setCookie("PANEL' . COOKIE_KEY . '", 0, 1);
	}

	function welive_open_panel(){
		document.getElementById("welive-righDiv").style.display="block";
		document.getElementById("welive-closeDiv").style.display="none";
		welive_setCookie("PANEL' . COOKIE_KEY . '", 1, 1);
	}

	function welive_move(){
		var diffY;
		if (document.documentElement && document.documentElement.scrollTop){
			diffY = document.documentElement.scrollTop;
		}else if (document.body){
			diffY = document.body.scrollTop;
		}
		percent=0.1*(diffY-welive_lastScrollY);

		if(percent>0){
			percent=Math.ceil(percent);
		}else{
			percent=Math.floor(percent);
		}
		document.getElementById("welive-righDiv").style.top = parseInt(document.getElementById("welive-righDiv").style.top)+percent+"px";
		document.getElementById("welive-closeDiv").style.top = parseInt(document.getElementById("welive-closeDiv").style.top)+percent+"px";
		welive_lastScrollY=welive_lastScrollY+percent;
	}

	window.setInterval("welive_move()",1);

	var welive_panel_top = "<style type=\"text\/css\">#welive-righDiv,#welive-closeDiv{padding:0px;position:absolute;}</style>" +

	"<div id=\"welive-closeDiv\" style=\"z-index:200018;width:60px;height:60px;top:108px;right:0px;display:" + (panel_status == 1? "none" : "block") + ";\">" +

	"<div style=\"width:60px;height:60px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.TURL.'images/panel_open_bg.png\', sizingMethod=\'scale\');background:url(\''.TURL.'images/panel_open_bg.png\') !important;background:;\"><div style=\"position:absolute;right:12px;top:12px;\"><a onClick=\"welive_open_panel();return false;\" style=\"cursor:pointer;\" title=\"Open Panel\"><img src=\"'.TURL.'images/panel_open.png\" style=\"border:0;\" onMouseOver=\"this.src=\''.TURL.'images/panel_open2.png\';\" onMouseOut=\"this.src=\''.TURL.'images/panel_open.png\';\"></a></div></div></div>" +

	"<div id=\"welive-righDiv\" style=\"z-index:200008;width:168px;top:108px;right:0px;display:" + (panel_status == 1? "block" : "none") + ";\">" +

	"<div style=\"height:30px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.TURL.'images/panel_top.png\', sizingMethod=\'scale\');background:url(\''.TURL.'images/panel_top.png\') !important;background:;\"><div style=\"position:absolute;left:12px;top:9px;\"><img src=\"'.TURL.'images/' . Iif(IS_CHINESE, 'panel_title.png', 'panel_title_en.png'). '\" style=\"border:0;\"></div><div style=\"position:absolute;right:9px;top:9px;\"><a onClick=\"welive_close_panel();return false;\" style=\"cursor:pointer;\" title=\"Close\"><img src=\"'.TURL.'images/panel_close.png\" style=\"border:0;\"  onMouseOver=\"this.src=\''.TURL.'images/panel_close2.png\';\" onMouseOut=\"this.src=\''.TURL.'images/panel_close.png\';\"></a></div></div>";

	var welive_panel_main = "<div style=\"width:144px;height:100%;padding:0 12px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.TURL.'images/panel_main.png\', sizingMethod=\'scale\');background:url(\''.TURL.'images/panel_main.png\') !important;background:;\"><div style=\"position:relative;width:142px;height:100%;background:#fff;border:1px solid #666;padding:0;margin:0;\"><iframe id=\"welive_main_frame\" src=\"'.BASEURL.'online.php?url='.$thisUrl.'\" frameBorder=\"0\" style=\"margin:0;padding:0;width:100%;height:'.$iframe_height.'px;overflow:hidden;border:none;background:#FFF;\" scrolling=\"no\"></iframe></div></div>";

	var welive_panel_foot = "<div style=\"height:12px;overflow:hidden;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=\''.TURL.'images/panel_foot.png\', sizingMethod=\'scale\');background:url(\''.TURL.'images/panel_foot.png\') !important;background:;\"></div></div>";
	
	document.write(welive_panel_top);
	document.write(welive_panel_main);
	document.write(welive_panel_foot);';

}

?>

