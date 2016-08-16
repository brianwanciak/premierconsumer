<?php
	if ( defined( 'API_Canned_remove' ) ) { return ; }
	define( 'API_Canned_remove', true ) ;

	FUNCTION Canned_remove_Canned( &$dbh,
						$opid,
						$canid )
	{
		if ( ( $opid == "" ) || ( $canid == "" ) )
			return false ;

		LIST( $canid ) = database_mysql_quote( $dbh, $canid ) ;

		$query = "DELETE FROM p_canned WHERE canID = $canid AND opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>
