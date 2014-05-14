<?php
	/*	WHEEL > Core > Debug
	//  --------------------
	//
	//	Activate PHP error reports when DEBUG is activated, or desactivates it.
	*/
	
	
	// If the config says DEBUG ON :
	if($_['config']['core']['debug']){
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		echo '<!-- INITIAL $_ : ';
		var_dump($_);
		echo " -->";
	}
	
	// If the config says DEBUG OFF, or don't say anything :
	else{
		error_reporting(0);
		ini_set('display_errors', '0');
	}
?>