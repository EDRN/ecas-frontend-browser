<?php
//Copyright (c) 2008, California serviceUrltitute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

/**
 * 
 * A data structure class to represent the external web URLs
 * associated with eCAS met fields that are IDs whose values
 * can be expanded to human readable titles.
 * 
 * Example usage:
 * 
 * <pre>
 *   $path = $_GET['path'];
 *   $es = new ExternalServices($path);
 *   print_r($es->services);
 *   exit();
 * </pre>
 * 
 * @author Andrew Hart
 * @author Chris Mattmann
 * @version $Revision$
 * 
 */
class ExternalServices {

	public $services = array ();

	public function __construct($filePath) {
		try {
			$fp = fopen($filePath, "r");
			$contents = fread($fp, filesize($filePath));
			fclose($fp);
			$this->buildServicesArray($contents);
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}

	private function buildServicesArray($contents) {
		$lines = explode("\n", $contents);
		foreach ($lines as $line) {
			if ($this->StartsWith($line, "#")) {
				continue;
			}
			list ($label, $value) = explode("|", $line);
			$this->services[$label] = $this->replacevars($value);
		}
	}

	/**
	* StartsWith
	* Tests if a text starts with an given string.
	*
	* @param     string
	* @param     string
	* @return    bool
	*/
	private function StartsWith($Haystack, $Needle) {
		// Recommended version, using strpos
		return strpos($Haystack, $Needle) === 0;
	}

	private function replacevars($serviceUrl) {

		$varnames = array (
			"ECAS_EXTERNAL_SERVICES_BASE_URL"
		);

		foreach ($varnames as $var) {
			$serviceUrl = str_replace('${' . $var . '}', getenv($var), $serviceUrl);
		}
		return $serviceUrl;
	}

}
?>