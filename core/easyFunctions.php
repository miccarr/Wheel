<?php
	/*	WHEEL > Core > EasyFunctions
	//  ----------------------------
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
	function is_serialized( $data ) {
	    if ( !is_string( $data ) )
	        return false;
	    $data = trim( $data );
	    if ( 'N;' == $data )
	        return true;
	    if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
	        return false;
	    switch ( $badions[1] ) {
	        case 'a' :
	        case 'O' :
	        case 's' :
	            if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
	                return true;
	            break;
	        case 'b' :
	        case 'i' :
	        case 'd' :
	            if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
	                return true;
	            break;
	    }
	    return false;
	}
	function e($text){
		return htmlspecialchars($text);
	}

?>