<?php
//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

session_start();
require_once("login_status.php");
require_once("XML/RPC.php");
require_once("classes/Product.class.php");
require_once("classes/ProductType.class.php");
require_once("classes/XmlRpcManager.class.php");
require_once("config.php");
require_once("services/ExternalServices.class.php");
require_once("services/HTTPRequest.class.php");

function decode($str){
	$string = ereg_replace("%20"," ",$str);
	return $string;
}

function protocolsort($a, $b){
	return strcasecmp($a, $b);
}

function ptypesort($a, $b){
	
	$prodType1 = new ProductType($a);
	$prodType2 = new ProductType($b);

	return strcasecmp($prodType1->getName(), $prodType2->getName());
}

// Create an XmlRpcManager object to perform the work
$xmlRpcMgr   = new XmlRpcManager($filemgrUrl,'/');

// get the different product types
// then get the count for each one
$types = $xmlRpcMgr->getProductTypes();

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>EDRN eCAS Web Portal</title>
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
<!-- Breadcrumbs Area -->
<div id="breadcrumbs"/>
	Home
</div>
<!-- End Breadcrumbs -->

<div id="splash-image">
<img id="splash-logo" src="assets/images/ecas5logo.png" style="margin-top:0px;"/>
</div><!-- splash image -->

<!--  
<div class="splash-intro">
<h2 style="margin-top:5px;color:#993231;border-bottom:dotted 1px #456;padding-bottom:2px;padding-top:5px;">
About the EDRN CAS
</h2>
<p>
The EDRN Catalog and Archive Service (eCAS) is a central repository for cancer biomarker research data
collected from participating EDRN institutions.
</p>
<p>
eCAS data is organized into datasets corresponding to a particular EDRN protocol. Data can be obtained
either as a .zip archive of the entire dataset, or by downloading data products individually.
</p>
<p>
Due to the fact that the data has not yet been publicly released, users must log-in in order to browse and
download the data.
</p>
</div>
-->


<div class="clr"><!-- clear --></div>
<div class="dataset-list">
<h2 style="font-size:20px;margin-top:20px;padding-left:3px;color:#993231;border-bottom:dotted 1px #456;padding-bottom:2px;padding-top:5px;">
	Available Datasets
</h2>
<?php
	uasort($types, "ptypesort");
	$protocols = array();
	$er = new ExternalServices($externalServicesPath);

	foreach ($types as $type){
		$prodType = new ProductType($type);
		//ignore eCAS files for now
		if(!strcmp($prodType->getName(), "eCASFile")){
			continue;
		}
		
		$typeMetadata = $prodType->getTypeMetadata();
		$typeMetAssocArray = $typeMetadata->toAssocArray();
		$protocolId = $typeMetAssocArray["ProtocolName"][0];
	 	$r = new HTTPRequest($er->services["ProtocolName"]."?id=".$protocolId);
		$str = $r->DownloadToString();
		$protocolName = ($str == '') ? $protocolId:array($str);
		
		if(is_array($protocolName)){
			$protocolName = $protocolName[0];
		}
		
		if(array_key_exists($protocolName, $protocols)){
			$ptypes = $protocols[$protocolName];
			array_push($ptypes, $prodType);
			$protocols[$protocolName] = $ptypes;
		}
		else{
			$ptypes = array();
			array_push($ptypes, $prodType);
			$protocols[$protocolName] = $ptypes;
		}
	}

	uksort($protocols, "protocolsort");
	foreach ($protocols as $protName => $typeList): 
?>
	<div class="dataset-summary">
		<h2><img src="assets/images/redbullet2.png" style="margin-left:5px;margin-right:10px;"/><?php echo $protName?></h2>
		<ul class="sublist">
		<?php 
			foreach ($typeList as $pType){
			   $typeMet = $pType->getTypeMetadata()->toAssocArray();
			   $typeNameStr = $typeMet["DataSetName"][0];
			   $collabGroupStr = $typeMet["CollaborativeGroup"][0];
			   $organStr = $typeMet["OrganId"][0];
			   $piStr = $typeMet["Author"][0];
			   echo "<li><span class=\"title\">".$typeNameStr." (<a href=\"./dataset.php?typeID=".$pType->getId()."\">".$xmlRpcMgr->getNumProducts($pType)." products</a>)</span><br/>\n";		
			   echo "<span class=\"details\">[PI: ".$piStr.", Organ: ".$organStr.", Collaborative Group: ".$collabGroupStr."]</span><br/>";
			   echo "</li>";
			}
		?>
		</ul>
	</div>
<?php endforeach; ?>
</div><!-- dataset list -->

<div id="footer">
	A Service of the National Cancer Institute<br/><br/>
	<a href="http://hhs.gov">
		<img src="assets/edrn-skin/img/footer_hhs.gif" alt="Department of Health and Human Services"/>
	</a>
	<a href="http://nih.gov">
		<img src="assets/edrn-skin/img/footer_nih.gif" style="margin-left:12px;"/>
	</a>
	<a href="http://usa.gov">
		<img src="assets/edrn-skin/img/footer_usagov.gif"/>
	</a>
</div>

</body>
</html>