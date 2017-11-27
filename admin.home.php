<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

define('AUTH', true);

include('includes/welive.Core.php');
include(BASEPATH . 'includes/welive.Admin.php');

if($userinfo['usergroupid'] != 1) exit();

$updates = Iif(ForceIncomingInt('check'), 1, 0);

PrintHeader($userinfo['username']);

echo '<br/><div><ul>
<li>欢迎 <u>'.$userinfo['username'].'</u> 进入管理面板! 为了确保系统安全, 请在关闭前点击 <a href="index.php?logout=1" onclick="return confirm(\'确定退出管理面板吗?\');">安全退出</a>!</li>

</ul></div>
<table style="display:none;" border="0" cellpadding="0" cellspacing="0" class="normal" width="600">
<tr>
<td><b>程序名称</b></td>
<td><b>您现在的版本</b></td>
<td><b>最新版本</b></td>
</tr>
<tr>
<td><b>'.APP_NAME.'</b></td>
<td>' . APP_VERSION . '(UTF-8)授权版</td>
<td><span id="welive_latest_versioninfo"></span></td>
</tr>
</table>
<div id="welive_latest_moreinfo"></div>';

if(!$updates){
	echo '<script type="text/javascript">$("welive_latest_versioninfo").innerHTML = "<a href=\"admin.home.php?check=1\">检测最新版本</a>";</script>';
}else{
	echo '<script language="javascript" type="text/javascript" src="http://www.weentech.com/welive_version/versioninfo.js?temp='.rand().'"></script>
	<script type="text/javascript">
	if(typeof(v) == "undefined"){
		$("welive_latest_versioninfo").innerHTML = "<font class=red>无法连接!</font>";
	}else{
		var welive_old_version = parseInt("' . APP_VERSION . '".replace(/\./g,""));
		var welive_latest_version = parseInt(v.replace(/\./g,""));
		
		if(welive_old_version < welive_latest_version ){
			$("welive_latest_versioninfo").innerHTML = "<font class=red>"+v+"</font>";
			$("welive_latest_moreinfo").innerHTML = "<br>请登录 <a href=\"http://www.weentech.com/bbs/\" target=\"_blank\">闻泰网络 weentech.com</a> 下载升级!";
		}else{
			$("welive_latest_versioninfo").innerHTML = "<font class=green>暂无更新!</font>";
		}
	
	}
	</script>';
}

echo '<BR><BR><BR>
<table id="welive_list" border="0" cellpadding="0" cellspacing="0" class="maintable">
<thead>
	<tr>
		<th><B>客服基本使用说明:</B></th>
	</tr>
</thead>
<tbody>
	<tr>
		<td>1. 系统默认安装后, 客服人员的登录密码与管理员相同, 请自行修改(只有客服登录后方可提供在线服务).</td>
	</tr>
	<tr>
		<td>2. 在客服操作面板, 按Esc键: 快速关闭当前访客小窗口.</td>
	</tr>
	<tr>
		<td>3. 在客服操作面板, 按Ctrl + Enter键: 快速提交当前访客小窗口中输入的内容.</td>
	</tr>
	<tr>
		<td>4. 在客服操作面板, 按Ctrl + 下箭头键: 快速最小化访客小窗口.</td>
	</tr>
	<tr>
		<td>5. 在客服操作面板, 按Ctrl + 上箭头键: 快速展开访客小窗口.</td>
	</tr>
	<tr>
		<td>6. 在客服操作面板, 按Ctrl + 左或右箭头键: 快速在展开的访客小窗口中切换.</td>
	</tr>
	<tr>
		<td>7. 在客服操作面板, 点击"挂起"后, 当访客点击当前客服时, 系统将检测是否有同组的, 在线且未挂起的客服, 如果有则自动转接到其他客服(挂起功能相当于忙碌自动转接功能).</td>
	</tr>
</tbody>
</table>';


PrintFooter();

?>

