<?php
/**
 * Copyright (c) 2009, California Institute of Technology.
 * ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
 * 
 * $Id$
 * 
 * EcasBrowser
 * Encapsulates the functionality of the eCAS Browser. This class should
 * be included from all pages in the eCAS Browser web application and
 * should be used to provide an interface to the unique functionality
 * required (XMLRPC connections to the file manager, RESTful connections
 * to external web services) by the eCAS Browser application. 
 * 
 * @author ahart
 *
 */
require_once "Gov/Nasa/Jpl/Edrn/Security/EDRNAuth.php";
class EcasBrowser {
	
	const RESTRICTED = 0;	// Used to indicate content not accessible to the current user
	const ACCESSIBLE = 1;	// Used to indicate content that is accessible to the current user
	
	public $xmlrpc;
	
	private $externalServices;
	
	private $loginStatus;
	private $loginUsername;
	private $loginGroups;
	
	private $edrnAuth;
	
	public function __construct($CasFileManagerUrl,$externalServicesConfigPath) {
		// Create an XML_RPC manager
		$this->xmlrpc = new XmlRpcManager($CasFileManagerUrl,'/');
		
		// Create a new repository of external services
		$this->externalServices = new ExternalServices($externalServicesConfigPath);
		
		// Create an instance of the EDRN security service
		$this->edrnAuth = new Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication();

		// Check and store details about the login status of the current user
		$this->loginStatus   = $this->edrnAuth->isLoggedIn();
		$this->loginUsername = ($this->loginStatus)
			? $this->edrnAuth->getCurrentUsername()
			: false; 
		$this->loginGroups   = ($this->loginStatus)
			? $this->edrnAuth->retrieveGroupsForUser($this->loginUsername)
			: array();
	}
	
	
	/**
	 * displayDatasetsByProtocol
	 * List all datasets in the file manager (subject to the constraints specified in 'options')
	 * grouped by the protocol (dataset collection) that they belong to.
	 * @param $options  An array of options to pass to the function. Options are 
	 * 					specified in standard key => value format. Available options include
	 * 					-ignore: (array) - a list of the product type names to ignore
	 * 					-onlyPublished: (boolean) - show only those whose publishState is not 'no'
	 * 					Example:
	 * 						show all published datasets except those of type 'eCASFile':
	 * 						$options = array(
	 * 							"ignore"		=> array('ECASFile'),
	 * 							"onlyPublished" => true
	 * 						);
	 * 						$eb->displayDatasetsByProtocol($options);
	 * @return none
	 */
	public function displayDatasetsbyProtocol($options = array()) {
		
		$protocols = array();	// The master array of protocols (dataset collections)
		$filtered  = 0;
		
		// Get all product types
		$productTypes = $this->getProductTypes((isset($options['ignore']) ? $options['ignore'] : array()));
			
		// Sort the types into dataset collections by protocol
		foreach ($productTypes as $type){
			
			// Get the translated value for protocol name from the provided protocol id
			$protocolId = $type->getTypeMetadata()->getMetadata('ProtocolName');
			$typeMetAssocArray = $type->getTypeMetadata()->toAssocArray(); 
		 	$response = new EcasHttpRequest(
		 		"{$this->externalServices->services['ProtocolName']}?id={$protocolId}");
			$str = $response->DownloadToString();
			$protocolName = ($str == '') 
				? $protocolId	// No translation available, use the ID
				: $str;			// Use the translated value
			
			// Test if the QAState is 'accepted', in which case anyone
			// should be able to see the dataset
			$bIsAccepted = false;
			if (isset($typeMetAssocArray['QAState'])) {
				$bIsAccepted = (strtolower($typeMetAssocArray['QAState'][0]) == "accepted");
			}
			
			// If an authenticated user is present...
			if ($this->loginStatus == true) {
				// If the QAState is NOT 'accepted' then use LDAP groups
				// to determine whether or not the dataset should be visible
				if (!$bIsAccepted) {
					// If at least one of the "AccessGrantedTo" metadata values for
					// a given dataset matches an LDAP group associated with the currently logged
					// in user, then the dataset should be made visible. Otherwise it is hidden.

					// Get the LDAP groups that should have access to this dataset
					$datasetAccessGroups = isset($typeMetAssocArray['AccessGrantedTo'])
						? $typeMetAssocArray['AccessGrantedTo']
						: array();
						
					// Compare against access groups for the currently logged in user
					$ix = array_intersect($datasetAccessGroups,$this->loginGroups);
					if (empty($ix)) {
						// No access groups match the groups for the currently logged in
						// user, and QAState !='accepted' so this dataset should not be shown.
						$filtered++;
						$protocols[$protocolName][$type->getName()] = array(EcasBrowser::RESTRICTED,$type);
						continue;
					}
				}
			} else {
				// No authenticated user is present..
				if (!$bIsAccepted) {
					// The dataset is not QAState='accepted' so do not show it.
					$filtered++;
					$protocols[$protocolName][$type->getName()] = array(EcasBrowser::RESTRICTED,$type);
					continue;
				}
			}
			
			// Add the product type to the appropriate protocol
			$protocols[$protocolName][$type->getName()] = array(EcasBrowser::ACCESSIBLE,$type);
		}
		
		// Sort protocols by name
		uksort($protocols,"eb_protocolSort");
		
		// Finally, generate the list of protocols (dataset collections)
		if ($filtered > 0) {
			
			$plural = ($filtered != 1) ? 's':'';
			echo "<div class=\"notice\"><strong>Note:</strong> some dataset{$plural} not available due to security restrictions.</div>";
		}
		echo "<div class=\"dataset-summary\">";
		foreach ($protocols as $protName => $datasets) {
			echo "<h2 style=\"margin-right:100px\">
					<span class=\"redBullet\">&nbsp;</span>
						{$protName} <span class=\"datasetCount\">datasets: ".count($datasets)."</span>
				  </h2>";
			echo "<ul class=\"sublist\">";
			foreach ($datasets as $d) {
				$this->displayDatasetSummary($d);
			}
			echo "</ul>";
		}
		echo "</div>";
	}
	
	
	/**
	 * isDatasetAccessible
	 * Determine whether or not a particular dataset is accessible by
	 * any of a provided set of user LDAP groups.
	 * 
	 * @param $datasetID    - the dataset to check
	 * @param $userGroups   - the set of user groups to test
	 * @return boolean      - true if dataset is visible to at least one of $userGroups
	 */
	public function isDatasetAccessible($datasetID,$userGroups) {
		if (($pt = $this->getProductType($datasetID)) != null) {
			$qastate = strtolower( $pt->getTypeMetadata()->getMetadata('QAState') );
			$datasetAccessGroups = $pt->getTypeMetadata()->getMetadata('AccessGrantedTo');
			
			// QAState overrides all...
			if ($qastate == "accepted") { return true; }
			
			// Test permission groups
			if (is_array($datasetAccessGroups))	{
				$ix = array_intersect($datasetAccessGroups,$userGroups);
				return (!empty($ix));
			} else {
				// No permission match
				return false;
			}
		} else {
			// No such dataset
			return false;
		}
	}
	
	
	/**
	 * isProductAccessible
	 * Determine whether or not a particular dataset is accessible by
	 * any of a provided set of user LDAP groups.
	 * 
	 * @param $productID    - the productID to check
	 * @param $userGroups   - the set of user groups to test
	 * @return boolean      - true if product is visible to at least one of $userGroups
	 */
	public function isProductAccessible($productID,$userGroups) {
		if (($p = $this->getProduct($productID)) != null) {
			$pt = $this->getProductType($p->getType()->getId());
			$qastate = strtolower( $pt->getTypeMetadata()->getMetadata('QAState') );
			$datasetAccessGroups = $pt->getTypeMetadata()->getMetadata('AccessGrantedTo');
			
			// QAState overrides all...
			if ($qastate == "accepted") { return true; }
			
			// Test permission groups
			if (is_array($datasetAccessGroups))	{
				$ix = array_intersect($datasetAccessGroups,$userGroups);
				return (!empty($ix));
			} else {
				// No permission match
				return false;
			}
		} else {
			// No such product
			return false;
		}
	}
	
	
	/************************************************************************
	 * UTILITY FUNCTIONS
	 * 
	**/
	public function getProductType($id) {
		return new ProductType($this->xmlrpc->getProductTypeById($id));
	}
	
