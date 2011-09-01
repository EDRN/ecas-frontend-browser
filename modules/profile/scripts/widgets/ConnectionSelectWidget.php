<?php
/**
 * Copyright (c) 2010, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * 
 * PROFILE - STATUS DISPLAY
 * 
 * Widget to display basic information about the currently logged-in
 * user, as well as either a log-in or log-out link, depending on the
 * situation.
 * 
 * @author s.khudikyan
 * 
 */

class ConnectionSelectWidget 
	implements Gov_Nasa_Jpl_Oodt_Balance_Interfaces_IApplicationWidget {
		
	public $connectionData;
	public $selectedConnection;
	
	public function __construct($options = array()) {
		
		// Set main authentication data 
		$authenticationData = null;
		$name = App::Get()->settings['ldap_name'];
		$data['sso_ldap_host'] 	 	= App::Get()->settings['ldap_host'];
		$data['sso_ldap_port'] 		= App::Get()->settings['ldap_port'];
		$data['sso_base_dn']  		= App::Get()->settings['ldap_base_dn'];
		$authenticationData[$name]  = $data;
		
		// Check if there are multiple LDAP servers
		$isMore = 3;
		for ($i = 2; $i < $isMore ; $i++) {
			$name = App::Get()->settings['ldap'.$i.'_name'];
			if ( $name ) {
				$data['sso_ldap_host'] 	 = App::Get()->settings['ldap'.$i.'_host'];
				$data['sso_ldap_port'] 	 = App::Get()->settings['ldap'.$i.'_port'];
				$data['sso_base_dn']  = App::Get()->settings['ldap'.$i.'_base_dn'];
				$isMore++;
				
				$authenticationData[$name] = $data;
			} else {
				$isMore = 2;
			}
		}
		$this->connectionData = $authenticationData;
		
		if( count($this->connectionData) > 1 ) {
			
			// If there are multiple servers make sure user has selected authentication
			$name = null;
			if ( isset($_SESSION['authenticate']) ) {
				$name = $_SESSION['authenticate'];
			} else {
				$name = App::Get()->settings['ldap_name'];
				$authenticationData = $authenticationData[$name];
			}
			$authenticationData = $authenticationData[$name];
		} else {
			$name = App::Get()->settings['ldap_name'];
			$authenticationData = $authenticationData[$name];
		}
		$this->selectedConnection = $name;
		// Set authentication information of selected/main LDAP
		App::Get()->getAuthenticationProvider()->setConnectionData($this->connectionData[$name]);
	}
	
	public function render($bEcho = true) {
		
		$str 	= '';
		$module = App::Get()->loadModule();
		
		// Display authentication options
		if( count($this->connectionData) > 0 ) {
			$str .= '<div class="span-15 prepend-1">';
			$str .= '<form method="post" action="'. $module->moduleRoot . '/chooseAuthentication.do">';
			$str .= 'Please select authentication:&nbsp;&nbsp;&nbsp;&nbsp;';
			$str .= '<select name="auth" id="auth">';
			foreach ( $this->connectionData as $key => $value) {
				if ( $this->selectedConnection === $key ) {
					$str .= '<option selected="' . $key . '" value="' . $key . '">' . $key . '</option>';
				} else 
					$str .= '<option value="' . $key . '">' . $key . '</option>';
			}
			$str .= '</select>&nbsp;&nbsp;&nbsp;&nbsp;';
			$str .= '<input type="submit" value="Select" />';
			$str .= '</form>';
			$str .= '</div>';
			$str .= '<hr class="space">';
			$str .= '<hr class="space">';
		}
		
		if($bEcho) {
			echo $str;
		} else {
			return $str;
		}
	}
	
}
?>
