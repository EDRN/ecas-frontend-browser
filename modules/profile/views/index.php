<?
/*
 * User Profile:
 * Lists all the information available 
 */
$module = App::Get()->loadModule();

function displayAttributes($userAttr) {

	$str = '';
 	foreach ($userAttr as $key=>$keyValue) {
 		foreach (App::Get()->settings['attr_titles'] as $attrTitle=>$value) {
 			if ( $key === $value) {
 				$str .= '<div class="span-13 prepend-1"><h4 align="left">';
 				$str .= $attrTitle;
 				$str .= '</h4></div>';
 				
 				$str .= '<div class="span-5"><h6 align="left">';
 				$str .= $keyValue;
 				$str .= '</h6></div>';
 			}
 		}
 	}
 	return $str;	
}

// Get user attributes
$userAttr = App::Get()->getAuthenticationProvider()->retrieveUserAttributes( 
			App::Get()->getAuthenticationProvider()->getCurrentUsername(), App::Get()->settings['profile_attributes'] );

// Prepare BreadcrumbsWigdet
$bcw = new BreadcrumbsWidget();
$bcw->add('Home', SITE_ROOT . '/');
$bcw->add('Profile');
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#profile_fieldset').corner();
		});
	</script>
	
	<div class='span-22 append-1 prepend-1 last' id='profile_container'>
		<h1>Welcome <?php echo $userAttr[ App::Get()->settings['firstname_attr'] ] ?>! </h1>
		<div id="submenu">
			<p>
				<a href="<?php echo $module->moduleRoot ?>/manage"> Manage Profile</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="<?php echo $module->moduleRoot?>/changePwd">Change Password</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="<?php echo $module->moduleRoot?>/groups">Groups</a>
			</p>
		</div>
		
 		<hr class="space">

		<h3>Profile</h3>		
 		<fieldset id='profile_fieldset'>
 			<hr class="space">
 			<hr class="space">
			
			<?php 	echo displayAttributes($userAttr) ?>
			
			<hr class="space">
		</fieldset>
		
    	<hr class="space">
    	
	</div>
	
