<?php
	if ( defined( 'API_IPs_get' ) ) { return ; }
	define( 'API_IPs_get', true ) ;

	FUNCTION IPs_get_IPInfo( &$dbh,
					$vis_token,
					$ip )
	{
		if ( ( $vis_token == "" ) || ( $ip == "" ) )
			return false ;

		LIST( $vis_token, $ip ) = database_mysql_quote( $dbh, $vis_token, $ip ) ;

		$q_param = ( $vis_token != "null" ) ? "md5_vis = '$vis_token'" : "ip = '$ip'" ;
		$query = "SELECT * FROM p_ips WHERE $q_param LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

?>