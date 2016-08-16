<?php
	if ( defined( 'API_IPs_update' ) ) { return ; }
	define( 'API_IPs_update', true ) ;

	FUNCTION IPs_update_IpValue( &$dbh,
					  $vis_token,
					  $tbl_name,
					  $value )
	{
		if ( ( $vis_token == "" ) || ( $tbl_name == "" ) || ( $value == "" ) )
			return false ;

		LIST( $vis_token, $tbl_name, $value ) = database_mysql_quote( $dbh, $vis_token, $tbl_name, $value ) ;

		$query = "UPDATE p_ips SET $tbl_name = '$value' WHERE md5_vis = '$vis_token'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$flag = database_mysql_nresults( $dbh ) ;
			return $flag ;
		}
		return false ;
	}
?>