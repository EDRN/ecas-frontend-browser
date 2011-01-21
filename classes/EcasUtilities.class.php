<?php
/**
 * Utility functions for the ECAS Browser
 * 
 * @author ahart
 */
class EcasUtilities {
	
	/**
	 * Perform a translation request utilizing the external ECAS services
	 * for the translation lookup.
	 * 
	 * Note that this function expects the configuration setting 
	 *   `external_services_base_url` to be defined in config.ini. The value should
	 *   point to the fully qualified URL of the base directory for the service
	 * 
	 * @param string $type      the type of translation to perform (site, protocol,etc)
	 * @param string $candidate the value to translate
	 */
	public static function translate($type,$candidate) {
		switch (strtolower($type)) {
			case 'site':
				$url = App::Get()->settings['external_services_base_url'] 
					 . '/ecas-services/sites.php?id=' . $candidate;
				break;
			case 'protocol':
				$url = App::Get()->settings['external_services_base_url'] 
					 . '/ecas-services/protocols.php?id=' . $candidate;
				break;
		}
		
		// The default is to use curl to make the request
		if ($useCurl && function_exists('curl_init')) {
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_URL,$url);
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		} 
		// Otherwise, use fopen as a fallback
		else {
			$opts = array(
				'http' => array (
					'method' => 'GET',
				)
			);
			$ctx = stream_context_create($opts);
			$handle = fopen ($url, 'r', false, $ctx);
			return stream_get_contents($handle);
		}
	}
}