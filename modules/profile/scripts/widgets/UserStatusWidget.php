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

class UserStatusWidget 
	implements Gov_Nasa_Jpl_Oodt_Balance_Interfaces_IApplicationWidget {
		
	public $isLoggedIn;
	public $username;
	public $profileLink;
	
	/**
	 * Pass boolean true or false as the first parameter in the options array
	 * to generate either a login link or a logout link, whichever is appropriate.
	 *  	True:  user is logged in, show logout link
	 *  	False: user is not logged in, show login link
	 * 
	 * Pass username as the second paramerter in the options array
	 * 
	 * Pass boolean true or false as the third parameter in the options array
	 * to generate the username as a link to the porfile module.
	 *  	True:  link will be created- *Default*
	 *  	False: link will not be created 
	 */ 
	public function __construct($options = array()) {
		$this->isLoggedIn  = ( isset($options[0])   && $options[0] === true );
		$this->username    = ( isset($options[1]) ) ?  $options[1] : false;
		$this->profileLink = ( isset($options[2]) ) ?  $options[2] : true;
	}
	
	public function render($bEcho = true) {
		
		$str = '';
		
		// Display the appropriate information about the user
		if($this->isLoggedIn) {
			$str .= "Logged in as ";
			if ($this->profileLink) {
				$str .= '<a href="' . SITE_ROOT . '/profile/">' . $this->username . '</a>&nbsp;|&nbsp;'
					.'<a href="' . SITE_ROOT . '/profile/logout.do">Log Out</a>';
			} else {
				$str .=  $this->username . '&nbsp;|&nbsp;'
						.'<a href="' . SITE_ROOT . '/profile/logout.do">Log Out</a>';
			}
		} else {
			$str .= '<a href="' . SITE_ROOT . '/profile/login">Log In</a>';
		}
		
		if($bEcho) {
			echo $str;
		} else {
			return $str;
		}
	}
	
}