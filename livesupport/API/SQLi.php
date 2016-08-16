<?php
	/////////////////////////////////////////
	// PHP Live! Database API
	/////////////////////////////////////////
	if ( defined( 'API_Util_SQL' ) ) { return ; }	
	define( 'API_Util_SQL', true ) ; if ( !isset( $dbh ) ) { $dbh = Array() ; $dbh['free'] = ( isset( $VARS_MYSQL_FREE_RESULTS ) && is_numeric( $VARS_MYSQL_FREE_RESULTS ) && $VARS_MYSQL_FREE_RESULTS ) ? 1 : 0 ; }

	if ( !isset( $connection ) )
	{
		if ( !function_exists('mysqli_connect') )
		{
			if ( !defined( 'API_Util_Error' ) )
				include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
			ErrorHandler( 614, "MySQLi Extension Not Enabled", $PHPLIVE_FULLURL, 0, Array() ) ;
		}
		$connection = new mysqli( $CONF["SQLHOST"], $CONF["SQLLOGIN"], stripslashes( $CONF["SQLPASS"] ) ) ;
		if ( $connection->select_db( $CONF["DATABASE"] ) ) { $dbh['con'] = $connection ; $dbh['qc'] = 0 ; }
		else
		{
			if ( !defined( 'API_Util_Error' ) )
				include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
			ErrorHandler( 600, "DB Connection Failed", $PHPLIVE_FULLURL, 0, Array() ) ;
		}
	}

	FUNCTION database_mysql_query( &$dbh, $query )
	{
		$dbh['ok'] = 0 ; $dbh['result'] = 0 ; $dbh['error'] = "None" ;  $dbh['query'] = $query ; ++$dbh['qc'] ;
		$result = $dbh['con']->query( $query ) ;
		if ( $result ) { $dbh['result'] = $result ; $dbh['ok'] = 1 ; $dbh['error'] = "None" ; }
		else { $dbh['result'] = 0 ; $dbh['ok'] = 0 ; $dbh['error'] = $dbh['con']->error ; }
	}

	FUNCTION database_mysql_fetchrow( &$dbh )
	{
		$result = $dbh['result']->fetch_array( MYSQLI_ASSOC ) ;
		return $result ;
	}

	FUNCTION database_mysql_fetchrowa( &$dbh )
	{
		$result = $dbh['result']->fetch_assoc() ;
		return $result ;
	}

	FUNCTION database_mysql_insertid( &$dbh )
	{
		$id = $dbh['con']->insert_id ;
		return $id ;
	}

	FUNCTION database_mysql_nresults( &$dbh )
	{
		$total = $dbh['con']->affected_rows ;
		return $total ;
	}

	FUNCTION database_mysql_quote( &$dbh )
	{
		$output = Array() ;
		for ( $i = 1; $i < func_num_args(); $i++ )
			$output[] = $dbh['con']->real_escape_string( stripslashes( func_get_arg( $i ) ) ) ;
		return $output ;
	}

	FUNCTION database_mysql_close( &$dbh )
	{
		if ( isset( $dbh['con'] ) )
		{
			if ( $dbh['free'] && is_object( $dbh['result'] ) ) { $dbh['result']->free() ; }
			$dbh['con']->close() ;
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