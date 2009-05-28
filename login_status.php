<?php
//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

// use new single sign on API
require_once "Gov/Nasa/Jpl/Edrn/Security/EDRNAuth.php";

function checkLoginStatus($refererUrl){
 $edrnAuth = new Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication();
 
 if(!$edrnAuth->isLoggedIn()){
  ?>
  Not logged in. <a href="login.php?from=<?php echo $refererUrl ?>">Log In</a>  
  <?
 }else{
 	?>
  Logged in as <? echo $edrnAuth->getCurrentUsername();?>. <a href="logout.php?from=<?php echo $refererUrl?>">Log Out</a>
 	<?
 }
}
?>
