<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$setupinfo = Util_Security_AuthSetup( $dbh, $ses ) ){ $json_data = "json_data = { \"status\": 0, \"error\": \"Authentication error.\" };" ; exit ; }
	// STANDARD header end
	/****************************************/

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	if ( $action == "update_profile_pic_onoff" )
	{
		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "n" ) ;

		if ( $opid )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
			Ops_update_OpValue( $dbh, $opid, "pic", $value ) ;
		}
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
			Util_Vals_WriteToFile( "PROFILE", $value ) ;
		}
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	if ( $action == "update_pic_edit" )
	{
		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "n" ) ;
		$flag = Util_Format_Sanatize( Util_Format_GetVar( "flag" ), "n" ) ;

		if ( $opid )
		{
			if ( $flag )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
				Ops_update_OpVarValue( $dbh, $opid, "pic_edit", $value ) ;
			}
			else
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
				Ops_put_OpVars( $dbh, $opid ) ;
				Ops_update_OpVarValue( $dbh, $opid, "pic_edit", $value ) ;
			}
		}
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else if ( $action == "save_mapp_key" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

		$mkey = Util_Format_Sanatize( Util_Format_GetVar( "mkey" ), "ln" ) ;

		Util_Vals_WriteToConfFile( "MAPP_KEY", $mkey ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
	}
	else
		$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid action. [a2]\" };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>