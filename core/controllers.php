<?php
	/*	WHEEL > Core > Controllers
	//  ---------------------
	//
	//	Define the default controllers methods and properties.
	*/

	class Controllers {
		// Desactive the controller if not in DEBUG mode
		public $debugOnly = false;

		function __get($varName){
			global $_;
			if(isset($_[$varName]))
				return $_[$varName];
			else
				$_["error"]->error("WHEEL : Tryed to call inexistant wheel variable ('$varName').")
		}
	}
?>