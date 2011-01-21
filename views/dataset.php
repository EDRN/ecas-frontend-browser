<?php
// Load the Cas-Browser module
App::Get()->loadModule('browse');

require_once(HOME . "/classes/EcasUtilities.class.php");
require_once(HOME . "/modules/browse/classes/CasBrowser.class.php");
require_once(HOME . "/modules/browse/scripts/widgets/MetadataDisplayWidget.php");

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

// Apply the element visibility policy
$visibleMetadata = $browser->getProductTypeVisibleMetadata($ptID, CasBrowser::VIS_AUTH_ANONYMOUS);

// Sort the visible elements according to the ordering policy
$sortedMetadata  = $browser->getSortedProductTypeMetadata($ptID,$visibleMetadata);
$sortedMetadata['ProtocolName'][0] = Ecas::translate('protocol',$sortedMetadata['ProtocolName'][0]);


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
<?php echo $sortedMetadata['DataSetName'][0]?>
</div>
<hr class="space"/>
<div id="cas_browser_container" class="span-24 last">
	<h2><?php echo $sortedMetadata['DataSetName'][0]?> (<?php echo $ptName?>)</h2>
	<h3 style="margin-bottom:3px;">PI: <?php echo $sortedMetadata['LeadPI'][0]?>,
		<span class="quiet" style="font-size:85%;"><em><?php echo Ecas::translate('sitename',$sortedMetadata['SiteName'][0])?></em></span></h3>
	<hr/>
	<?php if (!empty($response['description'])): ?>
	<div id="section_type_description">
		<h3>About this Product Type:</h3>
		<?php echo $response['description']?>
		<br/><br/>
	</div>
	<?php endif?>
	<div id="section_type_metadata">
		<h3>Description:</h3>
		<p><?php echo $typeInfo['description']?></p>
		<hr class="space"/>
		
		<ul class="tabmenu">
			<li class="selected">Additional Information</li>
			<li>Downloadable Files</li>
		</ul>
		<div id="additional-information">
		<p>The following additional information has been defined for this dataset. The information has been provided by
		   the Principal Investigator or staff from his or her laboratory.</p>
		<?php echo $typeMetadataWidget->render()?>
		</div>
		<div id="data">
		
		</div>
	</div>
</div>
</div>
