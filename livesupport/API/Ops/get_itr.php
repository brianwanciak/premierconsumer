<?php
	if ( defined( 'API_Ops_get_itr' ) ) { return ; }
	define( 'API_Ops_get_itr', true ) ;

	FUNCTION Ops_get_itr_AnyOpsOnline( &$dbh,
					$deptid )
	{
		$dept_query = $visible_string = "" ;
		if ( $deptid )
		{
			LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;
			$dept_query = "AND deptID = $deptid" ;
		}
		else
			$visible_string = "visible = 1 AND" ;

		$query = "SELECT count(*) AS total FROM p_dept_ops WHERE $visible_string status = 1 $dept_query" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data["total"] ;
		}
		return false ;
	}
?>