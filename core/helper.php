<?php
	/*	WHEEL > Core > Helper
	//  ---------------------
	//
	//	Helpers and i18n
	*/

	class wheel_helper {
		private $_i18n;

		public function wheelurl($text = ''){
			if (isset($_GET['debug'])){
				if(strcontains($text, '?'))
					$debug = '&debug';
				else
					$debug = '?debug';
			}else
				$debug = '';
			if(startWith($text,'/'))
				return substr($_SERVER['PHP_SELF'],0,-14).$text.$debug;
			return substr($_SERVER['PHP_SELF'],0,-13).$text.$debug;
		}

		public function i18n($text = '', $lang = null){
			if(!isset($_i18n))
				$this->_i18n = new wheel_i18n($lang);
			return $this->_i18n->translate($text);
		}

	}

	class wheel_i18n{
		private $_file;

		public function __construct($lang = null){
			global $_;

			if(!empty($lang)){
				if(is_file('../views/langs/'.$lang.'.yml')){
					$this->_file = Spyc::YAMLLoad('../views/langs/'.$lang.'.yml');
					if(empty($this->_file))
						$_["error"]->error("WHEEL > I18N : The file '$lang.yml' is corrupted.");
					else
						$_["error"]->log("WHEEL > I18N : File '$lang.yml' loaded.");
				}
				return true;
			}
			foreach($this->_detectLangs() as $lang){
				if(is_file('../views/langs/'.$lang.'.yml')){
					$this->_file = Spyc::YAMLLoad('./views/langs/'.$lang.'.yml');
					if(empty($this->_file))
						$_["error"]->error("WHEEL > I18N : The file '$lang.yml' is corrupted.");
					else
						$_["error"]->log("WHEEL > I18N : File '$lang.yml' loaded.");
				}
			}
		}

		private function _detectLangs(){
			global $_;

			$langs = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
			preg_match_all('/(\W|^)([a-z]{2})([^a-z]|$)/six', $langs, $m, PREG_PATTERN_ORDER);
			$langs = $m[2];
			$langs[] = $_['config']['render']['default_i18n'];
			$langs = array_unique($langs);
			$_["error"]->log("WHEEL > I18N : Client languages : ".implode(', ', $langs));
			return $langs;
		}

		public function translate($text){
			if(!empty($this->_file[$text]))
				return $this->_file[$text];
			else
				return $text;
		}
	}

	$_['helper'] = new wheel_helper;

?>