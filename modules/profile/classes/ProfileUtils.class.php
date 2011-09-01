<?php
/**
 * Profile Utilities Class
 * 
 * This class provides methods for accessing and manipulating individual
 * elements of a user's profile. The class is designed to facilitate the
 * integration of profile information into different areas of the application.
 * 
 * @author ahart
 */
class ProfileUtils {
	
	protected $moduleName = 'profile';
	
	protected $moduleContext = null;
	
	/**
	 * Constructor
	 * 
	 * @param string $moduleName - The name of the root directory of the profile module.
	 *               This is necessary for correct path generation to module assets, etc.
	 */
	public function __construct($moduleName = 'profile') {
		
		// Store the provided module name
		$this->moduleName = $moduleName;
		
		// Load the profile module config
		$this->moduleContext = App::Get()->loadModule($moduleName);
	}
	
	/**
	 * Get the filesystem path for the user's profile photo
	 * 
	 * @param string $id - the unique profile id
	 */
	public function getPhotoPathFor($id) {
		
		$dataDir = App::Get()->settings['profile_data_dir'];
		$path    = "{$dataDir}/{$id}/{$id}";
		if (is_file("{$path}.jpg")) { return "{$path}.jpg"; }
		if (is_file("{$path}.gif")) { return "{$path}.gif"; }
		if (is_file("{$path}.png")) { return "{$path}.png"; }
		
		return false;
	}
	
	/**
	* Get the fully qualified URL for the user's profile photo
	*
	* @param string $id - the unique profile id
	*/
	public function getPhotoUrlFor($id) {
		
		$dataDir = App::Get()->settings['profile_data_dir'];
		$path    = "{$dataDir}/{$id}/{$id}";
		if (is_file("{$path}.jpg")) { $ext = "jpg"; }
		if (is_file("{$path}.gif")) { $ext = "gif"; }
		if (is_file("{$path}.png")) { $ext = "png"; }
		
		if (!$ext) { return false; }
		
		return $this->moduleContext->moduleStatic . "/data/{$id}/{$id}.{$ext}";
	}
	
	/**
	 * Get biographical/profile text for the user
	 * 
	 * @param string $id - the unique profile id
	 * @param boolean    - whether or not to apply markdown formatting (default = false)
	 */
	public function getBioFor($id, $formatted = false) {
		
		$dataDir = App::Get()->settings['profile_data_dir'];
		$path    = "{$dataDir}/{$id}/{$id}.txt";
		
		if (is_file($path)) {
			return ($formatted)
				? markdown(file_get_contents($path))
				: file_get_contents($path);
		}
	}
	
	/**
	 * Get an array representation of a user's profile data
	 * 
	 * @param string $id - the unique profile id
	 */
	public function getProfileFor($id) {
		
		$dataDir = App::Get()->settings['profile_data_dir'];
		if (is_dir("{$dataDir}/{$id}")) {
			
			// Obtain data from the auth provider for this username
			$udata = App::Get()->getAuthenticationProvider()
				->retrieveUserAttributes($id,App::Get()->settings['profile_attributes']);
			
			// Obtain profile data for this username
			$udata['profile'] = @file_get_contents($dataDir . "/{$id}/{$id}.txt");
			
			// Obtain the url to the photo for this username
			$udata['photo']   = $this->getPhotoUrlFor($id);
			
			return $udata;
			
		} else {
			return false;
		}
	}
	
	/**
	 * Get an array representation of all users who have profile data
	 * @return array
	 */
	public function getProfiles() {
		
		$accounts = array();
		
		// Determine which accounts have profile data
		$dataDir = App::Get()->settings['profile_data_dir'];
		$dir = dir($dataDir);
		while (false !== ($entry = $dir->read())) {
			if ('.' == $entry || '..' == $entry) { continue; }
			
			// Obtain data from the auth provider for this username
			$udata = App::Get()->getAuthenticationProvider()
				->retrieveUserAttributes($entry,App::Get()->settings['profile_attributes']);
			
			// Obtain profile data for this username
			$udata['profile'] = @file_get_contents($dataDir . "/{$entry}/{$entry}.txt");
			
			// Obtain the url to the photo for this username
			$udata['photo']   = $this->getPhotoUrlFor($entry);
			
			// Add to the list of accounts
			$accounts[$entry] = $udata;	
		}
		$dir->close();
		
		return $accounts;
	}
}