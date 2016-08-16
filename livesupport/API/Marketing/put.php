<?php
	if ( defined( 'API_Marketing_put' ) ) { return ; }
	define( 'API_Marketing_put', true ) ;

	FUNCTION Marketing_put_Marketing( &$dbh,
					$marketid,
					$skey,
					$name,
					$color )
	{
		if ( ( $name == "" ) || ( $color == "" ) )
			return false ;

		if ( !$marketid ) { $marketid = "NULL" ; }
		LIST( $marketid, $skey, $name, $color ) = database_mysql_quote( $dbh, $marketid, $skey, $name, $color ) ;

		$query = "SELECT * FROM p_marketing WHERE name = '$name'" ;
		database_mysql_query( $dbh, $query ) ;
		$marketing = database_mysql_fetchrow( $dbh ) ;

		if ( isset( $marketing["marketID"] ) && ( $marketing["name"] == $name ) )
		{
			if ( $marketing["marketID"] != $marketid )
				return false ;
		}

		$query = "REPLACE INTO p_marketing VALUES ( $marketid, '$skey', '$name', '$color' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$id = database_mysql_insertid( $dbh ) ;
			return $id ;
		}

		return false ;
	}

	FUNCTION Marketing_put_Click( &$dbh,
					$marketid,
					$clicks )
	{
		if ( ( $marketid == "" ) || ( $clicks == "" ) )
			return false ;

		LIST( $marketid, $clicks ) = database_mysql_quote( $dbh, $marketid, $clicks ) ;
		$sdate = mktime( 0, 0, 1, date("m"), date("j"), date("Y") ) ;

		$query = "INSERT INTO p_market_c VALUES ( $sdate, $marketid, $clicks )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}
?>