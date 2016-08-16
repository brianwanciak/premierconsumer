<?php
	if ( defined( 'API_Marketing_get_itr' ) ) { return ; }
	define( 'API_Marketing_get_itr', true ) ;

	FUNCTION Marketing_get_itr_MarketingByID( &$dbh,
						$marketid )
	{
		if ( $marketid == "" )
			return false ;

		LIST( $marketid ) = database_mysql_quote( $dbh, $marketid ) ;

		$query = "SELECT * FROM p_marketing WHERE marketID = $marketid LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		$ops = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Marketing_get_itr_ClickInfo( &$dbh,
						$marketid )
	{
		if ( $marketid == "" )
			return false ;

		LIST( $marketid ) = database_mysql_quote( $dbh, $marketid ) ;
		$sdate = mktime( 0, 0, 1, date("m"), date("j"), date("Y") ) ;

		$query = "SELECT * FROM p_market_c WHERE marketID = $marketid AND sdate = $sdate LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		$ops = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}
?>
