<?php
require_once(HOME . '/scripts/widgets/BreadcrumbsWidget.php');
$ctx = App::Get()->loadModule();
require_once($ctx->modulePath . "/classes/CasBrowser.class.php");
require_once($ctx->modulePath . "/scripts/widgets/MetadataDisplayWidget.php");
require_once($ctx->modulePath . "/scripts/widgets/ProductDownloadWidget.php");

// Get a CAS-Browser XML/RPC client
$browser  = new CasBrowser();
$client   = $browser->getClient();
    
// Get the specified product
$product = $client->getProductById(App::Get()->request->segments[0]);
$productName     = $product->getName();
$productTypeInfo = $product->getType()->toAssocArray();
$productTypeName = $productTypeInfo[App::Get()->settings['browser_pt_name_key']];
$productTypeId   = $productTypeInfo[App::Get()->settings['browser_pt_id_key']];
$productMetadata = $client->getMetadata($product);

// Get metadata for product as associative array
$productInfo     = $productMetadata->toAssocArray();


/****************************************************************************
 * Security Check
 ****************************************************************************
 * 
 * The behavior of this page depends upon the `browser_p_auth_policy`
 * configuration setting in this module's config.ini file. If the current
 * user does not have permission to view this product, and the 
 * auth policy is:
 *    LIMIT  , then apply the element-visibility.ini policy.
 *    DENY   , then force redirect with 403 Not Authorized error.
 *
 * Note that, if no authentication provider information is specified
 * in the main application config file, the above is disregarded and
 * all products are always visible by default.
 * 
 * The determination of whether or not a user should be granted access
 * to this page is made by examining the user's security groups (aka,
 * roles, permissions, etc) and comparing them against the set of 
 * security groups annotated in the product metadata itself. This
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
 * Note that if the product metadata does not contain the key specified
 * in `browser_data_access_key`, then the page will try to find the key
 * in the corresponding productType metadata. This means that, unless 
 * explicitly set, products inherits the accessibility of their productType. 
 * 
 */

 // Assume nothing.
 $authorizedUser = false;
 $limitedVisibility = false;

 // Has authentication provider informationbeen specified?
 if (($auth = App::Get()->getAuthenticationProvider()) != false ) {

    $connData['sso_base_dn'] = App::Get()->settings['sso_ldap_base_dn'];
    $connData['sso_group_dn'] = App::Get()->settings['sso_ldap_group_dn'];
    $connData['sso_ldap_host'] = App::Get()->settings['sso_ldap_host'];
    $connData['sso_ldap_port'] = App::Get()->settings['sso_ldap_port'];
    $auth->setConnectionData($connData);
    $auth->connect();

 	// Is the user currently logged in?
 	if (($username = $auth->getCurrentUsername()) != false ) {
 		
 		// Obtain the groups for the current user
 		$userGroups = $auth->retrieveGroupsForUser($username);

 		
 		// Obtain the groups for the current resource. This is accomplished by
 		// first checking the product metadata for an existing `browser_data_access_key` 
 		// metadata key. If one is not found, the productType metadata is then inspected.
 		// If both fail, then no usable information exists, so resourceGroups is an empty array.
 		$resourceGroups = isset($productInfo[App::Get()->settings['browser_data_access_key']])
 			? $productInfo[App::Get()->settings['browser_data_access_key']]
 			: (isset($productTypeInfo['typeMetadata'][App::Get()->settings['browser_data_access_key']])
 				? $productTypeInfo['typeMetadata'][App::Get()->settings['browser_data_access_key']]
 				: array());
 			
 		// Perform a comparison via array intersection to determine overlap
 		$x = array_intersect($userGroups,$resourceGroups);
			
 		if (empty($x)) { // No intersection found between user and resource groups

 			// Examine `browser_pt_auth_policy` to determine how to handle the failure
 			switch (strtoupper(App::Get()->settings['browser_p_auth_policy'])) {
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
 		if (strtoupper(App::Get()->settings['browser_p_auth_policy']) == "DENY"
 			&& $productTypeInfo['typeMetadata']['QAState'][0] != 'Accepted') {
 			App::Get()->redirect(SITE_ROOT . '/errors/403');
 		} else if ($productTypeInfo['typeMetadata']['QAState'][0] != 'Accepted') {
 			$authorizedUser = true;
 			$limitedVisibility = false;
 		} else {
 			$limitedVisibility = true;
 		}
 	}
 } else {
 	// If no authentication provider information exists in the application
 	// configuration file, it is assumed that authentication and authorization
 	// are not needed for this application, and thus every user is authorized
 	// by default.
 	$authorizedUser = true; 	
 }
/****************************************************************************/


// Create a MetadataDisplay widget
$metadataWidget = new MetadataDisplayWidget(array());


// Is someone logged in?
$status = (App::Get()->getAuthenticationProvider()
	   && App::Get()->getAuthenticationProvider()->getCurrentUsername() != false)
  ? CasBrowser::VIS_AUTH_AUTHENTICATED
  : CasBrowser::VIS_AUTH_ANONYMOUS;

$metadataWidget->loadMetadata($browser->getProductVisibleMetadata(App::Get()->request->segments[0], $status));

// Record the product page to send the user back to, if provided
$returnPage = isset(App::Get()->request->segments[1]) ? App::Get()->request->segments[1] : 1;

// Create a ProductDownloadWidget
$productDownloadWidget = new ProductDownloadWidget(array(
	"dataDeliveryUrl" => App::Get()->settings['browser_datadeliv_url']));
$productDownloadWidget->setClient($client);
$productDownloadWidget->load($product);

// Add the cas-browser styles
App::Get()->response->addStylesheet($ctx->moduleStatic . '/css/cas-browser.css');

// Prepare BreadcrumbWigdet
$bcw = new BreadcrumbsWidget();
$bcw->add('Home',SITE_ROOT . '/');
$bcw->add($productTypeName, $ctx->moduleRoot."/dataset/{$productTypeId}");
$bcw->add('Products', $ctx->moduleRoot."/products/{$productTypeId}/page/{$returnPage}");
$bcw->add(App::Get()->request->segments[0]);


?>
<script type="text/javascript">
	$(document).ready(function() {

		// Handle Page Tabs
		$('#section_products').hide();
		$('a#tab_metadata').click(function() {
			$('#section_products').hide();
			$('#section_metadata').show();
			$('ul.tabs a').removeClass('selected');
			$(this).addClass('selected');
			$(this).blur();
		});
		$('a#tab_products').click(function() {
			$('#section_metadata').hide();
			$('#section_products').show();
			$('ul.tabs a').removeClass('selected');
			$(this).addClass('selected');
			$(this).blur();			
		});

		// Handle Metadata Widget
		$('table.metwidget .multivalue').each(function() {
			var rows = $(this).find('.value');
			$(this).parent().prepend('<h4 class="toggler">'+ rows.length + ' additional entries suppressed</h4>');
			$(this).hide();
		});
		$('.toggler').live('click',function() {
			$(this).next().show();
			$(this).hide();
		});

		$('td.value > div').corner();
		
	});
	
</script>
<div class="span-22 last prepend-1 append-1">
	<div id="cas_browser_product_metadata">
		<h3 class="loud">Product Metadata: <?php echo $productName?></h3>
		<?php $metadataWidget->render()?>
	</div>
	<div id="cas_browser_product_download">
		<h3 class="loud">Download this Product:</h3>
		<?php $productDownloadWidget->render()?>
	</div>
</div>
