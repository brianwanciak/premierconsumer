<?php
	if ( defined( 'API_Marquee_remove' ) ) { return ; }
	define( 'API_Marquee_remove', true ) ;

	FUNCTION Marquee_remove_Marquee( &$dbh,
						$marqid )
	{
		if ( $marqid == "" )
			return false ;

		LIST( $marqid ) = database_mysql_quote( $dbh, $marqid ) ;

		$query = "DELETE FROM p_marquees WHERE marqID = $marqid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>