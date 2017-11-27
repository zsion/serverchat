<?php

// +---------------------------------------------+
// |     Copyright  2010 - 2028 WeLive           |
// |     http://www.weentech.com                 |
// |     This file may not be redistributed.     |
// +---------------------------------------------+

include('includes/welive.Core.php');

function Create_VVC_Image(){	
	global $DB;
	
	$width=280;
	$height=60;
	
	$image=imagecreatetruecolor($width,$height);
	imagesetthickness($image,1);
	imagealphablending($image,true);
	$color_black=imagecolorallocatealpha($image,0,0,0,0);
	$color_black_semi=imagecolorallocatealpha($image,0,0,0,115);
	$color_white=imagecolorallocatealpha($image,255,255,255,0);
	imagefill($image,0,0,$color_white);
	imagecolortransparent($image,$color_white);
	
	$acceptedCharsV="AEIOUY";
	$acceptedCharsC="BCDFGHJKLMNPQRSTVWXZ";
	$wordbuild=array("cvcc","ccvc","ccvcc","cvccc","cvcvc","cvcv","cvccv","ccvcv");
	
	$thisword=$wordbuild[mt_rand(0,sizeof($wordbuild)-1)];
	$stringlength=strlen($thisword);
	for($i=0;$i<$stringlength;$i++){
		if ($thisword[$i]=="c") {$password.=$acceptedCharsC{mt_rand(0,strlen($acceptedCharsC)-1)};}
		if ($thisword[$i]=="v") {$password.=$acceptedCharsV{mt_rand(0,strlen($acceptedCharsV)-1)};}
	}
	
	for($i=0;$i<50;$i++){
		$color=imagecolorallocatealpha($image,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255),110);
		imagestring($image,mt_rand(1,3),mt_rand(-$width*0.25,$width*1.25),mt_rand(-$height*0.25,$height*1.25),
		$acceptedCharsC{mt_rand(0,strlen($acceptedCharsC)-1)},$color);
		imagestring($image,mt_rand(1,3),mt_rand(-$width*0.25,$width*1.25),mt_rand(-$height*0.25,$height*1.25),
		$acceptedCharsV{mt_rand(0,strlen($acceptedCharsV)-1)},$color);
	}
	
	for($i=0;$i<$stringlength;$i++){
		$buffer=imagecreatetruecolor(40,40);
		imagefill($buffer,0,0,$color_white);
		imagecolortransparent($buffer,$color_white);
		
		$buffer2=imagecreatetruecolor(40,40);
		imagefill($buffer2,0,0,$color_white);
		imagecolortransparent($buffer2,$color_white);
		
		$red=0;$green=0;$blue=0;
		while ($red+$green+$blue<400||$red+$green+$blue>450)
			{
			$red = mt_rand(0,255);
			$green = mt_rand(0,255);
			$blue = mt_rand(0,255);
			}
		
		$color=imagecolorallocate($buffer,$red,$green,$blue);
		imagestring($buffer,5,0,0,substr($password,$i,1),$color);
		
		imagecopyresized($buffer2,$buffer,2,-5,0,0,mt_rand(30,40),mt_rand(30,40),10,14);
		$buffer=imagerotate($buffer2,mt_rand(-25,25),$color_white);
		
		$xpos=$i/$stringlength*($width-30)+(($width-30)/$stringlength/2)+5+mt_rand(-8,8);
		$ypos=(($height-50)/2)+5+mt_rand(-8,8);
		
		imagecolortransparent($buffer,$color_white);
		
		imagecopymerge($image,$buffer,$xpos,$ypos,0,0,imagesx($buffer),imagesy($buffer),100);
		imagedestroy($buffer);
		imagedestroy($buffer2);
	}
	
	for($i=0;$i<12;$i++){
		$color=imagecolorallocatealpha($image,mt_rand(0,200),mt_rand(0,200),mt_rand(0,200),110);
		imagefilledellipse($image,mt_rand(0,$width),mt_rand(0,$height),mt_rand(10,40),mt_rand(10,40),$color);
	}
	
	for($i=0;$i<12;$i++){
		$color=imagecolorallocatealpha($image,mt_rand(0,200),mt_rand(0,200),mt_rand(0,200),110);
		imagesetthickness($image,mt_rand(8,20));
		imageline($image,mt_rand(-$width*0.25,$width*1.25),mt_rand(-$height*0.25,$height*1.25),
		mt_rand(-$width*0.25,$width*1.25),mt_rand(-$height*0.25,$height*1.25), $color);  
		imagesetthickness($image,1);
	}
	
	$sindivide=mt_rand(1,20);
	$sinwidth=mt_rand(1,$sindivide)/4;
	for ($i=0;$i<$height;$i++){
		$buffer=imagecreatetruecolor($width,1);
		imagecopy($buffer,$image,0,0,0,$i,$width,1);
		imageline($image,0,$i,$width,$i,$color_white);
		imagecopy($image,$buffer,(sin($i/$sindivide)-.5)*2*$sinwidth,$i,0,0,$width,1);
		imagedestroy($buffer);
	}
	
	$sindivide=mt_rand(1,20);
	$sinwidth=mt_rand(1,$sindivide)/4;
	for ($i=0;$i<$width;$i++){
		$buffer=imagecreatetruecolor(1,$height);
		imagecopy($buffer,$image,0,0,$i,0,1,$height);
		imageline($image,$i,0,$i,$width,$color_white);
		imagecopy($image,$buffer,$i,(sin($i/$sindivide)-.5)*2*$sinwidth,0,0,1,$height);
		imagedestroy($buffer);
	}
	
	$red_from=mt_rand(0,255);	$red_to=mt_rand(0,255);
	$green_from=mt_rand(0,255);	$green_to=mt_rand(0,255);
	$blue_from=mt_rand(0,255);	$blue_to=mt_rand(0,255);
	
	for ($i=0;$i<$height;$i++){
		$color=imagecolorallocatealpha($image,$red_from+($red_to-$red_from)/$height*$i,$green_from+($green_to-$green_from)/$height*$i,$blue_from+($blue_to-$blue_from)/$height*$i,100);
		imageline($image,0,$i,$width,$i,$color);
	}
	
	$red_from=mt_rand(0,255);	$red_to=mt_rand(0,255);
	$green_from=mt_rand(0,255);	$green_to=mt_rand(0,255);
	$blue_from=mt_rand(0,255);	$blue_to=mt_rand(0,255);
	
	for ($i=0;$i<$width;$i++){
		$color=imagecolorallocatealpha($image,$red_from+($red_to-$red_from)/$width*$i,$green_from+($green_to-$green_from)/$width*$i,$blue_from+($blue_to-$blue_from)/$width*$i,100);
		imageline($image,$i,0,$i,$height,$color);
	}
	
	$key = ForceIncomingInt('key');

	if($key){
		@$DB->exe("UPDATE ".TABLE_PREFIX."vvc SET code = '$password' WHERE vvcid = '$key'");
	}
	
	header("Expires: " . gmdate("D, d M Y H:i:s", time()) . " GMT");
	header('Content-Type: image/png');

	imagepng($image);
	imagedestroy($image);
	
}
	
Create_VVC_Image();

?>