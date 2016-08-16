<?php
	if ( defined( 'API_Footprints_put_itr' ) ) { return ; }
	define( 'API_Footprints_put_itr', true ) ;

	FUNCTION Footprints_put_itr_Print( &$dbh,
					$deptid,
					$os,
					$browser,
					$ip,
					$vis_token,
					$onpage,
					$title )
	{
		if ( ( $deptid == "" ) || ( $os == "" ) || ( $browser == "" )
			|| ( $ip == "" ) || ( $vis_token == "" ) || ( $onpage == "" ) )
			return false ;

		$now = time() ;
		$today = mktime( 0, 0, 1, date( "m", time() ), date( "j", time() ), date( "Y", time() ) ) ;
		$url_mdfive = md5( $onpage ) ;

		LIST( $deptid, $os, $browser, $ip, $vis_token, $onpage, $title, $url_mdfive ) = database_mysql_quote( $dbh, $deptid, $os, $browser, $ip, $vis_token, $onpage, $title, $url_mdfive ) ;

		$query = "INSERT INTO p_footprints VALUES ( $now, 0, '$ip', $os, $browser, '$vis_token', '$url_mdfive', '$onpage', '$title' )" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] && $onpage )
		{
			$query = "INSERT INTO p_footstats VALUES ( $today, '$url_mdfive', 1, '$onpage' ) ON DUPLICATE KEY UPDATE total = total + 1" ;
			database_mysql_query( $dbh, $query ) ;

			if ( $dbh[ 'ok' ] )
				return true ;
		}
		return false ;
	}
?>