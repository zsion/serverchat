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
if(IsPost('updatemsgs')) $action= 'updatemsgs';
if(IsPost('deletemsgs')) $action= 'deletemsgs';


PrintHeader($userinfo['username'], 'automsg');

//########### UPDATE MESSAGES ###########

if($action == 'updatemsgs'){
	$msgids = $_POST['msgids'];
	$ordernums   = $_POST['ordernums'];
	$activateds   = $_POST['activateds'];
	$msgs   = $_POST['msgs'];

	$page = ForceIncomingInt('p');

	for($i = 0; $i < count($msgids); $i++){
		$DB->exe("UPDATE " . TABLE_PREFIX . "automsg SET ordernum = '".ForceInt($ordernums[$i])."',
		activated = '".ForceInt($activateds[$i])."',
		msg = '".ForceString($msgs[$i])."'
		WHERE msgid = '".ForceInt($msgids[$i])."'");

	}

	GotoPage('admin.automsg.php'.Iif($page, '?p='.$page), 1);
}

//########### DELETE MESSAGES ###########

if($action == 'deletemsgs'){
	$deletemsgids = $_POST['deletemsgids'];
	$page = ForceIncomingInt('p');

	for($i = 0; $i < count($deletemsgids); $i++){
		$DB->exe("DELETE FROM " . TABLE_PREFIX . "automsg WHERE msgid = '".ForceInt($deletemsgids[$i])."'");
	}

	GotoPage('admin.automsg.php'.Iif($page, '?p='.$page), 1);
}

//########### UPDATE OR ADD MSG ###########
if($action == 'insertmsg' OR $action == 'updatemsg'){
	$msgid          = ForceIncomingInt('msgid');
	$activated       = ForceIncomingInt('activated');
	$ordernum       = ForceIncomingInt('ordernum');
	$msg        = ForceIncomingString('msg');

	$deletemsg       = ForceIncomingInt('deletemsg');

	if($deletemsg){
		$DB->exe("DELETE FROM " . TABLE_PREFIX . "automsg WHERE msgid = '$msgid'");
		GotoPage('admin.automsg.php', 1);
	}

	if(strlen($msg) == 0){
		$errors = '请输入短语内容!';
	}

	if(isset($errors)){
		$errortitle = Iif($msgid, '编辑短语错误', '添加短语错误');
		$action = Iif($msgid, 'editmsg', 'addmsg');
	}else{
		if($action == 'updatemsg'){
			$DB->exe("UPDATE " . TABLE_PREFIX . "automsg SET ordernum    = '$ordernum',
			activated       = '$activated',
			msg       = '$msg'										 
			WHERE msgid      = '$msgid'");
		}else{
			$DB->exe("INSERT INTO " . TABLE_PREFIX . "automsg (ordernum, activated, msg) VALUES (0, 1, '$msg')");

			$newmsgid = $DB->insert_id();
			$DB->exe("UPDATE " . TABLE_PREFIX . "automsg SET ordernum = '$newmsgid' WHERE msgid = '$newmsgid'");
		}

		GotoPage('admin.automsg.php', 1);
	}
}

//########### ADD OR EDIT MESSAGE ###########

if($action == 'editmsg' OR $action == 'addmsg'){

	$msgid = ForceIncomingInt('msgid');

	if(isset($errors)){
		PrintErrors($errors, $errortitle);

		$msg = array('msgid'   => $msgid,
			  'activated'  => $activated,
			  'ordernum'     => $ordernum,
			  'msg'     => $_POST['msg']);

	} else if($msgid) {
		$msg = $DB->getOne("SELECT * FROM " . TABLE_PREFIX . "automsg WHERE msgid = '$msgid'");
	}else{
		$msg = array('msgid' => 0, 'activated' => 1);
	}

	echo '<form method="post" action="admin.automsg.php">
	<input type="hidden" name="action" value="' . Iif($msgid, 'updatemsg', 'insertmsg') . '">
	<input type="hidden" name="msgid" value="' . $msg['msgid'] . '">
	<table id="welive_list" border="0" cellpadding="0" cellspacing="0" class="maintable">
	<thead>
	<tr>
	<th colspan="2">添加短语</th>
	</tr>
	</thead>
	<tbody>
	<tr>
	<td>短语内容:<br><br><span class=note>说明:</span> <br>1. 允许使用HTML代码, 如换行可输入&lt;br&gt;<br>2. 插入链接, 必须在新窗口打开, 否则在当前窗口打开链接将导致离线<br>如: &lt;a href="链接地址" target="_blank"&gt;链接文字&lt;/a&gt;</td>
	<td><textarea name="msg" rows="6"  style="width:460px;">'.$msg['msg'].'</textarea> <font class=red>* 必填项</font></td>
	</tr>	';

	if($msgid){
		echo '<tr>
		<td>是否显示?</td>
		<td><input type="checkbox" name="activated" value="1" ' . Iif($msg['activated'] == 1, 'checked="checked"') .'></td>
		</tr>
		<tr>
		<td>是否删除?:</td>
		<td><input type="checkbox" name="deletemsg" value="1"></td>
		</tr>	';
	}

	echo '</tbody></table>';

	PrintSubmit(Iif($msgid, '保存更新', '添加短语'));
}

//########### PRINT DEFAULT ###########

if($action == 'default'){
	$NumPerPage =10;
	$page = ForceIncomingInt('p', 1);
	$start = $NumPerPage * ($page-1);
	$search = ForceIncomingString('s');
	if(IsGet('s')){
		$search = urldecode($search);
	}

	$searchsql = Iif($search, "WHERE msg like '%".$search."%'", "");
	
	$getmessages = $DB->query("SELECT * FROM " . TABLE_PREFIX . "automsg ".$searchsql." ORDER BY ordernum ASC LIMIT $start,$NumPerPage");

	$maxrows = $DB->getOne("SELECT COUNT(msgid) AS value FROM " . TABLE_PREFIX . "automsg ".$searchsql);

	echo '<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
	<td>&nbsp;&nbsp;&nbsp;共有: <span class=note>'.$maxrows['value'].'</span> 条短语&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="admin.automsg.php?action=addmsg">添加短语</a></td>
	<td>
	<form method="post" action="admin.automsg.php" name="searchform">
	关键字:&nbsp;<input type="text" name="s" size="22">&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="search" value=" 搜索短语 " />
	</form>
	</td>
	</tr>
	</table>
	<BR>
	<form method="post" action="admin.automsg.php" name="messagesform">
	<input type="hidden" name="action" value="deletemessages">
	<input type="hidden" name="p" value="'.$page.'">
	<table id="welive_list" border="0" cellpadding="0" cellspacing="0" class="moreinfo">
	<thead>
	<tr>
	<th>排序编号</th>
	<th>状态</th>
	<th>短语内容</th>
	<th>编辑</th>
	<th><input type="checkbox" checkall="group" onclick="select_deselectAll (\'messagesform\', this, \'group\');"> 删除</th>
	</tr>
	</thead>
	<tbody>';

	if($maxrows['value'] < 1){
		echo '<tr><td colspan="5"><center><span class=red>暂无任何短语!</span></center></td></tr></tbody></table></form>';
	}else{
		while($message = $DB->fetch($getmessages)){
			echo '<tr>
			<td><input type="hidden" name="msgids[]" value="'.$message['msgid'].'" /><input type="text" name="ordernums[]" value="' . $message['ordernum'] . '" size="4" /></td>
			<td><select name="activateds[]"><option value="1">显示</option><option style="color:red;" value="0" ' . Iif(!$message['activated'], 'SELECTED', '') . '>隐藏</option></select></td>
			<td><textarea name="msgs[]" style="height:32px;width:360px;">'.$message['msg'].'</textarea></td>
			<td><a href="admin.automsg.php?action=editmsg&msgid='.$message['msgid'].'">'.Iif($message['activated'], '编辑', '<span class=red>编辑</span>').'</a></td>
			<td><input type="checkbox" name="deletemsgids[]" value="' . $message['msgid'] . '" checkme="group"></td>
			</tr>';
		}

		$totalpages = ceil($maxrows['value'] / $NumPerPage);
		if($totalpages > 1){
			echo '<tr><th colspan="5" class="last">'.GetPageList('admin.automsg.php', $totalpages, $page, 10, 's', urlencode($search)).'</th></tr>';
		}
	
		echo '</tbody>
		</table>
		<div style="margin-top:20px;text-align:center;">
		<input type="submit" name="updatemsgs" value=" 保存更新 " />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="submit" name="deletemsgs" onclick="return confirm(\'确定删除所选短语吗?\');" value=" 删除短语 " />
		</div>
		</form>';
	}
}

PrintFooter();

?>

