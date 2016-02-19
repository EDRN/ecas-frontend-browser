<?php
  /**
   * Copyright (c) 2010, California Institute of Technology.
   * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
   * 
   * $Id$
   * 
   * 
   * OODT Balance
   * Web Application Base Framework
   * 
   * Implementation of iApplicationAuthenticationProvider, which extends SingleSignOn.
   * 
   * Note: This class has a dependency on the OODT CAS-SSO package 
   *      (http://oodt.jpl.nasa.gov/repo/framework/cas-sso/trunk/src/php/pear)
   *      
   *      To build this dependency, check out the above project and then:
   *      1) cd into the checked out project (you should see a package.xml file)
   *      2) pear package
   *      3) (sudo) pear install --force Gov_Nasa_Jpl...tar.gz
   * 
   * @author ahart
   * 
   */

require_once(dirname(__FILE__) . '/EDRNAuth.php');

class EDRNAuthenticationProvider
  extends    Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication {

  private $baseDn;
  private $groupDn;
  private $ldapHost;
  private $ldapPort;
  
  public function __construct() {
    define("SSO_COOKIE_KEY", App::Get()->settings['cookie_key']);
  }

  public function setConnectionData( $data ) {
    $this->baseDn = $data['sso_base_dn'];
    $this->groupDn = $data['sso_group_dn'];
    $this->ldapHost = $data['sso_ldap_host'];
    $this->ldapPort = $data['sso_ldap_port'];
  }
  
  /* Should be called only once before actual connection to LDAP
   * This function sets constants that will be used in the 
   * Gov_Nasa_Jpl_Oodt_Cas_Security_SingleSignOn class
   */
  private function setConnection() {
    define("CAS_SECURITY",true);
    define("SSO_BASE_DN", $this->baseDn);
    define("SSO_GROUPS_DN", $this->groupDn);
    define("SSO_LDAP_HOST", $this->ldapHost);
    define("SSO_LDAP_PORT", $this->ldapPort);
  }
  
  public function connect() {
    $this->setConnection();
    if ($numargs >= 2) {
       return parent::connect(func_get_arg(0),func_get_arg(1));
     }else{
       return parent::connect($this->ldapHost,$this->ldapPort);
     }
  }
  
  public function disconnect() {
    
  }
  
  public function isLoggedIn() {
    return parent::isLoggedIn();
  }
  
  public function login( $username, $password ) {
    if ( func_num_args() > 2 ) {
      
      $data= func_get_arg(3);
      $this->baseDn= $data['sso_base_dn'];
      $this->groupDn= $data['sso_group_dn'];
      $this->ldapHost= $data['sso_ldap_host'];
      $this->ldapPort= $data['sso_ldap_port'];
    }
    
    $this->setConnection();
    return parent::login( $username, $password );
  }
  
  public function logout() {
    parent::logout();
  }

  public function getCurrentUsername() {
    return parent::getCurrentUsername();
  }
  
  public function changePassword( $newPassword ) {
    // not supported
    return false;
  }
  
  public function validateChangePassword( $newPass ) {
    // not supported
    return false;
  }
  
  public function retrieveUserAttributes( $username, $attributes ) {
    // not supported
    return array();
  }
  
  public function addUser($userInfo) {
    // not supported
    return false;
  }
  
  public function usernameAvailability( $username ) {
    // not supported
    return false;
  }
  
  public function updateProfile($newInfo) {
    // not supported
    return false;
  }
  
}
