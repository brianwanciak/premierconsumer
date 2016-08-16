<?php
	if ( defined( 'API_Footprints_put' ) ) { return ; }
	define( 'API_Footprints_put', true ) ;

	FUNCTION Footprints_put_Print_U( &$dbh,
					$footc,
					$vis_token,
					$deptid,
					$os,
					$browser,
					$footprints,
					$requests,
					$initiates,
					$resolution,
					$ip,
					$onpage,
					$title,
					$marketid,
					$refer,
					$country = "",
					$region = "",
					$city = "",
					$latitude = 0,
					$longitude = 0 )
	{
		if ( ( $os == "" ) || ( $browser == "" )
			|| ( $footprints == "" ) || ( $ip == "" ) || ( $vis_token == "" ) 
			|| ( $onpage == "" ) )
			return false ;

		global $CONF ;
		if ( !defined( 'API_Util_Functions_itr' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;

		$now = time() ;

		LIST( $vis_token, $deptid, $os, $browser, $footprints, $requests, $initiates, $resolution, $ip, $onpage, $title, $marketid, $refer, $country, $region, $city, $latitude, $longitude ) = database_mysql_quote( $dbh, $vis_token, $deptid, $os, $browser, $footprints, $requests, $initiates, $resolution, $ip, $onpage, $title, $marketid, $refer, $country, $region, $city, $latitude, $longitude ) ;
		$onpage_string = ( !$footc ) ? ", onpage = '$onpage' , title = '$title', footprints = $footprints " : "" ;
		$requests_string = ( $requests ) ? ", requests = $requests " : "" ; $initiates_string = ( $initiates ) ? ", initiates = $initiates " : "" ;

		$query = "SELECT footID FROM p_footprints_u WHERE md5_vis = '$vis_token' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $data = database_mysql_fetchrow( $dbh ) )
			$query = "UPDATE p_footprints_u SET updated = $now $onpage_string $requests_string $initiates_string WHERE footID = '$data[footID]'" ;
		else
			$query = "INSERT INTO p_footprints_u VALUES ( 0, '$vis_token', $now, $now, $deptid, $marketid, 0, $os, $browser, $footprints, $requests, $initiates, '$resolution', '$ip', '$onpage', '$title', '$refer', '$country', '$region', '$city', $latitude, $longitude )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
			return 1 ;
		return 0 ;
	}

	FUNCTION Footprints_put_Refer( &$dbh,
					$vis_token,
					$marketid,
					$refer )
	{
		if ( $vis_token == "" )
			return false ;

		$now = time() ;
		$url_mdfive = ( $refer ) ? md5( $refer ) : "" ;
		LIST( $vis_token, $marketid, $url_mdfive, $refer ) = database_mysql_quote( $dbh, $vis_token, $marketid, $url_mdfive, $refer ) ;

		$query = "INSERT INTO p_refer VALUES ( '$vis_token', $now, 0, $marketid, '$url_mdfive', '$refer' ) ON DUPLICATE KEY UPDATE md5_vis = '$vis_token'" ;
		database_mysql_query( $dbh, $query ) ;
		$nresults = database_mysql_nresults( $dbh ) ;

		if ( $dbh[ 'ok' ] && $nresults && $refer )
		{
			$today = mktime( 0, 0, 1, date( "m", time() ), date( "j", time() ), date( "Y", time() ) ) ;
			$query = "INSERT INTO p_referstats VALUES ( $today, '$url_mdfive', 1, '$refer' ) ON DUPLICATE KEY UPDATE total = total + 1" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
				return true ;
		}

		return false ;
	}
?>