<?php
//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

// use new single sign on API
require_once "Gov/Nasa/Jpl/Edrn/Security/EDRNAuth.php";
require_once("XML/RPC.php");
require_once("classes/EcasBrowser.class.php");
require_once("classes/Product.class.php");
require_once("classes/ProductType.class.php");
require_once("classes/XmlRpcManager.class.php");
require_once("services/ExternalServices.class.php");
require_once("services/EcasHttpRequest.class.php");
require_once "config.php";

// Set up some required data structures
$eb        = new EcasBrowser($FILEMGR_URL,$EXTERNAL_SERVICES_PATH);
$edrnAuth  = new Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication();
$mustLogin = false;		// whether anonymous must log in to view resource
$notAuth   = false;		// whether current user is authorized to view resource

// Gather some information about the current request
$authUserPresent = $edrnAuth->isLoggedIn();
$wantsDataset    = isset($_GET['typeID']);
$wantsProduct    = isset($_GET['productID']);

// Determine whether the page should be shown to the user
if ($authUserPresent) {
	
	if ($wantsDataset) {
		
		if (! $eb->isDatasetAccessible($_GET['typeID'],
				  $edrnAuth->retrieveGroupsForUser(
					  $edrnAuth->getCurrentUsername()))) {
			// The selected dataset is NOT visible to this user			
			$notAuth  = true;
		}
	}
	
	if ($wantsProduct) {
		
		if (! $eb->isProductAccessible($_GET['productID'],
				  $edrnAuth->retrieveGroupsForUser(
					  $edrnAuth->getCurrentUsername()))) {
			// The selected product is NOT visible to this user			
			$notAuth = true;
		}
	}
} else { // Anonymous (non-authenticated) user present
	
	if ($wantsDataset) {
		
		if (! $eb->isDatasetAccessible($_GET['typeID'],array())) {
			// The selected dataset is NOT visible to anonymous users			
			$mustLogin = true;
		}
	}
	
	if ($wantsProduct) {
		
		if (! $eb->isProductAccessible($_GET['productID'],array())) {
			// The selected product is NOT visible to anonymous users			
			$mustLogin = true;
		}
	}
}


// If any of the above checks resulted in $mustLogin=true, then
// redirect the user to the login page.
if ($mustLogin) {
	header("Location:login.php?from=".$_SERVER["REQUEST_URI"]);
	exit();
}

// If any of the above checks resulted in $notAuth=true, then
// redirect the user to the noauth page
if ($notAuth) {
	header('Location:noauth.php?from='.$_SERVER['REQUEST_URI']);
	exit();
}
?>