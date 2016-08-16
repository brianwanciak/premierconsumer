<?php
	if ( defined( 'API_External_get' ) ) { return ; }
	define( 'API_External_get', true ) ;

	FUNCTION External_get_AllExternal( &$dbh )
	{
		$query = "SELECT * FROM p_external ORDER BY name ASC" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}

	FUNCTION External_get_OpExternals( &$dbh,
						$opid )
	{
		if ( $opid == "" )
			return false ;

		LIST( $opid ) = database_mysql_quote( $dbh, $opid ) ;

		$query = "SELECT p_external.extID AS extID, p_external.name AS name, p_external.url AS url FROM p_external, p_ext_ops WHERE p_external.extID = p_ext_ops.extID AND p_ext_ops.opID = $opid ORDER BY p_external.name ASC" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}

	FUNCTION External_get_ExternalByID( &$dbh,
						$extid )
	{
		if ( $extid == "" )
			return false ;

		LIST( $extid ) = database_mysql_quote( $dbh, $extid ) ;

		$query = "SELECT * FROM p_external WHERE extID = $extid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION External_get_ExtOps( &$dbh,
						$extid )
	{
		if ( $extid == "" )
			return false ;

		LIST( $extid ) = database_mysql_quote( $dbh, $extid ) ;

		$query = "SELECT p_operators.opID AS opID, p_operators.name AS name FROM p_ext_ops, p_operators WHERE p_ext_ops.opID = p_operators.opID AND extID = $extid ORDER BY p_operators.name ASC" ;
		database_mysql_query( $dbh, $query ) ;

		$ops = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$ops[] = $data ;
			return $ops ;
		}
		return false ;
	}
?>