<?php
	if ( defined( 'API_Footprints_get_ext' ) ) { return ; }
	define( 'API_Footprints_get_ext', true ) ;

	FUNCTION Footprints_get_ext_FootprintsRangeHash( &$dbh,
						$stat_start,
						$stat_end )
	{
		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $dbh, $stat_start, $stat_end ) ;

		$query = "SELECT SUM( total ) AS total, sdate FROM p_footstats WHERE sdate >= $stat_start AND sdate <= $stat_end GROUP BY sdate" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
			{
				$sdate = $data["sdate"] ;

				if ( !isset( $output[$sdate] ) )
				{
					$output[$sdate] = Array() ;
					$output[$sdate]["total"] = 0 ;
				}

				$output[$sdate]["total"] = $data["total"] ;
			}
		}
		return $output ;
	}

	FUNCTION Footprints_get_ReferRangeHash( &$dbh,
						$stat_start,
						$stat_end )
	{
		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $dbh, $stat_start, $stat_end ) ;

		$query = "SELECT SUM( total ) AS total, sdate FROM p_referstats WHERE sdate >= $stat_start AND sdate <= $stat_end GROUP BY sdate" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
			{
				$sdate = $data["sdate"] ;

				if ( !isset( $output[$sdate] ) )
				{
					$output[$sdate] = Array() ;
					$output[$sdate]["total"] = 0 ;
				}

				$output[$sdate]["total"] = $data["total"] ;
			}
		}
		return $output ;
	}

	FUNCTION Footprints_get_FootStatsData( &$dbh,
					$stat_start,
					$stat_end )
	{
		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $dbh, $stat_start, $stat_end ) ;

		$query = "SELECT SQL_NO_CACHE * FROM p_footstats WHERE sdate >= $stat_start AND sdate <= $stat_end ORDER BY total DESC LIMIT 100" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}

	FUNCTION Footprints_get_ReferStatsData( &$dbh,
					$stat_start,
					$stat_end )
	{
		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $dbh, $stat_start, $stat_end ) ;

		$query = "SELECT SQL_NO_CACHE SUM(total) AS total, refer FROM p_referstats WHERE sdate >= $stat_start AND sdate <= $stat_end GROUP BY md5_page ORDER BY total DESC LIMIT 100" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$output = Array() ;
			while( $data = database_mysql_fetchrow( $dbh ) )
				$output[] = $data ;
			return $output ;
		}
		return false ;
	}

	FUNCTION Footprints_get_IPRefer( &$dbh,
					$vis_token )
	{
		if ( $vis_token == "" )
			return false ;

		LIST( $vis_token ) = database_mysql_quote( $dbh, $vis_token ) ;

		$query = "SELECT refer, p_marketing.marketID, p_marketing.name, p_marketing.color FROM p_refer LEFT JOIN p_marketing ON p_refer.marketID = p_marketing.marketID WHERE md5_vis = '$vis_token' ORDER BY created DESC LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}
?>