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

			return $dest;
		}
		
		// Translate request to controller / action
		static private function req2ca($request){
			global $_;
			$output = false;
			
			foreach( $_['config']['routes'] as $route){
				$regex = self::p2regex($route['path']);
				if(preg_match($regex, $request, $m)){
					return self::match2ca($route, $m);
					break;
				}
				return false;
			}
		}
		
		// Route to the good controller / action
		static public function route($request){
			
		}
	}
?>