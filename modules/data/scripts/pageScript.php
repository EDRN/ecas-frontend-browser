<?php

require_once("CasBrowser.class.php");

// Get client handle
$cb = new CasBrowser();
$client = $cb->getClient();

// Extract ProductType from POST
if(!isset($_POST['Type'])){
	Utils::reportError("Error: POST does not contain 'Type' ProductType");
}
$typeName = $_POST['Type'];
$allTypes = $client->getProductTypes();
$allTypeNames = array_map(create_function('$t', 'return $t->getName();'), $allTypes);
if(!in_array($typeName, $allTypeNames)){
	$errStr = "Error: The type " . $typeName . " is not used in the repository.  Please use one of: ";
	for($i = 0; $i < count($allTypeNames) - 1; $i++){
		$errStr .= $allTypeNames[i] . ", ";
	}
	$errStr .= $allTypeNames[count($allTypeNames) - 1];
	Utils::reportError($errStr);
}
$type = $client->getProductTypeByName($typeName);

// Extract page number from POST
if(!isset($_POST['PageNum'])){
	Utils::reportError("Error: POST does not contain 'PageNum'");
}
$pageNum = intval($_POST['PageNum']);

// Get the requested page
$page = Utils::getPage($type, $pageNum);

// Get the products from the requested page -- what we're really after
$pageProducts = array();
foreach($page->getPageProducts() as $p){
	array_push($pageProducts, array('product'=>$p, 'metadata'=>$client->getMetadata($p)));
}

// Extract desired output format from POST
if(isset($_POST['OutputFormat'])){
	$outputFormat = Utils::getRequestedReturnType($_POST['OutputFormat']);
}else{
	$outputFormat = 'html';
}

// Format results
if($outputFormat == 'html'){
	$payload = '<ul class="pp_productList" id="product_list">';
	foreach($pageProducts as $p){
		$payload .= '<li><a href="' . MODULE_ROOT . '/product/' . $p['product']->getId() . '">';
		$payload .= urlDecode($p['product']->getName()) . '</a></li>';
	}
	$payload .= "</ul>\n";
	$payload .= '<input type="hidden" id="total_pages" value="' . $page->getTotalPages() . '">';
	$payload .= '<input type="hidden" id="page_size" value="' . $page->getPageSize() . '">';
}elseif ($outputFormat == 'json') {
	$payload = array();
	$payload['productList'] = array();
	foreach($pageProducts as $p){
		array_push($payload['productList'], array('id'=>$p['product']->getId(), 'name'=>urlDecode($p['product']->getName()), 'metadata'=>$p['metadata']->toAssocArray()));
	}
	$payload['totalPages'] = $page->getTotalPages();
	$payload['pageSize'] = $page->getPageSize();
	$payload = json_encode($payload);
}

echo $payload;

?>