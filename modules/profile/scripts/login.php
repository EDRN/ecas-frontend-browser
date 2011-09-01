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
$module = App::Get()->loadModule();
$message = '';

// Get instance of authentication class
$authentication = App::Get()->getAuthenticationProvider();

// Prepare Connection options
require_once( $module->modulePath . '/scripts/widgets/ConnectionSelectWidget.php' );
$csw = new ConnectionSelectWidget();

// If a user is already logged in, redirect to home page
if( $authentication->isLoggedIn() ) App::Get()->Redirect(SITE_ROOT . "/"); 

// Otherwise, if a user is attempting to log in, process:
else if(isset($_POST["username"]) && isset($_POST["password"])) {

	// Check the provided login credentials:
	if($authentication->login($_POST["username"], $_POST["password"])){
	  App::Get()->Redirect(SITE_ROOT . "/" ); // login successful
  	 } else {
  	 	App::Get()->SetMessage("Invalid Credentials Provided",CAS_MSG_ERROR);
  	 	App::Get()->Redirect($module->moduleRoot . "/login");
  	 }
}