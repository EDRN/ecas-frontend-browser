<?php
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>EDRN eCAS Browser</title>

<!-- Base stylesheets (Blueprint + Balance) -->
<link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT?>/static/css/blueprint/screen.css"/>
<!--[if lt IE 8]><link rel="stylesheet" type="text/css" href="/modules/browser/static/css/blueprint/ie.css"/><![endif]-->
<link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT?>/static/css/balance/balance.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT?>/static/edrn-skin/css/edrn-informatics.css"/>

<!-- Base Javascript -->
<script type="text/javascript" type="text/javascript" src="<?php echo SITE_ROOT?>static/js/jquery-1.4.2.min.js"></script>

<!-- Dynamically Added Stylesheets -->
<!-- STYLESHEETS -->

<!-- Dynamically Added Javascripts -->
<!-- JAVASCRIPTS -->

<!-- Site specific stylesheet overrides -->
<link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT .'/static/css/site.css'?>"/>

</head>
<body>

<!-- EDRN HEADER START -->
<body>
<div id="ncibanner">

	<div id="ncibanner-inner">
		  <a href="http://www.cancer.gov/"><h2 class="ncilogo">National Cancer Institute</h2></a>
		  <a href="http://www.cancer.gov/"><h2 class="cdglogo">www.cancer.gov</h2></a>
		  <a href="http://www.nih.gov/"><h2 class="nihlogo">National Institutes of Health</h2></a>
	</div>
</div>
<br class="clr"/>
<div class="container" style="margin-top:36px;">
	<h1 style="margin-bottom:7px;">Early Detection Research Network</h1>
	<h3 style="letter-spacing:0.2em;color:#666">Early Detection Research Network Catalog and Archive Service</h3>
	<hr/>
</div>

<!-- EDRN HEADER END -->
<div class="container">
<?php echo App::Get()->GetMessages(); ?>
