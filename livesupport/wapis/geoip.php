<?php
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$akey = Util_Format_Sanatize( Util_Format_GetVar( "akey" ), "ln" ) ;
	$format = Util_Format_Sanatize( Util_Format_GetVar( "f" ), "ln" ) ;
	$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

	if ( $akey && isset( $CONF["API_KEY"] ) && ( $akey == $CONF["API_KEY"] ) )
	{
		if ( $geoip )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Hash.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/Util.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/GeoIP/get.php" ) ;

			$countries = Util_Hash_Countries() ;
			LIST( $ip_num, $network ) = UtilIPs_IP2Long( $ip ) ;
			$geoinfo = GeoIP_get_GeoInfo( $dbh, $ip_num, $network ) ;
			database_mysql_close( $dbh ) ;
			
			$country = ( isset( $geoinfo["country"] ) && $geoinfo["country"] ) ? $geoinfo["country"] : "unknown" ;
			$country_name = ( isset( $geoinfo["country"] ) && $geoinfo["country"] ) ? $countries[$geoinfo["country"]] : "Location Unknown" ;
			$region = ( isset( $geoinfo["region"] ) && $geoinfo["region"] ) ? $geoinfo["region"] : "-" ;
			$city = ( isset( $geoinfo["city"] ) && $geoinfo["city"] ) ? $geoinfo["city"] : "-" ;
			$latitude = ( isset( $geoinfo["latitude"] ) && $geoinfo["latitude"] ) ? $geoinfo["latitude"] : 28.613459424004414 ;
			$longitude = ( isset( $geoinfo["longitude"] ) && $geoinfo["longitude"] ) ? $geoinfo["longitude"] : -40.4296875 ;

			$output = "" ;
			if ( $format == "csv" )
				$output = "$country,$country_name,$region,$city,$latitude,$longitude" ;
			else if ( $format == "json" )
				$output = "{ \"country\": \"$country\", \"country_name\": \"$country_name\", \"region\": \"$region\", \"city\": \"$city\", \"latitude\": $latitude, \"longitude\": $longitude }" ;
			else
				$output = "Invalid Format" ;

			print $output ;
		}
		else
			print "GeoIP Not Enabled" ;
	}
	else
		print "Invalid API Key" ;
?>