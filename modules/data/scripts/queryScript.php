<?php

require_once("CasBrowser.class.php");

// Create a criteria subtree that will search for the value at the given criteriaIndex across all 
// metadata elements associated with the given productTypes.
function createBasicSearchSubtree($criteriaIndex, $queryTypes){
	$criterion = new CAS_Filemgr_BooleanQueryCriteria();
	$criterion->setOperator(CAS_Filemgr_BooleanQueryCriteria::$OR_OP);
	$metadataNames = Utils::getMetadataElements($queryTypes);
	foreach($metadataNames as $name){
		$term = new CAS_Filemgr_TermQueryCriteria();
		$term->setElementName($name);
		$term->setValue($_POST['Criteria'][$criteriaIndex]['Value']);
		$criterion->addTerm($term);
	}
	return $criterion;
}

function createTermCriteria($criteriaIndex, $queryTypes){
	if(!isset($_POST['Criteria'][$criteriaIndex]['ElementName'])){
		Utils::reportError("Error: Query Term criterion " . $criteriaIndex . " does not contain 'ElementName' specification");
	}
	if(!isset($_POST['Criteria'][$criteriaIndex]['Value'])){
		Utils::reportError("Error: Query Term criterion " . $criteriaIndex . " does not contain 'Value' specification");
	}
	if($_POST['Criteria'][$criteriaIndex]['ElementName'] == '*'){
		$criterion = createBasicSearchSubtree($criteriaIndex, $queryTypes);
	}else{
		$criterion = new CAS_Filemgr_TermQueryCriteria();
		$criterion->setElementName($_POST['Criteria'][$criteriaIndex]['ElementName']);
		$criterion->setValue($_POST['Criteria'][$criteriaIndex]['Value']);
	}
	return $criterion;
}

function createRangeCriteria($criteriaIndex){
	if(!isset($_POST['Criteria'][$criteriaIndex]['ElementName'])){
		Utils::reportError("Error: Query Term criterion " . $criteriaIndex . " does not contain 'ElementName' specification");
	}
	if(!isset($_POST['Criteria'][$criteriaIndex]['Min'])){
		Utils::reportError("Error: Query Range criterion " . $criteriaIndex . " does not contain 'Min' specification");
	}
	if(!isset($_POST['Criteria'][$criteriaIndex]['Max'])){
		Utils::reportError("Error: Query Range criterion " . $criteriaIndex . " does not contain 'Max' specification");
	}
	$criterion = new CAS_Filemgr_RangeQueryCriteria();
	$criterion->setElementName($_POST['Criteria'][$criteriaIndex]['ElementName']);
	$criterion->setStartValue($_POST['Criteria'][$criteriaIndex]['Min']);
	$criterion->setEndValue($_POST['Criteria'][$criteriaIndex]['Max']);
	if(isset($_POST['Criteria'][$criteriaIndex]['Inclusive'])){
		$criterion->setInclusive($_POST['Criteria'][$criteriaIndex]['Inclusive']);
	}
	return $criterion;
}

function createBooleanCriteria($criteriaIndex, $queryTypes, $createdIndices){
	if(!isset($_POST['Criteria'][$criteriaIndex]['Operator'])){
		Utils::reportError("Error: Query Boolean criterion " . $criteriaIndex . " does not contain 'Operator' specification");
	}
	if(!isset($_POST['Criteria'][$criteriaIndex]['CriteriaTerms'])){
		Utils::reportError("Error: Query Boolean criterion " . $criteriaIndex . " does not contain 'CriteriaTerms' specification");
	}
	if(!count($_POST['Criteria'][$criteriaIndex]['CriteriaTerms'])){
		Utils::reportError("Error: Query Boolean criterion " . $criteriaIndex . " does not contain any terms");
	}
	$criterion = new CAS_Filemgr_BooleanQueryCriteria();
	$operator = trim(strtoupper($_POST['Criteria'][$criteriaIndex]['Operator']));
	if($operator == 'AND'){
		$criterion->setOperator(CAS_Filemgr_BooleanQueryCriteria::$AND_OP);
	}elseif($operator == 'OR'){
		$criterion->setOperator(CAS_Filemgr_BooleanQueryCriteria::$OR_OP);
	}elseif($operator == 'NOT'){
		if(count($_POST['Criteria'][$criteriaIndex]['CriteriaTerms']) != 1){
			Utils::reportError("Error: Query Boolean criterion " . $criteriaIndex . " cannot negate more than one term");
		}
		$criterion->setOperator(CAS_Filemgr_BooleanQueryCriteria::$NOT_OP);
	}else{
		Utils::reportError("Error: Query Boolean criterion " . $criteriaIndex . " tries to use undefined operator '" . $operator . "'");
	}
	foreach(array_map("intval", $_POST['Criteria'][$criteriaIndex]['CriteriaTerms']) as $childIndex){
		if(in_array($childIndex, $createdIndices)){		// Check for loops in criteria tree
			Utils::reportError("Error: Criterion " . $criteriaIndex . " lists " . $childIndex . "as a child, making a loop.");
		}
		array_push($createdIndices, $childIndex);
		$child = createCriteriaTree($childIndex, $queryTypes);
		$criterion->addTerm($child);
	}
	return $criterion;
}

