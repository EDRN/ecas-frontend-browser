<?php
/*
 * Copyright (c) 2008, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * @author ahart
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EDRN eCAS Web Portal</title>
<!-- JS Includes  -->
<script type="text/javascript" src="assets/js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="assets/js/ecas-jquery.js"></script>
<!-- CSS Includes -->
<link rel="stylesheet" type="text/css" href="assets/edrn-skin/css/edrn-informatics.css"/>
<link rel="stylesheet" type="text/css" href="css/ecas-ui.css" />
</head>
<body>
<div id="ncibanner">
	<div id="ncibanner-inner">
	  <a href="http://www.cancer.gov/"><h2 class="ncilogo">National Cancer Institute</h2></a>
	  <a href="http://www.cancer.gov/"><h2 class="cdglogo">www.cancer.gov</h2></a>
	  <a href="http://www.nih.gov/"><h2 class="nihlogo">National Institutes of Health</h2></a>
	</div>
</div>
<br class="clr"/>
<div id="edrnlogo">
	<h1 class="header-title">
		<img id="edrnlogo-logo" src="./assets/edrn-skin/img/edrn-logo.png"/>Early Detection Research Network</h1>
	<h2 class="header-title">Research and development of biomarkers and technologies for the clinical application of early cancer detection strategies</h2>
</div>
<div id="dcplogo">
	<a href="http://prevention.cancer.gov"><h2 class="dcplogo">Division of Cancer Prevention</h2></a>
</div>
<div class="userdetails"><?php echo $eb->checkLoginStatus();?></div>
<div id="edrninformatics">
	<h2 class="app-title">EDRN Catalog &amp; Archive Service</h2>
