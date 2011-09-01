<?
/*
 * User Profile:
 * Lists all the groups and roles of the user 
 */
$module = App::Get()->loadModule();

// Get instance of authentication and authorization class
$authorization 	= App::Get()->getAuthorizationProvider();
$authentication = App::Get()->getAuthenticationProvider();

if ( $authorization != false) {
	$groups = $authorization->retrieveGroupsForUser($authentication->getCurrentUsername(),App::Get()->settings['authorization_ldap_group_dn']);
}

// Prepare BreadcrumbsWigdet
$bcw = new BreadcrumbsWidget();
$bcw->add('Home', SITE_ROOT . '/');
$bcw->add('User Groups');
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#profile_fieldset').corner();
		});
	</script>
	
	<div class='span-22 append-1 prepend-1 last' id='profile_container'>
		<h1><?php echo $authentication->getCurrentUsername() ?>'s Groups </h1>
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

		<fieldset id='profile_fieldset'>
		<?php 
			
		if (count($groups) > 0) {
			echo "<div class='span-19 prepend-1'>";
			foreach ($groups as $g) {
				List($group, $role) =  explode("_", $g);
				
				if( $group != $groupIndex ) {
					echo "</ul></ul>";
					echo "<hr class='space'>";
					echo "<h4> {$group} </h4>";
					echo "<ul><ul>";
					$groupIndex = $group;
				}		
				if ( !empty($role)) {
					echo "<li>";
				  	echo $role;
					echo "</li>";
				}
				echo "<hr class='space'>";
			}
			
			echo "</div>";
		} else{
			echo "<h4> No Groups found!</h4>";
		}
		?>
		</fieldset>
		<hr class='space'>
	</div>
	