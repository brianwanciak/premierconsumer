<?php
	if ( defined( 'API_Depts_remove' ) ) { return ; }
	define( 'API_Depts_remove', true ) ;

	FUNCTION Depts_remove_Dept( &$dbh,
						$deptid )
	{
		if ( $deptid == "" )
			return false ;

		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$query = "DELETE FROM p_canned WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_dept_ops WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_marquees WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_rstats_ops WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_rstats_depts WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_rstats_log WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_req_log WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_transcripts WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_notes WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_messages WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_departments WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;
		$query = "DELETE FROM p_dept_vars WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>