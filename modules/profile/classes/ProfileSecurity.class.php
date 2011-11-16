<?php
/**
 * Copyright (c) 2010, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * 
 * PROFILE SECURITY CLASS
 * 
 * This class extends the CAS-security project which provides single-sign-on
 * functionality and the ability to interact with an LDAP directory server.
 * 
 * 
 * @author s.khudikyan
 */
require_once('Gov/Nasa/Jpl/Oodt/Cas/Security/SingleSignOn.php');

class ProfileSecurity 
	extends Gov_Nasa_Jpl_Oodt_Cas_Security_SingleSignOn {
	
	/**
	 * ProfileSecurity::retrieveUserProfile
	 * Uses {@link retrieveUserAttributes} to retrieve information about 
	 * the provided user from ldap
	 * @param string $username user for which attributes will be returned
	 */
	public function retrieveUserProfile($username) {

		// get user attributes
		$justthese = array( App::Get()->settings['sso_username_attr'],
							App::Get()->settings['sso_lastname_attr'],
			 				App::Get()->settings['sso_firstname_attr'],
			 				App::Get()->settings['sso_email_attr'] );
		$profileAttributes = $this->retrieveUserAttributes($username, $justthese);
		
		if (count($profileAttributes) != 0) {
				
			$profileAttributes = $profileAttributes[0];
			$attr['uid']   = $profileAttributes[ App::Get()->settings['sso_username_attr'] ][0];
			$attr['sn']    = $profileAttributes[ App::Get()->settings['sso_lastname_attr'] ][0];
			$attr['gn']    = $profileAttributes[ App::Get()->settings['sso_firstname_attr'] ][0];
			$attr['email'] = $profileAttributes[ App::Get()->settings['sso_email_attr'] ][0];
			return $attr;
		} else {
			return false;
		}
	}
	
	/**
	 * ProfileSecurity::usernameAvailability
	 * Uses {@link retrieveUserAttributes} to retrieve information about 
	 * the provided user from ldap. If count of array is > 1 then the 
	 * username is not available
	 * @param string $username user for which attributes will be returned
	 */	
	public function usernameAvailability($username) {
		
		// Search for specified username in ldap directory
		$justthese = array( App::Get()->settings['sso_username_attr'] );
		$profile = $this->retrieveUserAttributes($username, $justthese);
		
		if (count($profile) > 1) {
			return false;
		} else {
			// available
			return true;
		}
	}
	
	/**
	 * ProfileSecurity::validateChangePassword
	 * Validates rules from config file and uses {@link changePassword} to change password 
	 * @param string $newPass the new password
	 * @param unknown_type $encryptionMethod
	 */
	public function validateChangePassword($newPass,$encryptionMethod = "SHA") {
		$isValid = true;
		$messages = array();
		// validate rules from config file
		$rules = App::Get()->settings['security_password_rules'];

		if ( isset($rules) ) {
			foreach($rules as $rule){
				
				// Separate the rule from the error message
				list($regularExpression,$errorMessage) = explode('|',$rule,2);
				
				// Test the rule
				$rulePassed = preg_match($regularExpression, $newPass);
				
				// If the rule failed, append the error message
				if (!$rulePassed) {
					$messages[] = $errorMessage;
					$isValid    = false;
				}
			}
		}

		if ($isValid && $this->connect(SSO_LDAP_HOST,SSO_LDAP_PORT)) {
			
			$result = $this->changePassword($newPass,$encryptionMethod);
			return true;
		} else
		  return $messages;
	}
	
	/**
	 * ProfileSecurity::manageProfile
	 * If user is logged in, the user's ldap attributes will be replaced with
	 * the new information provided in $newInfo
	 * @param array $newInfo - array key must be a ldap attribute
	 */
	public function manageProfile($newInfo) {

		if ($this->isLoggedIn()) {
			$user     = "uid={$this->getCurrentUsername()}," . SSO_BASE_DN ;
			$ldapconn = $this->connect(SSO_LDAP_HOST,SSO_LDAP_PORT);
			
			if (ldap_mod_replace($ldapconn,$user,$newInfo)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}	
	
	/**
	 * ProfileSecurity::addUser
	 * Connects to ldap and creates a new account with the information provided
	 * @param array $userInfo can include different values depending on ldap
	 * 		  i.e. username, firstname, lastname, email
	 */
	public function addUser($userInfo) {
		$ldapconn = $this->connect(SSO_LDAP_HOST,SSO_LDAP_PORT);
		if ($ldapconn) {
			$user  = "uid={$userInfo[ $GLOBALS["app"]->settings['sso_username_attr'] ]}," . SSO_BASE_DN;
			return ldap_add($ldapconn,$user,$userInfo);
		}
		// connection failed
		return false;
	}
	
	
	
}
