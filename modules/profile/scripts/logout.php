<?php
/**
 * Copyright (c) 2009, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * 
 * PROFILE LOGOUT
 * 
 * End an authenticated user session
 * 
 * @author ahart
 * @author davoodi
 * @author s.khudikyan
**/

// Log the user out
App::Get()->getAuthenticationProvider()->logout();

// End user session
App::Get()->EndUserSession();

// Redirect to the home page
App::Get()->Redirect(SITE_ROOT . "/");