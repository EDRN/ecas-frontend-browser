<?php 
	// Load the Cas-Browser Module
	App::Get()->loadModule('data');
	
	require_once(HOME . '/classes/EcasUtilities.class.php');
	require_once(HOME . '/modules/browse/classes/CasBrowser.class.php');
	
	// Add the home page javascript
	App::Get()->response->addJavascript(SITE_ROOT . '/static/js/home.js');
	App::Get()->response->addJavascript(SITE_ROOT . '/static/js/jquery.labelify.js');
	App::Get()->response->addJavascript(SITE_ROOT . '/static/js/jquery.corner.js');
	
	// Get information about all product types in this cas instance
	$browser = new CasBrowser();
	$client  = $browser->getClient();
	$ptypes  = $client->getProductTypes();
	
	// Build informational arrays
	$investigators = array();
	$collabGroups  = array();
	$datasets      = array();
	$sites         = array();
	foreach ($ptypes as $ptype) {
		$met = $ptype->toAssocArray();
		$met = $met['typeMetadata'];
		
		// Investigator (LeadPI)
		$investigators[$met['LeadPI'][0]] = array(
			"label" => $met['LeadPI'][0],
			"href"  => '/data/productTypeFilter.do?key=LeadPI&value='.urlencode($met['LeadPI'][0]));
	
		// Protocol (ProtocolID)
		$protocols[$met['ProtocolId'][0]] = array(
			"label" => EcasUtilities::translate('protocol',$met['ProtocolId'][0]),
			"href"  => '/data/productTypeFilter.do?key=ProtocolId&value='.urlencode($met['ProtocolId'][0]));
		
		// Sites (SiteName)
		$siteName = EcasUtilities::translate('site', $met['SiteName'][0]);
		$sites[$met['SiteName'][0]] = array(
			"label" => $siteName,
			"href"  => '/data/productTypeFilter.do?key=SiteName&value=' . urldecode($met['SiteName'][0])
		);
	}
?>

<div class="span-10" style="padding:0.9em;height:150px;">
	<h3>About eCAS</h3>
	<p>The EDRN Catalog and Archive Service (eCAS) is the official source for
	   research data generated by EDRN participants. </p>
</div>
<div class="box span-6" style="padding:0.9em;height:150px;">
	<h3>Data Access</h3>
	<p>Some of the data on this site is not made available to
	   anonymous users. Data currently undergoing annotation or 
	   review requires a login name.</p>
</div>
<div class="box span-6 last" style="padding:0.9em;height:150px;">
	<h3>More About EDRN</h3>
	<p>Learn more about the EDRN by 
	   visiting the EDRN Public Portal.	
</div>
<br class="clr"/>
<hr/>

<div class="span-13 colborder" style="padding:1.4em;padding-top:0px;padding-left:0px;padding-right:1.4em;">
	<h2 style="border-bottom:dotted 1px #ccc;margin-top:0px;">Data Browser</h2>
	<div id="loadingDatasets" class="loading">
		<img src="<?php echo SITE_ROOT?>/static/img/loading.gif" style="vertical-align:middle;"/>
		&nbsp;Loading datasets...</div>
	<div id="ptBrowser"></div>
	

</div>
<div class="span-10 last">
	<div id="facetMenu">
		<ul>
			<li class="selected"><a href="#facet-pi" title="Principal Investigator"><img src="<?php echo SITE_ROOT?>/static/img/user_gray.png"/></a></li>
			<li><a href="#facet-protocol" title="Protocol"><img src="<?php echo SITE_ROOT?>/static/img/page_white_text.png"/></a></li>
			<li><a href="#facet-organ" title="Organ Group"><img src="<?php echo SITE_ROOT?>/static/img/group.png"/></a></li>
			<li><a href="#facet-site" title="Participating Site"><img src="<?php echo SITE_ROOT?>/static/img/building.png"/></a></li>
		</ul>
		<span class="label">Principal Investigator</span>
	</div>
	<div id="facet-pi" class="facet box">
		<h4><img src="<?php echo SITE_ROOT?>/static/img/user_gray.png"/>Filter By Principal Investigator</h4>
		<input type="text" class="search" id="investigatorSearch" title="Search for an investigator by name..."/>
		<ul id="investigatorList" class="filterList">
		<?php foreach ($investigators as $inv) :?>
		<li title="<?php echo $inv['label']?>">
			<a href="<?php echo $inv['href']?>"><?php echo $inv['label']?></a>&nbsp;
			<a class="removeLink"><img src="<?php echo SITE_ROOT?>/static/img/icon_remove.png"/>
			</a>
		</li>
		<?php endforeach ?>
		</ul>
	</div>
	<div id="facet-protocol" class="facet box">
		<h4><img src="<?php echo SITE_ROOT?>/static/img/page_white_text.png"/>Filter By Protocol</h4>
		<input type="text" class="search" id="protocolSearch" title="Search for a protocol by name..."/>
		<ul id="protocolList" class="filterList">
		<?php foreach ($protocols as $inv) :?>
		<li title="<?php echo $inv['label']?>">
			<a href="<?php echo $inv['href']?>"><?php echo $inv['label']?></a>&nbsp;
			<a class="removeLink"><img src="<?php echo SITE_ROOT?>/static/img/icon_remove.png"/>
			</a>
		</li>
		<?php endforeach ?>
		</ul>
	</div>
	<div  id="facet-organ" class="facet box">
		<h4><img src="<?php echo SITE_ROOT?>/static/img/group.png"/>Filter By Collaborative Group</h4>
		<div style="position:relative">
		  <div class="collabGroupLink" style="top:20px;left:50px;">
		  	<a href="">Lung &amp; Upper Aerodigestive</a>
		  </div>
		  <div class="collabGroupLink" style="top:45px;left:50px;">
		  	<a href="">Breast / GYN</a>
		  </div>		  
		  <div class="collabGroupLink" style="top:70px;left:50px;">
		  	<a href="">GI &amp; Other Associated</a>
		  </div>
		  <div class="collabGroupLink" style="top:95px;left:50px;">
		  	<a href="">Prostate &amp; Urologic</a>
		  </div>
		
		</div>
		<img src="<?php echo SITE_ROOT?>/static/img/humanbody_144x400.png" style="height:220px;"/>
		
	</div>
	<div id="facet-site" class="facet box">
		<h4><img src="<?php echo SITE_ROOT?>/static/img/building.png"/>Filter By Participating Site</h4>
		<input type="text" class="search" id="siteSearch" title="Search for a site by name..."/>
		<ul id="siteList" class="filterList">
		<?php foreach ($sites as $inv) :?>
		<li title="<?php echo $inv['label']?>">
			<a href="<?php echo $inv['href']?>"><?php echo $inv['label']?></a>&nbsp;
			<a class="removeLink"><img src="<?php echo SITE_ROOT?>/static/img/icon_remove.png"/>
			</a>
		</li>
		<?php endforeach ?>
		</ul>
	</div>
</div>
<br class="clr"/>