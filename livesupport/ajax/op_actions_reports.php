<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else if ( $action == "fetch_request_timeline" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;

		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
		$opid_cookie = Util_Format_Sanatize( $_COOKIE["phplive_opID"], "n" ) ;
		$timeline = Util_Format_Sanatize( Util_Format_GetVar( "timeline" ), "ln" ) ;
		$now = time() ;

		$m = date( "m", $now ) ;
		$d = date( "j", $now ) ;
		$y = date( "Y", $now ) ;

		switch ( $timeline )
		{
			case ( "7d" ):
			{
				$stat_start = mktime( 0, 0, 1, $m, $d-7, $y ) ;
				$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;
				break ;
			}
			case ( "14d" ):
			{
				$stat_start = mktime( 0, 0, 1, $m, $d-14, $y ) ;
				$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;
				break ;
			}
			case ( "1m" ):
			{
				$stat_start = mktime( 0, 0, 1, $m-1, $d, $y ) ;
				$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;
				break ;
			}
			case ( "2m" ):
			{
				$stat_start = mktime( 0, 0, 1, $m-2, $d, $y ) ;
				$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;
				break ;
			}
			case ( "3m" ):
			{
				$stat_start = mktime( 0, 0, 1, $m-3, $d, $y ) ;
				$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;
				break ;
			}
			case ( "6m" ):
			{
				$stat_start = mktime( 0, 0, 1, $m-6, $d, $y ) ;
				$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;
				break ;
			}
			case ( "1y" ):
			{
				$stat_start = mktime( 0, 0, 1, $m, $d, $y-1 ) ;
				$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;
				break ;
			}
			case ( "2y" ):
			{
				$stat_start = mktime( 0, 0, 1, $m, $d, $y-2 ) ;
				$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;
				break ;
			}
			case ( "3y" ):
			{
				$stat_start = mktime( 0, 0, 1, $m, $d, $y-3 ) ;
				$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;
				break ;
			}
			default:
				$stat_start = mktime( 0, 0, 1, $m, $d, $y ) ;
				$stat_end = mktime( 23, 59, 59, $m, $d, $y ) ;
		}
		$timeline = Chat_ext_get_RequestTimeline( $dbh, $deptid, $opid_cookie, $stat_start, $stat_end ) ;

		$hours = Array() ;
		for ( $c = 0; $c < count( $timeline ); ++$c )
		{
			$data = $timeline[$c] ;

			$hour = date( "G", $data["created"] ) ;
			if ( isset( $hours[$hour] ) )
			{
				++$hours[$hour]["requests"] ;
				$status = ( !$data["status"] ) ? 0 : 1 ;
				$hours[$hour]["accepted"] += $status ;
			}
			else
			{
				$hours[$hour] = Array() ;
				$hours[$hour]["requests"] = 1 ;
				$status = ( !$data["status"] ) ? 0 : 1 ;
				$hours[$hour]["accepted"] = $status ;
			}
		}

		$now = time() ; $total_overall = $max = $total_accepted = 0 ;
		$json_data = "json_data = { \"status\": 1, \"timeline\": [ " ;
		for ( $c = 0; $c <= 23; ++$c )
		{
			$now_ = mktime( $c, 0, 1, date( "m", $now ), date( "j", $now ), date( "Y", $now ) ) ;
			$ampm = date( "a", $now_ ) ;
			$hour_ = date( "g", $now_ ) ;
			$hour_display = "%span%$hour_:00$ampm - $hour_:59$ampm%span_%" ;

			$unixtime = $now_ ;
			if ( isset( $hours[$c] ) )
			{
				$total_overall += $hours[$c]["requests"] ;
				$total_accepted += $hours[$c]["accepted"] ;
				if ( $hours[$c]["requests"] > $max ) { $max = $hours[$c]["requests"] ; }
				$json_data .= "{ \"hour\": \"$c\", \"timestamp\": \"$unixtime\", \"hour_display\": \"$hour_display\", \"ampm\": \"$ampm\", \"hour_\": \"$hour_\", \"total\": \"".$hours[$c]["requests"]."\", \"accepted\": \"".$hours[$c]["accepted"]."\" }," ;
			}
			else { $json_data .= "{ \"hour\": \"$c\", \"timestamp\": \"$unixtime\", \"hour_display\": \"$hour_display\", \"ampm\": \"$ampm\", \"hour_\": \"$hour_\", \"total\": \"0\", \"accepted\": \"0\" }," ; }
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	], \"hour_max\": \"$max\", \"total_overall\": \"$total_overall\", \"total_accepted\": \"$total_accepted\" };" ;
	}
	else
		$json_data = "json_data = { \"status\": 0, \"error\": \"Invalid action.\" };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>