<?php
/**
 * Copyright (c) 2010, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * 
 * PROFILE - MANAGE USER PROFILE
 * 
 * Process user's changes to profile information. 
 * 
 * @author s.khudikyan
**/
$module = App::Get()->loadModule();
$allSet = true;

// Prepare Connection options
require_once( $module->modulePath . '/scripts/widgets/ConnectionSelectWidget.php' );
$csw = new ConnectionSelectWidget();


foreach ($_POST as $key=>$value) {

	if ( $key != submit_button && $key != document) {
		if ( isset($value) && $value != "" ) {
			$info[$key] = $value;
		} else {
			App::Get()->SetMessage( 
				array_search( $key, App::Get()->settings['attr_titles'] ) 
				. " cannot be left blank.", CAS_MSG_ERROR );
			$allSet = false;
		}
	}
}

if ($allSet) {
	// method to edit user
	if( App::Get()->getAuthenticationProvider()->updateProfile($info) ){
 		
   		App::Get()->Redirect($module->moduleRoot . "/" ); // user info change successful
   	} else{
   		
   		// if not logged in - cannot change pwd
   		App::Get()->SetMessage("Invalid entry",CAS_MSG_ERROR);
   		App::Get()->Redirect($module->moduleRoot . "/manage" );
   	}
} else {
	
	App::Get()->Redirect($module->moduleRoot . "/manage" );
}
