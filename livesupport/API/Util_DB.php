<?php
	if ( defined( 'API_Util_DB' ) ) { return ; }	
	define( 'API_Util_DB', true ) ;

	FUNCTION Util_DB_GetTableNames( &$dbh )
	{
		$query = "SHOW TABLES" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrowa( $dbh ) )
			{
				foreach( $data as $db => $table )
					$output[] = $table ;
			}
		}

		return $output ;
	}

	FUNCTION Util_DB_AnalyzeTable( &$dbh,
					$table )
	{
		if ( $table == "" )
			return false ;
	
		LIST( $table ) = database_mysql_quote( $dbh, $table ) ;

		$query = "ANALYZE TABLE $table" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrowa( $dbh ) ;
			return $data ;
		}

		return false ;
	}

	FUNCTION Util_DB_TableStats( &$dbh,
					$table )
	{
		if ( $table == "" )
			return false ;
	
		LIST( $table ) = database_mysql_quote( $dbh, $table ) ;

		$query = "SHOW TABLE STATUS LIKE '$table'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrowa( $dbh ) ;
			return $data ;
		}

		return false ;
	}

?>