<?php													$WHEEL['version'] = '14.05.17';
	/*
		WHEEL : Light php framework					By MicCarr 	< miccarr@me.com >
		-------------------------------
		Do what the fuck you want with this, just keep this comment intact ;-)
	*/
	

	// Loading the WHEEL core
	include('./core/easyFunctions.php');
	include('./core/config.php');
	include('./core/debug.php');
	include('./core/error.php');
	include('./core/router.php');
	include('./core/database.php');
	include('./core/models.php');
	include('./core/controllers.php');
	include('./core/session.php');
	include('./core/helper.php');
	include('./core/render.php');
	

	$_['stdOut'] = wheel_Router::route( $_SERVER['REQUEST_URI'] );
	ob_end_clean();

	if($_['config']['core']['debug']){
		$_['error']->showErrors();
	}

	if(!empty($_['controller']->render))
		echo $_['controller']->render;
	else
		echo $_['controller']->min($_['stdOut']);
?>