<?php
	if ( defined( 'API_IPs_remove' ) ) { return ; }
	define( 'API_IPs_remove', true ) ;

	FUNCTION IPs_remove_Expired_IPs( &$dbh )
	{
		global $VARS_IP_LOG_EXPIRE ;
		global $VARS_FOOTPRINT_STATS_EXPIRE ;

		$expired_ips = time() - (60*60*24*$VARS_IP_LOG_EXPIRE) ;
		$expired_stats = time() - (60*60*24*$VARS_FOOTPRINT_STATS_EXPIRE) ;

		$query = "DELETE FROM p_ips WHERE created < $expired_ips" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_footprints WHERE created < $expired_stats AND archive = 0" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_refer WHERE created < $expired_stats AND archive = 0" ;
		database_mysql_query( $dbh, $query ) ;
		return true ;
	}
?>
