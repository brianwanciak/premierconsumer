<?php
	if ( defined( 'API_External_remove' ) ) { return ; }
	define( 'API_External_remove', true ) ;

	FUNCTION External_remove_External( &$dbh,
						$extid )
	{
		if ( $extid == "" )
			return false ;

		LIST( $extid ) = database_mysql_quote( $dbh, $extid ) ;

		$query = "DELETE FROM p_external WHERE extID = $extid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_ext_ops WHERE extID = $extid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

	FUNCTION External_remove_AllExtOps( &$dbh,
						$extid )
	{
		if ( $extid == "" )
			return false ;

		LIST( $extid ) = database_mysql_quote( $dbh, $extid ) ;

		$query = "DELETE FROM p_ext_ops WHERE extID = $extid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>
