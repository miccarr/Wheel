<?php
	/*	WHEEL > Core > Router
	//  ---------------------
	//
	//	Route the path expression to the good controller / action.
	*/
	
	class wheel_Router{
		
		// Transform the path definition to regular expression
		static private function p2regex($path){

			$start = '#';
			// If not start with / may have params before
			if(startWith($path,'/'))
				$start.= '^';
			elseif(startWith($path,'#'))
				return $path;

			$path = str_replace('[', '(', $path);
			$path = str_replace(']', ')?', $path);
			$path = str_replace('/', '\/', $path);
			//$path = preg_replace('#\{([\w\:\w]+)\}#', '(?<$1>[$2-]+)', $path);
			$path = preg_replace('#\{(\w+)\}#', '(?<$1>[\\w-]+)', $path);

			// If end with ... , can have params after
			if ( endWith($path, '...') ){
				return $start. substr($path, 0, -3) .'#';
			}elseif ( endWith($path, '(...)?') ){
				return $start. substr($path, 0, -6) .'#';
			}elseif ( endWith($path, '...)?') ){
				return $start. substr($path, 0, -5) .')?#';
			}
				
			return $start.$path.'$#';
		}
		
		// Translate match to controller / action
		static private function match2ca($route, $m){
			foreach ($m as $varName => $value){
				if (!is_numeric($varName)){
					foreach($route as $key => $val){
						$route[$key] = str_replace('{'.$varName.'}', $value, $val);
					}
				}
			}

			return $route;
		}
		
		// Translate request to controller / action
		static private function req2ca($request){
			global $_;
			
			foreach( $_['config']['routes'] as $path => $route){
				if($path != 'fallback'){
					$regex = self::p2regex($path);
					if(preg_match($regex, $request, $m)){
						$_["error"]->log("WHEEL > Router : Find route '".$path."' ('".$regex."') for this request.");
						return self::match2ca($route, $m);
					}
				}
			}

			return $_['config']['routes']['fallback'];
		}

		static private function includeController($controller){
			global $_;
			$ctrlSuffix = $_['config']['core']['controllerSuffix'];

			// Try with controllerSuffix (.php , .inc.php and .class.php)
			if(is_file('../controllers/'.$controller.$ctrlSuffix.'.php')){
				include('../controllers/'.$controller.$ctrlSuffix.'.php');
			}
			// Try without controllerSuffix
			elseif(is_file('../controllers/'.$controller.'.php')){
				include('../controllers/'.$controller.'.php');
			}
			// If controller file not foud
			else{
				$_['error']->error("WHEEL : Controller file '".$controller."(".$ctrlSuffix.").php' not found.");
				$controller = $_['config']['routes']['fallback']['controller'];
				if(is_file('../controllers/'.$controller.'.php')){
					include('../controllers/'.$controller.'.php');
				}elseif(is_file('../controllers/'.$controller.$ctrlSuffix.'.php')){
					include('../controllers/'.$controller.$ctrlSuffix.'.php');
				}else{
					$_['error']->fatal("WHEEL > Router : Controller file and fallback '".$controller."(".$ctrlSuffix.").php' not found.");
				}
			}
		}
		
		// Route to the good controller / action
		static public function route($request){
			global $_;

			// Removing GET vars
			if(strcontains($request, '?'))
				list($request, $gets) = explode('?', $request, 2);

			// Removing base directories
			$base = substr($_SERVER['PHP_SELF'],0,-14);
			$request = substr($request, strlen($base));

			$_["error"]->log("WHEEL > Router : Request route for '".$request."'");

			// Catch output
			ob_start();

			$route = self::req2ca( $request );

			if(!empty($route['sass'])){
				self::sass($route['sass']);
			}else{

				// Get the controller name
				$controller = empty($route['controller']) ? $_['config']['routes']['fallback']['controller'] : $route['controller'];
				

				$action = empty($route['controller']) ? $_['config']['routes']['fallback']['action'] : $route['action'];

				$options = $route;	// Transfer the options from the route

				self::includeController($controller);

				// Check and load the controller
				if( class_exists($controller.$_['config']['core']['controllerSuffix']) ){
					$controller.=$_['config']['core']['controllerSuffix'];
				}elseif( class_exists($controller.'Ctrl') ){
					$controller.='Ctrl';
				}elseif( class_exists($controller.'Controller') ){
					$controller.='Controller';
				}elseif( class_exists($controller) ){
					$_['error']->fatal("WHEEL > Router : Please add the suffix '".$_['config']['core']['controllerSuffix']."' to your controller.");
				}else{
					$_['error']->fatal("WHEEL > Router : Controller file loaded, but can't find '".$controller.$_['config']['core']['controllerSuffix']."' class.");
				}

				$_['controller'] = new $controller($action);

				if(isset($_['controller'])){
					// Call action
					if(method_exists($_['controller'], $action)){
						$_['error']->log("WHEEL > CTRL : Call action '$action'.");
						$_['controller']->$action($options);
					}elseif(method_exists($_['controller'], '__call')){
						$_['error']->log("WHEEL > CTRL : Call action '$action' via the magic __call method.");
						$_['controller']->$action($options);
					}else{
						$_['error']->error("WHEEL > CTRL : Action not found, calling fallback.");
						$_['controller'] = $_['config']['routes']['fallback']['controller'];
						if(method_exists($_['controller'], $_['config']['routes']['fallback']['action']))
							$_['controller']->$_['config']['routes']['fallback']['action']($options);
						else
							$_['error']->fatal("WHEEL > CTRL : Fallback action not found.");
					}
				}
			}

			return ob_get_contents();
		}

		static private function sass($file){
			include('../lib/scss.php');
			$sass = new scss_server('../views/styles/', '../views/cache/');
			$sass->serve('', '../views/styles/'.$file);
		}
	}
?>