<?php
	if ( defined( 'API_Util_IP' ) ) { return ; }	
	define( 'API_Util_IP', true ) ;

	FUNCTION Util_IP_GetIP( $token )
	{
		$cookie_vid = ( isset( $_COOKIE["phplive_vid"] ) && $_COOKIE["phplive_vid"] ) ? $_COOKIE["phplive_vid"] : "" ;
		global $VARS_IP_CAPTURE ; $ip = "0.0.0.0" ;
		for ( $c = 0; $c < count( $VARS_IP_CAPTURE ); ++$c )
		{
			$env_var = $VARS_IP_CAPTURE[$c] ;
			if ( isset( $_SERVER[$env_var] ) && $_SERVER[$env_var] ) {
				if ( $env_var == "HTTP_X_FORWARDED_FOR" ) {
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ;
					if ( preg_match( "/,/", $ip ) ) { LIST( $ip, $ip_ ) = explode( ",", preg_replace( "/ +/", "", $ip ) ) ; }
				} else { $ip = $_SERVER[$env_var] ; } break 1 ;
			}
		} if ( !$token ) { $token = $ip ; }
		$http_agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : $ip ;
		$http_token = "$ip$token$http_agent$cookie_vid" ;
		$ip_output = Array( $ip, md5($http_token) ) ; return $ip_output ;
	}
?>