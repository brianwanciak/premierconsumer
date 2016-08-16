<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else if ( $action == "footprints" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_itr.php" ) ;
		$vis_token = Util_Format_Sanatize( Util_Format_GetVar( "vis_token" ), "ln" ) ;

		$footprint_u_info = Footprints_get_itr_IPFootprints_U( $dbh, $vis_token ) ;
		if ( isset( $footprint_u_info["md5_vis"] ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
			$requestinfo = Chat_get_itr_RequestIPInfo( $dbh, "null", $vis_token ) ;
			if ( !isset( $requestinfo["ip"] ) )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/update.php" ) ;
				Footprints_update_FootprintUniqueValue( $dbh, $vis_token, "chatting", 0 ) ;
			}
		}
		$footprints = Footprints_get_itr_IPFootprints( $dbh, $vis_token, 55 ) ;
		$json_data = "json_data = { \"status\": 1, \"footprints\": [  " ;
		for ( $c = 0; $c < count( $footprints ); ++$c )
		{
			$footprint = $footprints[$c] ;
			$title = preg_replace( "/\"/", "&quot;", $footprint["title"] ) ;
			$onpage = preg_replace( "/hphp/i", "http", preg_replace( "/\"/", "&quot;", $footprint["onpage"] ) ) ;

			$json_data .= "{ \"total\": \"$footprint[total]\", \"mdfive\": \"$footprint[md5_page]\", \"onpage\": \"$onpage\", \"title\": \"$title\" }," ;
		}
		$json_data = substr_replace( $json_data, "", -1 ) ;
		$json_data .= "	] };" ;
	}
	else
		$json_data = "json_data = { \"status\": 0 };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
	$json_data = preg_replace( "/\t/", "", $json_data ) ;
	print "$json_data" ;
	exit ;
?>