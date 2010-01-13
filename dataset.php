<?php require_once "page_auth.php"?>
<?php

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
?>
<!-- Breadcrumbs Area -->
<div id="breadcrumbs"/>
	<a href="./">Home</a>&nbsp/
	Protocol Dataset: <?php echo $productType->getName();?>
</div>
<!-- End Breadcrumbs -->
<h1 class="dataset-name"> <?php echo $typeNameStr; ?> </h1>
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

$abstract = $productType->getDescription();
$specific_order = array (
   "ProtocolName" => array ("Protocol Name", "TBD"),
   "ProtocolID" => array ("Protocol ID", "TBD"),
   "description" => array ("Dataset Abstract", $abstract),
   "DataSetName" => array ("Dataset Name", "TBD"), 
   "LeadPI" => array ("Principal Investigator", "TBD"),
   "SiteName" => array ("Site Name", "TBD"),
   "DataCustodian" => array ("Data Custodian", "TBD"),
   "DataCustodianEmail" => array ("Data Custodian Email", "TBD"),
   "OrganSite" => array ("Organ Site", "TBD"),
   "CollaborativeGroup" => array ("Organ Collaborative Groups", "TBD"),
   "MethodDetails" => array ("Method Details", "TBD"),
   "ResultsAndConclusionSummary" => array ("Analytic Results and Conclusions", "TBD"),
   "PubMedID" => array ("PubMed ID", "TBD"),
   "DateDatasetFrozen" => array ('Date Dataset was "frozen"', "TBD"),
   "Date" => array ("Date", "TBD"),
   "DataDisclaimer" => array ("Disclaimer", "TBD")
);

if ($_GET["reveal"]) {
  $reveal_all_flag = true; 
} else {
  $reveal_all_flag = false;
}

foreach ($datasetMetArr as $label => $value) {
	if (isset ($er->services[$label])) {
		$r = new EcasHttpRequest($er->services[$label] . "?id={$value[0]}");
		$str = $r->DownloadToString();
		$value = ($str == '') ? $value : array (
			$str
		);
	}
	##echo '<tr class="' . (($evenOddCounter++ % 2 == 0) ? 'even' : 'odd') . '"><td class="metadata-label">' . $label . '</td><td>';
        $tmp_v = "";
	foreach ($value as $v) {
		#echo "$v ";
                $tmp_v .= $v . " ";
	}
        if (! $specific_order[$label] && $reveal_all_flag == true) {
          $specific_order[$label][0] = $label;
          $specific_order[$label][1] = $tmp_v;
        } 
        if ($specific_order[$label]) {
          $specific_order[$label][1] = $tmp_v;
        }

	#echo "</td></tr>";
}

foreach ($specific_order as $key => $value) {
   $label = $value[0];
   $display = $value[1];
   echo '<tr id="datasetMetadataRow-'. str_replace('.','',$key).'" class="' . (($evenOddCounter++ % 2 == 0) ? 'even' : 'odd') . '"><td class="metadata-label">' . $label . '</td><td>' . $display . "</td></tr>";
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
echo '<div class="detailsToggler" id="downloadDetailsToggler" ';
echo "onclick=\"toggleDetails('downloadDetails');\">less information [-]</div>";
echo '<div class="searchCriteria" id="downloadDetailsContents" style="display:block;">';
echo "<table id=\"metadataTable\" >";
echo "<tr class=\"even\"><td>Download Zip Archive:</td><td style=\"text-align:center\"><a href=\"$dataDelivUrl/dataset?typeID={$_GET['typeID']}&format=application/x-zip\">Click Here</td></tr>";
echo "</table>";
echo "</div>";
echo "</div>";
?>

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
<h5 class="sectionTitle" style="margin-top:10px;margin-bottom:5px;"><b><?php echo $startIdx;?></b>-<b><?php echo $endIdx;?></b> of <?php echo $numProducts?> Products Associated With This Dataset:</h5>
<ul>
<?php

/**
 * Display information about each product associated with this product type ID. 
 * 
 * 
 */

foreach ($products as $prod) {
	$product = new Product($prod);
	echo "<li><a href=\"product.php?productID=" . $product->getId() . "\">" . $product->getName() . "</a></li>";

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

for ($i = $startPage; $i <= $endPage; $i++) {
?>
           <td><?php if($currPage == $i){ ?><b><? } ?><a <? if($currPage == $i){ ?>style="color:red;"<?} ?> href="./dataset.php?page=<? echo $i; ?>&typeID=<? echo $_GET['typeID'];?>"><? echo $i; ?></a><? if($currPage == $i){ ?></b><? } ?></td>                    	 
          <?

}
?>
	</tr>
</table>
</div>

</div><!-- End 'right side' -->
<br class="clr"/>
<?php include('views/common/ecas-footer.inc.php'); ?>