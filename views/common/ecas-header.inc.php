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
<div id="edrninformatics">
	<div id="edrnlogo"><strong>Early Detection Research Network</strong><br/>
		<span class="smaller">Division of Cancer Prevention</span>
	 </div>

	<div id="edrn-dna"><!-- dna graphic --></div>
	<h2 class="app-title">EDRN Catalog &amp; Archive Service</h2>
	<div class="userdetails"><?php echo $eb->getLoginStatus();?></div>
</div>