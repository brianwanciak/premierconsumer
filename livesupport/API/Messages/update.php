<?php
	if ( defined( 'API_Messages_update' ) ) { return ; }
	define( 'API_Messages_update', true ) ;

	FUNCTION Messages_update_MessageValue( &$dbh,
					  $messageid,
					  $tbl_name,
					  $value )
	{
		if ( ( $messageid == "" ) || ( $tbl_name == "" ) || ( $value == "" ) )
			return false ;
		
		LIST( $messageid, $tbl_name, $value ) = database_mysql_quote( $dbh, $messageid, $tbl_name, $value ) ;

		$query = "UPDATE p_messages SET $tbl_name = '$value' WHERE messageID = $messageid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}
?>