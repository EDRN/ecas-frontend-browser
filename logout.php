<?php
//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

// use new single sign on API
require_once "Gov/Nasa/Jpl/Edrn/Security/EDRNAuth.php";

$edrnAuth = new Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication();

$refererUrl = $_REQUEST["from"];
$edrnAuth->logout();
header("Location:".$refererUrl);
 
?>