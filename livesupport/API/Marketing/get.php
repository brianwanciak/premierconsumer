<?php
	if ( defined( 'API_Marketing_get' ) ) { return ; }
	define( 'API_Marketing_get', true ) ;

	FUNCTION Marketing_get_AllMarketing( &$dbh )
	{
		$query = "SELECT * FROM p_marketing ORDER BY name ASC" ;
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

	FUNCTION Marketing_get_ClicksRangeHash( &$dbh,
						$stat_start,
						$stat_end )
	{
		if ( ( $stat_start == "" ) || ( $stat_end == "" ) )
			return false ;

		LIST( $stat_start, $stat_end ) = database_mysql_quote( $dbh, $stat_start, $stat_end ) ;

		$query = "SELECT * FROM p_market_c WHERE sdate >= $stat_start AND sdate <= $stat_end" ;
		database_mysql_query( $dbh, $query ) ;

		$output = Array() ;
		$output[0] = Array() ; $output[0]["clicks"] = 0 ;
		if ( $dbh[ 'ok' ] )
		{
			while( $data = database_mysql_fetchrow( $dbh ) )
			{
				$sdate = $data["sdate"] ;
				$marketid = $data["marketID"] ;

				if ( !isset( $output[$sdate] ) )
				{
					$output[$sdate] = Array() ;
					$output[$sdate]["clicks"] = 0 ;
				}
				if ( !isset( $output[$sdate][$marketid] ) )
				{
					$output[$sdate][$marketid] = Array() ;
					$output[$sdate][$marketid]["clicks"] = 0 ;
				}
				if ( !isset( $output[0][$marketid] ) )
				{
					$output[0][$marketid] = Array() ;
					$output[0][$marketid]["clicks"] = 0 ;
				}

				$output[0][$marketid]["clicks"] += $data["clicks"] ;
				$output[0]["clicks"] += $data["clicks"] ;

				$output[$sdate][$marketid]["clicks"] += $data["clicks"] ;
				$output[$sdate]["clicks"] += $data["clicks"] ;
			}
		}
		return $output ;
	}
?>
