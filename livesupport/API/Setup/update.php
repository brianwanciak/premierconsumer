<?php
	if ( defined( 'API_Setup_update' ) ) { return ; }
	define( 'API_Setup_update', true ) ;

	FUNCTION Setup_update_SetupValue( &$dbh,
					  $adminid,
					  $tbl_name,
					  $value )
	{
		if ( ( $adminid == "" ) || ( $tbl_name == "" ) )
			return false ;
		
		LIST( $adminid, $tbl_name, $value ) = database_mysql_quote( $dbh, $adminid, $tbl_name, $value ) ;

		$query = "UPDATE p_admins SET $tbl_name = '$value' WHERE adminID = $adminid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}
?>