<?php 
/*
 * User Profile:
 * Display confirmation of password change and allow user to log back in
 */
$module = App::Get()->loadModule();

// Prepare BreadcrumbsWigdet
$bcw = new BreadcrumbsWidget();
$bcw->add('Home', SITE_ROOT . '/');
$bcw->add('Password Change');
?>

    <div class='span-22 append-1 prepend-1 last' id="profile_container">
    	<div class="span-24">
    		<br>
			<h3>Password has been changed.</h3>
			<br>
    	</div>
		
		<div class="span-22">
			<h5><a href="<?php echo $module->moduleRoot?>/login">Please log in with new password.</a></h5>
		</div>
		<hr class="space">
		
    </div>
    