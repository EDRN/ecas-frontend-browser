<?php
require_once(MODULE . "/classes/CasBrowser.class.php");

// Get a CAS-Browser XML/RPC client
$browser  = new CasBrowser();
$client   = $browser->getClient();
 
// Get a  Product Type object
$productType  = $client->getProductTypeById(App::Get()->request->segments[0]);
$productCount = $client->getNumProducts($productType);
$ptID = $productType->getId();
$ptName = $productType->getName();

// Determine which search widget to show
$widgetClassName = isset(App::Get()->settings['browser_product_search_widget'])
	? App::Get()->settings['browser_product_search_widget']
	: 'FilterWidget';

$querySiteRoot = (isset(App::Get()->settings['query_service_url']))
	? App::Get()->settings['query_service_url']
	: 'http://' . $_SERVER['HTTP_HOST'] . MODULE_ROOT;

// Create the search widget
require_once(MODULE . "/scripts/widgets/{$widgetClassName}.php");
$searchWidget = new $widgetClassName(array(
	'productType'=>$productType,
	'htmlID'=>'cas_browser_product_list',
	'siteUrl'=>$querySiteRoot,
	'pagedResults'=>true,
	'resultFormat'=>'json'));

// Render search widget javascript
$searchWidget->renderScript();
?>
<div class="container">
<div class="breadcrumbs">
<a href="<?php echo SITE_ROOT?>/">Home</a>&nbsp;&rarr;&nbsp;
<a href="<?php echo MODULE_ROOT?>/">Browser</a>&nbsp;&rarr;&nbsp;
<a href="<?php echo MODULE_ROOT."/dataset/{$ptID}"?>"><?php echo $ptName?></a>&nbsp;&rarr;&nbsp;
Product Search
</div>
<hr class="space"/>
<div id="cas_browser_container" class="span-24 last">
	<ul class="tabs">
	  <li><a id="tab_metadata" href="<?php echo MODULE_ROOT?>/dataset/<?php echo $ptID?>">Metadata</a></li>
	  <li><a id="tab_browse"   href="<?php echo MODULE_ROOT?>/products/<?php echo $ptID?>">Browse</a></li>
	  <li><a id="tab_search"   href="<?php echo MODULE_ROOT?>/search/<?php echo $ptID?>" class="selected">Search</a></li>
	</ul>
	<div id="section_products">
		<h2 class="larger loud">Product Search: <?php echo $ptName?></h2>
		<br/>
		<div id="cas_browser_search_widget" class="span-24 last">
	  		<?php $searchWidget->render(); ?>
			<input type="hidden" id="page_num" value="1">
		</div>
		<div id="cas_browser_product_list" class="span-16 colborder">
	  		<h3>Product Search Results</h3>
		</div>
		<div id="cas_browser_dataset_download" class="span-6 last">
			<a href="<?php echo App::Get()->settings['browser_datadeliv_url']?>/dataset?typeID=<?php echo App::Get()->request->segments[0]?>&format=application/x-zip">
				<img src="<?php echo MODULE_STATIC?>/img/zip-icon-smaller.gif" alt="zip-icon" style="float:left;margin-right:15px;"/>
			</a>
			Click on the icon to download all <?php echo $productCount ?> data products associated with
			this search as a single Zip archive.<br/>
		</div>
	</div>
</div>
</div>
