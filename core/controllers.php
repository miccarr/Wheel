<?php
	/*	WHEEL > Core > Controllers
	//  --------------------------
	//
	//	Define the default controllers methods and properties.
	*/

	class Controllers {
		// Desactive the controller if not in DEBUG mode
		public $debugOnly = false;
		public $render = null;
		private $_view = null;
		private $_layout = 'default';

		public function __construct($action){
			$this->error->log("WHEEL : Constructing the controller '".get_class($this)."'");
			$this->_view = get_class($this).'/'.$action;
		}

		public function __get($varName){
			global $_;
			if(isset($_[$varName]))
				return $_[$varName];
			else
				$this->error->error("WHEEL : Tryed to call inexistant property (\$this->'$varName').");
		}

		public function render( $data = array(), $view = null, $layout = null){
			global $_;

			$data = rawData($data);

			if(empty($view))
				$view = $this->_view;

			if($layout==null)
				$layout = $this->_layout;

			$this->render = $_['mustache']->render($view, $data);

			if(!empty($layout)){
				$data = array_merge($data, array('body'=>$this->render));
				$this->render = $_['mustache']->render('layouts/'.$layout, $data);
			}
		}
	}
?>