<?php 
/*
 * User Profile:
 * Login page
 */
$module = App::Get()->loadModule();
$csw = new ConnectionSelectWidget();

// If a user is already logged in, redirect to home page
if( App::Get()->getAuthenticationProvider()->isLoggedIn() ) App::Get()->Redirect(SITE_ROOT . "/"); 

// Prepare BreadcrumbsWigdet
$bcw = new BreadcrumbsWidget();
$bcw->add('Home', SITE_ROOT . '/');
$bcw->add('Login');
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#profile_fieldset').corner();
		});
	</script>
	
	<div class='span-22 append-1 prepend-1 last' id='profile_container'> 
		<h1>Please log in to continue...</h1>
		
		<hr class="space">
		
		<fieldset id='profile_fieldset'>
			<hr class="space">
		 	<hr class="space">
                <?php //if ( isset($csw) ) { $csw->render(); } ?>
		
		<form id="signupform" autocomplete="off" method="post" action="<?php echo $module->moduleRoot ?>/login.do">
		 	
			<div class="span-2 prepend-1">
				<label for="username">Username</label>
			</div>
			<div class="span-15">
				<input class="profile_input" id="username" name="username" type="text" value="" maxlength="50" />
			</div>
			
			<hr class="space">
			
			<div class="span-2 prepend-1">
	    		<label for="password">Password</label>
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
