<?php
	if ( defined( 'API_Chat_update_itr' ) ) { return ; }
	define( 'API_Chat_update_itr', true ) ;

	FUNCTION Chat_update_itr_ResetChat( &$dbh,
					$requestid,
					$ces )
	{
		if ( ( $requestid == "" ) || ( $ces == "" ) )
			return false ;

		$now = time() ;
		LIST( $requestid, $ces ) = database_mysql_quote( $dbh, $requestid, $ces ) ;

		$query = "UPDATE p_requests SET vupdated = $now, opID = 0, sim_ops_ = '', rstring = '' WHERE requestID = $requestid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;

		return false ;
	}

	FUNCTION Chat_update_itr_RouteChat( &$dbh,
					$requestid,
					$ces,
					$opid,
					$sms,
					$rstring )
	{
		if ( ( $requestid == "" ) || ( $ces == "" ) || ( $opid == "" ) )
			return false ;
		
		global $VARS_SMS_BUFFER ;

		$now = ( $sms == 1 ) ? time() + $VARS_SMS_BUFFER : time() ;
		LIST( $requestid, $ces, $opid, $rstring ) = database_mysql_quote( $dbh, $requestid, $ces, $opid, $rstring ) ;

		$query = "UPDATE p_requests SET vupdated = $now, opID = $opid, rstring = '$rstring' WHERE requestID = $requestid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$query = "UPDATE p_req_log SET opID = $opid WHERE ces = '$ces'" ;
			database_mysql_query( $dbh, $query ) ;

			return true ;
		}
		return false ;
	}
?>