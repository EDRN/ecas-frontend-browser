<?php
  //Copyright (c) 2009-2011, California Institute of Technology.
  //ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
  //
  //$Id$

  /**
   * 
   * @package Gov_Nasa_Jpl_Edrn_Security
   *  @author Chris A. Mattmann
   *  @version $Revision$
   *  @copyright Copyright (c) 2009, California Institute of Technology.
   *  ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
   *  @version $Id$
   * 
   *  PHP Single Sign On Library for EDRN PHP-based products.
   * 
   */
class Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication {

  private $baseDn= "dc=edrn, dc=jpl, dc=nasa, dc=gov";

  private $cancerLdapUrl= "ldaps://cancer.jpl.nasa.gov";

  private $dmccLdapUrl= "ldaps://cancer.jpl.nasa.gov";

  private $dmccLdapPort= 19009;

  private $cancerLdapPort= 636;

  private $connectionStatus;

  private $conn;

  const PLONE_COOKIE_KEY= "__ac__sc__";

  public function __construct() {
    $this->connectionStatus= 1;
  }

  public function getCurrentUsername() {
    return $this->getSingleSignOnUsername();
  }

  public function isLoggedIn() {
    return($this->getSingleSignOnUsername() != null);
  }

  public function dump($txt) {
    echo "<li>{$txt}</li>";
  }

  public function login($username, $password) {
    // first check to see if we are already signed on via the portal
    // or via some other app
    if($this->getSingleSignOnUsername() <> "" && strcmp($this->getSingleSignOnUsername(), $username) == 0) {
      // we're logged in already, so set our cookie that sez we're logged in
      // and return true
      return true;
    } else {
      // implement CA-736
      // log in via cancer LDAP
      if(!$this->bind($this->cancerLdapUrl, $this->cancerLdapPort, $username, $password) ||  
	 $this->connectionStatus == 0){

	// Failover to DMCC ldap
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldapconn, LDAP_OPT_REFERRALS, 0);
	$con = ldap_connect($this->dmccLdapUrl . ':' . $this->dmccLdapPort);

	if (ldap_bind($con, "uid={$username},{$this->baseDn}",$password)) {
	  $this->connectionStatus = 1;
	  $this->createSingleSignOnCookie($username,$password);
	  return true;
	} else {
	  return false;
	}
      }
      else {
	return true; // done!
      }
    }
  }

  public function logout() {
    $this->clearSingleSignOnInfo();
  }

  public function getLastConnectionStatus() {
    return($this->connectionStatus == 1);
  }

  public function retrieveGroupsForUser($username) {
    // attempt to connect to ldap server 
    $ldapconn= $this->connect($this->cancerLdapUrl, $this->cancerLdapPort);
    $groups= array();
    if($ldapconn) {
      $filter= "(&(objectClass=groupOfUniqueNames)(uniquemember=uid={$username},{$this->baseDn}))";
      $result= ldap_search($ldapconn, $this->baseDn, $filter, array('cn'));

      if($result) {
        $entries= ldap_get_entries($ldapconn, $result);
        foreach($entries as $rawGroup) {
          if(isset($rawGroup['cn'][0]) && $rawGroup['cn'][0] != '') {
            $groups[]= $rawGroup['cn'][0];
          }
        }
      }
    }

    return $groups;
  }

  public function changePassword($newPass, $encryptionMethod= "SHA") {
    if($this->isLoggedIn()) {
      $user= "uid={$this->getSingleSignOnUsername()},{$this->baseDn}";
      $entry= array();

      switch(strtoupper($encryptionMethod)) {
      case "SHA" :
	$entry['userPassword']= "{SHA} ".base64_encode(pack("H*", sha1($newPass)));
	break;
      case "MD5" :
	$entry['userPassword']= "{MD5} ".base64_encode(pack("H*", md5($newPass)));
	break;
      default :
	throw new Exception("Unsupported encryption method requested");
      }

      if(ldap_mod_replace($this->conn, $user, $entry)) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function connect($server, $port) {
    if($port == 0){
      return false;
    }
    if($conn= ldap_connect($server, $port)) {
      // Connection established
      $this->connectionStatus= 1;
      ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
      ldap_set_option($conn, LDAP_OPT_DEBUG_LEVEL, 7);
      ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
      $this->conn= $conn;
      return $conn;
    } else {
      // Connection failed
      return false;
    }
  }

  private function bind($ldapUrl, $ldapPort, $username, $password) {
    $this->dump("bind-called: {$ldapUrl}:{$ldapPort} ({$username}) ({$password})");
    // connect to ldap server 
    $ldapconn= $this->connect($ldapUrl, $ldapPort);
    // log in via LDAP
    $ldaprdn= "uid=".$username.','.$this->baseDn;
    $ldappass= $password;

    $this->dump("ldap rdn is: {$ldaprdn} {$ldappass}");

    if($ldapconn) {
      // binding to ldap server 
      $ldapbind=  ldap_bind($ldapconn, $ldaprdn, $ldappass);

      // verify binding 
      if($ldapbind) {
        $this->createSingleSignOnCookie($username, $password);
        return true;
      } else {
        return false;
      }

    } else {
      $this->connectionStatus= 0;
      return false;
    }
  }

  private function clearSingleSignOnInfo() {
    $oldCookie= $_COOKIE[Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication :: PLONE_COOKIE_KEY];
    setcookie(Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication :: PLONE_COOKIE_KEY, $oldCookie, 1, "/");
  }

  private function getSingleSignOnUsername() {
    $ploneCookie= $_COOKIE[Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication :: PLONE_COOKIE_KEY];
    if($ploneCookie <> "") {
      $userpass= base64_decode(urldecode($ploneCookie));
      $userpassArr= explode(":", $userpass);
      return $userpassArr[0];
    } else
      return null;
  }

  private function createSingleSignOnCookie($username, $password) {
    if(!isset($_COOKIE[Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication :: PLONE_COOKIE_KEY])) {
      $ploneCookieStrUnencoded= $username.":".$password;
      $ploneCookieStrEncoded= "\"".base64_encode($ploneCookieStrUnencoded)."\"";
      setcookie(Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication :: PLONE_COOKIE_KEY, $ploneCookieStrEncoded, time() +(86400 * 7), "/"); // expire in 1 day
    }
  }

}
?>
