<?php
	/*	WHEEL > Core > Config
	//  ---------------------
	//
	//	Loads all the YAML files from the config directory to the $_['config'] variable.
	*/
	
	require_once('../lib/spyc.php');
	
	// Listing all the files in the /config directory
	$configDirectory = scandir('../config/');
	
	foreach($configDirectory as $configFile){
	
		// If *.YML extension file
		if(substr($configFile, -4) == ".yml"){
			
			// Select the filename without .YML to define the varname
			$configSection = substr($configFile, 0, -4);
			
			// Parse all files to the $_['config'] variable
			$_['config'][$configSection] = Spyc::YAMLLoad('../config/'.$configFile);
		}
	}
?>