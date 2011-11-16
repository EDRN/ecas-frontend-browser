<?php 
/*
 * User Profile:
 * Login page
 */

// If a user is already logged in, redirect to home page
if( App::Get()->getAuthenticationProvider()->isLoggedIn() ) App::Get()->Redirect(SITE_ROOT . "/"); 

?>

	<div class='span-22 append-1 prepend-1 last' id='profile_container'> 
		<h1>Please log in to continue...</h1>
		
		<hr class="space">
		
		<fieldset id='profile_fieldset'>
		<form id="signupform" autocomplete="off" method="post" action="<?php echo SITE_ROOT?>/profile/login.do">
			<hr class="space">
		 	<hr class="space">
		 	
			<div class="span-3">
				<h6 align="right">Username</h6>
			</div>
			<div class="span-15">
				<input class="profile_input" id="username" name="username" type="text" value="" maxlength="50" />
			</div>
			
			<hr class="space">
			
			<div class="span-3">
	    		<h6 align="right">Password</h6>
	    	</div>
	    	<div class="span-15">
	    		<input class="profile_input" id="password" name="password" type="password" maxlength="50" value="" />
	    	</div>
	    	
	    	<hr class="space">
	    	<hr class="space">
	    	
	    	<div class="span-10" align="center">
	    		<input class="profile_input" id="button_new_account" type="submit" value="Submit">
    		</div>
    		
    		<hr class="space">
    		<hr class="space">
		</form>
		</fieldset>
	</div>
