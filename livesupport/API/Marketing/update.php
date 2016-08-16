<?php
	if ( defined( 'API_Marketing_update' ) ) { return ; }
	define( 'API_Marketing_update', true ) ;

	FUNCTION Marketing_update_MarketClickValue( &$dbh,
					  $marketid,
					  $tbl_name,
					  $value )
	{
		if ( ( $marketid == "" ) || ( $tbl_name == "" ) || ( $value == "" ) )
			return false ;
		
		LIST( $marketid, $tbl_name, $value ) = database_mysql_quote( $dbh, $marketid, $tbl_name, $value ) ;
		$sdate = mktime( 0, 0, 1, date("m"), date("j"), date("Y") ) ;

		$query = "UPDATE p_market_c SET $tbl_name = '$value' WHERE marketID = $marketid AND sdate = $sdate" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

?>