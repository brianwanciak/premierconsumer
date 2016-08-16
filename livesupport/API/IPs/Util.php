<?php
	if ( defined( 'API_IPs_Util' ) ) { return ; }	
	define( 'API_IPs_Util', true ) ;

	function UtilIPs_IP2Long( $ip )
	{
		if ( $ip == "" ) {
			return 0 ;
		} else {
			$ips = explode( ".", $ip ) ;
			if ( !isset( $ips[0] ) ) { $ips[0] = 0 ; }
			return Array( ip2long( $ip ), $ips[0] ) ;
		}
	}

	function UtilIPs_Long2IP( $digit )
	{
		if ( $digit == "" ) {
			return 0 ;
		} else {
			$ip = long2ip( $digit ) ;
			$ips = explode(".", $ip) ;
			$net = isset( $ips[0] ) ? $ips[0] : 0 ;
			return Array( $ip, $net ) ;
		}
	}
?>