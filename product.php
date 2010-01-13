<?php require_once "page_auth.php"?>
<?php
//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

require_once("XML/RPC.php");
require_once("classes/EcasBrowser.class.php");
require_once("classes/Product.class.php");
require_once("classes/ProductType.class.php");
require_once("classes/XmlRpcManager.class.php");
require_once("config.php");

require_once("services/ExternalServices.class.php");
require_once("services/EcasHttpRequest.class.php");

function decode($str){
	$string = ereg_replace("%20"," ",$str);
	return $string;
}

$eb = new EcasBrowser($FILEMGR_URL,$EXTERNAL_SERVICES_PATH);
?>
<?php include ('views/common/ecas-header.inc.php')?>

<link rel="stylesheet" type="text/css" href="css/metadata-visibility.css" />
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
<?php 
/**
 * Ensure that a product ID has been specified.
 */
if (!isset($_GET['productID'])){
	echo "<h4>No productID specified in the GET parameters</h4>";
	exit();
}


// Get the Product from its ProductId
$product     = $eb->getProduct($_GET['productID']);
if ($product->getId() == ''){
	echo "<div class=\"error\">";
	echo "Error encountered while attempting to retrieve product data: <br/>";
	echo "no data found for product with ID =  $_GET[productID]<br/><br/>";
	echo "The most likely cause of this error is an invalid or out of date productID value.<br/>";
	echo "Please check the value and try again."; 
	echo "</div>";
	exit();	
}
// Get the ProductType from the Product
$productType = $product->getType();
// Get the Product References 
$references  = $eb->getProductReferences($product);
if (isset($references['faultCode'])){
	echo "<div class=\"error\">";
	echo "Error encountered while attempting to retrieve product references.<br/>";
	echo "FAULT CODE: $references[faultCode]<br/>";
	echo "FAULT STRING: $references[faultString]<br/>";
	echo "</div>";
	exit();	
}

// Get the Product Metadata for the product
$metadata    = $eb->getMetadata($product);
if (isset($metadata['faultCode'])) {
	echo "<div class=\"error\">";
	echo "Error encountered while attempting to retrieve product metadata.<br/>";
	echo "FAULT CODE: $metadata[faultCode]<br/>";
	echo "FAULT STRING: $metadata[faultString]<br/>";
	echo "</div>";
	exit();
}

?>
<!-- Breadcrumbs Area -->
<div id="breadcrumbs"/>
	<a href="./">Home</a>&nbsp/
	<a href="./dataset.php?typeID=<?php echo $productType->getID();?>"><?php echo $productType->getName()?></a>&nbsp;/
	Product: <?php echo $product->getName();?>
</div>
<!-- End Breadcrumbs -->
<h1 class="dataset-name"><?php echo $productType->getName();?> / <?php echo $product->getName()?></h1>
<div id="leftSide">
<?php
/**
 * Display the Metadata Information for this product ID.
 * 
 * 
 */
echo '<div id="productDetails" class="leftBox">';
echo '<div><h5 class="sectionTitle">Product Name: '.$product->getName().'</h5></div>';
echo '<div class="detailsToggler" id="productDetailsToggler" ';
echo "onclick=\"toggleDetails('productDetails');\">more information [+]</div>";
echo '<div class="searchCriteria" id="productDetailsContents" style="display:none;">';
echo "<table>";
echo "<tr><td class=\"important\">Product Id:</td><td>{$product->getId()}</td></tr>";
echo "<tr><td class=\"important\">Dataset: </td><td><a href=\"dataset.php?typeID=".$productType->getId()."\">".$productType->getName()."</td></tr>";
echo "<tr><td class=\"important\">Structure:</td><td>{$product->getStructure()}</td></tr>";
echo "<tr><td class=\"important\">TransferStatus:</td><td>{$product->getTransferStatus()}</td></tr>";
echo "</table>";
echo "</div>";
echo "</div>";


/**
 * Display the Metadata Information for this product ID.
 * 
 * 
 */
