<?php

require_once(HOME . "/modules/profile/scripts/widgets/UserStatusWidget.php");
$userStatusWidget = new UserStatusWidget(array(
	App::Get()->getAuthenticationProvider()->isLoggedIn(),
	App::Get()->getAuthenticationProvider()->getCurrentUsername()));

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
<script type="text/javascript" type="text/javascript" src="<?php echo SITE_ROOT?>/static/js/jquery-1.4.2.min.js"></script>

<!-- Dynamically Added Stylesheets -->
<!-- STYLESHEETS -->

<!-- Dynamically Added Javascripts -->
<!-- JAVASCRIPTS -->

<!-- Site specific stylesheet overrides -->
<link rel="stylesheet" type="text/css" href="<?php echo SITE_ROOT .'/static/css/site.css'?>"/>

</head>
<body>

<!-- EDRN HEADER START -->
<div id="page">
	<div id="ncibanner">

		<div id="ncibanner-inner">
		  <a href="http://www.cancer.gov/"><h2 class="ncilogo">National Cancer Institute</h2></a>
		  <a href="http://www.cancer.gov/"><h2 class="cdglogo">www.cancer.gov</h2></a>
		  <a href="http://www.nih.gov/"><h2 class="nihlogo">National Institutes of Health</h2></a>
		</div>
	</div>
	<br class="clr"/>

	<div id="edrnlogo">
		<h1  class="header-title"><img id="edrnlogo-logo" src="<?php echo SITE_ROOT?>/static/edrn-skin/img/edrn-logo.png"/>Early Detection Research Network</h1>
		<h2  class="header-title">Biomarkers: The key to early detection.</h2>
	</div>
	<div id="dcplogo">
		<h2 class="dcplogo"><a href="http://prevention.cancer.gov">Division of Cancer Prevention</a></h2>
	</div>

	<div class="userdetails">
		<?php echo $userStatusWidget->render();?>	
		
	</div>
	<div class="container" style="position:relative;">
		<h2 class="app-title" style="margin-top:-4px;margin-right:-12px;">EDRN Catalog and Archive Service</h2>
	</div>
	
	<div class="menu">
		<!-- Breadcrumbs Area -->
		<div id="breadcrumbs"/>
			<ul><li><a href="<?php echo SITE_ROOT?>/">Home</a></li></ul>
		</div><!-- End Breadcrumbs -->
	</div>

	<hr class="space"/>


<!-- EDRN HEADER END -->
<div class="container">
<?php echo App::Get()->GetMessages(); ?>
