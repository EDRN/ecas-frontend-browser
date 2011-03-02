<?php
/**
 * Copyright (c) 2010, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * CAS-Browser Module
 * 
 * This module provides applications a means for browsing a CAS File 
 * Manager catalog and obtaining products from a CAS File Manager repository.
 * 
 * For complete functionality, the following configuration variables
 * are expected to be present in the containing application's config.ini file:
 * 
 * browser_filemgr_url    - filemanager host (e.g.: http://somehost:9000)
 * browser_filemgr_path   - filemanager url on server (e.g.: /)  
 * browser_datadeliv_url  - the base url to use when downloading products
 *   
 * NOTE: This module has a dependency upon the CAS-Filemgr PHP classes
 * (http://oodt.jpl.nasa.gov/repo/framework/cas-filemgr/trunk/src/main/php)
 *      
 *      To build this dependency, check out the above project and then:
 *      1) cd into the checked out project (you should see a package.xml file)
 *      2) pear package
 *      3) (sudo) pear install --force CAS_Filemgr...tar.gz
 *   
 * @author ahart
 *
 */
// Require CAS Filemgr Classes
require_once("CAS/Filemgr/BooleanQueryCriteria.class.php");
require_once("CAS/Filemgr/Element.class.php");
require_once("CAS/Filemgr/Metadata.class.php");
require_once("CAS/Filemgr/Product.class.php");
require_once("CAS/Filemgr/ProductType.class.php");
require_once("CAS/Filemgr/ProductPage.class.php");
require_once("CAS/Filemgr/Query.class.php");
require_once("CAS/Filemgr/RangeQueryCriteria.class.php");
require_once("CAS/Filemgr/TermQueryCriteria.class.php");
require_once("CAS/Filemgr/XmlRpcFilemgrClient.class.php");
require_once(dirname(__FILE__) . "/Utils.class.php");


class CasBrowser {
	
	const VIS_INTERPRET_HIDE     = 'hide';
	const VIS_INTERPRET_SHOW     = 'show';
	const VIS_AUTH_ANONYMOUS     = false;
	const VIS_AUTH_AUTHENTICATED = true;	
	
	public $client;
	
	public function __construct() {
		try {
			$this->client = new CAS_Filemgr_XmlRpcFilemgrClient(
				App::Get()->settings['browser_filemgr_url'],
				App::Get()->settings['browser_filemgr_path']);
		} catch (Exception $e) {
			App::Get()->fatal("Unable to instantiate a connection to "
				. App::Get()->settings['browser_filemgr_url']
				. App::Get()->settings['browser_filemgr_path']);	
		}
	} 
	
	public function getClient() {
		return $this->client;
	}
	
	/**
	 * Internal helper function for sorting(ordering) a metadata array according to policy. 
	 * 
	 * @param array $unsortedMetadata An associative array of unsorted metadta key/(multi)values
	 * @param array $sortFirst        A scalar array of metadata keys that must be ordered first
	 * @param array $sortLast         A scalar array of metadata keys that must be ordered last
	 * @returns array An associative array of sorted(ordered) metadata key/(multi)values
	 */
	protected function sortMetadata($unsortedMetadata,$sortFirst,$sortLast) {
		$orderedMetadata = array();
		foreach ($sortFirst as $key) {
			if (isset($unsortedMetadata[$key])) {
				$orderedMetadata[$key] = $unsortedMetadata[$key];
				unset($unsortedMetadata[$key]);
			}
		}
		$lastMetadata = array();
		foreach ($sortLast as $key) {
			if (isset($unsortedMetadata[$key])) {
				$lastMetadata[$key] = $unsortedMetadata[$key];
				unset($unsortedMetadata[$key]);
			}
		}
		$orderedMetadata += $unsortedMetadata;
		$orderedMetadata += $lastMetadata;
		
		return $orderedMetadata;
	}
	
	
	/**
	 * Use the rules in element-ordering.ini to determine the display order
	 * for product type metadata elements. See element-ordering.ini for more
	 * information on how to specify element order rules.
	 * 
	 * @param array $productTypeId The id of the product type to get met for
	 */
	public function getSortedProductTypeMetadata($productTypeId,$metadataToUse = null) {
		
		if (!is_array($metadataToUse)) {
			$pt = $this->client->getProductTypeById($productTypeId);
			$pt = $pt->toAssocArray();
			$metadataAsArray = $pt['typeMetadata'];
		} else {
			$metadataAsArray = $metadataToUse;
		}
		
		$orderingPolicyFilePath = dirname(dirname(__FILE__)) . '/element-ordering.ini';
		if (file_exists($orderingPolicyFilePath)) {
			$orderPolicy = parse_ini_file($orderingPolicyFilePath,true);

			$first    = isset($orderPolicy[$productTypeId]['pt.element.ordering.first']) 
				? $orderPolicy[$productTypeId]['pt.element.ordering.first']
				: $orderPolicy['*']['pt.element.ordering.first'];
			$last     = isset($orderPolicy[$productTypeId]['pt.element.ordering.last']) 
				? $orderPolicy[$productTypeId]['pt.element.ordering.last']
				: $orderPolicy['*']['pt.element.ordering.last'];
								
			// Using the odering policy, determine the order in which the metadata will be listed
			return $this->sortMetadata($metadataAsArray,$first,$last);	
		} else {
			return $metadataAsArray;
		}
	}
	
