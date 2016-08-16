<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	include_once( "../../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;

	if ( $opid && $action )
	{
		if ( $action == "set_arn" )
		{
			$arn = Util_Format_Sanatize( Util_Format_GetVar( "arn" ), "url" ) ;

			$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
			if ( $arn && ( !isset( $mapp_array[$mapp_opid] ) || ( $mapp_array[$mapp_opid] != $arn ) ) )
			{
				$mapp_array[$opid] = "$arn" ;
				Util_Vals_WriteToFile( "MAPP", serialize( $mapp_array ) ) ;
			}
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else if ( $action == "pause" )
		{
			$confirm = Util_Format_Sanatize( Util_Format_GetVar( "confirm" ), "n" ) ;
			if ( !is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ) { touch( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ; }
			if ( $confirm )
			{
				sleep(1) ;
				$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
				if ( is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;
					if ( isset( $mapp_array[$opid] ) ) { $arn = $mapp_array[$opid]["a"] ; $platform = $mapp_array[$opid]["p"] ; }
					if ( isset( $arn ) && $arn ) { Util_MAPP_Publish( $opid, "system", $platform, $arn, "Notification is Active \xF0\x9F\x93\xB2" ) ; }
				}
			}

			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else if ( $action == "resume" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
			Ops_update_OpValue( $dbh, $opid, "lastactive", time() ) ;

			if ( is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ; }
			$json_data = "json_data = { \"status\": 1 };" ;
		}
		else
			$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid Mapp action.\" };" ;
	} else { $json_data = "json_data = { \"status\": 0, \"error\": \"Invalid Mapp parameters.\" };" ; }

	if ( isset( $dbh ) && $dbh['con'] ) { database_mysql_close( $dbh ) ; }
	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>