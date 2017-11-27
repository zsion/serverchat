<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

//if(!defined('WELIVE')) die('File not found!');

// #####################

function PrintHeader($username, $where = 'home') {
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>' . SITE_TITLE . '</title>
	<script type="text/javascript" src="includes/javascript/Ajax.js"></script>
	<script type="text/javascript" src="includes/javascript/Admin.js"></script>
	<link rel="stylesheet" type="text/css" href="templates/support.css">
	<link rel="stylesheet" type="text/css" href="templates/admin.css">
	<link rel="shortcut icon" href="favicon.ico" />
	</head>
	<body>
	<a href="#" name="top"></a>
	<div id="maindiv">
	<div id="header">
	<div class="logo"><img src="'.TURL.'images/logo.gif" align="absmiddle"> ' . SITE_TITLE . '</div>
	<div id="memu"><a href="admin.home.php" '.Iif($where=='home', 'class="on"').' hidefocus="true">首页</a><a href="admin.settings.php" '.Iif($where=='settings', 'class="on"').' hidefocus="true">设置</a><a href="admin.groups.php" '.Iif($where=='groups', 'class="on"').' hidefocus="true">群组</a><a href="admin.users.php" '.Iif($where=='users', 'class="on"').' hidefocus="true">用户</a><a href="admin.automsg.php" '.Iif($where=='automsg', 'class="on"').' hidefocus="true">常用短语</a><a href="admin.comments.php" '.Iif($where=='comments', 'class="on"').' hidefocus="true">留言</a><a href="admin.messages.php" '.Iif($where=='messages', 'class="on"').' hidefocus="true">记录</a><a href="admin.upgrade.php" '.Iif($where=='upgrade', 'class="on"').' hidefocus="true"></a></div>
	<div class="loginout">管理员: <span class=spec>'.$username.'</span>&nbsp;&nbsp;[ <a href="index.php?logout=1" hidefocus="true" onclick="return confirm(\'确定退出管理面板吗?\');">安全退出</a> ]</div>
	</div>
	<div class="contentdiv">
	<div class="welive_div">';
}

// #####################
function PrintFooter() {
	echo '</div>
	</div>
	<div class="blank40"></div>
	</div>
	<div id="footer">
	<div class="copyright">' . COPYRIGHT . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="#top" title="TOP"><img src="'.TURL.'images/btn_top.gif" align="absmiddle" /></a></div>
	</div>
	<script type="text/javascript">
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

	exit();
}

// #####################
function GetLangs($filename = 0) {
	$Languages = array();
	$LangPath = BASEPATH . 'languages/';
	$FolderHandle = @opendir($LangPath);
	while (false !== ($Item = readdir($FolderHandle))) {
		if (filesize($LangPath.$Item) && $Item != '.' 	&& $Item != '..' && substr($Item, -4) == '.php') {
			if (substr($Item, 0, 1) != ".") {
				$Languages[] = Iif($filename, $Item, substr($Item, 0, -4));
			}
		}
	}
	@closedir($LangPath);
	return $Languages;
}

// #####################
function PrintErrors($errors, $errortitle = ''){
	if(is_array($errors)){
		for($i = 0; $i < count($errors); $i++)
			$errorinfo .= ($i + 1) . ') ' . $errors[$i] . '<br />';
	}else {
		$errorinfo = $errors . '<br />';
	}

	echo '<div id="sysinfo_error"><div class="e_lborder"><div class="e_rborder"><div class="e_bborder"><div class="e_blcorner"><div class="e_brcorner"><div class="e_tborder"><div class="e_tlcorner"><div class="e_trcorner">'.Iif($errortitle, '<B><U>'.$errortitle.':</U></B><BR><BR>').$errorinfo.'	</div></div></div></div></div></div></div></div></div>';
}

