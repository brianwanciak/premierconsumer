<?php
	if ( defined( 'API_GeoIP_get' ) ) { return ; }
	define( 'API_GeoIP_get', true ) ;

	FUNCTION GeoIP_get_GeoInfo( &$dbh,
			$ip_num,
			$network )
	{
		if ( ( $network == "" ) || ( $ip_num == "" ) )
			return false ;

		$ips = Array() ;
		$mask = pow(2, 32) - 1 ;
		for ($i = 0; $i < 32; ++$i) {
			$ips[] = $ip_num & ($mask << $i) ;
		}
		$ips = Array_unique($ips) ;
		$between_ips = " AND startIpNum IN (" ;
		foreach ( $ips as $ip ) {
			$between_ips .= "$ip," ;
		}
		$between_ips = substr_replace( $between_ips, "", -1 ) . ") " ;
		if ( !count( $ips ) ) { $between_ips = "" ; }

		$query = "SELECT l.country, l.region, l.city, l.latitude, l.longitude FROM p_geo_loc l JOIN p_geo_bloc b ON ( l.locId = b.locId ) WHERE b.network = $network AND $ip_num BETWEEN startIpNum AND endIpNum $between_ips limit 1" ;
		database_mysql_query( $dbh, $query ) ;

		if ( $dbh[ 'ok' ] )
		{
			$data = database_mysql_fetchrow( $dbh ) ;
			return $data ;
		}
		return false ;
	}
?>