<?php

//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

// use new single sign on API
require_once "Gov/Nasa/Jpl/Edrn/Security/EDRNAuth.php";

$edrnAuth = new Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication();

$ldapuser = $_REQUEST["username"];
$ldappass = $_REQUEST["password"];
$refererUrl = $_REQUEST["from"];

if ($edrnAuth->login($ldapuser, $ldappass)) {
	header("Location:" . $refererUrl);
} else {
	if ($edrnAuth->getLastConnectionStatus()) {
		header("Location:login.php?loginFail=true&from=" . $refererUrl);
	} else {
		header("Location:login.php?loginConnectFail=true&from=" . $refererUrl);
	}
}
?>