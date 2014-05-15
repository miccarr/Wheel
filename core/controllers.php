<?php
	/*	WHEEL > Core > Controllers
	//  --------------------------
	//
	//	Define the default controllers methods and properties.
	*/

	class Controllers {
		// Desactive the controller if not in DEBUG mode
		public $debugOnly = false;

		function __construct(){
			$this->error->log("WHEEL : Constructing the controller '".get_class($this)."'");
		}

		function __get($varName){
			global $_;
			if(isset($_[$varName]))
				return $_[$varName];
			else
				$this->error->error("WHEEL : Tryed to call inexistant variable (\$this->'$varName').");
		}
	}
?>