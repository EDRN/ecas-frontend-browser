<?php
//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

/**
 * 
 * Configuration variables for the eCAS UI.
 * 
 * @author Chris Mattmann
 * @author Andrew Hart
 * @version $Revision$
 * 
 */

// set this to the filemgr url that this UI will connect to
$FILEMGR_URL   = getenv("EDRN_ECAS_FILEMGR_URL");

// set this to the url to the data delivery product server
// Note: do not include a trailing slash ('/')
$DATADELIV_URL = getenv("EDRN_ECAS_DATA_DELIV_URL");

// set this to the path of the file containing the URLs for
// the ecas mapping services 
$EXTERNAL_SERVICES_PATH = "./services/services.txt";

?>
