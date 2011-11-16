<?php 
/*
 * User Profile:
 * Allow user to change password
 */

?>

    <div class='span-22 append-1 prepend-1 last' id='profile_container'>
		<h1>Change Password</h1>
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
		<form id="signupform" autocomplete="off" method="post" action="<?php echo SITE_ROOT?>/profile/changePwd.do">
    		<hr class="space">
    			
        	<div class="span-4 ">
	    		<h6 align="right">Choose a password</h6>
	    	</div>
	    	<div id="form_input" class="span-12">
	    		<input class="profile_input" id="password" name="password" type="password" maxlength="50" value="" />
	    	</div>
	    	
	    	<hr class="space">
	    	
	    	<div class="span-4 ">
	    		<h6 align="right">Confirm password</h6>
	    	</div>
	    	<div id="form_input" class="span-12">
	    		<input class="profile_input" id="password_confirm" name="password_confirm" type="password" maxlength="50" value="" />
	    	</div>
	    	
	    	<hr class="space">
	    	<hr class="space">
	    	
	    	<div class="span-10" align="center">
	    		<input class="profile_input" id="button_passwdReset" type="submit" value="Submit">
    		</div>
    		
			<hr class="space">
    	</form>
    	</fieldset>
		
    </div>
