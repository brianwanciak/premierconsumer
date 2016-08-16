<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else if ( $action == "status" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
	
		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
		$status = Util_Format_Sanatize( Util_Format_GetVar( "status" ), "n" ) ;
		$mapp = Util_Format_Sanatize( Util_Format_GetVar( "mapp" ), "n" ) ;

		Ops_update_PutOpStatus( $dbh, $opid, $status, $mapp ) ;
		Ops_update_OpValue( $dbh, $opid, "status", $status ) ;
		Ops_update_OpValue( $dbh, $opid, "lastactive", time() ) ;

		if ( !$status && !Ops_get_itr_AnyOpsOnline( $dbh, 0 ) )
		{
			$initiate_dir = $CONF["TYPE_IO_DIR"] ; 
			$dh = dir( $initiate_dir ) ; 
			while( $file = $dh->read() ) {
				if ( is_file( $file ) ) { unlink( "$initiate_dir/$file" ) ; }
			} 
			$dh->close() ;
		}

		$json_data = "json_data = { \"status\": 1 }; " ;
	}
	else
		$json_data = "json_data = { \"status\": 0 };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	print "$json_data" ;
	exit ;
?>