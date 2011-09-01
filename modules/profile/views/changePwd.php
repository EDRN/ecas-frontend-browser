<?php 
/*
 * User Profile:
 * Allow user to change password
 */
$module = App::Get()->loadModule();

// Prepare BreadcrumbsWigdet
$bcw = new BreadcrumbsWidget();
$bcw->add('Home', SITE_ROOT . '/');
$bcw->add('Change Password');
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#profile_fieldset').corner();
		});
	</script>
    
    <div class='span-22 append-1 prepend-1 last' id='profile_container'>
		<h1>Change Password</h1>
		<div id="submenu">
			<p>
				<a href="<?php echo $module->moduleRoot?>/manage"> Manage Profile</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="<?php echo $module->moduleRoot?>/changePwd">Change Password</a>
				&nbsp;&nbsp;|&nbsp;&nbsp;
				<a href="<?php echo $module->moduleRoot?>/groups">Groups</a>
			</p>
		</div>
		
		<hr class="space">
		
		<fieldset id='profile_fieldset'>
		<form id="signupform" autocomplete="off" method="post" action="<?php echo $module->moduleRoot?>/changePwd.do">
    		<hr class="space">
    			
        	<div class="span-4 prepend-1">
	    		<label for="password">Choose a password</label>
	    	</div>
	    	<div id="form_input" class="span-12">
	    		<input class="profile_input" id="password" name="password" type="password" maxlength="50" value="" />
	    	</div>
	    	
	    	<hr class="space">
	    	
	    	<div class="span-4 prepend-1">
	    		<label for="password_confirm">Confirm password</label>
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
