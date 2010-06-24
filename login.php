<?php
//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

require_once("config.php");
require_once("login_status.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EDRN ecas user interface</title>
<!-- CSS Includes -->
<link rel="stylesheet" type="text/css" href="assets/edrn-skin/css/edrn-informatics.css"/>
<link rel="stylesheet" type="text/css" href="css/ecas-ui.css" />
<script type="text/javascript">
  function $(id){
    return document.getElementById(id);
  }
  function toggleDetails(id){
    if ($(id + "Contents").style.display != 'none'){
      $(id + "Contents").style.display = 'none';
      $(id + "Toggler").innerHTML = 'more information [+]';
    } else {
      $(id + "Contents").style.display = 'block';
      $(id + "Toggler").innerHTML = 'less information [-]';
    }
  }
</script> 
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
	<h1>Early Detection Research Network</h1>
	<h2>Research and development of biomarkers and technologies for the clinical application of early cancer detection strategies</h2>
</div>
<div id="dcplogo">
	<a href="http://prevention.cancer.gov"><h2 class="dcplogo">Division of Cancer Prevention</h2></a>
</div>
<div class="userdetails"><?php echo checkLoginStatus($_SERVER["REQUEST_URI"]);?></div>
<div id="edrninformatics">
	<h2 class="app-title">EDRN Catalog &amp; Archive Service</h2>
	
	
<!-- Breadcrumbs Area -->
<div id="breadcrumbs"/>
	<a href="./">Home</a>&nbsp/
	Log In
</div>
<!-- End Breadcrumbs -->

<h1 class="sectionTitle" style="width:auto;font-size:22px;font-weight:bold;margin-top:24px;text-align:center;">Please Log In to the EDRN Catalog and Archive Service</h1>
<p>&nbsp;</p>

  
<form  id="login-form" method="post" action="ldap_login.php" name="login_form">
 <input type="hidden" name="from" value="<?php echo $_REQUEST["from"]?>"/>
 <center>
 <?
 if(isset($_REQUEST["loginFail"])){
 	?>
 	<div class="error" style="margin:0px;margin-bottom:5px;width:230px;font-size:90%">Invalid Credentials...<br/>Please try again.</div>
 	<?
 }
 
 if(isset($_REQUEST["loginConnectFail"])){
 	?>
 	<div class="error" style="margin:0px;margin-bottom:5px;width:230px;font-size:90%">Unable to contact LDAP authentication server...<br/>Please try again later.</div>
 	<?
 }
?>
 <table>
   <tr>
     <td>Username</td>
     <td><input id="login-username" type="text" name="username" value="" size="20" maxlength="255"/></td>
   </tr>
   <tr>
     <td>Password</td>
     <td><input type="password" name="password" value="" size="20" maxlength="255"/></td>
   </tr>
   <tr>
     <td>&nbsp;</td>
     <td><input type="submit" name="login_submit" value="Log In"/></td>
   </tr>
 </table>
 </center>
</form>



<div style=\"clear:both;\"></div>
<script type="text/javascript">
	document.getElementById('login-username').focus();
</script>
</body>
</html>