function createCriteriaTree($criteriaIndex, $queryTypes, $createdIndices=null){
	if(!isset($createdIndices)){
		$createdIndices = array();
	}
	if(!isset($_POST['Criteria'][$criteriaIndex])){
		Utils::reportError("Error: Query Boolean criterion " . $criteriaIndex . " does not exist.");
	}
	$type = strtolower($_POST['Criteria'][$criteriaIndex]['CriteriaType']);
	if($type == 'term'){
		$criterion = createTermCriteria($criteriaIndex, $queryTypes);
	}elseif($type == 'range'){
		$criterion = createRangeCriteria($criteriaIndex);
	}elseif($type == 'boolean'){
		$criterion = createBooleanCriteria($criteriaIndex, $queryTypes, $createdIndices);
	}else{
		Utils::reportError("Error: Query criterion " . $criteriaIndex . " contains an unknown type " . $type . ".  Please use one of 'term', 'range' or 'boolean'");
	}
	return $criterion;
}



// Get client handle
$cb = new CasBrowser();
$client = $cb->getClient();

// Ceate an array of ProductTypes to be queried
if(!isset($_POST['Types'])){
	Utils::reportError("Error: POST does not contain 'Types' sub-array");
}
$queryTypes = array();
$allTypes = $client->getProductTypes();
if($_POST['Types'][0] == '*'){
	$queryTypes = $allTypes;
}else{
	$allTypeNames = array_map(create_function('$t', 'return $t->getName();'), $allTypes);
	foreach($_POST['Types'] as $type){
		if(!in_array($type, $allTypeNames)){
			$errStr = "Error: The type " . $type . " is not used in the repository.  Please use one of: ";
			for($i = 0; $i < count($allTypeNames) - 1; $i++){
				$errStr .= $allTypeNames[i] . ", ";
			}
			$errStr .= $allTypeNames[count($allTypeNames) - 1];
			Utils::reportError($errStr);
		}
		array_push($queryTypes, $client->getProductTypeByName($type));
	}
	if(!count($queryTypes)){
		Utils::reportError("Error: No ProductTypes were given to query");
	}
}

// Check if results are desired in a ProductPage and which page of results is desired
$pagedResults = false;
$pageNum = 1;
if(isset($_POST['PagedResults'])){
	if($_POST['PagedResults']){
		if(count($queryTypes) != 1){
			Utils::reportError("Error: Paged queries can only be performed on one ProductType");
		}
		$pagedResults = true;
		if(isset($_POST['PageNum'])){
			$pageNum = intval($_POST['PageNum']);
		}		
	}
}

// Create the tree of criteria objects that define the query
if(!isset($_POST['Criteria'])){
	Utils::reportError("Error: POST does not contain 'Criteria' sub-array");
}
if(!count($_POST['Criteria'])){
	Utils::reportError("Error: POST sub-array 'Criteria' contains no criteria");
}
$rootIndex = (isset($_POST['RootIndex']))
			? intval($_POST['RootIndex'])
			: 0;
$criteriaTree = createCriteriaTree($rootIndex, $queryTypes, null);

// Add criteria to query object
$query = new CAS_Filemgr_Query();
$query->addCriterion($criteriaTree);

// Perform the query and collect results
if($pagedResults){
	$resultPage = $client->pagedQuery($query, $queryTypes[0], $pageNum);
	$results = array();
	foreach($resultPage->getPageProducts() as $p){
		array_push($results, array('product'=>$p, 'metadata'=>$client->getMetadata($p)));
	}
}else{
	$results = array();
	foreach($queryTypes as $type){
		foreach($client->query($query, $type) as $p){
			array_push($results, array('product'=>$p, 'metadata'=>$client->getMetadata($p)));
		}
	}
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
	foreach($results as $r){
		$payload .= '<li><a href="' . MODULE_ROOT . '/product/' . $r['product']->getId() . '">';
		$payload .= urlDecode($r['product']->getName()) . '</a></li>';
	}
	$payload .= "</ul>\n";
	if($pagedResults){
		$payload .= '<input type="hidden" id="total_pages" value="' . $resultPage->getTotalPages() . '">';
		$payload .= '<input type="hidden" id="page_size" value="' . $resultPage->getPageSize() . '">';
		$payload .= '<input type="hidden" id="total_type_products" value="' . $client->getNumProducts($queryTypes[0]) . '">';
	}
}elseif ($outputFormat == 'json') {
	$payload = array();
	$payload['productList'] = array();
	foreach($results as $r){
		array_push($payload['productList'], array('id'=>$r['product']->getId(), 'name'=>urlDecode($r['product']->getName()), 'metadata'=>$r['metadata']->toAssocArray()));
	}
	if($pagedResults){
		$payload['totalPages'] = $resultPage->getTotalPages();
		$payload['pageSize'] = $resultPage->getPageSize();
		$payload['totalTypeProducts'] = $client->getNumProducts($queryTypes[0]);
	}
	$payload = json_encode($payload);
}

echo $payload;

?>

