<?php
	/*	WHEEL > Core > Render
	//  ---------------------
	//
	//	Initialise mustache
	*/

	include('./lib/Mustache/Autoloader.php');

	Mustache_Autoloader::register();

	$_['mustache'] = new Mustache_Engine(array(
    		'loader' => new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/../views/'),
		));

?>