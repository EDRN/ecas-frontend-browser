<?php
require_once(MODULE . "/classes/CasBrowser.class.php");
require_once(MODULE . "/scripts/widgets/ProductTypeListWidget.php");

$auth = App::Get()->getAuthProvider();

// Get a CAS-Browser XML/RPC client
$browser  = new CasBrowser();
$client   = $browser->getClient();
    
// Security check: ensure the resource's security groups overlap with the current user's
if ( $auth ) {
	$username = $auth->getCurrentUsername();
}
$userGroups = (!empty($username))
		? $auth->retrieveGroupsForUser($username)
		: array();
		
// Get a list of the product types managed by this server
$response     = $client->getProductTypes();
$productTypes = array();
foreach ($response as $pt) {
	
	$ptArray = $pt->toAssocArray();
	// Check resource groups vs user groups to determine whether this should be shown
	$resourceGroups = (isset($ptArray['typeMetadata'][App::Get()->settings['browser_data_access_key']]))
		? $ptArray['typeMetadata'][App::Get()->settings['browser_data_access_key']]
		: array();
	
	if ( !isset($auth) ) {
		$canView  = true;
	} else {
		$canView  = Utils::UserCanView($resourceGroups, $userGroups);
	}
	
	if ($canView) {
		$merged = array_merge($ptArray['typeMetadata'],array(
			"name" => array($ptArray[App::Get()->settings['browser_pt_name_key']]),
			"description" => array($ptArray[App::Get()->settings['browser_pt_desc_key']]),
			"id"   => array($ptArray[App::Get()->settings['browser_pt_id_key']])));
		$productTypes[$ptArray[App::Get()->settings['browser_pt_id_key']]] = $merged;
	}
}

$productTypeListWidget = new ProductTypeListWidget(array("productTypes" => $productTypes));
$productTypeListWidget->setUrlBase(MODULE_ROOT);

?>
<script type="text/javascript" src="<?php echo MODULE_STATIC?>/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	// Turn the table into a sortable, searchable table
	$("#productTypeSearch").dataTable();
	// Give the search box the initial focus
	$("#productTypeSearch_filter > input").focus();
}); 
</script>

<div class="container">
<div class="breadcrumbs">
<a href="<?php echo SITE_ROOT?>/">Home</a>&nbsp;&rarr;&nbsp;
<?php echo Browser?>
</div>
<hr class="space"/>
<div id="cas_browser_container" class="span-22 last prepend-1 append-1">
	<h2 id="cas_browser_title"><?php echo App::Get()->settings['browser_index_title_text']?></h2>
<?php $productTypeListWidget->render();?>
</div>
<hr class="space"/>
</div>