// #####################
function PrintSuss($success, $successtitle = ''){
	if(is_array($success)){
		for($i = 0; $i < count($success); $i++)
			$successinfo .= ($i + 1) . ') ' . $success[$i] . '<br />';
	}else {
		$successinfo = $success . '<br />';
	}

	echo '<div id="sysinfo_success"><div class="s_lborder"><div class="s_rborder"><div class="s_bborder"><div class="s_blcorner"><div class="s_brcorner"><div class="s_tborder"><div class="s_tlcorner"><div class="s_trcorner">'.Iif($successtitle, '<B><U>'.$successtitle.':</U></B><BR><BR>').$successinfo.'</div></div></div></div></div></div></div></div></div>';
}

// #####################
function GotoPage($gotopage, $timeout = 0) {
	$gotopage = str_replace('&amp;', '&', $gotopage);

	$gotoscript = 'window.location="'.$gotopage.'";';
	$ahref = '<a href="'.$gotopage.'" onclick="javascript:clearTimeout(timerID);">';
	$refreshinfo = '';

	if($timeout == 0){
		echo '<script type="text/javascript">'.$gotoscript.'</script>';
	}else{
		BR(6);
		PrintSuss('页面跳转中 ...<BR>'.$ahref.'<font class=blue>数据更新已完成! 如果页面没有跳转, 请点击这里.</font></a></font>', '操作成功');
		echo '<script type="text/javascript">
		timeout = '.($timeout*10).';
		'.$refreshinfo.'
		function Refresh() {
			timerID = setTimeout("Refresh();", 100);
			if (timeout > 0)
			{
				timeout -= 1;
			}else{
				clearTimeout(timerID);
				'.$gotoscript.'
			}
		}
		Refresh();
		</script>';
	}
	PrintFooter();
	exit();
}

// #####################
function NewObject($ClassName, $Param1 = '', $Param2 = '', $Param3 = '') {
	if (!class_exists($ClassName)) {

		$File = BASEPATH . 'includes/Class.' . $ClassName . '.php';

		if (file_exists($File)) {
			include($File);
		}
	}

	return new $ClassName($Param1, $Param2, $Param3);
}

