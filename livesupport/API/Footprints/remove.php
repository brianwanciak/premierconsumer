<?php
	if ( defined( 'API_Footprints_remove' ) ) { return ; }
	define( 'API_Footprints_remove', true ) ;

	FUNCTION Footprints_remove_ExpiredStats( &$dbh )
	{
		global $VARS_FOOTPRINT_STATS_EXPIRE ;
		$expired_stats = time() - (60*60*24*$VARS_FOOTPRINT_STATS_EXPIRE) ;

		$query = "DELETE FROM p_footstats WHERE sdate < $expired_stats" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_referstats WHERE sdate < $expired_stats" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>