<?php
	if ( defined( 'API_Chat_put' ) ) { return ; }
	define( 'API_Chat_put', true ) ;

	FUNCTION Chat_put_Request( &$dbh,
					$deptid,
					$opid,
					$status,
					$initiate,
					$op2op,
					$t_vses,
					$os,
					$browser,
					$ces,
					$resolution,
					$vname,
					$vemail,
					$ip,
					$vis_token,
					$vis_token_,
					$onpage,
					$title,
					$question,
					$marketid,
					$refer,
					$custom,
					$auto_pop = 0,
					$sim_ops = "" )
	{
		if ( ( $deptid == "" ) || ( $opid == "" ) || ( $os == "" ) || ( $browser == "" )
			|| ( $ces == "" ) || ( $vname == "" ) || ( $ip == "" ) || ( $vis_token_ == "" ) )
			return false ;

		global $CONF ;
		global $VARS_SMS_BUFFER ;
		global $opinfo_next ;
		global $embed ;
		if ( !defined( 'API_Chat_get_itr' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
		if ( !defined( 'API_Util_Functions_itr' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;

		$now = ( isset( $opinfo_next["sms"] ) && ( $opinfo_next["sms"] == 1 ) ) ? time() + $VARS_SMS_BUFFER : time() ;
		$onpage = strip_tags( $onpage ) ;

		LIST( $deptid, $opid, $status, $initiate, $op2op, $t_vses, $os, $browser, $ces, $resolution, $vname, $vemail, $ip, $vis_token, $vis_token_, $onpage, $title, $question, $marketid, $refer, $custom, $auto_pop, $sim_ops ) = database_mysql_quote( $dbh, $deptid, $opid, $status, $initiate, $op2op, $t_vses, $os, $browser, $ces, $resolution, $vname, $vemail, $ip, $vis_token, $vis_token_, $onpage, $title, $question, $marketid, $refer, $custom, $auto_pop, $sim_ops ) ;

		$requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $ces ) ;
		if ( isset( $requestinfo["requestID"] ) )
		{
			if ( $requestinfo["initiated"] )
			{
				if ( !defined( 'API_Chat_update' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;

				Chat_update_RequestValue( $dbh, $requestinfo["requestID"], "status", 1 ) ;
				Chat_update_RequestValue( $dbh, $requestinfo["requestID"], "auto_pop", $auto_pop ) ;
			}

			return $requestinfo["requestID"] ;
		}
		else
		{
			if ( !defined( 'API_Chat_get' ) )
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;

			$vupdated = ( isset( $opinfo_next["sms"] ) && ( $opinfo_next["sms"] == 1 ) ) ? time() + $VARS_SMS_BUFFER : time() ;
			$rstring = "-$opid" ;

			$requests = Chat_get_IPTotalRequests( $dbh, $vis_token_, "req_log" ) ;
			if ( !$requests ) { $requests = 1 ; }

			$query = "SELECT * FROM p_footprints_u WHERE md5_vis = '$vis_token_' LIMIT 1" ;
			database_mysql_query( $dbh, $query ) ;
			$footu_data = database_mysql_fetchrow( $dbh ) ;
			$country = ( isset( $footu_data["country"] ) ) ? $footu_data["country"] : "" ;

			$query = "INSERT INTO p_requests VALUES ( NULL, $now, 0, 0, $now, $vupdated, $status, $auto_pop, $initiate, $deptid, $opid, $op2op, $t_vses, $marketid, $os, $browser, $requests, '$ces', '$resolution', '$vname', '$vemail', '$ip', '$country', '$vis_token', '$vis_token_', '$sim_ops', '', '$onpage', '$title', '$rstring', '$refer', '$custom', '$question' )" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
			{
				$id = database_mysql_insertid( $dbh ) ;
				return $id ;
			}

			return false ;
		}
	}

	FUNCTION Chat_put_ReqLog( &$dbh,
					$requestid )
	{
		if ( $requestid == "" )
			return false ;

		LIST( $requestid ) = database_mysql_quote( $dbh, $requestid ) ;

		$query = "INSERT INTO p_req_log ( ces, created, ended, status, archive, status_msg, initiated, deptID, opID, op2op, marketID, os, browser, idle_disconnect, resolution, vname, vemail, ip, md5_vis, sim_ops, onpage, title, custom, question ) SELECT ces, created, 0, status, 0, 0, initiated, deptID, opID, op2op, marketID, os, browser, 0, resolution, vname, vemail, ip, md5_vis_, sim_ops, onpage, title, custom, question FROM p_requests WHERE p_requests.requestID = $requestid" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;

		return false ;
	}

	FUNCTION Chat_put_RstatsLog( &$dbh,
					$ces,
					$status,
					$deptid,
					$opid )
	{
		if ( ( $ces == "" ) || ( $deptid == "" ) || ( $opid == "" ) )
			return false ;

		$now = time() ;
		LIST( $ces, $status, $opid, $deptid ) = database_mysql_quote( $dbh, $ces, $status, $opid, $deptid ) ;

		$query = "INSERT INTO p_rstats_log VALUES( '$ces', $now, $status, $opid, $deptid )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return true ;

		return false ;
	}
?>