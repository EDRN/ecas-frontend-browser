<?
/*
 * User Profile:
 * Lists all the information available 
 */

$userAttr = App::Get()->getAuthProvider()->retrieveUserAttributes( 
				App::Get()->getAuthProvider()->getCurrentUsername(), App::Get()->settings['auth_ldap_attributes'] );

?>

	<div class='span-22 append-1 prepend-1 last' id='profile_container'>
		<h1>Welcome <?php echo $userAttr['gn'] ?>! </h1>
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

		<h3>Profile</h3>		
 		<fieldset id='profile_fieldset'>
 			<hr class="space">
 			<hr class="space">
	    	<div class="span-13 ">
	    		<h4 align="left">First Name</h4>
	    	</div>
	    	<div class="span-5">
	    		<h6><?php echo $userAttr['givenname']; ?></h6>
	    	</div>
	    	
	    	<hr class="space">
	    	
	    	<div class="span-13 ">
	    		<h4 align="left">Last Name</h4>
	    	</div>
	    	<div class="span-5">
	    		<h6><?php echo $userAttr['sn']; ?></h6>
	    	</div>
	    	
	    	<hr class="space">
	    	
	    	<div class="span-13 ">
	    		<h4 align="left">Login Name </h4>
	    	</div>
	    	<div class="span-5">
	    		<h6><?php echo $userAttr['uid']; ?></h6>
	    	</div>
	    	
	    	<hr class="space">
	    	
	    	<div class="span-13 ">
	    		<h4 align="left">Email</h4>
	    	</div>
	    	<div class="span-5">
	    		<h6><?php echo $userAttr['mail']; ?></h6>
	    	</div>
	    	<hr class="space">
		</fieldset>
		
    	<hr class="space">
    	
	</div>
	