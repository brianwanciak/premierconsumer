<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	include_once( "../../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
	{
		$json_data = "json_data = { \"status\": -1 };" ;
	}
	else
	{
		$opid_cookie = Util_Format_Sanatize( $_COOKIE["phplive_opID"], "n" ) ;

		if ( $action == "update_mapp_c" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
			$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "n" ) ;
			Ops_update_OpVarValue( $dbh, $opid_cookie, "mapp_c", $value ) ;
			$json_data = "json_data = { \"status\": 1 };" ; 
		}
		else { $json_data = "json_data = { \"status\": 0 };" ; }
	}

	if ( isset( $dbh ) && $dbh['con'] ) { database_mysql_close( $dbh ) ; }
	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ; exit ;
?>