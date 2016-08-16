<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$jkey = Util_Format_Sanatize( Util_Format_GetVar( "jkey" ), "ln" ) ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;

	if ( $jkey && isset( $CONF["API_KEY"] ) && ( $jkey == md5( $CONF["API_KEY"] ) ) && $opid )
	{
		if ( !isset( $CONF['SQLTYPE'] ) ) { $CONF['SQLTYPE'] = "SQL.php" ; }
		else if ( $CONF['SQLTYPE'] == "mysql" ) { $CONF['SQLTYPE'] = "SQL.php" ; }

		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_itr.php" ) ;

		Ops_update_itr_IdleOps( $dbh ) ;
		$opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ;

		database_mysql_close( $dbh ) ;
		
		$json_data = "json_data = { \"status\": $opinfo[status] };" ;
	}
	else { $json_data = "json_data = { \"status\": 0, \"error\": \"Invalid API Key.\" };" ; }

	print "$json_data" ;
	exit ;
?>