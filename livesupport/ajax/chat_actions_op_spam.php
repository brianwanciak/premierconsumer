<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else if ( $action == "spam_check" )
	{
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		if ( preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"], $matches ) && isset( $matches[0] ) )
			$exist = 1 ;
		else
			$exist = 0 ;

		$json_data = "json_data = { \"status\": 1, \"exist\": $exist }; " ;
	}
	else if ( $action == "spam_block" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;

		$flag = Util_Format_Sanatize( Util_Format_GetVar( "flag" ), "n" ) ;
		$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

		LIST( $ip_, $vis_token ) = Util_IP_GetIP( "" ) ;

		if ( $ip == $ip_ ) { $json_data = "json_data = { \"status\": 0, \"error\": \"Cannot block your own IP.\" }; " ; }
		else if ( !preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) && $flag )
		{
			$val = preg_replace( "/ +/", "", $VALS["CHAT_SPAM_IPS"] ) . "-$ip" ;
			$val = preg_replace( "/--/", "-", $val ) ;
			Util_Vals_WriteToFile( "CHAT_SPAM_IPS", $val ) ;
			$json_data = "json_data = { \"status\": 1 }; " ;
		}
		else if ( preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) && !$flag )
		{
			$val = preg_replace( "/$ip/", "", preg_replace( "/ +/", "", $VALS["CHAT_SPAM_IPS"] ) ) ;
			Util_Vals_WriteToFile( "CHAT_SPAM_IPS", $val ) ;
			$json_data = "json_data = { \"status\": 1 }; " ;
		}
		else { $json_data = "json_data = { \"status\": 1 }; " ; }
	}
	else
		$json_data = "json_data = { \"status\": 0 };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>