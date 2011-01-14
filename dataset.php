<?php 
require_once "page_auth.php";

//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

require_once ("XML/RPC.php");
require_once ("classes/EcasBrowser.class.php");
require_once ("classes/Product.class.php");
require_once ("classes/ProductPage.class.php");
require_once ("classes/ProductType.class.php");
require_once ("classes/XmlRpcManager.class.php");
require_once ("config.php");
require_once ("services/ExternalServices.class.php");
require_once ("services/EcasHttpRequest.class.php");

$eb = new EcasBrowser($FILEMGR_URL,$EXTERNAL_SERVICES_PATH);

function decode($str) {
	$string = ereg_replace("%20", " ", $str);
	return $string;
}

function cmp($a, $b)
{
    $a = preg_replace('@^(a|an|the) @', '', $a);
    $b = preg_replace('@^(a|an|the) @', '', $b);
    return strcasecmp($a, $b);
}
?>
<?php include ('views/common/ecas-header.inc.php')?>

<?php

/**
 * Ensure that a product type ID has been specified.
 */
if (!isset ($_GET['typeID'])) {
	echo "<h4>No typeID specified in the GET parameters</h4>";
	exit ();
}

// Get the Product from its ProductId
$productType   = $eb->getProductType($_GET['typeID']);

if ($productType->getId() == '') {
	echo "<div class=\"error\" style=\"margin-top:60px;margin-left:10px;\">";
	echo "An error was encountered while attempting to retrieve dataset data: <br/>";
	echo "no data found for product type with ID =  $_GET[typeID]<br/><br/>";
	echo "The most likely cause of this error is an invalid or out of date typeID value.<br/>";
	echo "Please check the value and try again.";
	echo "</div>";
	exit ();
}

// Get the Metadata for the product type
$metadata = $productType->getTypeMetadata();
$typeMet = $productType->getTypeMetadata()->toAssocArray();
$typeNameStr = $typeMet["DataSetName"][0];

// Obtain visibility policy information for this dataset
define ("INTERPRET_HIDE", 'hide');
define ("INTERPERT_SHOW", 'show');
define ("AUTH_ANONYMOUS",    false);
define ("AUTH_AUTHENTICATED",true);

$interp     = $eb->visibilityPolicy['interpretation.policy'];
$global_vis = $eb->visibilityPolicy['*'];
$pt_vis     = isset($eb->visibilityPolicy[$_GET['typeID']])
	? $eb->visibilityPolicy[$_GET['typeID']]
	: array("visibility.always"=>array(),"visibility.anonymous"=>array(),"visibility.authenticated"=>array());

// Obtain ordering policy information for the dataset
$pt_order   = array();
$pt_order['first'] = isset($eb->orderingPolicy[$_GET['typeID']])
	? $eb->orderingPolicy[$_GET['typeID']]['pt.element.ordering.first']
	: $eb->orderingPolicy['*']['pt.element.ordering.first'];
$pt_order['last']  = isset($eb->orderingPolicy[$_GET['typeID']])
	? $eb->orderingPolicy[$_GET['typeID']]['pt.element.ordering.last']
	: $eb->orderingPolicy['*']['pt.element.ordering.last'];
	
// Obtain the authentication status of the current user. This is trivial
// if the user has not yet logged in. If the user HAS logged in, a further
// check is necessary to ensure that s/he has unfettered access to this 
// particular product type.
$isAuthenticated = $eb->isDatasetAccessible($_GET['typeID'], $eb->getLoginGroups());
?>
<!-- Breadcrumbs Area -->
<div id="breadcrumbs">
	<a href="./">Home</a>&nbsp/
	Protocol Dataset: <?php echo $productType->getName();?>
</div>
<!-- End Breadcrumbs -->
<h1 class="dataset-name"> <?php echo $typeNameStr; ?> </h1>