echo '<div id="metadataDetails" class="leftBox">';
echo '<div><h5 class="sectionTitle">Product Metadata: </h5></div>';
echo '<div class="detailsToggler" id="metadataDetailsToggler" ';
echo "onclick=\"toggleDetails('metadataDetails');\">less information [-]</div>";
echo '<div class="searchCriteria" id="metadataDetailsContents" style="display:block;">';
echo "<table id=\"metadataTable\" >";
$er = new ExternalServices($EXTERNAL_SERVICES_PATH);
$evenOddCounter = 0;
foreach ($metadata as $label => $value){
	if (isset($er->services[$label])){
		$r = new EcasHttpRequest($er->services[$label]."?id={$value[0]}");
		$str = $r->DownloadToString();
		$value = ($str == '')? $value : array($str);
	}
	echo '<tr id="metadataRow-'.str_replace(".","",$label).'" class="'.(($evenOddCounter++ % 2 == 0)?'even':'odd').'"><td>'.$label.'</td><td>';
	foreach ($value as $v) {
		echo "$v ";
	}
	echo "</td></tr>\r\n";
}
echo "</table>";
echo "</div>";
echo "</div>";

/** ADDED TO ALLOW FOR DOWNLOAD OF PRODUCT AS A ZIP FILE **/
echo '<div id="downloadDetails" class="leftBox">';
echo '<div><h5 class="sectionTitle">Download Product as a Zip Archive:</h5></div>';
echo '<div class="detailsToggler" id="downloadDetailsToggler" ';
echo "onclick=\"toggleDetails('downloadDetails');\">less information [-]</div>";
echo '<div class="searchCriteria" id="downloadDetailsContents" style="display:block;">';
echo "<table id=\"metadataTable\" >";
echo "<tr class=\"even\"><td>Download Zip Archive:</td><td style=\"text-align:center\"><a href=\"$dataDelivUrl/data?productID={$_GET['productID']}&format=application/x-zip\">Click Here</td></tr>";
echo "</table>";
echo "</div>";
echo "</div>";
/** END DOWNLOAD PRODUCT AS A ZIP FILE **/
?>

</div><!-- End 'left side' -->
<div id="rightSide">
<h5 class="sectionTitle">Files Associated With This Product:</h5>

<?php
/**
 * Display information about each file associated with this product ID. 
 * 
 * 
 */
$referenceCounter = 0;
echo "<table>";
foreach ($references as $reference){
	$fileNameParts = split("/",$reference['dataStoreReference']);
	$fileName = $fileNameParts[sizeof($fileNameParts) -1];
	$fileSize = $reference['fileSize'];
	$fileSizeStr = "";
	($fileSize > (1024*1024)) 
		? $fileSizeStr = number_format(($fileSize/(1024*1024)),1) . " MB"
		: (($fileSize > (1024))
			? $fileSizeStr = number_format(($fileSize / 1024),1) . " KB"
			: $fileSizeStr = $fileSize . " bytes");
	echo "<tr>";
	echo "<td>";
	if ($reference['mimeType'] == 'image/jpeg') {
		echo "<img class=\"tn\" src=\"".$dataDelivUrl."/data?refIndex=$referenceCounter&productID={$product->getID()}\">";	
	} else {
		echo "&nbsp;";
	}
	echo "</td>";
	echo "<td style=\"vertical-align:top;\">".decode($fileName)." <br/><span style=\"color:#555;font-size:0.9em;\">$fileSizeStr</span><br/>";
	echo "Mime Type: $reference[mimeType]<br/>";
	if($reference['mimeType'] == 'image/jpeg') {
		echo "&nbsp;<a href=\"".$dataDelivUrl."/data?refIndex=$referenceCounter&productID={$product->getID()}\" target=\"_new\">view</a> &nbsp;";
		echo "<a href=\"getImage.php?productID={$product->getID()}&refNumber=$referenceCounter&fileName=$fileName\">save</a>&nbsp;";	
	}
	else{
		echo "<a href=\"".$dataDelivUrl."/data?refIndex=$referenceCounter&productID={$product->getID()}\">save</a> &nbsp;";		
	}
		
	echo "</td>";
	echo "</tr>";
	$referenceCounter++;
}
echo "</table>";
?>

</div><!-- End 'right side' -->
<div style=\"clear:both;\"></div>
</div><!-- End outer container -->
<br class="clr"/>
<?php include('views/common/ecas-footer.inc.php'); ?>