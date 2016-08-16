<?php
	if ( defined( 'API_External_put' ) ) { return ; }
	define( 'API_External_put', true ) ;

	/****************************************************************/
	FUNCTION External_put_External( &$dbh,
					$extid,
					$name,
					$url )
	{
		if ( ( $name == "" ) || ( $url == "" ) )
			return false ;

		if ( !$extid ) { $extid = "NULL" ; }
		LIST( $extid, $name, $url ) = database_mysql_quote( $dbh, $extid, $name, $url ) ;

		$query = "SELECT * FROM p_external WHERE name = '$name'" ;
		database_mysql_query( $dbh, $query ) ;
		$external = database_mysql_fetchrow( $dbh ) ;

		if ( isset( $external["extID"] ) && ( $external["name"] == $name ) )
		{
			if ( $external["extID"] != $extid )
				return false ;
		}

		$query = "REPLACE INTO p_external VALUES ( $extid, '$name', '$url' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$id = database_mysql_insertid( $dbh ) ;
			return $id ;
		}
		return false ;
	}

	/****************************************************************/
	FUNCTION External_put_ExtOp( &$dbh,
					$extid,
					$opid )
	{
		if ( ( $extid == "" ) || ( $opid == "" ) )
			return false ;

		LIST( $extid, $opid ) = database_mysql_quote( $dbh, $extid, $opid ) ;

		$query = "REPLACE INTO p_ext_ops VALUES ( $extid, $opid )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

?>