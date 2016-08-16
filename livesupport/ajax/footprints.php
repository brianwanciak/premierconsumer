<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;

	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$onpage = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "onpage" ), "url" ) ) ;
	$title = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "title" ), "title" ) ) ;
	$refer = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "r" ), "url" ) ) ; if ( !$refer ) { $refer = "" ; }
	$resolution = Util_Format_Sanatize( Util_Format_GetVar( "resolution" ), "ln" ) ;
	$token = Util_Format_Sanatize( Util_Format_GetVar( "token" ), "ln" ) ;
	$c = Util_Format_Sanatize( Util_Format_GetVar( "c" ), "n" ) ;
	$image_dir = realpath( "$CONF[DOCUMENT_ROOT]/pics/icons/pixels" ) ; $image_path = "$image_dir/4x4.gif" ;

	LIST( $ip, $vis_token ) = Util_IP_GetIP( $token ) ;
	$pi = $marketid = $skey = $excluded = 0 ;
	if ( preg_match( "/$ip/", $VALS["TRAFFIC_EXCLUDE_IPS"] ) ) { $excluded = 1 ; }
	preg_match( "/plk(=|%3D)(.*)-m/", $onpage, $matches ) ;
	if ( isset( $matches[2] ) ) { LIST( $pi, $marketid, $skey ) = explode( "-", $matches[2] ) ; }
	if ( !isset( $CONF["foot_log"] ) ) { $CONF["foot_log"] = "on" ; }

	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$agent = substr( $agent, 0, 255 ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ; $now = time() ;

	if ( preg_match( "/(statichtmlapp.com)/", $onpage ) && !$title ) { $title = "Facebook Page" ; }
	else if ( !$title ) { $title = "- no title -" ; }

	if ( $excluded ) { $image_path = "$image_dir/1x1.gif" ; }
	else
	{
		if ( !isset( $CONF['SQLTYPE'] ) ) { $CONF['SQLTYPE'] = "SQL.php" ; }
		else if ( $CONF['SQLTYPE'] == "mysql" ) { $CONF['SQLTYPE'] = "SQL.php" ; }
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/put.php" ) ;

		$country = $region = $city = "" ; $latitude = $longitude = 0 ;
		if ( $geoip && !$c )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/Util.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/GeoIP/get.php" ) ;

			LIST( $ip_num, $network ) = UtilIPs_IP2Long( $ip ) ;
			$geoinfo = GeoIP_get_GeoInfo( $dbh, $ip_num, $network ) ;
			if ( isset( $geoinfo["latitude"] ) )
			{
				$country = $geoinfo["country"] ;
				$region = $geoinfo["region"] ;
				$city = $geoinfo["city"] ;
				$latitude = $geoinfo["latitude"] ;
				$longitude = $geoinfo["longitude"] ;
			}
		}
		if ( $onpage && !$c )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/get.php" ) ;
			$ipinfo = IPs_get_IPInfo( $dbh, $vis_token, $ip ) ;
			$footprints = isset( $ipinfo["t_footprints"] ) ? $ipinfo["t_footprints"]+1 : 1 ;
			$requests = isset( $ipinfo["t_requests"] ) ? $ipinfo["t_requests"] : 0 ;
			$initiates = isset( $ipinfo["t_initiate"] ) ? $ipinfo["t_initiate"] : 0 ;
			$query = "SELECT * FROM p_refer WHERE md5_vis = '$vis_token' LIMIT 1" ;
			database_mysql_query( $dbh, $query ) ; $refer_data = database_mysql_fetchrow( $dbh ) ;
			$refer = ( isset( $refer_data["refer"] ) ) ? $refer_data["refer"] : $refer ;

			if ( $pi && $marketid && $skey )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/get_itr.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/update.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/put.php" ) ;

				$marketinfo = Marketing_get_itr_MarketingByID( $dbh, $marketid ) ;
				if ( $marketinfo["skey"] == $skey )
				{
					$clickinfo = Marketing_get_itr_ClickInfo( $dbh, $marketid ) ;
					if ( isset( $clickinfo["marketID"] ) ) { Marketing_update_MarketClickValue( $dbh, $marketid, "clicks", $clickinfo["clicks"]+1 ) ; }
					else { Marketing_put_Click( $dbh, $marketid, 1 ) ; }
				}
			}
		}
		else { $footprints = 1 ; $requests = $initiates = 0 ; }
		$nresults = Footprints_put_Print_U( $dbh, $c, $vis_token, $deptid, $os, $browser, $footprints, $requests, $initiates, $resolution, $ip, $onpage, $title, $marketid, $refer, $country, $region, $city, $latitude, $longitude ) ;
		if ( $c > $VARS_JS_FOOTPRINT_MAX_CYCLE ) { $image_path = "$image_dir/4x4.gif" ; }
		else if ( !$c )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/put.php" ) ;
	
			if ( $CONF["foot_log"] == "on" )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/put_itr.php" ) ;
				Footprints_put_itr_Print( $dbh, $deptid, $os, $browser, $ip, $vis_token, $onpage, $title ) ;
			}
			Footprints_put_Refer( $dbh, $vis_token, $marketid, $refer ) ;
			IPs_put_IP( $dbh, $ip, $vis_token, $deptid, 1, 0, 0, 0, 1, 1, $now, $onpage ) ;

			$vars = Util_Format_Get_Vars( $dbh ) ;
			if ( $vars["ts_clear"] <= ( $now - ( $VARS_CYCLE_CLEAN * 2 ) ) )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove_itr.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/remove.php" ) ;
				Util_Format_Update_TimeStamp( $dbh, "clear", $now ) ;
				Footprints_remove_itr_Expired_U( $dbh ) ;
				IPs_remove_Expired_IPs( $dbh ) ;
			}
			$image_path = "$image_dir/1x1.gif" ;
		}
		else
		{
			// repeat
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/put.php" ) ;
			IPs_put_IP( $dbh, $ip, $vis_token, $deptid, 0, 0, 0, 0, 1, 0, 0, $onpage ) ;
			$image_path = "$image_dir/1x1.gif" ;
		}
	}

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	Header( "Content-type: image/GIF" ) ;
	Header( "Content-Transfer-Encoding: binary" ) ;
	if ( !isset( $VALS['OB_CLEAN'] ) || ( $VALS['OB_CLEAN'] == 'on' ) ) { ob_clean(); flush(); }
	readfile( $image_path ) ;
?>