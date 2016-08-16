<?php
	if ( defined( 'API_IPs_put' ) ) { return ; }
	define( 'API_IPs_put', true ) ;

	FUNCTION IPs_put_IP( &$dbh, $ip, $vis_token, $deptid, $t_footprints, $t_requests, $t_initiate, $request, $initiate, $i_footprints, $i_timestamp, $onpage = "" )
	{
		if ( ( $ip == "" ) || ( $vis_token == "" ) )
			return false ;
		global $CONF ; global $VALS ;
		if ( !defined( 'API_IPs_get' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/get.php" ) ;
		$now = time() ; $ipinfo = IPs_get_IPInfo( $dbh, $vis_token, $ip ) ;
		if ( isset( $ipinfo["ip"] ) )
		{
			$t_footprints = $ipinfo["t_footprints"] + $t_footprints ; $t_requests = $ipinfo["t_requests"] + $t_requests ; $t_initiate = $ipinfo["t_initiate"] + $t_initiate ;
			$t_footprints_query = ( $t_footprints ) ? "t_footprints = $t_footprints" : " " ; $t_requests_query = ( $t_requests ) ? "t_requests = $t_requests" : " " ; $t_initiate_query = ( $t_initiate ) ? "t_initiate = $t_initiate" : " " ; $t_query = "$t_footprints_query,$t_requests_query,$t_initiate_query," ;
			$t_query = preg_replace( "/ ,/", "", $t_query ) ;
			$initiate_array = ( isset( $VALS["auto_initiate"] ) && $VALS["auto_initiate"] ) ? unserialize( html_entity_decode( $VALS["auto_initiate"] ) ) : Array() ;
			$auto_initiate_duration = ( isset( $initiate_array["duration"] )&& $initiate_array["duration"] ) ? $initiate_array["duration"] : 0 ;
			$auto_initiate_footprints = ( isset( $initiate_array["footprints"] ) && $initiate_array["footprints"] ) ? $initiate_array["footprints"] : 0 ;
			$auto_initiate_andor = ( isset( $initiate_array["andor"] ) ) ? $initiate_array["andor"] : 0 ;
			$auto_initiate_reset = ( isset( $initiate_array["reset"] ) ) ? $initiate_array["reset"] : 0 ;
			$exclude_string = "" ;
			if ( isset( $initiate_array["exclude"] ) && $initiate_array["exclude"] )
			{
				$exclude_array = explode( ",", $initiate_array["exclude"] ) ;
				for ( $c = 0; $c < count( $exclude_array ); ++$c ) { $exclude_string .= "($exclude_array[$c])|" ; }
				if ( $exclude_string ) { $exclude_string = substr_replace( $exclude_string, "", -1 ) ; }
			}
			$reset = 60*60*$auto_initiate_reset ;
			$auto_initiate = $auto_initiate_exclude_flag = 0 ;
			$auto_initiate_duration_flag = ( $auto_initiate_duration && ( ( $now - $ipinfo["i_timestamp"] ) >= $auto_initiate_duration ) ) ? 1 : 0 ;
			$auto_initiate_footprints_flag = ( $auto_initiate_footprints && ( $ipinfo["i_footprints"] >= $auto_initiate_footprints ) ) ? 1 : 0 ;
			if ( isset( $initiate_array["exin"] ) && ( $initiate_array["exin"] == "include" ) )
			{ $auto_initiate_exclude_flag = 1 ; if ( $exclude_string && preg_match( "/$exclude_string/", $onpage ) ) { $auto_initiate_exclude_flag = 0 ; } }
			else if ( $exclude_string && preg_match( "/$exclude_string/", $onpage ) ) { $auto_initiate_exclude_flag = 1 ; }
			if ( ( $auto_initiate_andor == 2 ) && ( $auto_initiate_duration_flag && $auto_initiate_footprints_flag ) && !$auto_initiate_exclude_flag ) { $auto_initiate = 1 ; }
			else if ( ( $auto_initiate_andor == 1 ) && ( $auto_initiate_duration_flag || $auto_initiate_footprints_flag ) && !$auto_initiate_exclude_flag ) { $auto_initiate = 1 ; }
			if ( $initiate && $auto_initiate && ( $ipinfo["i_initiate"] <= $now ) )
			{
				if ( preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) ) { $isonline = 0 ; }
				else { $isonline = ( is_file( "$CONF[CHAT_IO_DIR]/online_$deptid.info" ) ) ? 1 : 0 ; }
				if ( $isonline && $vis_token )
				{
					if ( !is_file( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ) { touch( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ; }
					$i_initiate = $now + $reset ; $i_footprints = 0 ;
				}
				else { $i_initiate = $ipinfo["i_initiate"] ; $i_footprints = $ipinfo["i_footprints"] + $i_footprints ; }
			}
			else if ( $initiate ) { $i_initiate = $ipinfo["i_initiate"] ; $i_footprints = $ipinfo["i_footprints"] + $i_footprints ; }
			else if ( $t_initiate || $request ) { $i_initiate = $now + $reset ; $i_footprints = 0 ; }
			if ( !$i_timestamp ) { $i_timestamp = $ipinfo["i_timestamp"] ; }
		} else { $i_initiate = 0 ; }

		LIST( $vis_token, $t_footprints, $t_requests, $t_initiate, $i_footprints, $i_timestamp ) = database_mysql_quote( $dbh, $vis_token, $t_footprints, $t_requests, $t_initiate, $i_footprints, $i_timestamp ) ;
		if ( isset( $ipinfo["ip"] ) )
			$query = "UPDATE p_ips SET created = $now, $t_query i_footprints = $i_footprints, i_timestamp = $i_timestamp, i_initiate = $i_initiate WHERE md5_vis = '$vis_token'" ;
		else
			$query = "INSERT INTO p_ips VALUES ( '$vis_token', '$ip', $now, $t_footprints, $t_requests, $t_initiate, $i_footprints, $i_timestamp, $i_initiate )" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh[ 'ok' ] )
			return true ;
		return false ;
	}

?>