<?php
/**
 * Copyright (c) 2010, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * 
 * PROFILE - CREATE USER
 * 
 * Create new user, required attributes:
 * 
 * 	1. First name
 * 	2. Last name
 * 	3. Username
 * 	4. Email 
 * 
 * 	- password
 * 	- confirm password
 * 
 * @author s.khudikyan
**/
$module = App::Get()->loadModule();

// Prepare Connection options
require_once( $module->modulePath . '/scripts/widgets/ConnectionSelectWidget.php' );
$csw = new ConnectionSelectWidget();

$module = App::Get()->loadModule();
$allSet = true;


if ( isset($_POST["firstname"]) && $_POST["firstname"] != "" ) {
	$info[ App::Get()->settings['firstname_attr'] ] = $_POST["firstname"];
} else {
	// first name is required
	App::Get()->SetMessage("First name cannot be blank",CAS_MSG_ERROR);
	$allSet = FALSE;
}

if ( isset($_POST["lastname"]) && $_POST["lastname"] != "" ) {
	$info[ App::Get()->settings['lastname_attr'] ] = $_POST["lastname"];
} else {
	// last name is required
	App::Get()->SetMessage("Last name cannot be blank",CAS_MSG_ERROR);
	$allSet = FALSE;
}

if ( isset($_POST["username"]) && $_POST["username"] != "" ) {
	
	// check username availability
	$isAvailable = App::Get()->getAuthenticationProvider()->usernameAvailability($_POST["username"]);	
	if ( $isAvailable ) {
		$info[ App::Get()->settings['username_attr'] ] = $_POST["username"];
	} else{
		App::Get()->SetMessage("Username has been taken",CAS_MSG_ERROR);
		$allSet = FALSE;
	}
} else {
	// username is required
	App::Get()->SetMessage("Username cannot be blank",CAS_MSG_ERROR);
	$allSet = FALSE;
}

if ( isset($_POST["email"]) && $_POST["email"] != "") {
	$info[ App::Get()->settings['email_attr'] ] = $_POST["email"];
} else {
	// email is required
	App::Get()->SetMessage("Email cannot be blank",CAS_MSG_ERROR);
	$allSet = FALSE;
}

if ( !isset($_POST["password_confirm"]) || $_POST["password_confirm"] == "" ) {
	
	// password confirm is required
	App::Get()->SetMessage("Password cannot be blank and must match",CAS_MSG_ERROR);
	$allSet = FALSE;
} elseif ( (isset($_POST["password"]) || $_POST["password"] != "") && 
		   ( $_POST["password"] == $_POST["password_confirm"] ) ) {
		   	
	$info['userPassword'] = $_POST["password"];
} else {
	
	// password is required and must match
	App::Get()->SetMessage("Password cannot be blank and must match",CAS_MSG_ERROR);
	$allSet = FALSE;
}

if ($allSet) {
	
	$info[ App::Get()->settings['commonname_attr'] ] = $_POST["firstname"] . " " . $_POST["lastname"];
	$info[ "objectClass" ] = "inetOrgPerson";
	if ( App::Get()->getAuthenticationProvider()->addUser($info) ) {
		
		App::Get()->Redirect($module->moduleRoot . "/login" ); // add account successful	
	} else {
		App::Get()->setMessage("Could not add user.",CAS_MSG_ERROR);
	}
} else {
	
	App::Get()->Redirect($module->moduleRoot . "/createUser" );
}
