<?php
	$module = App::Get()->loadModule();
?>
<h1><img src="<?php echo $module->moduleStatic?>/img/beaker.png" style="margin-left:-35px;"/>LabCas</h1>
<h2>Laboratory Catalog and Archive Service</h2>
<hr/>
<img src="<?php echo $module->moduleStatic?>/img/icons/silk/add.png" style="vertical-align:middle;"/> 
	&nbsp;<a href="<?php echo $module->moduleRoot?>/upload">Upload a new file to share</a> &nbsp;|&nbsp;
<img src="<?php echo $module->moduleStatic?>/img/icons/silk/pages.png" style="vertical-align:middle;"/>	
	&nbsp;<a href="<?php echo $module->moduleRoot?>/myfiles">View my uploaded files</a>