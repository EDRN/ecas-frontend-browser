<?php
//Copyright (c) 2008, California Institute of Technology.
//ALL RIGHTS RESERVED. U.S. Government sponsorship acknowledged.
//
//$Id$

session_start();
require_once("XML/RPC.php");
require_once("classes/EcasBrowser.class.php");
require_once("classes/Product.class.php");
require_once("classes/ProductType.class.php");
require_once("classes/XmlRpcManager.class.php");
require_once("config.php");
require_once("services/ExternalServices.class.php");
require_once("services/EcasHttpRequest.class.php");
require_once("Gov/Nasa/Jpl/Edrn/Security/EDRNAuth.php");


$eb = new EcasBrowser($FILEMGR_URL,$EXTERNAL_SERVICES_PATH);

?>
<?php include('views/common/ecas-header.inc.php');?>

<!-- Breadcrumbs Area -->
<div id="breadcrumbs"/>
	Home
</div>
<!-- End Breadcrumbs -->

<div id="splash-image">
	<img id="splash-logo" src="assets/images/ecas5logo.png" style="margin-top:0px;"/>
</div>

<div class="dataset-list">
	<h2 class="sectionTitle">Datasets By Protocol</h2>
	<?php $eb->displayDatasetsbyProtocol(array('ignore'=>array('eCASFile'))); ?>
</div>

<?php include('views/common/ecas-footer.inc.php'); ?>