	public function getProductTypeVisibleMetadata($productTypeId,$authState = self::VIS_AUTH_ANONYMOUS) {
		$pt = $this->client->getProductTypeById($productTypeId);
		$pt = $pt->toAssocArray();
		
		return $this->getVisibleMetadata($pt['typeMetadata'], $productTypeId, $state);
	}
	
	public function getProductVisibleMetadata($productId,$authState = self::VIS_AUTH_ANONYMOUS) {
		$p  = $this->client->getProductById($productId);
		$productTypeInfo = $p->getType()->toAssocArray();
		$productTypeId   = $productTypeInfo[App::Get()->settings['browser_pt_id_key']];
		$productMetadata = $this->client->getMetadata($p);
		
		return $this->getVisibleMetadata($productMetadata->toAssocArray(), $productTypeId, $state);		
	}
	
	protected function getVisibleMetadata($metadataAsArray, $productTypeId, $state) {
		
		$visibilityPolicyFilePath = dirname(dirname(__FILE__)) . '/element-visibility.ini';
		if (file_exists($visibilityPolicyFilePath)) {
			$visibilityPolicy = parse_ini_file($visibilityPolicyFilePath,true);
			$interp     = $visibilityPolicy['interpretation.policy'];
			$global_vis = $visibilityPolicy['*'];
			$pt_vis     = isset($visibilityPolicy[$productTypeId])
				? $visibilityPolicy[$productTypeId]
				: array("visibility.always" => array(),
						"visibility.anonymous" => array(),
						"visibility.authenticated" => array());

				// Using the visibility policy, determine which metadata to display
				switch ($interp) {
					// If the policy defines only those metadata which should be hidden:
					case self::VIS_INTERPRET_HIDE:
						$displayMet = $metadataAsArray;                               // everything is shown unless explicitly hidden via the policy
						foreach ($global_vis['visibility.always'] as $elm)            // iterate through the global 'always hide' array...
							unset($displayMet[$elm]);                                 // and remove all listed elements
						foreach ($pt_vis['visibility.always'] as $elm)                // now iterate through the product-type 'always hide' array...
							unset($displayMet[$elm]);                                 // and remove all listed elements
						switch ($state) {                                             // check the login status of the user
							case self::VIS_AUTH_ANONYMOUS:                            // if the user is anonymous...
								foreach($global_vis['visibility.anonymous'] as $elm)  // iterate through the global 'anonymous hide' array...
									unset($displayMet[$elm]);                         // and remove all listed elements
								foreach ($pt_vis['visibility.anonymous'] as $elm)     // now iterate through the product-type 'anonymous hide' array...
									unset($displayMet[$elm]);                         // and remove all listed elements
								break;                                                // done.
							case self::VIS_AUTH_AUTHENTICATED:                        // if the user is authenticated...
								foreach($global_vis['visibility.authenticated'] as $elm)  // iterate through the global 'authenticated hide' array...
									unset($displayMet[$elm]);                             // and remove all listed elements
								foreach ($pt_vis['visibility.authenticated'] as $elm)     // now iterate through the product-type 'authenticated hide' array...
									unset($displayMet[$elm]);                             // and remove all listed elements
								break;                                                    // done.
						}
						break;
					// If the policy defines only those metadata which should be shown:
					case self::VIS_INTERPRET_SHOW:
						$displayMet = $global_vis['visibility.always']                // merge the global 'always show' array
							+ $pt_vis['visibility.always'];                           // with the product-type specific 'always show' array
						switch ($state) {                                             // check the login status of the user
							case self::VIS_AUTH_ANONYMOUS:                            // if the user is anonymous...
								$displayMet += $global_vis['visibility.anonymous'];   // merge the global 'anonymous show' array
								$displayMet += $pt_vis['visibility.anonymous'];       // and the product-type specific 'anonymous show' array
								break;                                                // done.
							case self::VIS_AUTH_AUTHENTICATED:                        // if the user is authenticated...
								$displayMet += $global_vis['visibility.authenticated'];   // merge the global 'authenticated show' array 
								$displayMet += $pt_vis['visibility.authenticated'];       // and the product-type specific 'authenticated show' array
								break;                                                    // done.
						}
				}
				
				return $displayMet;				
				
		} else {
			return $metadataAsArray;
		}
	}
}
