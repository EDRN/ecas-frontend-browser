<?
/*
 * User Profile:
 * Lists all the information that is editable 
 */
$module = App::Get()->loadModule();
require_once($module->modulePath . '/classes/markdown.php');

function manageAttribute($userAttr) {
	$userAttr = App::Get()->getAuthenticationProvider()->retrieveUserAttributes( 
				App::Get()->getAuthenticationProvider()->getCurrentUsername(), App::Get()->settings['profile_attributes'] );
	$str = '';
 	foreach ($userAttr as $key=>$keyValue) {
 		foreach (App::Get()->settings['attr_titles'] as $attrTitle=>$value) {
 			if ( $key != App::Get()->settings['username_attr']) {

	 			if ( $key === $value) {
	 				$str .= '<div class="span-3 prepend-1"><label for="';
	 				$str .= $key . '"> ' . $attrTitle;
	 				$str .= '</label></div>';
	 				
	 				$str .= '<div class="span-12"><input class="profile_input" type="text" maxlength="100" id=';
	 				$str .= $key . ' name=' . $key . ' value=' . $keyValue;
	 				$str .= '></div>';
	 				$str .= '<hr class="space">';
	 			}
 			}
 		}
 	}

 	return $str;
}

// Get user attributes
$username = App::Get()->getAuthenticationProvider()->getCurrentUsername();
$userAttr = App::Get()->getAuthenticationProvider()->retrieveUserAttributes( 
				$username, App::Get()->settings['profile_attributes'] );
				
// Open the publication metadata file, and ensure it is writeable
$dataDir = App::Get()->settings['profile_data_dir'];
if (!is_writeable($dataDir)) {
	App::Get()->setMessage("Warning: The profile data directory is not writeable",CAS_MSG_WARN);
}

$path     = $dataDir . '/' . $username . '/' . $username . '.txt';
$contents = (file_exists( $path ))
	? (file_get_contents( $path ))
	: '';
// Prepare BreadcrumbsWigdet
$bcw = new BreadcrumbsWidget();
$bcw->add('Home', SITE_ROOT . '/');
$bcw->add('Manage');
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('.profile_fieldset').corner();
		});
	</script>
    
    <div class='span-22 append-1 prepend-1 last' id='profile_container'>
    	<h1>Manage Your Profile</h1>
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

		<!--	Form for updating user profile information	-->
		<fieldset class='profile_fieldset'>
		<form id="signupform" autocomplete="off" method="post" enctype="multipart/form-data" action="<?php echo $module->moduleRoot?>/manage.do">
	    	<hr class="space">

			<?php echo manageAttribute($userAttr)?>
			
	    	<hr class="space">
	    	<hr class="space">
	    	
	    	<div class="span-10" align="center">
	    		<input class="profile_input" type="submit" value="Submit" name="submit_button">
    		</div>
    		
	    	<hr class="space">
    	</form>
		</fieldset>
		
		<!--	Form for uploading user profile image	-->
		<fieldset class='profile_fieldset'>
		<form id="signupform" autocomplete="off" method="post" enctype="multipart/form-data" action="<?php echo $module->moduleRoot?>/newProfileImage.do">
			<h3>Upload profile image for <?php echo $userAttr[ App::Get()->settings['firstname_attr'] ]  . " " . $userAttr[ App::Get()->settings['lastname_attr'] ]?> </h3>

	    	<hr class="space">
   			<div class="span-3 prepend-1">
				<label>Upload Photo:</label>
			</div>
			<div class="span-12">
				<input class="profile_input" type="file" name="document"/>
			</div>
	    	<hr class="space">
	    	<div class="span-10" align="center">
    			<input class="profile_input" type="submit" value="Submit" name="submit_button">
    		</div>
		</form>	
		</fieldset>
		
		<!--	Form for uploading user profile image	-->
		<fieldset class='profile_fieldset'>
		<form id="signupform" autocomplete="off" method="post" enctype="multipart/form-data" action="<?php echo $module->moduleRoot?>/updateProfileBio.do">
			<h3>Edit profile:</h3>
			<input type="hidden" name="bio_path" value="<?php echo $path?>"/>

	    	<hr class="space">
			<div class="span-12">
				<em>Note:</em>  You can use <a href="http://michelf.com/projects/php-markdown/syntax/" target="_new">Markdown syntax</a>. <br/>
				<textarea class="profile_text" type="text" name="bio"><?php echo $contents?></textarea>
			</div>
	    	<hr class="space">
	    	<div class="span-10" align="center">
    			<input class="profile_input" type="submit" value="Save" name="submit_button">
    		</div>
		</form>	
		</fieldset>
    </div>
