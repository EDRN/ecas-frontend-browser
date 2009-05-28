<?php require_once "page_auth.php"?>
<?php
//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

require_once("config.php");
$productID = $_GET['productID'];
$refNo = $_GET['refNumber'];
$fileName = isset($_GET['fileName'])
	? $_GET['fileName']
	: 'img.jpg';

$imgName = $dataDelivUrl."/data?refIndex=$refNo&productID=$productID";

function loadJpeg($imgName){
	$im = @imagecreatefromjpeg($imgName);
	if (!$im){
	    $im  = imagecreatetruecolor(350, 30); /* Create a black image */
        $bgc = imagecolorallocate($im, 255, 255, 255);
        $tc  = imagecolorallocate($im, 0, 0, 0);
        imagefilledrectangle($im, 0, 0, 350, 30, $bgc);
        /* Output an errmsg */
        imagestring($im, 1, 5, 5, "Error loading $imgName", $tc);
	}
	return $im;
}

header("Content-Type: img/jpeg");
header('Content-Disposition: attachment; filename="'.$fileName.'"');
$img = loadJpeg($imgName);
imagejpeg($img);
?>