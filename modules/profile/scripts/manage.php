<?php
/**
 * Copyright (c) 2010, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * 
 * PORTAL - MANAGE USER PROFILE
 * 
 * Process user's changes to profile information. 
 * Available information, required attributes:
 * 
 * 	1. First name
 * 	2. Last name
 * 	3. Username
 * 	4. Email
 * 
 * @author s.khudikyan
**/

$allSet = true;


if ( isset($_POST["firstname"]) && $_POST["firstname"] != "" ) {
	$info[ App::Get()->settings['sso_firstname_attr'] ] = $_POST["firstname"];
} else {
	// first name is required
	App::Get()->SetMessage("First name cannot be blank",CAS_MSG_ERROR);
	$allSet = FALSE;
}

if ( isset($_POST["lastname"]) && $_POST["lastname"] != "" ) {
	$info[ App::Get()->settings['sso_lastname_attr'] ] = $_POST["lastname"];
} else {
	// last name is required
	App::Get()->SetMessage("Last name cannot be blank",CAS_MSG_ERROR);
	$allSet = FALSE;
}

if ( isset($_POST["email"]) && $_POST["email"] != "") {
	$info[ App::Get()->settings['sso_email_attr'] ] = $_POST["email"];
} else {
	// email is required
	App::Get()->SetMessage("Email cannot be blank",CAS_MSG_ERROR);
	$allSet = FALSE;
}

if ($allSet) {
	// method to edit user
	if( App::Get()->getAuthProvider()->manageProfile($info) ){
 		
   		App::Get()->Redirect(SITE_ROOT . "/profile/" ); // user info change successful
   	} else{
   		
   		// if not logged in - cannot change pwd
   		App::Get()->SetMessage("Invalid entry",CAS_MSG_ERROR);
   		App::Get()->Redirect(SITE_ROOT . "/profile/manage" );
   	}
} else {
	
	App::Get()->Redirect(SITE_ROOT . "/profile/manage" );
}
