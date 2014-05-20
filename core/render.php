<?php
	/*	WHEEL > Core > Render
	//  ---------------------
	//
	//	Initialise mustache
	*/


	// Include and load
	include('../lib/Mustache/Autoloader.php');
	Mustache_Autoloader::register();

	// Setting options
	$_['mustache']['loader'] = new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/../views/', array('extension'=>'.html'));
	if($_['config']['render']['cache'])
		$_['mustache']['cache'] = dirname(__FILE__) . '/../views/cache';

	// Instancy
	$_['mustache'] = new Mustache_Engine($_['mustache']);


	// Add the helpers
	$_['mustache']->addHelper('WHEELURL', function($text ='') {
		global $_;
		return $_['helper']->wheelurl($text);
	});

	// Add the helpers
	$_['mustache']->addHelper('i18n', function($text ='') {
		global $_;
		return $_['helper']->i18n($text);
	});

?>