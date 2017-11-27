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

$action = ForceIncomingString('action', 'default');
if(IsPost('updateusers')) $action= 'updateusers';
if(IsPost('deleteusers')) $action= 'deleteusers';

PrintHeader($userinfo['username'], 'users');

$cache_errortitle = '更新客服缓存错误';
$cache_errors = '用户信息已保存到数据库, 但更新在线客服缓存文件失败, 前台客服小面板状态无法更新! 请检查cache/目录是否存在或可写?';

//########### UPDATE OR ADD USER ###########
if($action == 'insertuser' OR $action == 'updateuser'){
	$userid          = ForceIncomingInt('userid');
	$usergroupid     = ForceIncomingInt('usergroupid');
	$activated       = ForceIncomingInt('activated');
	$displayorder          = ForceIncomingInt('displayorder');
	$username        = ForceIncomingString('username');
	$password        = ForceIncomingString('password');
	$passwordconfirm = ForceIncomingString('passwordconfirm');
	$userfrontname        = ForceIncomingString('userfrontname');
	$userfrontename        = ForceIncomingString('userfrontename');
	$infocn        = ForceIncomingString('infocn');
	$infoen        = ForceIncomingString('infoen');
	$advcn        = ForceIncomingString('advcn');
	$adven        = ForceIncomingString('adven');

	if(strlen($username) == 0){
		$errors[] = '请输入用户名!';
	}elseif(!IsName($username)){
		$errors[] = '用户名存在非法字符!';
	}elseif($DB->getOne("SELECT userid FROM " . TABLE_PREFIX . "user WHERE type = 1 AND username = '$username' AND userid != '$userid'")){
		$errors[] = '用户名已存在, 请重新输入!';
	}

	if($action == 'updateuser'){
		if(strlen($password) OR strlen($passwordconfirm)){
			if(!IsPass($password)){
				$errors[] = '密码存在非法字符!';
			}elseif(strcmp($password, $passwordconfirm)){
				$errors[] = '两次输入的密码不相同!';
			}
		}
	}else{
		if(strlen($password) == 0){
			$errors[] = '请输入密码!';
		}elseif(!IsPass($password)){
			$errors[] = '密码存在非法字符!';
		}elseif($password != $passwordconfirm){
			$errors[] = '两次输入的密码不相同!';
		}
	}

	if(strlen($userfrontname) == 0){
		$errors[] = '请输入中文姓名!';
	}

	if(strlen($userfrontename) == 0){
		$errors[] = '请输入英文姓名!';
	}


	if(isset($errors)){
		$errortitle = Iif($userid, '编辑用户错误', '添加用户错误');
		$action = Iif($userid, 'edituser', 'adduser');
	}else{
		if($action == 'updateuser'){
			$DB->exe("UPDATE " . TABLE_PREFIX . "user SET username    = '$username',
			".Iif($userid != 1 AND $userid != $userinfo['userid'], "usergroupid = '$usergroupid', activated = '$activated',")."
			".Iif($password, "password = '" . md5($password) . "',")."
			displayorder       = '$displayorder',
			userfrontname       = '$userfrontname',
			userfrontename       = '$userfrontename',
			infocn       = '$infocn',
			infoen       = '$infoen',
			advcn       = '$advcn',
			adven       = '$adven'										 
			WHERE userid      = '$userid'");
		}else{
			$DB->exe("INSERT INTO " . TABLE_PREFIX . "user (usergroupid, username, type, password, activated, userfrontname, userfrontename, infocn, infoen, advcn, adven) VALUES ('$usergroupid', '$username', 1, '".md5($password)."', 1, '$userfrontname', '$userfrontename', '$infocn', '$infoen', '$advcn', '$adven')");

			$newuserid = $DB->insert_id();
			$DB->exe("UPDATE " . TABLE_PREFIX . "user SET displayorder = '$newuserid' WHERE userid = '$newuserid'");
		}

		if(!storeCache()){ //更新小面板在线客服缓存文件
			$errortitle = $cache_errortitle;
			$errors = $cache_errors;
			$action = Iif($userid, 'edituser', 'adduser');
		}else{
			GotoPage('admin.users.php', 1);
		}
	}
}

//########### UPDATE OR ADD QQ MSN SKYPE ###########
if($action == 'insertqms' OR $action == 'updateqms'){
	$userid          = ForceIncomingInt('userid');
	$usergroupid     = ForceIncomingInt('usergroupid');
	$activated       = ForceIncomingInt('activated');
	$displayorder          = ForceIncomingInt('displayorder');
	$username        = ForceIncomingString('username');
	$userfrontname        = ForceIncomingString('userfrontname');
	$userfrontename        = ForceIncomingString('userfrontename');
	$type          = ForceIncomingInt('type');

	if(strlen($username) == 0){
		$errors[] = '请输入特殊客服的名称!';
	}

	if(isset($errors)){
		$errortitle = Iif($userid, '编辑特殊客服错误', '添加特殊客服错误');
		$action = Iif($userid, 'editqms', 'addqms');
	}else{
		if($action == 'updateqms'){
			$DB->exe("UPDATE " . TABLE_PREFIX . "user SET usergroupid = '$usergroupid',
			displayorder       = '$displayorder',
			username    = '$username',
			type    = '$type',
			activated = '$activated',
			userfrontname = '$userfrontname',
			userfrontename = '$userfrontename'
			WHERE userid      = '$userid'");
		}else{
			$DB->exe("INSERT INTO " . TABLE_PREFIX . "user (usergroupid, username, type, activated, userfrontname, userfrontename) VALUES ('$usergroupid', '$username', '$type', 1, '$userfrontname', '$userfrontename')");

			$newuserid = $DB->insert_id();
			$DB->exe("UPDATE " . TABLE_PREFIX . "user SET displayorder = '$newuserid' WHERE userid = '$newuserid'");
		}

		if(!storeCache()){ //更新小面板在线客服缓存文件
			$errortitle = $cache_errortitle;
			$errors = $cache_errors;
			$action = Iif($userid, 'editqms', 'addqms');
		}else{
			GotoPage('admin.users.php', 1);
		}
	}
}


//########### UPDATE users ###########

if($action == 'updateusers'){
	$userids   = $_POST['userids'];
	$displayorders   = $_POST['displayorders'];
	$activateds   = $_POST['activateds'];

    for($i = 0; $i < count($userids); $i++){
		$DB->exe("UPDATE " . TABLE_PREFIX . "user SET displayorder = '".ForceInt($displayorders[$i])."',
		activated = '".Iif($userids[$i] == 1 OR $userids[$i] == $userinfo['userid'], 1, ForceInt($activateds[$i]))."'
		WHERE userid = '".ForceInt($userids[$i])."'");
    }

	if(!storeCache()){ //更新小面板在线客服缓存文件
		$errortitle = $cache_errortitle;
		$errors = $cache_errors;
		$action = 'default';
	}else{
		GotoPage('admin.users.php', 1);
	}
}

//########### DELETE users ###########

if($action == 'deleteusers'){
	$deleteuserids   = $_POST['deleteuserids'];

    for($i = 0; $i < count($deleteuserids); $i++){
		$DB->exe("DELETE FROM " . TABLE_PREFIX . "user WHERE userid <>1 AND userid = '".ForceInt($deleteuserids[$i])."'");
    }

	if(!storeCache()){ //更新小面板在线客服缓存文件
		$errortitle = $cache_errortitle;
		$errors = $cache_errors;
		$action = 'default';
	}else{
		GotoPage('admin.users.php', 1);
	}
}

// ############################ DISPLAY QQ MSN SKYPE FORM #############################

if($action == 'editqms' OR $action == 'addqms'){

	$userid = ForceIncomingInt('userid');

	if(isset($errors)){
		PrintErrors($errors, $errortitle);

		$user = array('userid'   => $userid,
			  'usergroupid'  => Iif($userid == $userinfo['usergroupid'], $userinfo['usergroupid'], $usergroupid),
			  'activated'  => Iif($userid == $userinfo['userid'], $userinfo['activated'], $activated),
			  'displayorder'     => $displayorder,
			  'username'     => $username,
			  'userfrontname'     => $userfrontname,
			  'userfrontename'     => $userfrontename,
			  'type'     => $type);

	} else if($userid) {
		$user = $DB->getOne("SELECT * FROM " . TABLE_PREFIX . "user WHERE userid = '$userid'");
	}else{
		$user = array('userid' => 0, 'activated' => 1);
	}

	$info = '<font class=red> * 必填项</font>';

	$getgroups = $DB->query("SELECT usergroupid, groupname FROM " . TABLE_PREFIX . "usergroup WHERE usergroupid<> 1 ORDER BY usergroupid");

	$usergroupselect = '<select name="usergroupid">';
	while($usergroup = $DB->fetch($getgroups)) {
		$usergroupselect .= '<option value="' . $usergroup['usergroupid'] . '" ' . Iif($user['usergroupid'] == $usergroup['usergroupid'], ' SELECTED') . '>' . $usergroup['groupname'] . '</option>';
	}
	$usergroupselect .= '</select>';

	echo '<form method="post" action="admin.users.php">
	<input type="hidden" name="action" value="' . Iif($userid, 'updateqms', 'insertqms') . '">
	<input type="hidden" name="userid" value="' . $user['userid'] . '">
	<table id="welive_list" border="0" cellpadding="0" cellspacing="0" class="maintable">
	<thead>
	<tr>
	<th colspan="2">'.Iif($userid, '编辑QQ, MSN或Skype等', '添加QQ, MSN或Skype等').'</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td width="360">所属客服群组:</td>
	<td>'.$usergroupselect.'</td>
	</tr>
	<tr>
	<td width="360">选择类别:</td>
	<td><select name="type"><option value="2" ' . Iif($user['type'] == 2, ' SELECTED') . '>QQ号码</option><option value="3" ' . Iif($user['type'] == 3, ' SELECTED') . '>MSN帐号</option><option value="4" ' . Iif($user['type'] == 4, ' SELECTED') . '>Skype帐号</option><option value="5" ' . Iif($user['type'] == 5, ' SELECTED') . '>旺旺帐号</option></select></td>
	</tr>
	<tr>
	<td>帐号名称:</td>
	<td><input type="text" name="username" value="'.$user['username'].'" size="30" maxlength="64">'.$info.'</td>
	</tr>
	<tr>
	<td>客服小面板中显示的中文名称:</td>
	<td><input type="text" name="userfrontname" value="'.$user['userfrontname'].'" size="30"><font class=gey> * 留空将显示账号名称</font></td>
	</tr>
	<tr>
	<td>客服小面板中显示的英文名称:</td>
	<td><input type="text" name="userfrontename" value="'.$user['userfrontename'].'" size="30"><font class=gey> * 留空将显示账号名称</font></td>
	</tr>	';

	if($userid){
		echo '<tr>
		<td>是否激活?</td>
		<td><input type="checkbox" name="activated" value="1" ' . Iif($user['activated'] == 1, 'checked="checked"') .'></td>
		</tr>
		<tr>
		<td>显示顺序:</td>
		<td><input type="text" name="displayorder" value="'.$user['displayorder'].'" size="10"></td>
		</tr>	';
	}

	echo '
	</tbody>
	</table>';

	PrintSubmit(Iif($userid, '保存更新', '添加客服'));
	
}

// ############################ DISPLAY USER FORM #############################

if($action == 'edituser' OR $action == 'adduser'){

	$userid = ForceIncomingInt('userid');

	if(isset($errors)){
		PrintErrors($errors, $errortitle);

		$user = array('userid'   => $userid,
			  'usergroupid'  => Iif($userid == $userinfo['usergroupid'], $userinfo['usergroupid'], $usergroupid),
			  'activated'  => Iif($userid == $userinfo['userid'], $userinfo['activated'], $activated),
			  'displayorder'     => $displayorder,
			  'username'     => $username,
			  'userfrontname'     => $userfrontname,
			  'userfrontename'     => $userfrontename,
			  'infocn'     => $_POST['infocn'],
			  'infoen'     => $_POST['infoen'],
			  'advcn'     => $_POST['advcn'],
			  'adven'     => $_POST['adven']);

	} else if($userid) {
		$user = $DB->getOne("SELECT * FROM " . TABLE_PREFIX . "user WHERE userid = '$userid'");
	}else{
		$user = array('userid' => 0, 'activated' => 1);
	}

	$info = '<font class=red> * 必填项</font>';
	$info_pass = Iif($userid, '<font class=green> * 不修改请留空</font>', $info);

	$getgroups = $DB->query("SELECT usergroupid, groupname FROM " . TABLE_PREFIX . "usergroup ORDER BY usergroupid");

	$usergroupselect = '<select name="usergroupid" ' . Iif($user['userid'] == 1 OR $userid == $userinfo['userid'], 'disabled') .'>';
	while($usergroup = $DB->fetch($getgroups)) {
		$usergroupselect .= '<option value="' . $usergroup['usergroupid'] . '" ' . Iif($user['usergroupid'] == $usergroup['usergroupid'], ' SELECTED') . '>' . $usergroup['groupname'] . '</option>';
	}
	$usergroupselect .= '</select>';

	echo '<form method="post" action="admin.users.php">
	<input type="hidden" name="action" value="' . Iif($userid, 'updateuser', 'insertuser') . '">
	<input type="hidden" name="userid" value="' . $user['userid'] . '">
	<table id="welive_list" border="0" cellpadding="0" cellspacing="0" class="moreinfo">
	<thead>
	<tr>
	<th colspan="2">'.Iif($userid, '编辑用户', '添加用户').'</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td width="360">所属客服群组:</td>
	<td>'.$usergroupselect.'</td>
	</tr>
	<tr>
	<td>登录名:</td>
	<td><input type="text" name="username" value="'.$user['username'].'" size="30">'.$info.'</td>
	</tr>';

	if($userid){
		echo '<tr>
		<td>是否激活?</td>
		<td><input type="checkbox" ' . Iif($user['userid'] == 1 OR $userid == $userinfo['userid'], 'disabled') .' name="activated" value="1" ' . Iif($user['activated'] == 1, 'checked="checked"') .'></td>
		</tr>
		<tr>
		<td>显示顺序:</td>
		<td><input type="text" name="displayorder" value="'.$user['displayorder'].'" size="10"></td>
		</tr>	';
	}

	echo '<tr>
	<td>密码:</td>
	<td><input type="password" name="password" size="30">'.$info_pass.'</td>
	</tr>
	<tr>
	<td>确认密码:</td>
	<td><input type="password" name="passwordconfirm" size="30">'.$info_pass.'</td>
	</tr>
	<tr>
	<td>中文名:</td>
	<td><input type="text" name="userfrontname" value="'.$user['userfrontname'].'" size="30">'.$info.'</td>
	</tr>
	<tr>
	<td>英文名:</td>
	<td><input type="text" name="userfrontename" value="'.$user['userfrontename'].'" size="30">'.$info.'</td>
	</tr>
	<tr>
	<td>中文简介:<BR><span class=note2>说明: 允许使用HTML代码, 如换行可输入&lt;br&gt;<BR>如要插入客服人员的像片, 图片宽度要求不越过106px.</span></td>
	<td><textarea name="infocn" rows="6"  style="width:300px;">'.$user['infocn'].'</textarea></td>
	</tr>
	<tr>
	<td>英文简介:<BR><span class=note2>说明: 同上.</span></td>
	<td><textarea name="infoen" rows="6"  style="width:300px;">'.$user['infoen'].'</textarea></td>
	</tr>
	<tr>
	<td>中文广告:<BR><span class=note2>说明: 同上.</span></td>
	<td><textarea name="advcn" rows="6"  style="width:300px;">'.$user['advcn'].'</textarea></td>
	</tr>
	<tr>
	<td>英文广告:<BR><span class=note2>说明: 同上.</span></td>
	<td><textarea name="adven" rows="6"  style="width:300px;">'.$user['adven'].'</textarea></td>
	</tr>
	</tbody>
	</table>';

	PrintSubmit(Iif($userid, '保存更新', '添加用户'));
	
}

//########### PRINT DEFAULT ###########

if($action == 'default'){
	if(isset($errors)){
		PrintErrors($errors, $errortitle);
	}

	$getgroups = $DB->query("SELECT usergroupid, groupname FROM " . TABLE_PREFIX . "usergroup ORDER BY usergroupid");
	while($usergroup = $DB->fetch($getgroups)) {
		$usergroups[$usergroup['usergroupid']] = $usergroup['groupname'];
	}

	$getusers = $DB->query("SELECT userid, usergroupid, displayorder, username, type, activated, isonline, userfrontname, userfrontename, lastlogin FROM " . TABLE_PREFIX . "user ORDER BY usergroupid, displayorder");

	echo '&nbsp;&nbsp;&nbsp;<a href="admin.users.php?action=adduser">添加新用户</a>&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;<a href="admin.users.php?action=addqms">添加QQ, MSN或Skype等</a>
	<BR><BR><form method="post" action="admin.users.php" name="usersform">
	<table id="welive_list" border="0" cellpadding="0" cellspacing="0" class="maintable">
	<thead>
	<tr>
	<th>显示顺序</th>
	<th>登录名</th>
	<th>状态</th>
	<th>所属群组</th>
	<th>中文名</th>
	<th>英文名</th>
	<th>服务状态</th>
	<th>最后登录</th>
	<th>删除</th>
	</tr>
	</thead>
	<tbody>';

	$types = array('1' => '', '2' => 'QQ', '3' => 'MSN', '4' => 'Skype', '5' => '旺旺');

    while($user = $DB->fetch($getusers)){

		$typename = $types[$user['type']];

		echo '<tr>
		<td>
		<input type="hidden" name="userids[]" value="' . $user['userid'] . '">
		<input type="text" name="displayorders[]" value="' . $user['displayorder'] . '"  size="4"></td>
		</td>
		<td><a href="admin.users.php?action='.Iif($user['type']>1, 'editqms', 'edituser').'&userid='.$user['userid'].'" '.Iif(!$user['activated'], 'class="red"').'>' . $user['username']. '</a>'.Iif($typename, '&nbsp;&nbsp;('.$typename.')').'</td>
		<td>
		<select name="activateds[]">
		<option value="1">正常</option>
		<option style="color:red;" value="0" ' . Iif(!$user['activated'], 'SELECTED', '') . '>禁止</option>
		</select></td>
		<td>' . $usergroups[$user['usergroupid']]. '</td>
		<td>' . Iif($user['userfrontname'], $user['userfrontname'], '-'). '</td>
		<td>' . Iif($user['userfrontename'], $user['userfrontename'], '-'). '</td>
		<td>'.Iif($typename, '-', Iif($user['isonline'], '<span class="green">在线</span>', '离线')).'</td>
		<td>'.Iif($typename, '-', Iif($user['lastlogin'], DisplayDate($user['lastlogin'], '', 1), '从未登录')).'</td>
		<td><input type="checkbox" name="deleteuserids[]" value="'.$user['userid'].'" '.Iif($user['userid'] == 1 OR $user['userid'] == $userinfo['userid'], 'disabled').'></td>
		</tr>';
	}

	echo '</tbody>
	</table>
	<div style="margin-top:20px;text-align:center;">
	<input type="submit" name="updateusers" value=" 保存更新 " />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="submit" name="deleteusers" onclick="return confirm(\'确定删除所选用户吗?\');" value=" 删除用户 " />
	</div>
	</form>';

}

PrintFooter();

?>