// #####################
function GetPageList($FileName, $PageCount, $CurrentPage = 1, $PagesToDisplay = 10, $PN01 = '', $PNV01 = '', $PN02 = '', $PNV02 = '', $PN03 = '', $PNV03 = '', $PN04 = '', $PNV04 = '', $PN05 = '', $PNV05 = '') {

	$PreviousText =  '&nbsp;&#60;&#60;&nbsp;';
	$NextText = '&nbsp;&#62;&#62;&nbsp;';

	$Params = '';
	$Params .= Iif($PN01 AND $PNV01, '&'.$PN01.'='.$PNV01);
	$Params .= Iif($PN02 AND $PNV02, '&'.$PN02.'='.$PNV02);
	$Params .= Iif($PN03 AND $PNV03, '&'.$PN03.'='.$PNV03);
	$Params .= Iif($PN04 AND $PNV04, '&'.$PN04.'='.$PNV04);
	$Params .= Iif($PN05 AND $PNV05, '&'.$PN05.'='.$PNV05);

	$iPagesToDisplay = $PagesToDisplay - 2;      
	if ($iPagesToDisplay <= 8) $iPagesToDisplay = 8;

	$MidPoint = ($iPagesToDisplay / 2);

	$FirstPage = $CurrentPage - $MidPoint;
	if ($FirstPage < 1) $FirstPage = 1;

	$LastPage = $FirstPage + ($iPagesToDisplay - 1);

	if ($LastPage > $PageCount) {
		$LastPage = $PageCount;
		$FirstPage = $PageCount - $iPagesToDisplay;
		if ($FirstPage < 1) $FirstPage = 1;
	}

	$sReturn = '<div class="PageListDiv"><ol class="PageList">';
	$Loop = 0;
	$iTmpPage = 0;

	if ($PageCount > 1) {
		if ($CurrentPage > 1) {
			$iTmpPage = $CurrentPage - 1;
			$sReturn .= '<li><a href="' . $FileName . '?p=' . $iTmpPage . $Params . '" class="PagePrev"  onfocus="this.blur()">'.$PreviousText.'</a></li>';
		} else {
			$sReturn .= '<li><span class="NoPagePrev">'.$PreviousText.'</span></li>';
		}

		if ($FirstPage > 2) {
			$sReturn .= '&nbsp;<li><a href="' . $FileName . '?p=1' . $Params . '" onfocus="this.blur()">1</a></li>&nbsp;<li>...</li>';
		} elseif ($FirstPage == 2) {
			$sReturn .= '&nbsp;<li><a href="' . $FileName . '?p=1' . $Params . '" onfocus="this.blur()">1</a></li>';
		}

		$Loop = 0;

		for ($Loop = 1; $Loop <= $PageCount; $Loop++) {
			if (($Loop >= $FirstPage) && ($Loop <= $LastPage)) {
				if ($Loop == $CurrentPage) {
					$sReturn .= '&nbsp;<li><span class="CurrentPage">'.$Loop.'</span></li>';
				} else {
					$sReturn .= '&nbsp;<li><a href="' . $FileName . '?p=' . $Loop . $Params . '" onfocus="this.blur()">'.$Loop.'</a></li>';
				}
			}
		}

		if ($CurrentPage < ($PageCount - $MidPoint) && $PageCount > $PagesToDisplay - 1) {
			$sReturn .= '&nbsp;<li>...</li>&nbsp;<li><a href="' . $FileName . '?p=' . $PageCount . $Params . '" onfocus="this.blur()">'.$PageCount.'</a></li>';
		} else if ($CurrentPage == ($PageCount - $MidPoint) && ($PageCount > $PagesToDisplay)) {
			$sReturn .= '&nbsp;<li><a href="' . $FileName . '?p=' . $PageCount . $Params . '" onfocus="this.blur()">'.$PageCount.'</a></li>';
		}

		if ($CurrentPage != $PageCount) {
			$iTmpPage = $CurrentPage + 1;
			$sReturn .= '&nbsp;<li><a href="' . $FileName . '?p=' . $iTmpPage . $Params . '" class="PageNext" onfocus="this.blur()">'.$NextText.'</a></li>';
		} else {
			$sReturn .= '&nbsp;<li><span class="NoPageNext">'.$NextText.'</span></li>';
		}
	} else {
		$sReturn .= '<li>&nbsp;</li>';
	}

	$sReturn .= '</ol></div>';

	return  $sReturn;
}

// #####################
function PrintSubmit($value, $name ='') {
	echo '<div style="margin-top:20px;text-align:center;">'.Iif($name, '<input type="hidden" name="'.$name.'" value="'.$name.'" />').'<input type="submit" value="&nbsp;'.$value.'&nbsp;" /></div></form>';
}

// #####################
function BR($n=1) {
	for($i = 0; $i < $n; $i++)
		echo '<BR>';
}

// #####################
function ShortTitle($string, $length=81){
	if(strlen($string) == 0) 	return '';
	if(strlen($string) <= $length) return $string;

	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);
	$strcut = '';

	$n = $tn = $noc = 0;
	while($n < strlen($string)) {
		$t = ord($string[$n]);
		if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
			$tn = 1; $n++; $noc++;
		} elseif(194 <= $t && $t <= 223) {
			$tn = 2; $n += 2; $noc += 2;
		} elseif(224 <= $t && $t < 239) {
			$tn = 3; $n += 3; $noc += 2;
		} elseif(240 <= $t && $t <= 247) {
			$tn = 4; $n += 4; $noc += 2;
		} elseif(248 <= $t && $t <= 251) {
			$tn = 5; $n += 5; $noc += 2;
		} elseif($t == 252 || $t == 253) {
			$tn = 6; $n += 6; $noc += 2;
		} else {
			$n++;
		}

		if($noc >= $length) break;
	}

	if($noc > $length) $n -= $tn;

	$strcut = substr($string, 0, $n);
	$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	return $strcut.'...';
}

?>