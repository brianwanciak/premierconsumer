<?php
	if ( defined( 'API_Marketing_remove' ) ) { return ; }
	define( 'API_Marketing_remove', true ) ;

	FUNCTION Marketing_remove_Marketing( &$dbh,
						$marketid )
	{
		if ( $marketid == "" )
			return false ;

		LIST( $marketid ) = database_mysql_quote( $dbh, $marketid ) ;

		$query = "DELETE FROM p_marketing WHERE marketID = $marketid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_market_c WHERE marketID = $marketid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "UPDATE p_footprints_u SET marketID = 0 WHERE marketID = $marketid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "UPDATE p_refer SET marketID = 0 WHERE marketID = $marketid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "UPDATE p_requests SET marketID = 0 WHERE marketID = $marketid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "UPDATE p_req_log SET marketID = 0 WHERE marketID = $marketid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>