	public function getProductTypes($ignores = array()) {
		$typeArray   = $this->xmlrpc->getProductTypes();
		uasort($typeArray,"eb_productTypeSort");
		
		// Skip those product types that should be ignored
		$returnTypes = array();
		foreach ($typeArray as $t) {
			$bIgnore = false;
			foreach ($ignores as $i) {
				if ($t['name'] == $i) {
					$bIgnore = true;
					break;	
				}
			}
			if (!$bIgnore) {
				$returnTypes[$t['name'] ] = new ProductType($t);
			}
		}
		
		return $returnTypes;
	}
	
	public function getProduct($id) {
		return new Product($this->xmlrpc->getProductById($id));
	}
	
	public function getProductReferences($product) {
		return $this->xmlrpc->getproductreferences($product);
	}
	
	public function getMetadata($product) {
		return $this->xmlrpc->getMetadata($product);
	}
	
	/**
	 * Display summary information about a dataset
	 * 
	 * @param $dsInfo array  An array consisting of two elements:
	 *   [0] A security designation indicating the availability of the content
	 *       in the context of the current user. This will be one of
	 *       EcasBrowser::ACCESSIBLE or EcasBrowser::RESTRICTED
	 *       
	 *   [1] A ProductType object representing the dataset
	 */
	public function displayDatasetSummary($dsInfo) {
		
		$accessibility = $dsInfo[0];
		$productType   = $dsInfo[1]; 
				
		// Get some metadata elements to display
		$id              = $productType->getId();
		$productCount    = $this->xmlrpc->getNumProducts($productType);
		$name            = $productType->getTypeMetadata()->getMetadata('DataSetName');
		$collabGroupName = $productType->getTypeMetadata()->getMetadata('CollaborativeGroup');
		$organName       = $productType->getTypeMetadata()->getMetadata('OrganSite');
		$pi              = $productType->getTypeMetadata()->getMetadata('LeadPI');
		   
		// Display the product type summary information
		echo "<li>";
		if ($accessibility == EcasBrowser::ACCESSIBLE) {
			echo "<span class=\"title\">{$name} (<a href=\"./dataset.php?typeID={$id}\">{$productCount} products</a>)</span><br/>\n";		
		} else {
			echo "<span class=\"title restricted\">{$name} (<span class=\"restricted\">{$productCount} products</span></span><br/>\n";
		}
		echo "<span class=\"details\">[ PI: {$pi}, Organ: {$organName}, Collaborative Group: {$collabGroupName} ]</span><br/>";
		echo "</li>";
	}
	
