<?php
	if ( defined( 'API_Vars_remove' ) ) { return ; }
	define( 'API_Vars_remove', true ) ;

	FUNCTION Vars_remove_DeptSocials( &$dbh,
						$deptid )
	{
		if ( $deptid == "" )
			return false ;

		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$query = "DELETE FROM p_socials WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>