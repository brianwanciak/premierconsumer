<?php
	if ( defined( 'API_Footprints_remove_itr' ) ) { return ; }
	define( 'API_Footprints_remove_itr', true ) ;

	FUNCTION Footprints_remove_itr_Expired_U( &$dbh )
	{
		global $VARS_FOOTPRINT_U_EXPIRE ;

		$expired = time() - $VARS_FOOTPRINT_U_EXPIRE ;
		$query = "DELETE FROM p_footprints_u WHERE updated < $expired" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>