<?php if(!$isAuthenticated):?>
<div class="notice" style="border-bottom:solid 2px #fc0;border-top:solid 2px #fc0;background-color:#fed;padding:5px 0px 2px 5px;margin-bottom:5px;">
	<img style="width: 24px;vertical-align:middle;padding-bottom:5px;" src="assets/images/lock-icon.png">
	<strong>Note:</strong> some information may not available until you 
	<a href="./login.php?from=./dataset.php?typeID=<?php echo $_GET['typeID']?>">Log In</a>
	<br/>
	<div style="padding:5px;font-size:90%;">
	This biomarker is currently being annotated or is under review. Contact Heather Kincaid at the EDRN Informatics Center 
	(<code>edrn-ic@jpl.nasa.gov</code>) if you should have access to this biomarker.
	</div>
</div>
<?php endif;?>

<div id="leftSide">
<?php

/**
 * Display the Product-Type Information for this product ID.
 * 
 * 
 */
echo '<div id="typeDetails" class="leftBox">';
echo '<div><h5 class="sectionTitle" style="margin-top:10px;margin-bottom:5px;">Dataset Metadata: </h5></div>';
echo '<div class="detailsToggler" id="metadataDetailsToggler" ';
echo "onclick=\"toggleDetails('metadataDetails');\">less information [-]</div>";
echo '<div class="searchCriteria" id="metadataDetailsContents" style="display:block;">';
echo "<table id=\"metadataTable\" >";
##echo '<tr class="even"><td class="metadata-label">Abstract</td><td>' . $productType->getDescription() . '</td></tr>';
$er = new ExternalServices($EXTERNAL_SERVICES_PATH);
$evenOddCounter = 1;
$datasetMetArr = $metadata->toAssocArray();
uksort($datasetMetArr, "cmp");

$datasetMetArr['description'] = $productType->getDescription();

// Human readable translations of key names. Can we put this in the
// policy somewhere? something like 'label', as in:
// <element id="urn:edrn:SiteName" name="SiteName" label="Site Name">
//   <description>...</description>
//   <dcElement/>
// </element>
$human_readables = array (
   "ProtocolName" => "Protocol Name",
   "ProtocolID" => "Protocol ID",
   "description" => "Dataset Abstract",
   "DataSetName" => "Dataset Name", 
   "LeadPI" => "Principal Investigator", 
   "SiteName" => "Site Name",
   "DataCustodian" => "Data Custodian",
   "DataCustodianEmail" => "Data Custodian Email",
   "OrganSite" => "Organ Site", 
   "CollaborativeGroup" => "Organ Collaborative Groups",
   "MethodDetails" => "Method Details",
   "ResultsAndConclusionSummary" => "Analytic Results and Conclusions",
   "PubMedID" => "PubMed ID",
   "DateDatasetFrozen" => 'Date Dataset was "frozen"',
   "Date" => "Date", 
   "DataDisclaimer" => "Disclaimer"
);

		
// Using the visibility policy, determine which metadata to display
switch ($interp) {
	// If the policy defines only those metadata which should be hidden:
	case INTERPRET_HIDE:
		$displayMet = $datasetMetArr;                                 // everything shown unless explicitly hidden via the policy
		foreach ($global_vis['visibility.always'] as $elm)            // iterate through the global 'always hide' array...
			unset($displayMet[$elm]);                                 // and remove all listed elements
		foreach ($pt_vis['visibility.always'] as $elm)                // now iterate through the product-type 'always hide' array...
			unset($displayMet[$elm]);                                 // and remove all listed elements
		switch ($isAuthenticated) {                                   // check the login status of the user
			case AUTH_ANONYMOUS:                                      // if the user is anonymous...
				foreach($global_vis['visibility.anonymous'] as $elm)  // iterate through the global 'anonymous hide' array...
					unset($displayMet[$elm]);                         // and remove all listed elements
				foreach ($pt_vis['visibility.anonymous'] as $elm)     // now iterate through the product-type 'anonymous hide' array...
					unset($displayMet[$elm]);                         // and remove all listed elements
				break;                                                // done.
			case AUTH_AUTHENTICATED:                                      // if the user is authenticated...
				foreach($global_vis['visibility.authenticated'] as $elm)  // iterate through the global 'authenticated hide' array...
					unset($displayMet[$elm]);                             // and remove all listed elements
				foreach ($pt_vis['visibility.authenticated'] as $elm)     // now iterate through the product-type 'authenticated hide' array...
					unset($displayMet[$elm]);                             // and remove all listed elements
				break;                                                    // done.
		}
		break;
	// If the policy defines only those metadata which should be shown:
	case INTERPRET_SHOW:
		$displayMet = $global_vis['visibility.always']                // merge the global 'always show' array
			+ $pt_vis['visibility.always'];                           // with the product-type specific 'always show' array
		switch ($isAuthenticated) {                                   // check the login status of the user
			case AUTH_ANONYMOUS:                                      // if the user is anonymous...
				$displayMet += $global_vis['visibility.anonymous'];   // merge the global 'anonymous show' array
				$displayMet += $pt_vis['visibility.anonymous'];       // and the product-type specific 'anonymous show' array
				break;                                                // done.
			case AUTH_AUTHENTICATED:                                      // if the user is authenticated...
				$displayMet += $global_vis['visibility.authenticated'];   // merge the global 'authenticated show' array 
				$displayMet += $pt_vis['visibility.authenticated'];       // and the product-type specific 'authenticated show' array
				break;                                                    // done.
		}
}

