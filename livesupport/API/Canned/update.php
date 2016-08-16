<?php
	if ( defined( 'API_Canned_update' ) ) { return ; }
	define( 'API_Canned_update', true ) ;

	FUNCTION Canned_update_Dorder( &$dbh,
					  $userid,
					  $deptid,
					  $display )
	{
		if ( ( $userid == "" ) || ( $deptid == "" ) )
			return false ;
		
		LIST( $userid, $deptid, $display ) = database_mysql_quote( $dbh, $userid, $deptid, $display ) ;

		$query = "REPLACE INTO chat_op_dept VALUES ( $userid, $deptid, $display )" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}
?>