	public function getLoginStatus() {
		return $this->loginStatus;
	}
	
	public function checkLoginStatus() {

		$referrer = $_SERVER['REQUEST_URI'];
		
		if ($this->edrnAuth->isLoggedIn()) {
			$this->loginStatus   = true;
			$this->loginUsername = $this->edrnAuth->getCurrentUsername(); 
			$this->loginGroups   = $this->edrnAuth->retrieveGroupsForUser($this->loginUsername);
			return "Logged in as {$this->loginUsername}. <a href=\"logout.php?from={$referrer}\">Log Out</a>";
		} else {
			$this->loginStatus   = false;
			$this->loginUsername = false;
			$this->loginGroups   = array();
			return "Not logged in. <a href=\"login.php?from={$referrer}\">Log in</a>";
		}
	}	
	
	public function decode($str){
		$string = ereg_replace("%20"," ",$str);
		return $string;
	}
}

/*
 * CUSTOM SORT FUNCTIONS
 */

// Sort Protocols by name
function eb_protocolSort($a, $b){
	return strcasecmp($a, $b);
}

// Sort ProductTypes by name
function eb_productTypeSort($a, $b){
	
	$prodType1 = new ProductType($a);
	$prodType2 = new ProductType($b);

	return strcasecmp($prodType1->getName(), $prodType2->getName());
}

?>