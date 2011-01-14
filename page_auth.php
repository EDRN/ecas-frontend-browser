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
		/*
		 * According to CA-654 (https://oodt.jpl.nasa.gov/jira/browse/CA-654), we now want to have all dataset
		 * pages available, albeit with possibly incomplete content depending on the login status of the user.
		 * Therefore, we should NOT redirect to the login page here if the user is not authorized.
		****
		if (! $eb->isDatasetAccessible($_GET['typeID'],
				  $edrnAuth->retrieveGroupsForUser(
					  $edrnAuth->getCurrentUsername()))) {
			// The selected dataset is NOT visible to this user			
			$notAuth  = true;
		}
		****/
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
		/*
		 * According to CA-654 (https://oodt.jpl.nasa.gov/jira/browse/CA-654), we now want to have all dataset
		 * pages available, albeit with possibly incomplete content depending on the login status of the user.
		 * Therefore, we should NOT force the user to log in at this point
		****
		if (! $eb->isDatasetAccessible($_GET['typeID'],array())) {
			// The selected dataset is NOT visible to anonymous users			
			$mustLogin = true;
		}
		****/
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