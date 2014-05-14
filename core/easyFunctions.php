<?php
	/*	WHEEL > Core > EasyFunctions
	//  ----------------------
	//
	//	add some easy functions to PHP ;)
	*/

	function startWith($haystack, $needle){
    	return $needle === "" || strpos($haystack, $needle) === 0;
	}
	function endWith($haystack, $needle){
	    return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
	}
	function strcontains($haystack, $needle){
		return strpos($haystack, $needle) !== false;
	}

?>