<?php
?>
<?php
$module = App::Get()->loadModule();
?>
<style type="text/css">
	td.separator {
		padding:15px 0px 8px 0px;
	}
	td,th {
		vertical-align:top;
	}
	td.hint {
		padding-top:6px;
		color:#444;
		font-size:90%;
	}
	td.important {
		background-color:#ffd873;
		padding:20px 15px;
		border:solid 1px #bf9730;
	}
	th {
		padding-top:6px;
	}
	textarea {
		height:140px;
		padding:10px;
		font-size:115%;
	}
	.centered {
		text-align:center;
	}
	.right {
		text-align:right;
	}
	td.quote {
		font-size:130%;
		color:#444;
		font-family:Georgia,"Times New Roman", serif;
		line-height:2em;
	}
	span.tag {
		padding:3px 15px;
		border:solid 1px #48c;
		background-color:#8cf;
		-webkit-border-radius: 6px;
		-moz-border-radius: 6px;
		border-radius: 6px;
	}
	option {
		padding:3px;
	}
	ul  {

	}
	ul li {
		line-height:30px;
	}
</style>
<div class="container">
	<h2><a href="<?php echo $module->moduleRoot?>/"><img src="<?php echo $module->moduleStatic?>/img/beaker.png" style="height:100px;margin-left:-25px;border:none;"/></a>Preview your File Upload...</h2>
	<p>Here are the settings we have associated with your file:
	<table>
		<tr><td colspan="4" class="separator"><hr/></td></tr>		
		<tr><th style="padding-bottom:25px;width:130px;">File:</th>
		    <td><big>Annexin-Lamr1 14-3-3.pdf</big></td>
	 	    <td><img src="./pdf.jpeg" style="height:40px;margin-right:8px;float:left;vertical-align:bottom"/><big><strong>Type:</strong> PDF</big></td>
		    <td class="right"><big><strong>File Size:</strong> 2.3MB</big></td>
		</tr>
		<tr><th style="padding-bottom:25px;width:130px;">Hash:</th>
		    <td colspan="3"><big>a8650256acd7127c1f1cf714b5188a5e</big></td>
		<tr><td colspan="4" class="separator"><hr/></td></tr>
		<tr><th>Description:</th>
		    <td class="quote" colspan="3"><big>&ldquo;</big>This file represents an ROC curve for the Hanash 3-marker panel. It was designed to visualize the ongoing evaluation 
			of the discriminatory capacity of selected fractions<big>&rdquo;</big></td>
		</tr>
		<tr><td colspan="4" class="separator"><hr/></td></tr>
		<tr><th>Protocol:</th>
		    <td colspan="2"><big>FHCRCHanashAnnexinLamr</big></td>
		    <td class="right"><big><strong>PI:</strong> Samir Hanash</big></td>
		</tr>
		<tr><td colspan="4" class="separator"><hr/></td></tr>
		<tr><th>Tags:</th>
		    <td colspan="3">
			<span class="tag">Lung</span> , 
			<span class="tag">3-Marker Panel</span> , 
			<span class="tag">Hanash</span> ,
			<span class="tag">ROC</span> ,
			<span class="tag">Annexin</span></td>
		</tr>
		<tr><td colspan="4" class="separator"><hr/></td></tr>	
	
		<tr><th style="padding-bottom:55px;">Visibility:</th>
		    <td colspan="2"><big>Public</big></td>
		</tr>
		<tr><td colspan="4" class="separator"><hr/></td></tr>

		<tr><th>Share with these Groups:</th>
		    <td colspan="3">
			<ul>
				<li>National Cancer Institute</li>
				<li>Lung and Upper Aerodigestive</li>
			</ul>
		    </td>
		    
		</tr>
		<tr><td colspan="4" class="separator"><hr/></td></tr>
		<tr><th>Share with these Individuals:</th>
		    <td colspan="3">
			<ul>
				<li>John Semmes</li>
				<li>Kristen Anton</li>
			</ul>
			
		    </td>
		</tr>
		<tr><td colspan="4" class="separator"><hr/></td></tr>
		
		
		<tr><td colspan="4" class="hint" style="text-align:right;">If this all looks correct, click here to commit your file: 
			<input type="submit" value="Commit to Upload"/> &nbsp;or:&nbsp;
			<input type="submit" value="Let Me Try Again"/></td>
		</tr>
	</table>



