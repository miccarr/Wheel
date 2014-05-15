<?php
	/*	WHEEL > Core > Twig
	//  --------------------
	//
	//	Twig loader
	*/

	include('./lib/Twig/Autoloader.php');
	Twig_Autoloader::register(true);

	$_['twig']['loader'] = new Twig_Loader_Filesystem('views/layouts');
	$_['twig']['env'] = new Twig_Environment($loader, array('cache' => 'views/cache'));