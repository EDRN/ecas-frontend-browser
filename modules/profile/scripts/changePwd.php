<?php
/**
 * Copyright (c) 2010, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * 
 * PROFILE - PASSWORD CHANGE
 * 
 * Process a password change. This script uses config file to 
 * check for password restrictions.
 * 
 * @author s.khudikyan
**/

// Get instance of sso class
$sso = App::Get()->getAuthProvider();


if ( !isset($_POST["password_confirm"]) || $_POST["password_confirm"] == "" ) {
	
	// password confirm is required
	App::Get()->SetMessage("Please confirm password.",CAS_MSG_ERROR);
	App::Get()->Redirect(SITE_ROOT . "/profile/changePwd" );
	
} elseif ( (isset($_POST["password"]) || $_POST["password"] != "") && 
		 ( $_POST["password"] == $_POST["password_confirm"] ) ) {

		 	$message = $sso->validateChangePassword( $_POST["password"] );

		 	if( is_array($message) ) {
 		
		 		foreach ($message as $value) {
		 			App::Get()->setMessage($value,CAS_MSG_ERROR);
		 		}
		   		
		   		App::Get()->Redirect(SITE_ROOT . "/profile/changePwd" );
		 	} else{
		 		
		 		// Log the user out
				$sso->logout();
				
				// End user session
				App::Get()->EndUserSession();
				
				// Redirect to confirmation page
		   		App::Get()->Redirect(SITE_ROOT . "/profile/passwordChangeConfirmed" ); // password change successful
   		
   			}
		 } else {
	
			// password is required and must match
			App::Get()->SetMessage("Password cannot be blank and must match",CAS_MSG_ERROR);
			App::Get()->Redirect(SITE_ROOT . "/profile/changePwd" );
		}
		