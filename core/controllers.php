<?php
	/*	WHEEL > Core > Controllers
	//  --------------------------
	//
	//	Define the default controllers methods and properties.
	*/

	class Controllers {
		// Desactive the controller if not in DEBUG mode
		public $debugOnly = false;

		// Access to the global
		private $_;

		function __construct($_){
			$this->_ = $_;
			$this->log->log("WHEEL : Constructing the controller '".get_class($this)."'");
		}

		function __get($varName){
			if(isset($this->_[$varName]))
				return $this->_[$varName];
			else
				$_["error"]->error("WHEEL : Tryed to call inexistant variable (\$this->'$varName').")
		}
	}
?>