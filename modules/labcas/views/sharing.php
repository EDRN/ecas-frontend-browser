<?php
$module = App::Get()->loadModule();
App::Get()->response->addStylesheet($module->moduleStatic . '/css/labcas.css');
?>
<div class="container">
	<h2><a href="<?php echo $module->moduleRoot?>/"><img src="<?php echo $module->moduleStatic?>/img/beaker.png" style="height:100px;margin-left:-25px;border:none;"/></a>Sharing and Visibility</h2>
	<p>Specify how others can interact with this file:
	<table>
		<tr><td colspan="4" class="separator"><hr/></td></tr>		
		<tr><th style="padding-bottom:55px;width:130px;">Visibility:</th>
		    <td colspan="2"><input type="checkbox" checked="checked">Public &nbsp; <input type="checkbox">Private (Just Me)</td>
		    <td class="hint" style="width:40%;">If you select "Private", this file will not be visible from the EDRN Public Portal, or by any members 
			of any collaborative groups, or by other EDRN investigators</td>
		</tr>
		<tr><td colspan="4" class="separator"><hr/></td></tr>

		<tr><th>Share with these Groups:</th>
		    <td colspan="2">
			<select size="5" style="width:100%;">
				<optgroup label="Programmatic">
					<option>National Cancer Institute</option>
					<option>Data Management and Coordinating Center</option>
					<option>Informatics Center</option>
				</optgroup>
				<optgroup label="Collaborative Groups">
					<option>Breast/GYN</option> 
					<option>GI &amp; Other Associated</option> 
					<option>Lung and Upper Aerodigestive</option>
					<option>Prostate</option>
				</optgroup>
			</select>
		    </td>
		    <td class="hint">Choose the EDRN groups that should be able to access this file. To select more than one group, simply hold down Control (Windows) or Command (Mac)</td>
		</tr>
		<tr><td colspan="4" class="separator"><hr/></td></tr>
		<tr><th>Share with these Individuals:</th>
		    <td colspan="2">
			<select size="5" style="width:100%;">
					<option>Alvin Liu</option>
					<option>Anna Lokshin</option>
					<option>Brian Habb</option>
					<option>Daniel W. Chan</option>
					<option>Eleftherios Diamandis</option>
					<option>Jeffrey Marks</option>
					<option>John Semmes</option>
					<option>Peter Barker</option>
					<option>Stephen J. Meltzer</option>
					<option>William Bigbee</option>
			</select>
		    </td>
		    <td class="hint">Choose the EDRN individuals that should be able to access this file. To select more than one individual, simply hold down Control (Windows) or Command (Mac)</td>
		</tr>
		<tr><td colspan="4" class="separator"><hr/></td></tr>
		
		
		<tr><td colspan="4" class="hint" style="text-align:right;">If this all looks correct, click here to final preview your upload: 
			<input type="submit" value="Final Preview"/> &nbsp;or:&nbsp;
			<input type="submit" value="Let Me Try Again"/></td>
		</tr>
	</table>





