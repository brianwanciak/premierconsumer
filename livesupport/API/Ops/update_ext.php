<?php
	if ( defined( 'API_Ops_update_ext' ) ) { return ; }
	define( 'API_Ops_update_ext', true ) ;

	FUNCTION Ops_update_ext_AdminValue( &$dbh,
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