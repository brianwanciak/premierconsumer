<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$setupinfo = Util_Security_AuthSetup( $dbh, $ses ) ){ $json_data = "json_data = { \"status\": 0, \"error\": \"Authentication error.\" };" ; exit ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;

	if ( $action == "footprints" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_ext.php" ) ;
		$sdate_start = Util_Format_Sanatize( Util_Format_GetVar( "sdate" ), "n" ) ;

		$stat_start = mktime( 0, 0, 1, date( "m", $sdate_start ), date( "j", $sdate_start ), date( "Y", $sdate_start ) ) ;
		$stat_end = mktime( 23, 59, 59, date( "m", $sdate_start ), date( "j", $sdate_start ), date( "Y", $sdate_start ) ) ;
		$footprints = Footprints_get_FootStatsData( $dbh, $stat_start, $stat_end ) ;

		usort( $footprints, 'Util_Functions_Sort_Compare' ) ;

		$json_data = "json_data = { \"status\": 1, \"footprints\": [ " ;
		for ( $c = 0; $c < count( $footprints ); ++$c )
		{
			$footprint = $footprints[$c] ;
			if ( $footprint["onpage"] != "null" )
			{
				$url = preg_replace( "/hphp/i", "http", $footprint["onpage"] ) ;
				$url_snap = ( strlen( $url ) > 130 ) ? substr( $url, 0, 130 ) . "..." : $url ;
				$json_data .= "{ \"total\": $footprint[total], \"url_snap\": \"$url_snap\", \"url_raw\": \"$url\" }," ;
			}
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "refers" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_ext.php" ) ;
		$sdate_start = Util_Format_Sanatize( Util_Format_GetVar( "sdate" ), "n" ) ;

		$stat_start = mktime( 0, 0, 1, date( "m", $sdate_start ), date( "j", $sdate_start ), date( "Y", $sdate_start ) ) ;
		$stat_end = mktime( 23, 59, 59, date( "m", $sdate_start ), date( "j", $sdate_start ), date( "Y", $sdate_start ) ) ;
		$refers = Footprints_get_ReferStatsData( $dbh, $stat_start, $stat_end ) ;

		$json_data = "json_data = { \"status\": 1, \"footprints\": [ " ;
		for ( $c = 0; $c < count( $refers ); ++$c )
		{
			$footprint = $refers[$c] ;
			if ( ( $footprint["refer"] != "null" ) && $footprint["refer"] )
			{
				$url = preg_replace( "/hphp/i", "http", Util_Format_ConvertQuotes( $footprint["refer"] ) ) ;
				$url_snap = ( strlen( $url ) > 130 ) ? substr( $url, 0, 130 ) . "..." : $url ;
				$json_data .= "{ \"total\": $footprint[total], \"url_snap\": \"$url_snap\", \"url_raw\": \"$url\" }," ;
			}
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "fetch_request_urls" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;
		$sdate_start = Util_Format_Sanatize( Util_Format_GetVar( "sdate" ), "n" ) ;

		if ( $sdate_start )
		{
			$stat_start = mktime( 0, 0, 1, date( "m", $sdate_start ), date( "j", $sdate_start ), date( "Y", $sdate_start ) ) ;
			$stat_end = mktime( 23, 59, 59, date( "m", $sdate_start ), date( "j", $sdate_start ), date( "Y", $sdate_start ) ) ;
		}
		else
		{
			$stat_start = Util_Format_Sanatize( Util_Format_GetVar( "stat_start" ), "n" ) ;
			$stat_end = Util_Format_Sanatize( Util_Format_GetVar( "stat_end" ), "n" ) ;
		}

		$urls = Chat_ext_get_RequestURLs( $dbh, $stat_start, $stat_end ) ;

		$json_data = "json_data = { \"status\": 1, \"urls\": [ " ;
		for ( $c = 0; $c < count( $urls ); ++$c )
		{
			$data = $urls[$c] ;
			$url = htmlentities( $data["onpage"] ) ;
			$json_data .= "{ \"url\": \"$url\", \"total\": \"$data[total]\" }," ;
		}

		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else if ( $action == "fetch_request_timeline" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;
		$sdate_start = Util_Format_Sanatize( Util_Format_GetVar( "sdate" ), "n" ) ;

		if ( $sdate_start )
		{
			$stat_start = mktime( 0, 0, 1, date( "m", $sdate_start ), date( "j", $sdate_start ), date( "Y", $sdate_start ) ) ;
			$stat_end = mktime( 23, 59, 59, date( "m", $sdate_start ), date( "j", $sdate_start ), date( "Y", $sdate_start ) ) ;
		}
		else
		{
			$stat_start = Util_Format_Sanatize( Util_Format_GetVar( "stat_start" ), "n" ) ;
			$stat_end = Util_Format_Sanatize( Util_Format_GetVar( "stat_end" ), "n" ) ;
		}
		$timeline = Chat_ext_get_RequestTimeline( $dbh, $deptid, $opid, $stat_start, $stat_end ) ;

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