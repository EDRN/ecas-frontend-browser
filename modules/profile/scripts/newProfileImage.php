<?php
$module = App::Get()->loadModule();
require_once( $module->modulePath . '/classes/Image.class.php' );

// Upload the user profile photo, if one was provided
if ( !empty($_FILES['document']) ) {
	if ( is_uploaded_file($_FILES['document']['tmp_name']) ) {

		$loadedFile = $_FILES['document']['tmp_name'];
		
		// Get user information
		$username = App::Get()->getAuthenticationProvider()->getCurrentUsername();
		$dataDir = App::Get()->settings['profile_data_dir'] . '/' . $username;
		if ( !is_dir($dataDir) ) {
			mkdir( $dataDir );
		}

		// Check to see if image already exists for user. If so, delete.
		$dataDir = App::Get()->settings['profile_data_dir'] . '/' . $username . '/' . $username;
		$file = null;
		if ( is_file($dataDir.'.jpg') ) {
			$file = $dataDir . '.jpg';
		} elseif ( is_file($dataDir.'.jpeg') ) {
			$file = $dataDir . '.jpeg';
		} elseif ( is_file($dataDir.'.gif') ) {
			$file = $dataDir . '.gif';
		} elseif ( is_file($dataDir.'.png') ) {
			$file = $dataDir . '.png';
		}
		if ( isset($file) ) {
			unlink($file);
		}

		// Get new image type
		$mimeType 	= mime_content_type($loadedFile);
		$typeArray 	= explode( "/", $mimeType );
		$dataDir 	= App::Get()->settings['profile_data_dir'] . '/' . $username . '/' . $username;
		if ( $typeArray[0] == 'image' ) {
			$imageType 	= $typeArray[1];
			$fileType 	= '.' . $typeArray[1];
		}
		$max_width = 100;
		$max_height = 100;
		list( $img_w, $img_h ) = getimagesize( $loadedFile );

		$image = new Image();
   		$image->load( $loadedFile );
		if ( $img_w > $max_width || $img_h > $max_height ) {
			if ( $img_w > $max_width ) {
				
				// If width is larger then max resize to fit max width
				$image->resizeToWidth($max_width);
			} else if ( $img_h > $max_height ) {
				
				// If height is larger then max resize to fit max height
				$image->resizeToHeight($max_height);
			} 
		}
		// Save image
		$image->save($dataDir . $fileType);

	} else { die('error uploading file'); }
}

App::Get()->Redirect($module->moduleRoot . "/view/" . $username );
