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


PrintHeader($userinfo['username'], 'messages');

//########### DELETE COMMENTS ###########

if($action == 'deletecomments'){
	$deletecommentids = $_POST['deletecommentids'];
	$page = ForceIncomingInt('p');
	$uid = ForceIncomingInt('u');

	for($i = 0; $i < count($deletecommentids); $i++){
		$DB->query("DELETE FROM " . TABLE_PREFIX . "msg WHERE msgid = '".ForceInt($deletecommentids[$i])."'");
	}

	GotoPage('admin.messages.php'.Iif($page, '?p='.$page.Iif($uid, '&u='.$uid), Iif($uid, '?u='.$uid)), 1);
}

//########### FAST DELETE COMMENTS ###########

if($action == 'fastdelete'){
	$days = ForceIncomingInt('days');
	$uid = ForceIncomingInt('u');
	$realtime = time();

	$searchsql = Iif($uid, " WHERE fromid ='$uid' ", "");
	$searchsql .= Iif($searchsql, Iif($days, " AND created < " .$realtime - 3600*24*$days), Iif($days, " WHERE created < " .$realtime - 3600*24*$days));

	$DB->query("DELETE FROM " . TABLE_PREFIX . "msg ". $searchsql);

	GotoPage('admin.messages.php'.Iif($uid, '?u='.$uid), 1);
}

//########### PRINT DEFAULT ###########

if($action == 'default'){
	$NumPerPage =20;
	$page = ForceIncomingInt('p', 1);
	$start = $NumPerPage * ($page-1);
	$fromid = ForceIncomingInt('u');
	$toid = ForceIncomingInt('toid');
	/*$searchsql = Iif($uid, "WHERE touserid ='$uid' ", "");
*/
	//die ("SELECT userid, userfrontname FROM " . TABLE_PREFIX . "user WHERE usergroupid <>1 ORDER BY userid");
	$getusers = $DB->query("SELECT userid, userfrontname FROM " . TABLE_PREFIX . "user WHERE usergroupid <>1 ORDER BY userid");
	while($user = $DB->fetch($getusers)) {
		$users[$user['userid']] = $user['userfrontname'];
		$useroptions .= '<option value="' . $user['userid'] . '" ' . Iif($uid == $user['userid'], 'SELECTED', '') . '>' . $user['userfrontname'] . '</option>';
	}
	$getguest = $DB->query("SELECT guestid,guestip FROM " . TABLE_PREFIX . "guest");
	while($guest = $DB->fetch($getguest)) {
		$guests[$guest['guestid']] = $guest['guestip'];
	}
	if($fromid){$searchsql="where `fromid`='$fromid'";}
	if($toid){$searchsql="where `toid`='$toid'";}
	$getcomments = $DB->query("SELECT * FROM " . TABLE_PREFIX . "msg ".$searchsql." ORDER BY created DESC LIMIT $start,$NumPerPage");

	$maxrows = $DB->getOne("SELECT COUNT(msgid) AS value FROM " . TABLE_PREFIX . "msg ".$searchsql);

	echo '<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
	<td>&nbsp;&nbsp;&nbsp;共有: <span class=note>'.$maxrows['value'].'</span> 条记录</td>
	<td>
	<form method="post" action="admin.messages.php" name="searchform">
	选择:&nbsp;<select name="u"><option value="0">全部客服</option>'. $useroptions .'</select>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="search" value=" 搜索记录 " />
	</form>
	</td>
	<td>
	<form method="post" action="admin.messages.php" name="deleteform">
	<input type="hidden" name="action" value="fastdelete">
	选择:&nbsp;<select name="u"><option value="0">全部客服</option>'. $useroptions .'</select>&nbsp;&nbsp;<select name="days"><option value="0">全部记录</option><option value="1">1 天前</option><option value="5">5 天前</option><option value="10">10 天前</option><option value="30">30 天前</option><option value="60">60 天前</option><option value="90">90 天前</option></select>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delete" onclick="return confirm(\'确定删除所选记录吗?\');" value=" 快速删除 " />
	</form>
	</td>
	</tr>
	</table>
	<BR>
	<form method="post" action="admin.messages.php" name="commentsform">
	<input type="hidden" name="action" value="deletecomments">
	<input type="hidden" name="p" value="'.$page.'">
	<input type="hidden" name="u" value="'.$uid.'">
	<table id="welive_list" border="0" cellpadding="0" cellspacing="0" class="moreinfo">
	<thead>
	<tr>
	<th>来自</th>
	<th>发给</th>
	<th>记录内容</th>
	<th>时间</th>
	<th><input type="checkbox" checkall="group" onclick="select_deselectAll (\'commentsform\', this, \'group\');"> 删除</th>
	</tr>
	</thead>
	<tbody>';

	if($maxrows['value'] < 1){
		echo '<tr><td colspan="6"><center><span class=red>暂无任何记录!</span></center></td></tr></tbody></table></form>';
	}else{
		while($comment = $DB->fetch($getcomments)){
				if($comment['type']){$from=$users[$comment['fromid']];$to=$guests[$comment['toid']];}else{$from=$guests[$comment['fromid']];$to=$users[$comment['toid']];}
			echo '<tr>
			<td>' .$from  . '</td>
			<td>' . $to . '</td>
			<td>'.nl2br($comment['msg']). '</a></td>
			<td>' . DisplayDate($comment['created'], 0, 1) . '</td>
			<td><input type="checkbox" name="deletecommentids[]" value="' . $comment['msgid'] . '" checkme="group"></td>
			</tr>';
		}

		$totalpages = ceil($maxrows['value'] / $NumPerPage);
		if($totalpages > 1){
			echo '<tr><th colspan="6" class="last">'.GetPageList('admin.comments.php', $totalpages, $page, 10, 'u', $uid).'</th></tr>';
		}
	
		echo '</tbody>
		</table>
		<div style="margin-top:20px;text-align:center;">
		<input type="submit" onclick="return confirm(\'确定删除所选记录吗?\');" value=" 删除记录 " />
		</div>
		</form>';
	}
}

PrintFooter();

?>