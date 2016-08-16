<?php
	/////////////////////////////////////////
	// PHP Live! Database API
	/////////////////////////////////////////
	if ( defined( 'API_Util_SQL' ) ) { return ; }	
	define( 'API_Util_SQL', true ) ; if ( !isset( $dbh ) ) { $dbh = Array() ; $dbh['free'] = ( isset( $VARS_MYSQL_FREE_RESULTS ) && is_numeric( $VARS_MYSQL_FREE_RESULTS ) && $VARS_MYSQL_FREE_RESULTS ) ? 1 : 0 ; }

	if ( !isset( $connection ) ) { $connection = mysql_connect( $CONF["SQLHOST"], $CONF["SQLLOGIN"], stripslashes( $CONF["SQLPASS"] ) ) ; }
	if ( mysql_select_db( $CONF["DATABASE"] ) ) { $dbh['con'] = $connection ; $dbh['qc'] = 0 ; }
	else
	{
		if ( !defined( 'API_Util_Error' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
		ErrorHandler( 600, "DB Connection Failed", $PHPLIVE_FULLURL, 0, Array() ) ;
	}

	FUNCTION database_mysql_query( &$dbh, $query )
	{
		$dbh['ok'] = 0 ; $dbh['result'] = 0 ; $dbh['error'] = "None" ;  $dbh['query'] = $query ; ++$dbh['qc'] ;
		$result = mysql_query( $query, $dbh['con'] ) ;
		if ( $result ) { $dbh['result'] = $result ; $dbh['ok'] = 1 ; $dbh['error'] = "None" ; }
		else { $dbh['result'] = 0 ; $dbh['ok'] = 0 ; $dbh['error'] = mysql_error() ; }
	}

	FUNCTION database_mysql_fetchrow( &$dbh )
	{
		$result = mysql_fetch_array( $dbh['result'] ) ;
		return $result ;
	}

	FUNCTION database_mysql_fetchrowa( &$dbh )
	{
		$result = mysql_fetch_assoc( $dbh['result'] ) ;
		return $result ;
	}

	FUNCTION database_mysql_insertid( &$dbh )
	{
		$id = mysql_insert_id( $dbh['con'] ) ;
		return $id ;
	}

	FUNCTION database_mysql_nresults( &$dbh )
	{
		$total = mysql_affected_rows( $dbh['con'] ) ;
		return $total ;
	}

	FUNCTION database_mysql_quote( &$dbh )
	{
		$output = Array() ;
		for ( $i = 1; $i < func_num_args(); $i++ )
			$output[] = mysql_real_escape_string( stripslashes( func_get_arg( $i ) ) ) ;
		return $output ;
	}

	FUNCTION database_mysql_close( &$dbh )
	{
		if ( isset( $dbh['con'] ) )
		{
			if ( $dbh['free'] && is_resource( $dbh['result'] ) ) { mysql_free_result( $dbh['result'] ) ; }
			mysql_close( $dbh['con'] ) ;
			return true ;
		}
		return false ;
	}

	FUNCTION database_mysql_version( &$dbh )
	{
		database_mysql_query( $dbh, "SELECT version() AS version" ) ;
		$output = database_mysql_fetchrow( $dbh ) ;
		return $output["version"] ;
	}

	FUNCTION database_mysql_old( &$dbh )
	{
		return preg_match( "/^(4.0|3)/", database_mysql_version( $dbh ) ) ? 1 : 0 ;
	}

	if ( isset( $CONF['UTF_DB'] ) )
	{
		//$query = "SET NAMES 'utf8'" ;
		//database_mysql_query( $dbh, $query ) ;
	}

?>