<?php
require_once(HOME . '/scripts/widgets/BreadcrumbsWidget.php');
$ctx = App::Get()->loadModule();
require_once($ctx->modulePath . "/classes/CasBrowser.class.php");
require_once($ctx->modulePath . "/scripts/widgets/FilterWidget.php");

// Get a CAS-Browser XML/RPC client
$browser  = new CasBrowser();
$client   = $browser->getClient();
 
// Get a  Product Type object
$productType = $client->getProductTypeById(App::Get()->request->segments[0]);
$productCount = $client->getNumProducts($productType);
$ptID     = $productType->getId();
$ptName   = $productType->getName();
$typeInfo = $productType->toAssocArray();

/****************************************************************************
 * Security Check
 ****************************************************************************
 * 
 * The behavior of this page depends upon the `browser_pt_auth_policy`
 * configuration setting in this module's config.ini file. If the current
 * user does not have permission to view this productType, and the 
 * auth policy is:
 *    LIMIT  , then apply the element-visibility.ini policy.
 *    DENY   , then force redirect with 403 Not Authorized error.
 *
 * Note that, if no authentication provider information is specified
 * in the main application config file, the above is disregarded and
 * all productTypes are always visible by default.
 * 
 * The determination of whether or not a user should be granted access
 * to this page is made by examining the user's security groups (aka,
 * roles, permissions, etc) and comparing them against the set of 
 * security groups annotated in the productType metadata itself. This
 * requires that product metadata include an element (it can be
 * given any name, which should be recorded in the `browser_data_access_key`
 * configuration setting for this module) which specifies a (possibly
 * multi-valued) list of security groups (aka roles, permissions, etc) 
 * that should have access to this product type. E.g:
 * 
 * <keyval>
 *   <key>AccessGrantedTo</key>
 *   <val>Curators</val>
 *   <val>Developers</val>
 * </keyval>
 * 
 * In this case, `browser_data_access_key` would be set to 'AccessGrantedTo'
 * and the application will compare the contents of the list with the 
 * list of groups retrieved from the authentication provider for the 
 * currently authenticated user. If there is an intersection, access is 
 * granted. If there is not, then the `browser_pt_auth_policy` setting 
 * (discussed above) is consulted to determine if there is a subset of the
 * information which is publicly visible (LIMIT), or if the user should be
 * kicked out completely (DENY). 
 * 
 */

 // Assume nothing.
 $authorizedUser = false;
 $limitedVisibility = false;

 // Has authentication provider informationbeen specified?
 if (($auth = App::Get()->getAuthenticationProvider()) != false ) {

 	// Is the user currently logged in?
 	if (($username = $auth->getCurrentUsername()) != false ) {
 		
 		// Obtain the groups for the current user
 		$userGroups = $auth->retrieveGroupsForUser($username);
 		
 		// Does the product type define a metadata element matching
 		// the `browser_data_access_key` config setting?
 		$keyFound   = isset($typeInfo['typeMetadata'][App::Get()->settings['browser_data_access_key']]);
 		
 		// Obtain the groups for the current resource
 		$resourceGroups = ($keyFound)
 			? $typeInfo['typeMetadata'][App::Get()->settings['browser_data_access_key']]
 			: array();
 			
 		// Perform a comparison via array intersection to determine overlap
 		$x = array_intersect($userGroups,$resourceGroups);
			
 		if (empty($x)) { // No intersection found between user and resource groups

 			// Examine `browser_pt_auth_policy` to determine how to handle the failure
 			switch (strtoupper(App::Get()->settings['browser_pt_auth_policy'])) {
 				case "LIMIT":
 					// Allow the user to proceed, the metadata visibility policy
 					// will be used to determine what is visible to non-authorized
 					// users.
 					$limitedVisibility = true;
 					break; 					
 				case "DENY":
 				default:
 					// Kick the user out at this point, deny all access. 
	 				App::Get()->redirect(SITE_ROOT . '/errors/403');
 			}
 		} else {
 			// We have an authorized user
 			$authorizedUser = true;
 		}
 	} else {
 		// If no logged in user, and policy says DENY, kick the user
 		if (strtoupper(App::Get()->settings['browser_pt_auth_policy']) == "DENY") {
 			App::Get()->redirect(SITE_ROOT . '/errors/403');
 		}
 		$limitedVisibility = true;
 	}
 } else {
 	// If no authentication provider information exists in the application
 	// configuration file, it is assumed that authentication and authorization
 	// are not needed for this application, and thus every user is authorized
 	// by default.
 	$authorizedUser = true; 	
 }
