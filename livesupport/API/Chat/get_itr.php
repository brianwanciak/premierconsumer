<?php
	if ( defined( 'API_Chat_get_itr' ) ) { return ; }
	define( 'API_Chat_get_itr', true ) ;

	FUNCTION Chat_get_itr_RequestCesInfo( &$dbh,
					$ces )
	{
		if ( $ces == "" )
			return false ;

		LIST( $ces ) = database_mysql_quote( $dbh, $ces ) ;

		$query = "SELECT * FROM p_requests WHERE ces = '$ces' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Chat_get_itr_RequestIPInfo( &$dbh,
					$ip,
					$vis_token )
	{
		if ( ( $ip == "" ) || ( $vis_token == "" ) )
			return false ;

		LIST( $ip, $vis_token ) = database_mysql_quote( $dbh, $ip, $vis_token ) ;

		$q_param = ( $vis_token != "null" ) ? "md5_vis = '$vis_token'" : "ip = '$ip'" ;
		$query = "SELECT * FROM p_requests WHERE $q_param AND ended = 0 LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}
?>