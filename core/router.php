<?php
	/*	WHEEL > Core > Router
	//  ---------------------
	//
	//	Route the path expression to the good controller / action.
	*/
	
	class wheel_Router{
		
		// Transform the path definition to regular expression
		static private function p2regex($path){
			$path = str_replace('[', '(', $path);
			$path = str_replace(']', ')?', $path);
			$path = str_replace('/', '\/', $path);
			//$path = preg_replace('#\{([\w\:\w]+)\}#', '(?<$1>[$2-]+)', $path);
			$path = preg_replace('#\{(\w+)\}#', '(?<$1>[\\w-]+)', $path);
			return '#^'.$path.'#';
		}
		
		// Translate match to controller / action
		static private function match2ca($route, $m){
			foreach($route as $key => $value){
				foreach ($m as $k => $v){
					$route[$key] = str_replace('{'.$k.'}', $v, $value);
				}
			}

			return $route;
		}
		
		// Translate request to controller / action
		static private function req2ca($request){
			global $_;
			$output = false;
			
			foreach( $_['config']['routes'] as $path => $route){
				if($path != 'fallback'){
					$regex = self::p2regex($path);
					if(preg_match($regex, $request, $m)){
						return self::match2ca($route, $m);
						break;
					}
					return false;
				}
			}

			return $_['config']['routes']['fallback'];
		}
		
		// Route to the good controller / action
		static public function route($request){
			global $_;

			$route = self::req2ca( $request );

			// Get the controller name
			$controller = empty($route['controller']) ? $_['config']['routes']['fallback']['controller'] : $route['controller'];

			$action = empty($route['controller']) ? $_['config']['routes']['fallback']['action'] : $route['action'];

			$options = $route;	// Transfer the options from the route

			// Try with controllerSuffix (.php , .inc.php and .class.php)
			if(is_file('./controllers/'.$controller.$_['config']['core']['controllerSuffix'].'.php')){
				include('./controllers/'.$controller.$_['config']['core']['controllerSuffix'].'.php');
			}
			// Try without controllerSuffix
			elseif(is_file('./controllers/'.$controller.'.php')){
				include('./controllers/'.$controller.'.php');
			}
			// If controller file not foud
			else{
				$_['error']->fatal("WHEEL : Controller file '".$controller.".php' not found.");
			}

			// Catch output
			ob_start();

			// Check and load the controller
			if( class_exists($controller.$_['config']['core']['controllerSuffix']) ){
				$controller.=$_['config']['core']['controllerSuffix'];
			}elseif( class_exists($controller.'Ctrl') ){
				$controller.='Ctrl';
			}elseif( class_exists($controller.'Controller') ){
				$controller.='Controller';
			}elseif( class_exists($controller) ){
				$_['error']->fatal("WHEEL : Please add the suffix '".$_['config']['core']['controllerSuffix']."' to your controller.");
			}else{
				$_['error']->fatal("WHEEL : Controller file loaded, but can't find '".$controller.$_['config']['core']['controllerSuffix']."' class.");
			}

			$_['controller'] = new $controller();

			if(isset($_['controller'])){
				// Call action
				if(method_exists($_['controller'], $action)){
					$_['controller']->$action($options);
				}else{
					$_['controller'] = $_['config']['routes']['fallback']['controller'];
					$_['controller']->$_['config']['routes']['fallback']['action']($options);
				}
			}
			return ob_get_contents();
		}
	}
?>