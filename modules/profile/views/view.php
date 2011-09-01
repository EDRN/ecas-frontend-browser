<?
/*
 * User Profile:
 * Lists all the information available about specified user
 * URL must be:
 * 		/view/{username} 
 */
$module = App::Get()->loadModule();
$uri = App::Get()->request->uri;
$segment = explode("/", $uri);
$lastIndex = count($segment) - 1;
$username = $segment[$lastIndex];
 
function displayAttributes($userAttr) {

	$str = '';
 	foreach ($userAttr as $key=>$keyValue) {
 		foreach (App::Get()->settings['attr_titles'] as $attrTitle=>$value) {
 			if ( $key === $value) {
 				$str .= '<div class="span-13 prepend-1"><h4 align="left">';
 				$str .= $attrTitle;
 				$str .= '</h4></div>';
 				
 				$str .= '<div class="span-5"><h6 align="left">';
 				$str .= $keyValue;
 				$str .= '</h6></div>';
 			}
 		}
 	}
 	return $str;	
}

// Get user attributes
$userAttr = App::Get()->getAuthenticationProvider()->retrieveUserAttributes( 
			$username, App::Get()->settings['profile_attributes'] );

$dataDir = App::Get()->settings['profile_data_dir'] . '/' . $username . '/' . $username;
$file = null;
if ( is_file($dataDir.'.jpg')) {
	$file = $username . '/' . $username.'.jpg';
} elseif (is_file($dataDir.'.jpeg')) {
	$file = $username . '/' . $username.'.jpeg';
} elseif (is_file($dataDir.'.gif')) {
	$file = $username . '/' . $username.'.gif';
} elseif (is_file($dataDir.'.png')) {
	$file = $username . '/' . $username.'.png';
} 

$path     = $dataDir . '.txt';
if ( file_exists( $path ) ) {
	require_once($module->modulePath . '/classes/markdown.php');
	$contents = Markdown(file_get_contents( $path )); 
}

// Prepare BreadcrumbsWigdet
$bcw = new BreadcrumbsWidget();
$bcw->add('Home', SITE_ROOT . '/');
$bcw->add('Profile');
?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#profile_fieldset').corner();
		});
	</script>
	
	<div class='span-22 append-1 prepend-1 last' id='profile_container'>
		<div class='span-16'>
			<h2>Profile: <?php echo $userAttr[ App::Get()->settings['firstname_attr'] ]  . " " . $userAttr[ App::Get()->settings['lastname_attr'] ]?> </h2>
		</div>
		<?php if ( isset($file) ) :?>
		<div class='span-4' id="user_image">
			<img alt="user photo" 
				src="<?php echo $module->moduleStatic?>/data/<?php echo $file?>">
		</div>
		<hr class="space">
		<?php endif; ?>
 		<fieldset id='profile_fieldset'>
 			<hr class="space">
 			<hr class="space">
			<?php 	echo displayAttributes($userAttr) ?>
<!--			<div class="span-13 prepend-1"><h4 align="left">-->
<!--			<a href="<?php echo $module->moduleRoot?>/groups">Groups</a>-->
<!--			</h4></div>-->
			<hr class="space">
			<hr class="space">
			<div class="span-19 prepend-1">
			<h4 align="center">About</h4>
			<h6><?php echo $contents?></h6>
			</div>
		</fieldset>
		
    	<hr class="space">
    	
	</div>
	
