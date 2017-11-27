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

$uid = $userinfo['userid'];
$ajaxpending = 'uid=' . $uid;        //需要动态变化, 用于将客服ID附加到AJAX URL

PrintHeader($userinfo['username'], 'comments');

echo '<script type="text/javascript">var ajaxpending = "'. $ajaxpending .'";</script>'; //用于AJAX

//########### DELETE COMMENTS ###########

if($action == 'deletecomments'){
	$deletecommentids = $_POST['deletecommentids'];
	$page = ForceIncomingInt('p');
	$uid = ForceIncomingInt('u');

	for($i = 0; $i < count($deletecommentids); $i++){
		$DB->exe("DELETE FROM " . TABLE_PREFIX . "comment WHERE commentid = '".ForceInt($deletecommentids[$i])."'");
	}

	GotoPage('admin.comments.php'.Iif($page, '?p='.$page.Iif($uid, '&u='.$uid), Iif($uid, '?u='.$uid)), 1);
}

//########### FAST DELETE COMMENTS ###########

if($action == 'fastdelete'){
	$days = ForceIncomingInt('days');
	$uid = ForceIncomingInt('u');
	$realtime = time();

	$searchsql = Iif($uid, " WHERE touserid ='$uid' ", "");
	$searchsql .= Iif($searchsql, Iif($days, " AND created < " .$realtime - 3600*24*$days), Iif($days, " WHERE created < " .$realtime - 3600*24*$days));

	$DB->exe("DELETE FROM " . TABLE_PREFIX . "comment ". $searchsql);

	GotoPage('admin.comments.php'.Iif($uid, '?u='.$uid), 1);
}

//########### PRINT DEFAULT ###########

if($action == 'default'){
	$NumPerPage =20;
	$page = ForceIncomingInt('p', 1);
	$start = $NumPerPage * ($page-1);
	$uid = ForceIncomingInt('u');

	$searchsql = Iif($uid, "WHERE touserid ='$uid' ", "");

	$getusers = $DB->query("SELECT userid, userfrontname FROM " . TABLE_PREFIX . "user WHERE usergroupid <>1 ORDER BY userid");
	while($user = $DB->fetch($getusers)) {
		$users[$user['userid']] = $user['userfrontname'];
		$useroptions .= '<option value="' . $user['userid'] . '" ' . Iif($uid == $user['userid'], 'SELECTED', '') . '>' . $user['userfrontname'] . '</option>';
	}

	$getcomments = $DB->query("SELECT * FROM " . TABLE_PREFIX . "comment ".$searchsql." ORDER BY commentid DESC LIMIT $start,$NumPerPage");

	$maxrows = $DB->getOne("SELECT COUNT(commentid) AS value FROM " . TABLE_PREFIX . "comment ".$searchsql);

	echo '<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
	<td>&nbsp;&nbsp;&nbsp;共有: <span class=note>'.$maxrows['value'].'</span> 条留言</td>
	<td>
	<form method="post" action="admin.comments.php" name="searchform">
	选择:&nbsp;<select name="u"><option value="0">全部客服</option>'. $useroptions .'</select>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="search" value=" 搜索留言 " />
	</form>
	</td>
	<td>
	<form method="post" action="admin.comments.php" name="deleteform">
	<input type="hidden" name="action" value="fastdelete">
	选择:&nbsp;<select name="u"><option value="0">全部客服</option>'. $useroptions .'</select>&nbsp;&nbsp;<select name="days"><option value="0">全部留言</option><option value="1">1 天前</option><option value="5">5 天前</option><option value="10">10 天前</option><option value="30">30 天前</option><option value="60">60 天前</option><option value="90">90 天前</option></select>&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" name="delete" onclick="return confirm(\'确定删除所选留言吗?\');" value=" 快速删除 " />
	</form>
	</td>
	</tr>
	</table>
	<BR>
	<form method="post" action="admin.comments.php" name="commentsform">
	<input type="hidden" name="action" value="deletecomments">
	<input type="hidden" name="p" value="'.$page.'">
	<input type="hidden" name="u" value="'.$uid.'">
	<table id="welive_list" border="0" cellpadding="0" cellspacing="0" class="moreinfo">
	<thead>
	<tr>
	<th>留言人</th>
	<th>IP地址</th>
	<th>留言内容</th>
	<th>留言给</th>
	<th>时间</th>
	<th><input type="checkbox" checkall="group" onclick="select_deselectAll (\'commentsform\', this, \'group\');"> 删除</th>
	</tr>
	</thead>
	<tbody>';

	if($maxrows['value'] < 1){
		echo '<tr><td colspan="6"><center><span class=red>暂无任何留言!</span></center></td></tr></tbody></table></form>';
	}else{
		while($comment = $DB->fetch($getcomments)){
			echo '<tr>
			<td>' . $comment['username'] . '</td>
			<td>' . Iif($comment['userip'], '<a href="javascript:;" hidefocus="true" onclick="iplocation(\'' . $comment['commentid'] . '\', \'' . $comment['userip'] . '\');return false;" title="查看IP归属地">' . $comment['userip'] . '</a><br><span id="ip_' . $comment['commentid'] . '"></span>', '&nbsp;') . '</td>
			<td>'.nl2br($comment['content']). '</a></td>
			<td>'.Iif($users[$comment['touserid']], '<a href="admin.users.php?action=edituser&userid='.$comment['touserid'].'">' . $users[$comment['touserid']] . '</a>', '已删除').'</td>
			<td>' . DisplayDate($comment['created'], 0, 1) . '</td>
			<td><input type="checkbox" name="deletecommentids[]" value="' . $comment['commentid'] . '" checkme="group"></td>
			</tr>';
		}

		$totalpages = ceil($maxrows['value'] / $NumPerPage);
		if($totalpages > 1){
			echo '<tr><th colspan="6" class="last">'.GetPageList('admin.comments.php', $totalpages, $page, 10, 'u', $uid).'</th></tr>';
		}
	
		echo '</tbody>
		</table>
		<div style="margin-top:20px;text-align:center;">
		<input type="submit" onclick="return confirm(\'确定删除所选留言吗?\');" value=" 删除留言 " />
		</div>
		</form>';
	}
}

PrintFooter();

?>

