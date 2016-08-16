<?php
	if ( defined( 'API_Depts_get' ) ) { return ; }
	define( 'API_Depts_get', true ) ;

	FUNCTION Depts_get_AllDepts( &$dbh )
	{
		$query = "SELECT * FROM p_departments ORDER BY name ASC" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		} return false ;
	}

	FUNCTION Depts_get_DeptOps( &$dbh,
						$deptid,
						$status = 0 )
	{
		if ( !$deptid )
			return false ;

		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$status_string = ( $status ) ? "AND p_dept_ops.status = $status" : "" ;
		$query = "SELECT * FROM p_operators, p_dept_ops WHERE p_operators.opID = p_dept_ops.opID AND p_dept_ops.deptID = $deptid $status_string ORDER BY p_dept_ops.display ASC" ;
		database_mysql_query( $dbh, $query ) ;

		$ops = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$ops[] = $data ;
			return $ops ;
		} return false ;
	}

	FUNCTION Depts_get_OpDepts( &$dbh,
						$opid )
	{
		if ( !$opid )
			return false ;

		LIST( $opid ) = database_mysql_quote( $dbh, $opid ) ;

		$query = "SELECT * FROM p_departments, p_dept_ops WHERE p_departments.deptID = p_dept_ops.deptID AND p_dept_ops.opID = $opid ORDER BY p_departments.name ASC" ;
		database_mysql_query( $dbh, $query ) ;

		$depts = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
				$depts[] = $data ;
			return $depts ;
		} return false ;
	}

	FUNCTION Depts_get_DeptInfo( &$dbh,
						$deptid )
	{
		if ( !$deptid )
			return false ;

		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$query = "SELECT * FROM p_departments WHERE deptID = $deptid LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		} return false ;
	}

	FUNCTION Depts_get_DeptInfoByName( &$dbh,
						$name )
	{
		if ( $name == "" )
			return false ;

		LIST( $name ) = database_mysql_quote( $dbh, $name ) ;

		$query = "SELECT * FROM p_departments WHERE name = '$name' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		} return false ;
	}

	FUNCTION Depts_get_AllDeptsVars( &$dbh )
	{
		$query = "SELECT * FROM p_dept_vars" ;
		database_mysql_query( $dbh, $query ) ;

		$vars = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
			{
				$deptid = $data["deptID"] ;
				$vars[$deptid] = $data ;
			}
			return $vars ;
		} return false ;
	}

	FUNCTION Depts_get_DeptVars( &$dbh,
						$deptid )
	{
		if ( !$deptid )
			return false ;

		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$query = "SELECT * FROM p_dept_vars WHERE deptID = $deptid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		} return false ;
	}
?>