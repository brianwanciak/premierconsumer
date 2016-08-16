<?php
	if ( defined( 'API_Ops_get' ) ) { return ; }
	define( 'API_Ops_get', true ) ;

	FUNCTION Ops_get_AllOps( &$dbh )
	{
		$query = "SELECT * FROM p_operators ORDER BY name ASC" ;
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

	FUNCTION Ops_get_TotalOps( &$dbh )
	{
		$query = "SELECT count(*) AS total FROM p_operators" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data["total"] ;
		}
		return false ;
	}

	FUNCTION Ops_get_IsOpInDept( &$dbh,
					$opid,
					$deptid )
	{
		if ( !$opid || !$deptid )
			return false ;

		LIST( $opid, $deptid ) = database_mysql_quote( $dbh, $opid, $deptid ) ;

		$query = "SELECT * FROM p_dept_ops WHERE deptID = $deptid AND opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			if ( database_mysql_nresults( $dbh ) ) { return true ; }
		}
		return false ;
	}

	FUNCTION Ops_get_OpInfoByID( &$dbh,
					$opid )
	{
		if ( !$opid )
			return false ;

		LIST( $opid ) = database_mysql_quote( $dbh, $opid ) ;

		$query = "SELECT * FROM p_operators WHERE opID = $opid LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Ops_get_OpInfoByToken( &$dbh,
					$token )
	{
		if ( $token == "" )
			return false ;

		LIST( $token ) = database_mysql_quote( $dbh, $token ) ;

		$query = "SELECT * FROM p_operators WHERE md5( CONCAT( login, password ) ) = '$token' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Ops_get_OpVars( &$dbh,
					$opid )
	{
		if ( !$opid )
			return false ;

		LIST( $opid ) = database_mysql_quote( $dbh, $opid ) ;

		$query = "SELECT * FROM p_op_vars WHERE opID = $opid LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}

	FUNCTION Ops_get_NextRequestOp( &$dbh,
					$deptid,
					$rtype,
					$rstring )
	{
		if ( !$deptid || ( $rtype == "" ) )
			return false ;

		global $VARS_EXPIRED_OPS ;
		$lastactive = time() - $VARS_EXPIRED_OPS - 10 ; // extra 10 seconds buffer

		if ( $rtype == 1 )
			$order_by = "ORDER BY p_dept_ops.display ASC" ;
		else if ( $rtype == 2 )
			$order_by = "ORDER BY p_operators.lastrequest ASC" ;
		else { return false ; }
		LIST( $deptid ) = database_mysql_quote( $dbh, $deptid ) ;

		$rstring_query = "" ;
		if ( $rstring )
		{
			$rstring_array = array_filter( explode( "-", $rstring ) ) ;
			$rstring_query = preg_replace( "/  +/", "", implode( " AND p_operators.opID <> ", $rstring_array ) ) ;
			$rstring_query = ( count( $rstring_array ) <= 1 ) ? " AND p_operators.opID <> $rstring_query " : $rstring_query ;
		}

		$query = "SELECT p_operators.opID AS opID, p_operators.maxc AS maxc, p_operators.mapp AS mapp, p_operators.rate AS rate, p_operators.sms AS sms, p_operators.smsnum AS smsnum, p_operators.name AS name, p_operators.email AS email FROM p_operators INNER JOIN p_dept_ops ON p_operators.opID = p_dept_ops.opID WHERE p_operators.status = 1 AND ( ( p_operators.lastactive > $lastactive ) OR ( p_operators.mapp = 1 ) ) AND p_dept_ops.deptID = $deptid $rstring_query $order_by" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$operators = Array() ;
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$operators[] = $data ;

			$op_chats = Array() ;
			$query = "SELECT opID, count(*) AS total from p_requests WHERE op2op = 0 AND status <> 0 GROUP BY opID" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				while ( $data = database_mysql_fetchrow( $dbh ) )
					$op_chats[$data["opID"]] = $data["total"] ;
			}

			for ( $c = 0; $c < count( $operators ); ++$c )
			{
				$operator = $operators[$c] ;
				if ( !isset( $op_chats[$operator["opID"]] ) || ( $operator["maxc"] == -1 ) || ( $op_chats[$operator["opID"]] < $operator["maxc"] ) )
					return $operator ;
			}
			return false ;
		}
		return false ;
	}

	FUNCTION Ops_get_OpDepts( &$dbh,
						$opid )
	{
		if ( !$opid )
			return false ;

		LIST( $opid ) = database_mysql_quote( $dbh, $opid ) ;

		$query = "SELECT p_dept_ops.deptID AS deptID, p_dept_ops.status AS status, p_departments.name AS name FROM p_dept_ops, p_departments WHERE p_dept_ops.opID = $opid AND p_dept_ops.deptID = p_departments.deptID" ;
		database_mysql_query( $dbh, $query ) ;

		$depts = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while ( $data = database_mysql_fetchrow( $dbh ) )
				$depts[] = $data ;
			return $depts ;
		}
		return false ;
	}
?>