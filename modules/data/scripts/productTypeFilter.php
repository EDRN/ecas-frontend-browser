<?php
/*
 *   PRODUCT-TYPE FILTER
 *   
 *   HTTP Method: GET
 *   Input:
 *     - key    (string) a ProductType metadata key to search on
 *     - value  (string) the value to use when determining matches
 *   Output:
 *     - json   (default) a json array representing all matching product types
 *              with their defined metadata
 *              
 */
$module = App::Get()->loadModule();
require_once($module->modulePath . "/classes/CasBrowser.class.php");
require_once($module->modulePath . "/scripts/widgets/ProductTypeListWidget.php");


function translate($type,$candidate) {
	switch (strtolower($type)) {
		case 'sitename':
			$url = App::Get()->settings['ecas_services_url'] . '/sites.php?id=' . $candidate;
			break;
		case 'protocol':
			$url = App::Get()->settings['ecas_services_url'] . '/protocols.php?id=' . $candidate;
			break;
		default:
			header('HTTP/1.0 400 Bad Request');
			echo json_encode(array());
			exit();
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

// Get a Cas-Browser XML/RPC Client
$browser = new CasBrowser();
$client  = $browser->getClient();

// Get a list of the product types managed by this server
$ptypes = $client->getProductTypes();

// Get the metadata key/val pair that will serve as the needle
$metKey = urldecode($_GET['key']);
$needle = urldecode($_GET['value']);

$productTypes = array();
foreach ($ptypes as $pt) {
	$ptArray = $pt->toAssocArray();
	
	// Check whether the requested met key value matches desired value
	if ($needle == '*' || (isset($ptArray['typeMetadata'][$metKey]) 
		&& $ptArray['typeMetadata'][$metKey][0] == $needle)) {

		$merged = array_merge($ptArray['typeMetadata'],array(
			"name" => array($ptArray[App::Get()->settings['browser_pt_name_key']]),
			"description" => array($ptArray[App::Get()->settings['browser_pt_desc_key']]),
			"id"   => array($ptArray[App::Get()->settings['browser_pt_id_key']])));

		/***EDRN-SPECIFIC***/
		if (isset($merged['SiteName'][0]))   { $merged['SiteName'][0]  = translate('SiteName',$merged['SiteName'][0]); }
		if (isset($merged['ProtocolId'][0])) { $merged['ProtocolName'] = array(translate('Protocol',$merged['ProdocolId'][0])); }
		if (isset($merged['ProtocolID'][0])) { $merged['ProtocolName'] = array(translate('Protocol',$merged['ProdocolID'][0])); }
		/***END EDRN-SPECIFIC***/
		
		$productTypes[$merged['DataSetName'][0]] = $merged;
	}	
}

ksort($productTypes);

// Format output as json
$json = json_encode($productTypes);

// Output the json result
header('Content-Type: application/json');
echo $json;

// We're done.
exit();