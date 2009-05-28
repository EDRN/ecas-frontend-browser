<?php require_once "page_auth.php"?>
<?php

//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

require_once ("login_status.php");
require_once ("XML/RPC.php");
require_once ("classes/Product.class.php");
require_once ("classes/ProductPage.class.php");
require_once ("classes/ProductType.class.php");
require_once ("classes/XmlRpcManager.class.php");
require_once ("config.php");
require_once ("services/ExternalServices.class.php");
require_once ("services/HTTPRequest.class.php");

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EDRN ecas user interface</title>
<!-- CSS Includes -->
<link rel="stylesheet" type="text/css" href="assets/edrn-skin/css/edrn-informatics.css"/>
<link rel="stylesheet" type="text/css" href="css/ecas-ui.css" />
<script type="text/javascript">
  function $(id){
    return document.getElementById(id);
  }
  function toggleDetails(id){
    if ($(id + "Contents").style.display != 'none'){
      $(id + "Contents").style.display = 'none';
      $(id + "Toggler").innerHTML = 'more information [+]';
    } else {
      $(id + "Contents").style.display = 'block';
      $(id + "Toggler").innerHTML = 'less information [-]';
    }
  }
</script>
</head>
<body>
<div id="edrninformatics">
	<div id="edrnlogo"><!-- nci logo --></div>
	<div id="edrn-dna"><!-- dna graphic --></div>
	<h2 class="app-title">EDRN Catalog &amp; Archive Service</h2>
	<div class="userdetails">
		<?php checkLoginStatus($_SERVER["REQUEST_URI"])?>
	</div>
</div>

<?php

/**
 * Ensure that a product type ID has been specified.
 */
if (!isset ($_GET['typeID'])) {
	echo "<h4>No typeID specified in the GET parameters</h4>";
	exit ();
}

// Create an XmlRpcManager object to perform the work
$xmlRpcMgr = new XmlRpcManager($filemgrUrl, '/');
// Get the Product from its ProductId
$productType = new ProductType($xmlRpcMgr->getProductTypeById($_GET['typeID']));

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
?>
<!-- Breadcrumbs Area -->
<div id="breadcrumbs"/>
	<a href="./">Home</a>&nbsp/
	Protocol Dataset: <?php echo $productType->getName();?>
</div>
<!-- End Breadcrumbs -->
<h1 class="dataset-name"><?php echo $productType->getName()?></h1>
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
echo '<tr class="even"><td class="metadata-label">Abstract</td><td>' . $productType->getDescription() . '</td></tr>';
$er = new ExternalServices($externalServicesPath);
$evenOddCounter = 1;
$datasetMetArr = $metadata->toAssocArray();
uksort($datasetMetArr, "cmp");
foreach ($datasetMetArr as $label => $value) {
	if (isset ($er->services[$label])) {
		$r = new HTTPRequest($er->services[$label] . "?id={$value[0]}");
		$str = $r->DownloadToString();
		$value = ($str == '') ? $value : array (
			$str
		);
	}
	echo '<tr class="' . (($evenOddCounter++ % 2 == 0) ? 'even' : 'odd') . '"><td class="metadata-label">' . $label . '</td><td>';
	foreach ($value as $v) {
		echo "$v ";
	}
	echo "</td></tr>";
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
		$thePage = $xmlRpcMgr->getFirstPage($productType);
	} else {
		$thePage = $xmlRpcMgr->getNextPage($productType, $thePageObj);
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
		$lastPageXmlRpc = $xmlRpcMgr->getLastPage($productType);
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
<div style=\"clear:both;\"></div>

</body>
</html>