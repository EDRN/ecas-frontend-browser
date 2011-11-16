<?
/*
 * User Profile:
 * Lists all the groups and roles of the user 
 */

// Get instance of sso class
$sso = App::Get()->getAuthProvider();

$groups = $sso->retrieveGroupsForUser($sso->getCurrentUsername(),App::Get()->settings['sso_group_dn']);

?>

	<div class='span-22 append-1 prepend-1 last' id='profile_container'>
		<h1><?php echo $sso->getCurrentUsername() ?>'s Groups </h1>
		<div id="submenu">
			<p>
				<a href="<?php echo SITE_ROOT?>/profile/manage"> Manage Profile</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="<?php echo SITE_ROOT?>/profile/changePwd">Change Password</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="<?php echo SITE_ROOT?>/profile/groups">Groups</a>
			</p>
		</div>
			
		<hr class="space">

		<fieldset id='profile_fieldset'>
		<?php 
			
		if (count($groups) > 0) {
			echo "<div class='span-15 prepend-1'>";
			foreach ($groups as $group) {
				List($mission, $role) =  explode("_", $group);
				
				if( $mission != $missionIndex ) {
					echo "</ul></ul>";
					echo "<hr class='space'>";
					echo "<h3><a href=\"".SITE_ROOT."/{$mission}\"> {$mission}</a></h3>";
					echo "<ul><ul>";
					$missionIndex = $mission;
				}		
			  	echo "<li>";
			  	echo $role;
				echo "</li>";
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
	