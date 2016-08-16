<?php
	/////////////////////////////////////////
	// PHP Live! Database API
	/////////////////////////////////////////
	if ( defined( 'API_Util_SQL' ) ) { return ; }	
	define( 'API_Util_SQL', true ) ; if ( !isset( $dbh ) ) { $dbh = Array() ; $dbh['free'] = ( isset( $VARS_MYSQL_FREE_RESULTS ) && is_numeric( $VARS_MYSQL_FREE_RESULTS ) && $VARS_MYSQL_FREE_RESULTS ) ? 1 : 0 ; }

	if ( !isset( $connection ) )
	{
		if ( !extension_loaded('pdo_mysql') )
		{
			if ( !defined( 'API_Util_Error' ) )
				include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
			ErrorHandler( 613, "PDO Extension Not Enabled", $PHPLIVE_FULLURL, 0, Array() ) ;
		}
		try {
			$connection = new PDO( "mysql:host=$CONF[SQLHOST];dbname=$CONF[DATABASE];", $CONF["SQLLOGIN"], stripslashes( $CONF["SQLPASS"] ) ) ;
			$dbh['con'] = $connection ; $dbh['qc'] = 0 ;
		}
		catch ( PDOException $e ) {
			if ( !defined( 'API_Util_Error' ) )
				include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
			ErrorHandler( 600, "MySQL DB Connection Failed", $PHPLIVE_FULLURL, 0, Array() ) ;
		}
	}

	FUNCTION database_mysql_query( &$dbh, $query )
	{
		$dbh['ok'] = 0 ; $dbh['result'] = 0 ; $dbh['error'] = "None" ;  $dbh['query'] = $query ; ++$dbh['qc'] ;
		$result = $dbh['con']->query( $query ) ;
		if ( $result ) { $dbh['result'] = $result ; $dbh['ok'] = 1 ; $dbh['error'] = "None" ; }
		else { $dbh['result'] = 0 ; $dbh['ok'] = 0 ; $dbh_error = $dbh['con']->errorInfo() ; $dbh['error'] = $dbh_error[2] ; }
	}

	FUNCTION database_mysql_fetchrow( &$dbh )
	{
		$result = $dbh['result']->fetch() ;
		return $result ;
	}

	FUNCTION database_mysql_fetchrowa( &$dbh )
	{
		$result = $dbh['result']->fetch( PDO::FETCH_ASSOC ) ;
		return $result ;
	}

	FUNCTION database_mysql_insertid( &$dbh )
	{
		$id = $dbh['con']->lastInsertId() ;
		return $id ;
	}

	FUNCTION database_mysql_nresults( &$dbh )
	{
		if ( preg_match( "/^select/i", $dbh['query'] ) )
			$total = $dbh['result']->rowCount() ;
		else
			$total = $dbh['result']->rowCount() ;
		return $total ;
	}

	FUNCTION database_mysql_quote( &$dbh )
	{
		$output = Array() ;
		for ( $i = 1; $i < func_num_args(); $i++ )
			$output[] = addslashes( stripslashes( func_get_arg( $i ) ) ) ;
		return $output ;
	}

	FUNCTION database_mysql_close( &$dbh )
	{
		if ( isset( $dbh['con'] ) )
		{
			$dbh['con'] = null ; unset( $dbh ) ;
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