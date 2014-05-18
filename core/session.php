<?php
	/*	WHEEL > Core > Session
	//  ----------------------
	//
	//	Simple way to get / set SESSION vars
	*/
	
	class wheel_Session {
		public function __construct(){
			global $_;
			if($_['config']['core']['sessionAutoStart']){
				$this->start();
			}
		}

		public function start($oid = null){
			if (session_status() == PHP_SESSION_NONE) {
				session_start($oid);
			}
		}

		public function destroy($oid = null){
			if (session_status() != PHP_SESSION_NONE) {
				session_destroy($oid);
			}
		}

		public function __get($varName){
			global $_;
			if(isset($_SESSION[$varName]))
				return $_SESSION[$varName];
			else{
				$_["error"]->info("WHEEL > Session : Tryed to call inexistant session variable('$varName'), returned false .");
				return false;
			}
		}

		public function __set($varName, $varValue){
			$_SESSION[$varName] = $varValue;
		}
	}

	$_['session'] = new wheel_Session;
	$_['session']->start();
?>