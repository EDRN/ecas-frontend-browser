<?php
// Load the module context for this module
$ctx  = App::Get()->loadModule();

require_once(HOME . '/classes/EcasUtilities.class.php');
require_once($ctx->modulePath . "/classes/CasBrowser.class.php");
require_once($ctx->modulePath . "/scripts/widgets/MetadataDisplayWidget.php");

// Get a CAS-Browser XML/RPC client
$browser  = new CasBrowser();
$client   = $browser->getClient();

// Get a  Product Type object
$productType = $client->getProductTypeById(App::Get()->request->segments[0]);
$ptID = $productType->getId();
$ptName = $productType->getName();

// Create a MetadataDisplayWidget to display productType metadata (only typeMetadata)
$typeMetadataWidget = new MetadataDisplayWidget(array());
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
 * security groups annotated in the product type metadata itself. This
 * requires that productType metadata include an element (it can be
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

 // Has authentication provider information been specified?
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
// Apply the element visibility policy
$visibleMetadata = $browser->getProductTypeVisibleMetadata($ptID, 
	($authorizedUser)
		? CasBrowser::VIS_AUTH_AUTHENTICATED
		: CasBrowser::VIS_AUTH_ANONYMOUS);

// Sort the visible elements according to the ordering policy
$sortedMetadata  = $browser->getSortedProductTypeMetadata($ptID,$visibleMetadata);

// Load up the metadata widget with the sorted, filtered, metadata
$sortedMetadata['ProtocolName'] = EcasUtilities::translate('protocol', $sortedMetadata['ProtocolName'][0]);
$sortedMetadata['SiteName']     = EcasUtilities::translate('site', $sortedMetadata['SiteName'][0]);
if (isset($sortedMetadata['PubMedID'])) {
	$sortedMetadata['PubMedID']     = EcasUtilities::translate('pubmed',$sortedMetadata['PubMedID'][0]);
}
if (isset($sortedMetadata['ResultsAndConclusionSummary'])) {
	$sortedMetadata['ResultsAndConclusionSummary'] = EcasUtilities::translate('longtext',$sortedMetadata['ResultsAndConclusionSummary'][0]);
}
if (isset($sortedMetadata['MethodDetails'])) {
	$sortedMetadata['MethodDetails'] = EcasUtilities::translate('longtext',$sortedMetadata['MethodDetails'][0]);
}
$typeMetadataWidget->loadMetadata($sortedMetadata);

// Create a MetadataDisplayWidget to display system metadata (all except typeMetadata)
$typeMetadata = $productType->toAssocArray();
unset($typeMetadata['typeMetadata']);
$systemMetadataWidget = new MetadataDisplayWidget(array());
$systemMetadataWidget->loadMetadata($typeMetadata)

?>
<script type="text/javascript">
	$(document).ready(function() {

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


<div class="container">
<div class="breadcrumbs">
<a href="<?php echo SITE_ROOT?>/">Home</a>&nbsp;&rarr;&nbsp;
<a href="<?php echo $ctx->moduleRoot?>/">Browser</a>&nbsp;&rarr;&nbsp;
<?php echo $ptName?>
</div>
<hr class="space"/>
<div id="cas_browser_container" class="span-24 last">
	<h3><?php echo $sortedMetadata['DataSetName'][0]?></h3>
	<?php if (!empty($response['description'])): ?>
	<div id="section_type_description">
		<h3>About this Product Type:</h3>
		<?php echo $response['description']?>
		<br/><br/>
	</div>
	<?php endif?>
	<div id="section_type_metadata">
		<h4>Dataset Abstract:</h4>
		<p><?php echo $typeInfo['description']?></p>
		<hr class="space"/>
		
		<ul class="tabmenu">
			<li class="selected">Additional Information</li>
			<li><a href="../products/<?php echo $ptID ?>">Downloadable Files</a></li>
		</ul>
		<div id="additional-information">
		<p>The following additional information has been defined for this dataset. The information has been provided by
		   the Principal Investigator or staff from his or her laboratory.</p>
		
		<?php if ($limitedVisibility):?>
		<div class="notice">
			Additional information may exist that is not visible due to your current access permissions.
		</div>
		<?php endif;?>
		<?php echo $typeMetadataWidget->render()?>
		</div>
		<div id="data">
		
		</div>
	</div>

<?php if (!App::Get()->settings['browser_suppress_system_metadata']):?>
	<div id="section_system_metadata">
		<h3>System Metadata:</h3>
		<?php echo $systemMetadataWidget->render()?>
	</div>
<?php endif;?>
</div>
</div>