/****************************************************************************/
/*
 * Accepted datasets should always be fully visible to the general public
 */ 
if ($typeInfo['typeMetadata']['QAState'][0] == 'Accepted') {
 	$authorizedUser = true;
 	$limitedVisibility = false;
}

// Initialize the FilterWidget
$querySiteRoot = (isset(App::Get()->settings['query_service_url']))
	? App::Get()->settings['query_service_url']
	: 'http://' . $_SERVER['HTTP_HOST'] . $ctx->moduleRoot;

$filterWidget = new FilterWidget(array(
	'productType'=>$productType,
	'htmlID'=>'cas_browser_product_list',
	'siteUrl'=>$querySiteRoot,
	'pagedResults'=>true,
	'resultFormat'=>"json"));
$filterWidget->renderScript();

// Determine which product page to display to the user
$pageWanted = (isset(App::Get()->request->segments[1]) && App::Get()->request->segments[1] == 'page')
	? (isset(App::Get()->request->segments[2]) ? App::Get()->request->segments[2] : 1)
	: 1;

$page = Utils::getPage($productType, $pageWanted);

// Create a ProductPage Widget to display the results
require_once($ctx->modulePath . '/scripts/widgets/ProductPageWidget.php');
$productPageWidget = new ProductPageWidget(
	array("productTypeId" => App::Get()->request->segments[0],
		  "returnPage"    => $pageWanted));
$productPageWidget->load($page);

// Prepare BreadcrumbWigdet
$bcw = new BreadcrumbsWidget();
$bcw->add('Home',SITE_ROOT . '/');
$bcw->add($ptName, $ctx->moduleRoot."/dataset/{$ptID}");
$bcw->add("Products");


?>
<div class="container">
<div id="cas_browser_container" class="span-24 last">

	<div id="section_products">
		<h3 ><?php echo $ptName?></h3>
		
		<div id="section_type_metadata">
			<h4>Dataset Abstract:</h4>
			<p><?php echo $typeInfo['description']?></p>
			<hr class="space"/>
			
			<ul class="tabmenu">
				<li><a href="../dataset/<?php echo $ptID ?>">Additional Information</a></li>
				<li class="selected">Downloadable Files</li>
			</ul>
		</div>
		<?php if ($limitedVisibility):?>
		<div class="notice">
			Additional information may exist that is not visible due to your current access permissions.
		</div>
		<?php endif;?>
		<div id="cas_browser_product_list" class="span-12 colborder">
		  <h4>Downloadable Files for this Dataset</h4>
			<?php echo $productPageWidget->renderPageDetails()?>
			<?php $productPageWidget->render()?>
		</div>
		<div id="cas_browser_product_actions" class="span-9 last">
			<div id="cas_browser_dataset_download">
			<?php if($limitedVisibility): ?>
				<div style="background-color:#bbddff;padding:5px">
					You do not currently have permission to download this dataset as a zip file
				</div>
			<?php else: ?>
				<a href="<?php echo App::Get()->settings['browser_datadeliv_url']?>/dataset?typeID=<?php echo App::Get()->request->segments[0]?>&format=application/x-zip">
        	                <img src="<?php echo $ctx->moduleStatic?>/img/zip-icon-smaller.gif" alt="zip-icon" style="float:left;margin-right:15px;"/>
                        </a>
		        Click on the icon to download all <?php echo $productCount ?> data products associated with
		        this product type as a single Zip archive.
			<?php endif ?>
			<br/>
			</div>
			<hr class="space"/>
			<hr/>
			<div id="cas_browser_filter_widget">
			  
			</div>
		</div>
	</div>
</div>
</div>
