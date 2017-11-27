<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+


error_reporting(E_ALL & ~E_NOTICE);

@include('./config/config.php');
include(BASEPATH . 'includes/welive.BaseUrl.php');
include(BASEPATH . 'includes/Class.Database.php');

if(defined('AJAX')){
	$printerror = false; //AJAX不打印SQL查询错误信息
}else{
	$printerror = true;
}

$DB = new MySQL($dbusername, $dbpassword, $dbname,  $servername, true, $printerror);

$dbpassword   = ''; //将config.php文件中的密码付值为空, 增加安全性

include(BASEPATH . 'config/settings.php');

define('APP_NAME', base64_decode($_CFG['cAppName']));
define('APP_URL', base64_decode($_CFG['cAppCopyrighURL']));
define('APP_VERSION', $_CFG['cAppVersion']);


define('TURL', BASEURL.'templates/');
define('COPYRIGHT', '&copy; '.date("Y") .' <a style="display:none;" href="'.APP_URL.'" target="_blank">'. APP_NAME .'</a> '.base64_decode('5Zyo57q/5a6i5pyN57O757uf	').'(v'. APP_VERSION . ')');

if(defined('AUTH')){ //客服和管理员只显示中文, 且需要授权
	include(BASEPATH . 'includes/welive.Support.php');

	define('IS_CHINESE', 1);
	define('SITE_TITLE', $_CFG['cTitle']);
	@include(BASEPATH . 'languages/Chinese.php');
	if(!defined('AJAX')){ //客服的AJAX操作无需授权
		include(BASEPATH.'includes/welive.Auth.php');
	}

}elseif($_CFG['cActived']){ //客人自动选择语言
	include(BASEPATH . 'includes/welive.Functions.php');

	$sitelang = ForceIncomingCookie('LANG'.COOKIE_KEY);

	if(!$sitelang){
		if($_CFG['cLang'] == 'Auto'){
			if (strstr(strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']), 'zh-cn') OR strstr(strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']), 'zh-tw'))	{
				$sitelang = 'Chinese';
			}else{
				$sitelang = 'English';
			}
		}else{
			$sitelang = $_CFG['cLang'];
		}
	}

	define('SITE_LANG', $sitelang);
	define('IS_CHINESE', Iif(SITE_LANG == 'Chinese', 1, 0));
	define('SITE_TITLE', Iif(IS_CHINESE, $_CFG['cTitle'], $_CFG['cTitle_en']));
	@include(BASEPATH . 'languages/' . SITE_LANG . '.php');
}


?>