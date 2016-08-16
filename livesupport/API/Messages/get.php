<?php
	if ( defined( 'API_Messages_get' ) ) { return ; }
	define( 'API_Messages_get', true ) ;

	FUNCTION Messages_get_Messages( &$dbh,
						$deptid,
						$page,
						$limit )
	{
		if ( $limit == "" )
			return false ;

		LIST( $deptid, $page, $limit ) = database_mysql_quote( $dbh, $deptid, $page, $limit ) ;
		$start = ( $page * $limit ) ;

		$dept_string = ( $deptid ) ? "WHERE deptID = $deptid" : "" ;
		$query = "SELECT * FROM p_messages $dept_string ORDER BY created DESC LIMIT $start, $limit" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}

	FUNCTION Messages_get_MessageByID( &$dbh,
						$messageid )
	{
		if ( $messageid == "" )
			return false ;

		LIST( $messageid ) = database_mysql_quote( $dbh, $messageid ) ;

		$query = "SELECT * FROM p_messages WHERE messageID = $messageid LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		$ops = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Messages_get_MessageByMd5( &$dbh,
						$md5_vis )
	{
		if ( $md5_vis == "" )
			return false ;

		LIST( $md5_vis ) = database_mysql_quote( $dbh, $md5_vis ) ;

		$query = "SELECT * FROM p_messages WHERE md5_vis = '$md5_vis' ORDER BY created DESC LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Messages_get_MessageByIP( &$dbh,
						$ip )
	{
		if ( $ip == "" )
			return false ;

		LIST( $ip ) = database_mysql_quote( $dbh, $ip ) ;

		$query = "SELECT * FROM p_messages WHERE ip = '$ip' ORDER BY created DESC LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		$ops = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Messages_get_MessageByCes( &$dbh,
						$ces )
	{
		if ( $ces == "" )
			return false ;

		LIST( $ces ) = database_mysql_quote( $dbh, $ces ) ;

		$query = "SELECT * FROM p_messages WHERE ces = '$ces'" ;
		database_mysql_query( $dbh, $query ) ;

		$ops = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Messages_get_TotalMessages( &$dbh,
						$deptid )
	{
		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$dept_string = ( $deptid ) ? "WHERE deptID = $deptid" : "" ;
		$query = "SELECT count(*) AS total FROM p_messages $dept_string" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data["total"] ;
		}
		return false ;
	}

	FUNCTION Messages_get_TotalUnreadMessages( &$dbh,
						$deptid )
	{
		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$dept_string = ( $deptid ) ? "AND deptID = $deptid" : "" ;
		$query = "SELECT count(*) AS total FROM p_messages WHERE status = 0 $dept_string" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data["total"] ;
		}
		return false ;
	}

	FUNCTION Messages_get_MessageURLs( &$dbh,
			$limit )
	{
		$limit_string = "" ;

		if ( $limit )
		{
			LIST( $limit ) = database_mysql_quote( $dbh, $limit ) ;
			$limit_string = "LIMIT $limit" ;
		}

		$query = "SELECT onpage, count(*) AS total FROM p_messages GROUP BY onpage ORDER BY total DESC $limit_string" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;

			return $output ;
		}
		return false ;
	}
?>