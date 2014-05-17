<?php
	/*	WHEEL > Core > Render
	//  ---------------------
	//
	//	Initialise mustache
	*/


	// Include and load
	include('./lib/Mustache/Autoloader.php');
	Mustache_Autoloader::register();

	// Setting options
	$_['mustache']['loader'] = new Mustache_Loader_FilesystemLoader(dirname(__FILE__) . '/../views/', array('extension'=>'.html'));
	if($_['config']['render']['cache'])
		$_['mustache']['cache'] = dirname(__FILE__) . '/../views/cache';

	// Instancy
	$_['mustache'] = new Mustache_Engine($_['mustache']);


	// Add the helpers
	$_['mustache']->addHelper('WHEELURL', function($text ='', $mustache = null) {
		if (isset($_GET['debug'])){
			if(strcontains($text, '?') AND )
				$debug = '&debug';
			else
				$debug = '?debug';
		}else
			$debug = '';
		if(startWith($text,'/'))
			return substr($_SERVER['PHP_SELF'],0,-10).$text.$debug;
		return substr($_SERVER['PHP_SELF'],0,-9).$text.$debug;
	});

?>