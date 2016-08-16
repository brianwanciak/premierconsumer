<?php
	if ( defined( 'API_Messages_remove' ) ) { return ; }
	define( 'API_Messages_remove', true ) ;

	FUNCTION Messages_remove_Messages( &$dbh,
						$messageid )
	{
		if ( $messageid == "" )
			return false ;

		LIST( $messageid ) = database_mysql_quote( $dbh, $messageid ) ;

		$query = "DELETE FROM p_messages WHERE messageID = $messageid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

	FUNCTION Messages_remove_LastMessages( &$dbh,
						$deptid,
						$months )
	{
		if ( ( $deptid == "" ) || ( $months == "" ) || !is_numeric( $months ) )
			return false ;

		$expired = time()-(60*60*24*29*$months) ;
		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$query = "DELETE FROM p_messages WHERE deptID = '$deptid' AND created < $expired" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>