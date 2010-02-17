<?php 
require_once ("XML/RPC.php");
require_once ("classes/EcasBrowser.class.php");
require_once ("classes/Product.class.php");
require_once ("classes/ProductPage.class.php");
require_once ("classes/ProductType.class.php");
require_once ("classes/XmlRpcManager.class.php");
require_once ("services/ExternalServices.class.php");
require_once ("services/EcasHttpRequest.class.php");
require_once ("config.php");

$eb = new EcasBrowser($FILEMGR_URL,$EXTERNAL_SERVICES_PATH);

require_once "views/common/ecas-header.inc.php";
?>
<!-- Breadcrumbs Area -->
<div id="breadcrumbs"/>
	<a href="./">Home</a>&nbsp;/
	Not Authorized
</div>
<!-- End Breadcrumbs -->
<div style="width:80%;margin-left:auto;margin-right:auto">
	<h3>An error has occurred:</h3>
	<div class="error">
	You are not authorized to view the requested resource.
	
	</div>
</div>
<?php 
require_once "views/common/ecas-footer.inc.php";
?>