<?
/*
 * User Profile:
 * Lists all the information that is editable 
 */

$userAttr = App::Get()->getAuthenticationProvider()->retrieveUserAttributes( 
				App::Get()->getAuthenticationProvider()->getCurrentUsername(), App::Get()->settings['auth_ldap_attributes'] );

?>

    <div class='span-22 append-1 prepend-1 last' id='profile_container'>
    	<h1>Manage Your Profile</h1>
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
		<form id="signupform" autocomplete="off" method="post" action="<?php echo SITE_ROOT?>/profile/manage.do">
	    	<hr class="space">

	    	<div class="span-3">
	    		<h6 align="right">First Name</h6>
	    	</div>
	    	<div class="span-12">
	    		<input class="profile_input" id="firstname" name="firstname" type="text" value="<?php echo $userAttr['givenname']; ?>"  maxlength="100" />
	    	</div>
	    	
	    	<hr class="space">
	    	
	    	<div class="span-3">
	    		<h6 align="right">Last Name</h6>
	    	</div>
	    	<div class="span-12">
	    		<input class="profile_input" id="lastname" name="lastname" type="text" value="<?php echo $userAttr['sn']; ?>"  maxlength="100" />
	    	</div>

	    	<hr class="space">
	    	
	    	<div class="span-3">
	    		<h6 align="right">Email</h6>
	    	</div>
	    	<div class="span-12">
	    		<input class="profile_input" id="email" name="email" type="text" value="<?php echo $userAttr['mail']; ?>" maxlength="150" />
	    	</div>
	    	
	    	<hr class="space">
	    	<hr class="space">
	    	
	    	<div class="span-10" align="center">
	    		<input class="profile_input" type="submit" value="Submit">
    		</div>
    		
	    	<hr class="space">
    	</form>
		</fieldset>
		
    </div>
