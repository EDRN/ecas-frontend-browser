<?php
$module = App::Get()->loadModule();
App::Get()->response->addStylesheet($module->moduleStatic . '/css/labcas.css');
?>

<div class="container">
	<h2><a href="<?php echo $module->moduleRoot?>/"><img src="<?php echo $module->moduleStatic?>/img/beaker.png" style="height:100px;margin-left:-25px;border:none;"/></a>Upload a File...</h2>
	<table>
		<tr><td colspan="3" class="separator"><hr/></td></tr>		
		<tr><th>File: *</th>
		    <td><input type="file"/></td>
	 	    <td class="hint">Choose a file from your hard drive that you would like to upload. The maximum file size accepted by this service is <strong>300 MB</strong></td>
		</tr>
		<tr><td colspan="3" class="separator"><hr/></td></tr>

		<tr><th style="width:110px;">Description: *</th>
		    <td><textarea></textarea></td>
	 	    <td class="hint">Provide descriptive information that will help others to understand your file in context. This information will be publicly visible to
				     others and may appear in search results on the EDRN Public Portal.</td>
		</tr>
		<tr><td colspan="3" class="separator"><hr/></td></tr>
		<tr><th>Protocol:</th>
		    <td><input type="text" style="width:100%;padding:5px 5px;font-size:110%"/></td>
	 	    <td class="hint">Begin typing, and a list of matching EDRN protocols will appear. Each uploaded file must be associated with an EDRN protocol.</td>
		</tr>
		<tr><td colspan="3" class="separator"><hr/></td></tr>
		<tr><th>Tags:</th>
		    <td><input type="text" style="width:100%;padding:5px 5px;font-size:110%"/></td>
	 	    <td class="hint">Add one or more tags, separated by a comma, to this file. Tags help users to quickly group and filter files of interest. Examples: Lung, Early Results, ASN-31</td>
		</tr>
		<tr><td colspan="3" class="separator"><hr/></td></tr>
		<tr><td colspan="3" class="hint" style="text-align:right;">When ready, click here to begin the file upload: <input type="submit" value="Upload File"</td>
		</tr>
	</table>




