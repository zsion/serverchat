<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

//if(!defined('WELIVE')) die('File not found!');

include('welive.GlobalFunctions.php');

// #####################

function IsBannedIP($clientip){
	global $_CFG;

	$addresses = explode(';', preg_replace('/[[:space:]]+/', '', $_CFG['cBannedips']));

	if(count($addresses) > 0){
		foreach($addresses as $ip){
			if(strpos($ip, '*') === false){
				if($ip == $clientip)  return true;
			}elseif(preg_match('/'.$ip.'/i', $clientip)){
				return true;
			}
		}
	}

	return false;
}

// #####################

function get_userAgent($userAgent){
	if(!$userAgent) return "unknown";

	$knownAgents = array("opera", "msie", "chrome", "safari", "firefox", "netscape", "mozilla");

	$userAgent = strtolower($userAgent);
	foreach ($knownAgents as $agent) {
		if (strstr($userAgent, $agent)) {
			if (preg_match("/" . $agent . "[\\s\/]?(\\d+(\\.\\d+(\\.\\d+(\\.\\d+)?)?)?)/", $userAgent, $matches)) {
				$ver = $matches[1];
				if ($agent == 'safari') {
					if (preg_match("/version\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
						$ver = $matches[1];
					} else {
						$ver = "1 or 2 (build " . $ver . ")";
					}
					if (preg_match("/mobile\/(\\d+(\\.\\d+(\\.\\d+)?)?)/", $userAgent, $matches)) {
						$userAgent = "iPhone " . $matches[1] . " ($agent $ver)";
						break;
					}
				}

				$userAgent = ucfirst($agent) . " " . $ver;
				break;
			}
		}
	}

	return $userAgent;
}

// #####################

function WeLive($sender, $content, $ctype = 2, $biu = '000', $color = '0') {
	return $sender .'|||'.$content .'|||'.$ctype .'|||'.$biu .'|||'.$color;
}

// #####################

function WeLiveSend($realtime, $lines, $ajax_last, $error = 0) {
	$info = '';

	if($error){
		$info = $ajax_last . '||||||2';
	}else{
		if(is_array($lines)) {
			foreach($lines as $value) {
				$info .= $value . '^^^';
			}

			$info = $realtime . '||||||' . $info;
		}else{
			$info = $realtime . '||||||' . $lines;
		}
	}

	echo $info;
}

// #####################

function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;
	$key = md5($key ? $key : 'default_key');
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

?>