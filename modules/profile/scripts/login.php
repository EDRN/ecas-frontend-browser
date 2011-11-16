<?php
/**
 * Copyright (c) 2010, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * 
 * PROFILE LOGIN
 * 
 * Process a login attempt.
 * 
 * @author s.khudikyan
**/

// Get instance of sso class
$sso = App::Get()->getAuthenticationProvider();

$message = '';

// If a user is already logged in, redirect to home page
if( $sso->isLoggedIn() ) App::Get()->Redirect(SITE_ROOT . "/"); 

// Otherwise, if a user is attempting to log in, process:
else if(isset($_POST["username"]) && isset($_POST["password"])) {

	// Check the provided login credentials:
	if($sso->login($_POST["username"], $_POST["password"])){
  	 	App::Get()->Redirect(SITE_ROOT . "/" ); // login successful
  	 } else {
  	 	App::Get()->SetMessage("Invalid Credentials Provided",CAS_MSG_ERROR);
  	 	App::Get()->Redirect(SITE_ROOT . "/profile/login");
  	 }
}