<?php
	if ( defined( 'API_Setup_remove' ) ) { return ; }
	define( 'API_Setup_remove', true ) ;

	FUNCTION Setup_remove_Admin( &$dbh,
						$adminid )
	{
		if ( $adminid == "" )
			return false ;

		LIST( $adminid ) = database_mysql_quote( $dbh, $adminid ) ;

		$query = "DELETE FROM p_admins WHERE adminID = $adminid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>