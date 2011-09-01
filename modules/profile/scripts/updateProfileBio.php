<?php
$module = App::Get()->loadModule();
$username = App::Get()->getAuthenticationProvider()->getCurrentUsername();

// Upload the user profile photo, if one was provided
if (!empty($_POST['bio'])) {
		$dataDir = App::Get()->settings['profile_data_dir'] . '/' . $username;
		if ( !is_dir($dataDir) ) {
			mkdir($dataDir);
		}
		file_put_contents($_POST['bio_path'],$_POST['bio']);
}

App::Get()->Redirect($module->moduleRoot . "/view/" . $username );
