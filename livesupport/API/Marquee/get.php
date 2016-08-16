<?php
	if ( defined( 'API_Marquee_get' ) ) { return ; }
	define( 'API_Marquee_get', true ) ;

	FUNCTION Marquee_get_AllMarquees( &$dbh )
	{
		$query = "SELECT * FROM p_marquees ORDER BY display ASC" ;
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

	FUNCTION Marquee_get_MarqueeInfo( &$dbh,
						$marqid )
	{
		if ( $marqid == "" )
			return false ;

		LIST( $marqid ) = database_mysql_quote( $dbh, $marqid ) ;

		$query = "SELECT * FROM p_marquees WHERE marqID = $marqid LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Marquee_get_DeptMarquees( &$dbh,
						$deptid )
	{
		$dept_string = ( $deptid ) ? "deptID = $deptid OR" : "" ;

		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$query = "SELECT * FROM p_marquees WHERE ( $dept_string deptID = 1111111111 ) ORDER BY display ASC" ;
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
?>