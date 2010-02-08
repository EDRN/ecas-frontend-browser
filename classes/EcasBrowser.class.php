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
class EcasBrowser {
	
	public $xmlrpc;
	
	private $externalServices;
	
	private $loginStatus;
	
	public function __construct($CasFileManagerUrl,$externalServicesConfigPath) {
		// Create an XML_RPC manager
		$this->xmlrpc = new XmlRpcManager($CasFileManagerUrl,'/');
		
		// Create a new repository of external services
		$this->externalServices = new ExternalServices($externalServicesConfigPath);

		// Check the login status of the current user
		$this->loginStatus = $this->checkLoginStatus();
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
		
		// Get all product types
		$productTypes = $this->getProductTypes((isset($options['ignore']) ? $options['ignore'] : array()));
			
		// Sort the types into dataset collections by protocol
		foreach ($productTypes as $type){
			
			// Get the translated value for protocol name from the provided protocol id
			$protocolId = $type->getTypeMetadata()->getMetadata('ProtocolName');
		 	$response = new EcasHttpRequest(
		 		"{$this->externalServices->services['ProtocolName']}?id={$protocolId}");
			$str = $response->DownloadToString();
			$protocolName = ($str == '') 
				? $protocolId	// No translation available, use the ID
				: $str;			// Use the translated value
			
			// Filter unpublished datasets if 'onlyPublished' option is set
			if (isset($options['onlyPublished']) && $options['onlyPublished'] == true) {
				$publishState = $typeMetAssocArray["PublishState"][0];
				if ($publishState == "no") {
				      continue;
				}
			}
			
			// Only show accepted datasets if 'onlyAccepted' option is set
			if (isset($options['onlyAccepted']) && $options['onlyAccepted'] == true) {
				if (isset($typeMetAssocArray['QAState'])) {
					$qastate = strtolower($typeMetAssocArray['QAState'][0]);
					if ($qastate != 'accepted') {
						continue;
					}
				} else {
					// The dataset did not have 'QAState' metadata, so default is to
					// not show the dataset.
					continue;	
				}
			}
			
			// Add the product type to the appropriate protocol
			$protocols[$protocolName][$type->getName()] = $type;
		}
		
		// Sort protocols by name
		uksort($protocols,"eb_protocolSort");
		
		// Finally, generate the list of protocols (dataset collections)
		echo "<div class=\"dataset-summary\">";
		foreach ($protocols as $protName => $datasets) {
			echo "<h2>
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
	
	public function displayDatasetSummary($productType) {
				
		// Get some metadata elements to display
		$id              = $productType->getId();
		$productCount    = $this->xmlrpc->getNumProducts($productType);
		$name            = $productType->getTypeMetadata()->getMetadata('DataSetName');
		$collabGroupName = $productType->getTypeMetadata()->getMetadata('CollaborativeGroup');
		$organName       = $productType->getTypeMetadata()->getMetadata('OrganSite');
		$pi              = $productType->getTypeMetadata()->getMetadata('LeadPI');
		   
		// Display the product type summary information
		echo "<li>";
		echo "<span class=\"title\">{$name} (<a href=\"./dataset.php?typeID={$id}\">{$productCount} products</a>)</span><br/>\n";		
		echo "<span class=\"details\">[ PI: {$pi}, Organ: {$organName}, Collaborative Group: {$collabGroupName} ]</span><br/>";
		echo "</li>";
	}
	
	public function getLoginStatus() {
		return $this->loginStatus;
	}
	
	private function checkLoginStatus() {
		return "logged in as guest";
		/*
		$referrer = $_SERVER['REQUEST_URI'];
		$edrnAuth = new Gov_Nasa_Jpl_Edrn_Security_EDRNAuthentication();
		
		return ($edrnAuth->isLoggedIn()) 
			? "Logged in as {$edrnAuth->getCurrentUsername()}. <a href=\"logout.php?from={$referrer}\">Log Out</a>"
			: "Not logged in. <a href=\"login.php?from={$referrer}\">Log in</a>";
		*/
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