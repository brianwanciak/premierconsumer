<?php
	if ( defined( 'API_Chat_update' ) ) { return ; }
	define( 'API_Chat_update', true ) ;

	FUNCTION Chat_update_RequestValue( &$dbh,
					  $requestid,
					  $tbl_name,
					  $value )
	{
		if ( ( $requestid == "" ) || ( $tbl_name == "" ) )
			return false ;
		
		LIST( $requestid, $tbl_name, $value ) = database_mysql_quote( $dbh, $requestid, $tbl_name, $value ) ;

		$query = "UPDATE p_requests SET $tbl_name = '$value' WHERE requestID = $requestid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Chat_update_RequestLogValue( &$dbh,
					  $ces,
					  $tbl_name,
					  $value )
	{
		if ( ( $ces == "" ) || ( $tbl_name == "" ) )
			return false ;
		
		LIST( $ces, $tbl_name, $value ) = database_mysql_quote( $dbh, $ces, $tbl_name, $value ) ;

		$query = "UPDATE p_req_log SET $tbl_name = '$value' WHERE ces = '$ces'" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Chat_update_RstatsLogValue( &$dbh,
					  $ces,
					  $opid,
					  $tbl_name,
					  $value )
	{
		if ( ( $ces == "" ) || ( $opid == "" ) || ( $tbl_name == "" ) )
			return false ;
		
		LIST( $ces, $opid, $tbl_name, $value ) = database_mysql_quote( $dbh, $ces, $opid, $tbl_name, $value ) ;

		$query = "UPDATE p_rstats_log SET $tbl_name = '$value' WHERE ces = '$ces' AND opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Chat_update_TranscriptValue( &$dbh,
					  $ces,
					  $tbl_name,
					  $value )
	{
		if ( ( $ces == "" ) || ( $tbl_name == "" ) || ( $value == "" ) )
			return false ;
		
		LIST( $ces, $tbl_name, $value ) = database_mysql_quote( $dbh, $ces, $tbl_name, $value ) ;

		$query = "UPDATE p_transcripts SET $tbl_name = '$value' WHERE ces = '$ces'" ;
		database_mysql_query( $dbh, $query ) ;

		return database_mysql_nresults( $dbh ) ;
	}

	FUNCTION Chat_update_TransferChat( &$dbh,
					  $ces,
					  $orig_opid,
					  $deptid,
					  $opid )
	{
		if ( ( $orig_opid == "" ) || ( $ces == "" ) || ( $deptid == "" ) || ( $opid == "" ) )
			return false ;

		$now = time() ;
		LIST( $orig_opid, $ces, $deptid, $opid ) = database_mysql_quote( $dbh, $orig_opid, $ces, $deptid, $opid ) ;

		// place the transfered op so the call does not route back to the operator during visitor routing process
		// reset initiate so it does not show up as existing chat but a new chat
		$query = "UPDATE p_requests SET tupdated = $now, status = 2, initiated = 0, opID = $opid, op2op = $orig_opid, rstring = CONCAT( rstring, '-$opid' ) WHERE ces = '$ces'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Chat_update_TransferChatOrig( &$dbh,
					  $opid,
					  $ces )
	{
		if ( ( $opid == "" ) || ( $ces == "" ) )
			return false ;

		LIST( $opid, $ces ) = database_mysql_quote( $dbh, $opid, $ces ) ;

		$query = "UPDATE p_requests SET tupdated = 0, status = 0, opID = $opid, op2op = 0 WHERE ces = '$ces'" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Chat_update_AcceptChat( &$dbh,
					  $requestid,
					  $opid,
					  $status,
					  $op2op )
	{
		if ( ( $requestid == "" ) || ( $status == "" ) || ( $op2op == "" ) )
			return false ;

		LIST( $requestid, $opid, $status, $op2op ) = database_mysql_quote( $dbh, $requestid, $opid, $status, $op2op ) ;

		if ( ( $status == 2 ) && $op2op )
			$query = "UPDATE p_requests SET status = 1, tupdated = 0, op2op = 0 WHERE requestID = $requestid" ;
		else
			$query = "UPDATE p_requests SET status = 1, tupdated = 0 WHERE requestID = $requestid AND ( opID = $opid OR op2op = $opid OR opID = 1111111111 )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Chat_update_OpRequestUpdated( &$dbh,
					  $opid )
	{
		if ( $opid == "" )
			return false ;
		
		$now = time() ;
		LIST( $opid ) = database_mysql_quote( $dbh, $opid ) ;

		$query = "UPDATE p_requests SET updated = $now WHERE ( opID = $opid OR op2op = $opid OR opID = 1111111111 ) AND ( status = 0 OR status = 1 OR status = 2 )" ;
		database_mysql_query( $dbh, $query ) ;
		
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

	FUNCTION Chat_update_RecentChat( &$dbh,
					$opid,
					$ces,
					$rating )
	{
		if ( ( $opid == "" ) || ( $ces == "" ) )
			return false ;
		
		LIST( $opid, $ces, $rating ) = database_mysql_quote( $dbh, $opid, $ces, $rating ) ;

		$query = "UPDATE p_operators SET ces = '$ces', rating = $rating WHERE opID = $opid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}
?>