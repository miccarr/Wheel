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
		protected $_view = null;
		protected $_layout = 'default';

		public function __construct($action){
			$this->error->log("WHEEL > CTRL : Constructing the controller '".get_class($this)."'");
			if(empty($this->_view))
				$this->_view = get_class($this).'/'.$action;
		}

		public function __get($varName){
			global $_;
			if(isset($_[$varName]))
				return $_[$varName];
			else
				$this->error->error("WHEEL > CTRL : Tryed to call inexistant property (\$this->'$varName').");
		}

		public function render( $data = array(), $view = null, $layout = null){
			$data = rawData($data);

			if(empty($view))
				$view = $this->_view;

			if($layout==null)
				$layout = $this->_layout;

			// Apply template
			if(is_file('../views/'.$view.'.html')){
				$this->error->log("WHEEL > Render : Rendering with view '".$view."'");
				$this->render = $this->mustache->render($view, $data);
			}else{
				$this->error->error("WHEEL > Render : View '".$view.".html' not found.");
			}
			
			// If layout refer to a file
			if(!empty($layout) AND is_file('../views/layouts/'.$layout.'.html')){
				$this->error->log("WHEEL > Render : Rendering layout '".$layout."'");
				$data = array_merge($data, array('body'=>$this->render));
				$this->render = $this->mustache->render('layouts/'.$layout, $data);
			}

			// Minify render
			if(!$this->config['core']['debug'])
				$this->render = $this->min($this->render);
		}

		public function min($html){
			// If config to false, just return
			if(!$this->config['render']['minify'])
				return $html;

			$this->error->log("WHEEL > Render : Minify");
			$html = str_replace("\t", '', $html);
			$html = str_replace("\n", '', $html);
			return $html;
		}
	}
?>