// Using the odering policy, determine the order in which the metadata will be listed
$orderedDisplayMet = array();
foreach ($pt_order['first'] as $key) {
	if (isset($displayMet[$key])) {
		$orderedDisplayMet[$key] = $displayMet[$key];
		unset($displayMet[$key]);
	}
}
$lastMetadata = array();
foreach ($pt_order['last'] as $key) {
	if (isset($displayMet[$key])) {
		$lastMetadata[$key] = $displayMet[$key];
		unset($displayMet[$key]);
	}
}
$orderedDisplayMet += $displayMet;
$orderedDisplayMet += $lastMetadata;

foreach ($pt_order['first'] as $oelm)  {                              // iterate through the provided 'first' ordering data...
	if (isset($displayMet[$oelm])) {                                  // if a corresponding display met key exists...
		$orderedDisplayMet[$oelm] = $displayMet[$oelm];               // add the element in order,
	}
	unset($displayMet[$oelm]);                                        // and remove it from the original array
}
$orderedDisplayMet += $displayMet;                                    // union the remaining elements to the ordered elements


foreach ($orderedDisplayMet as $key => $value) {
	
	// Perform service translation if a corresponding service is defined
	if (isset($er->services[$key])) {
		$req   = new EcasHttpRequest($er->services[$key] . "?id={$value[0]}");
		$str   = $req->DownloadToString(); 
		$value = (empty($str)) ? $value : $str;
	}
	
	// Translate keys to human readable
	$key = isset($human_readables[$key]) ? $human_readables[$key] : $key;
	
	// Handle multiple values cleanly
	$allValues = is_array($value) ? implode("\r\n",$value) : $value;
	
	// Output the metadata row
	echo '<tr id="datasetMetadataRow-'. str_replace('.','',$key).'" class="'
		. (($evenOddCounter++ % 2 == 0) ? 'even' : 'odd') .'">'
		. '<td class="metadata-label">' . $key . '</td>'
		. '<td>' . nl2br($allValues) . "</td></tr>";
}

echo "</table>";
echo "</div>";
echo "</div>";

/**
 * Download the contents of the Dataset as a Zip archive
 * 
 * 
 */
 
echo '<div id="downloadDetails" class="leftBox">';
echo '<div><h5 class="sectionTitle" style="margin-top:10px;margin-bottom:5px;">Download Dataset as a Zip Archive:</h5></div>';
if (!$isAuthenticated):
?>
<div class="notice" style="background-color:#bdf;padding:5px 0px 2px 5px;margin-bottom:5px;">
	<img style="width: 24px;vertical-align:middle;padding-bottom:5px;" src="assets/images/lock-icon.png">
	To download this dataset, please 
	<a href="./login.php?from=./dataset.php?typeID=<?php echo $_GET['typeID']?>">log in</a>
