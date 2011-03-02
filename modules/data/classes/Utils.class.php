<?php

require_once("CasBrowser.class.php");

class Utils{
	
	public static $acceptedReturnTypes = array('html', 'json');

	/**
	 * @param types
	 *		An array of PoductTypes.
	 *
	 * @return 
	 *		An array of unique names of Metadata and Elements associated with the given 
	 *		ProductTypes.
	 */
	public static function getMetadataElements($types){
		$cb = new CasBrowser();
		$client = $cb->getClient();
		$metadataNames = array();
		foreach($types as $type){
			foreach(array_keys($type->getTypeMetadata()->toAssocArray()) as $metadata){
				if(!in_array($metadata, $metadataNames)){
					array_push($metadataNames, $metadata);
				}
			}
			foreach($client->getElementsByProductType($type) as $element){
				$elementName = $element->getElementName();
				if(!in_array($elementName, $metadataNames)){
					array_push($metadataNames, $elementName);
				}
			}
		}
		return $metadataNames;
	}
	
	/**
	 * @param productType
	 *		The productType of the ProductPage desired.
	 *
	 * @param pageNum
	 *		The number of the page desired in the set of ProductPages of that ProductType.
	 *
	 * @return
	 *		The requested ProductPage object.
	 */
	public static function getPage($productType, $pageNum){
		$cb = new CasBrowser();
		$client = $cb->getClient();
		
		// Iterate until the proper page is reached
		for($page = $client->getFirstPage($productType);
			$page->getPageNum() < $pageNum && $page->getPageNum() < $page->getTotalPages();
			$page = $client->getNextPage($productType, $page)){}
			
		return $page;
	}
	
	/**
	 * This function will echo the given error message to the page that invoked
	 * the script that calls this function and then exit.
	 * 
	 * @param $errorText
	 * 		The message string to be echoed.
	 */
	public static function reportError($errorText){
		echo '<div class="error">' . $errorText . '</div>';
		exit();
	}
	
	public static function getRequestedReturnType($requestedType){
		$lowerRequestedType = strtolower($requestedType);
		if(!in_array($lowerRequestedType, self::$acceptedReturnTypes)){
			reportError('Error: The requested return type of '. $requestedType . 'is not accepted.');
		}
		return $lowerRequestedType;
	}
	
	/**
	 * Determines whether a user can view a resource by comparing the resource's
	 * `AccessGrantedTo` groups with the current user's groups (if any). If the 
	 * special group `Public` appears in `$resourceSecurityGroups` then the result is always
	 * true. Otherwise, the result is true only if there is some overlap between
	 * the resource's and the user's security group lists.
	 * 
	 * @param array $resourceSecurityGroups  Groups defined for the resource in question
	 * @param array $userSecurityGroups      Groups defined for the current user (empty if not logged in)
	 */
	public static function UserCanView($resourceSecurityGroups,$userSecurityGroups = array()) {
		if (in_array('Public',$resourceSecurityGroups)) { 
			return true;
		}
		else {
			$x = array_intersect($userSecurityGroups,$resourceSecurityGroups);
			return (!empty($x));
		}
	}
}

?>