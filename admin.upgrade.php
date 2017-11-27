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

$action = ForceIncomingString('action');

$upgradefinished = false;

// ############################### RUN  UPGRADES ###############################

if($action == 'upgraderunning'){
	include(BASEPATH . 'upgrade/upgrade.php');
	$upgradefinished = UpgradeSystem();
}

if($action == 'deleteupgradefiles'){
	@unlink(BASEPATH . 'upgrade/upgrade.php');
	@unlink(BASEPATH . 'upgrade/version.php');
}


// ############################### DISPLAY UPGRADES #############################

$availableupgrades = 0;

if(file_exists(BASEPATH . 'upgrade/upgrade.php') and file_exists(BASEPATH . 'upgrade/version.php')){
	$availableupgrades=1;
}

if($availableupgrades){
	$updatestatus = '<span class=blue>已检测到升级程序, 请按提示进行升级!</span>';
}else if($upgradefinished){
	$updatestatus = '<span class=green>系统升级已完成!</span>';
}else{
	$updatestatus = '<span class=note>暂无可用的升级程序!</span>';
}


PrintHeader($userinfo['username'], 'upgrade');

echo '<div>'.$updatestatus.'
<ul><li>请严格按升级说明进行系统升级, 升级说明一般随附在升级包中.</li>
<li>升级过程一般是先将升级包解压后, 设置FTP工具以 <span class=note>二进制方式</span> 上传到网站替换原文件, 然后在后台运行升级程序.</li>
<li>建议: 升级完成后删除upgrade目录内的所有文件.</li>
</ul>
</div>';

BR(3);

echo '<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr>
<td width="70%" valign="top" align="center">';

if($availableupgrades){
	include(BASEPATH . 'upgrade/version.php');
	$disableupgrade    = 'Disabled'; 
	
	$new = str_replace ('.', '', $WeLiveNewVersion);
	$old = str_replace ('.', '', APP_VERSION);

	If(intval ($new) <= intval ($old)){
		$messages = '<font class=red>您现在正在使用的版本高于或等于升级程序中的版本, 无需升级!</font>';
	}else{
		$messages = '';
		$disableupgrade    = 'Enabled'; 
	}
	 
	$availableupgrades++;


	if($upgradefinished){
		echo '<form method="post" action="admin.upgrade.php">
		<input type="hidden" name="action" value="deleteupgradefiles">
		<br><br><font class=blue>系统升级成功! 建议删除升级文件.</font><br><br><br>
		<input type="submit" name="deletefiles" value="删除升级文件"><br><br>
		</form>';
	}else{
		echo '<form method="post" action="admin.upgrade.php">
		<input type="hidden" name="action" value="upgraderunning">
		当前使用中的版本是: ' . APP_VERSION . '<br>
		正要升级到的版本是: <font class=red>' . $WeLiveNewVersion . '</font><br>
		<br><br>
		' . Iif($messages, $messages.'<br><br><br>') . '
		<input type="submit" name="upgrade" value="运行升级程序" '. $disableupgrade .'><br><br>
		</form>';
	}

}else{
		echo '<br><br><b>暂无可用的升级程序!</b><br><br><br>';
}

echo '</td></tr></table>';

PrintFooter();

?>