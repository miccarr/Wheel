<?php
	/*	WHEEL > Core > Helper
	//  ---------------------
	//
	//	Helper for html and links
	*/

	class wheel_helper {

		public function url( $request ){
			if(startWith($request, '/'))
				return substr($_SERVER['PHP_SELF'],0,-10).$request;
			else
				return $request;
		}

		public function img( $url, $alt = '' ){
			return '<img src="'.$url.'" alt="'.$alt.'" />';
		}

		public function link($url, $text = null, $title = ''){
			$out = '<a href="'.$this->url($url).'" title="'.$title.'">';
			if(!empty($text))
				$out.= $text.'</a>';
			return $out;
		}

	}

	$_['helper'] = new wheel_helper;

?>