</div>
<?php else:	
echo '<div class="detailsToggler" id="downloadDetailsToggler" ';
echo "onclick=\"toggleDetails('downloadDetails');\">less information [-]</div>";
echo '<div class="searchCriteria" id="downloadDetailsContents" style="display:block;">';
echo "<table id=\"metadataTable\" >";
echo "<tr class=\"even\"><td>Download Zip Archive:</td><td style=\"text-align:center\"><a href=\"$DATADELIV_URL/dataset?typeID={$_GET['typeID']}&format=application/x-zip\">Click Here</td></tr>";
echo "</table>";
echo "</div>";
endif;
?>
</div>
</div><!-- End 'left side' -->
<div id="rightSide">
<?php

$pageNum = $_REQUEST["page"];
if (!isset ($pageNum)) {
	$pageNum = 1;
}

$j = 1;

do {
	if ($j == 1) {
		$thePage = $eb->xmlrpc->getFirstPage($productType);
	} else {
		$thePage = $eb->xmlrpc->getNextPage($productType, $thePageObj);
	}

	$j++;
        $thePageObj = new ProductPage();
        $thePageObj->__initXmlRpc($thePage);
} while ($j <= $pageNum && (isset ($thePage) && !$thePageObj->isLastPage()));

$prodPage = new ProductPage();
$prodPage->__initXmlRpc($thePage);
$pageSize = $prodPage->getPageSize();
$products = $prodPage->getPageProducts();

if ($prodPage->getTotalPages() == 1) {
	$numProducts = count($prodPage->getPageProducts());
} else
	if ($prodPage->getTotalPages() == 0) {
		$numProducts = 0;
	} else {
		$numProducts = ($prodPage->getTotalPages() - 1) * $pageSize;
		//get the last page
		$lastPageXmlRpc = $eb->xmlrpc->getLastPage($productType);
		$lastPage = new ProductPage();
		$lastPage->__initXmlRpc($lastPageXmlRpc);
		$numProducts += count($lastPage->getPageProducts());
	}

$endIdx = $numProducts != 0 ? min(array (
	$numProducts,
	 (($pageSize) * ($pageNum))
)) : 0;
$startIdx = $numProducts != 0 ? (($pageNum -1) * $pageSize) + 1 : 0;
?>
<h5 class="sectionTitle" style="margin-top:10px;margin-bottom:5px;">
	<?php echo $startIdx;?>-<?php echo $endIdx;?> of 
	<?php echo $numProducts?> Products Associated With This Dataset:</h5>
<ul>
<?php if ($isAuthenticated) :
	/**
	* Display information about each product associated with this product type ID. 
	*/
	foreach ($products as $prod) {
		$product = new Product($prod);
		echo "<li><a href=\"product.php?productID=" 
			. $product->getId() . "\">" . $product->getName() . "</a></li>";
	}
?>
</ul>

<hr width="*">
<div align="center">
<table cellspacing="3" width="*">
  <tr>
    <td width="100" nowrap>Result Page</td>
     <?php

$numPages = $prodPage->getTotalPages();
$currPage = $prodPage->getPageNum();
$windowSize = 10;

$startPage = max(1, ($currPage - ($windowSize / 2)));
$endPage = min($currPage + ($windowSize / 2), $numPages);

for ($i = $startPage; $i <= $endPage; $i++):?>
  <td><?php if($currPage == $i){ ?><b><? } ?><a <? if($currPage == $i){ ?>style="color:red;"<?} ?> href="./dataset.php?page=<? echo $i; ?>&typeID=<? echo $_GET['typeID'];?>"><? echo $i; ?></a><? if($currPage == $i){ ?></b><? } ?></td>                    	 
<?php endfor; ?>
	</tr>
</table>
<?php endif;?>
<?php if (!$isAuthenticated):?>
<div class="notice" style="background-color:#bdf;padding:5px 0px 2px 5px;margin-bottom:5px;">
	<img style="width: 24px;vertical-align:middle;padding-bottom:5px;" src="assets/images/lock-icon.png">
	To see products for this dataset, please 
	<a href="./login.php?from=./dataset.php?typeID=<?php echo $_GET['typeID']?>">log in</a>
</div>
<?php endif?>
</div>

</div><!-- End 'right side' -->
<br class="clr"/>
<?php include('views/common/ecas-footer.inc.php'); ?>