<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$ip = Util_Format_Sanatize( Util_Format_GetVar( "ip" ), "ln" ) ;

	if ( $action == "disconnect" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/update.php" ) ;

		$isop = Util_Format_Sanatize( Util_Format_GetVar( "isop" ), "n" ) ;
		$isop_ = Util_Format_Sanatize( Util_Format_GetVar( "isop_" ), "n" ) ;
		$isop__ = Util_Format_Sanatize( Util_Format_GetVar( "isop__" ), "n" ) ;
		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "lns" ) ;
		$widget = Util_Format_Sanatize( Util_Format_GetVar( "widget" ), "n" ) ;
		$t_vses = Util_Format_Sanatize( Util_Format_GetVar( "t_vses" ), "n" ) ;
		$token = Util_Format_Sanatize( Util_Format_GetVar( "token" ), "ln" ) ;
		$vis_token = Util_Format_Sanatize( Util_Format_GetVar( "vis_token" ), "ln" ) ;
		$unload = Util_Format_Sanatize( Util_Format_GetVar( "unload" ), "n" ) ;
		$vclick = Util_Format_Sanatize( Util_Format_GetVar( "vclick" ), "n" ) ;
		$idle = Util_Format_Sanatize( Util_Format_GetVar( "idle" ), "ln" ) ; $idle = ( is_numeric( $idle ) && ( $idle == -1 ) ) ? 1 : 0 ;

		$now = time() ;
		if ( !$vis_token ) { LIST( $ip, $vis_token ) = Util_IP_GetIP( $token ) ; }
		if ( $widget )
		{
			$requestinfo = Chat_get_itr_RequestIPInfo( $dbh, $ip, $vis_token ) ;
			$isop_ = $requestinfo["opID"] ;
			$ces = $requestinfo["ces"] ;
		}
		else { $requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $ces ) ; }

		if ( isset( $requestinfo["requestID"] ) && ( $requestinfo["status"] || $requestinfo["initiated"] ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put_itr.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

			$lang = $CONF["lang"] ;
			$deptinfo = Depts_get_DeptInfo( $dbh, $requestinfo["deptID"] ) ;
			$deptvars = Depts_get_DeptVars( $dbh, $requestinfo["deptID"] ) ;
			if ( $deptinfo["lang"] ) { $lang = $deptinfo["lang"] ; }
			include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($lang, "ln").".php" ) ;

			if ( $isop )
			{
				$text = "<div class='cl'><disconnected><d1>".$LANG["CHAT_NOTIFY_ODISCONNECT"]."</div>" ;
				if ( $requestinfo["op2op"] )
				{
					if ( ( $isop && $isop_ ) && ( $isop == $isop_ ) ) { $wid = $isop_ ; }
					else if ( $isop && $isop_ ) { $wid = $isop__ ; }
					else { $wid = $isop_ ; }
					$filename = $ces."-$wid" ;
					UtilChat_AppendToChatfile( "$filename.text", $text ) ;
				}
				else
				{
					$max_vses = ( $requestinfo["t_vses"] > $VARS_MAX_EMBED_SESSIONS ) ? $VARS_MAX_EMBED_SESSIONS : $requestinfo["t_vses"] ;
					for ( $c = 1; $c <= $max_vses; ++$c )
					{
						$filename = $ces."-0"."_".$c ;
						UtilChat_AppendToChatfile( "$filename.text", $text ) ;
					}
				}
			}
			else if ( $unload )
			{
				$vupdated = time() - ( $VARS_EXPIRED_REQS - 45 ) ;
				Chat_update_RequestValue( $dbh, $requestinfo["requestID"], "vupdated", $vupdated ) ;
			}
			else
			{
				if ( $requestinfo["initiated"] && !$requestinfo["status"] )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

					$text = "<div class='cl'><disconnected><d2>Visitor has declined the chat invitation.</div>" ;
					$opinfo = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;
					$filename = $ces."-".$opinfo["opID"] ;
					UtilChat_AppendToChatfile( "$filename.text", $text ) ;
				}
				else
				{
					$text = "<div class='cl'><disconnected><d2>".$LANG["CHAT_NOTIFY_VDISCONNECT"]."</div>" ;
					$filename = $ces."-".$isop_ ;
					UtilChat_AppendToChatfile( "$filename.text", $text ) ;

					// check all sessions to indicate disconnect for notification
					$max_vses = ( $requestinfo["t_vses"] > $VARS_MAX_EMBED_SESSIONS ) ? $VARS_MAX_EMBED_SESSIONS : $requestinfo["t_vses"] ;
					for ( $c = 1; $c <= $max_vses; ++$c )
					{
						if ( $c != $t_vses )
						{
							$filename = $ces."-0"."_".$c ;
							UtilChat_AppendToChatfile( "$filename.text", $text ) ;
						}
					}
				}
			}

			if ( !$unload )
			{
				UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
				if ( !$requestinfo["initiated"] || ( $requestinfo["initiated"] && $requestinfo["status"] ) )
				{
					$output = UtilChat_ExportChat( "$ces.txt" ) ;
					if ( isset( $output[0] ) )
					{
						$formatted = $output[0] ; $plain = $output[1] ;
						$fsize = strlen( $formatted ) ;
						$vis_token = ( $requestinfo["md5_vis"] ) ? $requestinfo["md5_vis"] : $vis_token ;
						if ( Chat_put_itr_Transcript( $dbh, $ces, $requestinfo["status"], $requestinfo["created"], $now, $requestinfo["deptID"], $requestinfo["opID"], $requestinfo["initiated"], $requestinfo["op2op"], 0, $fsize, $requestinfo["vname"], $requestinfo["vemail"], $requestinfo["ip"], $vis_token, $requestinfo["question"], $formatted, $plain, $deptinfo, $deptvars ) )
						{
							if ( $idle ) { Chat_update_RequestLogValue( $dbh, $ces, "idle_disconnect", 1 ) ; }
							Chat_remove_Request( $dbh, $requestinfo["requestID"] ) ;
							Chat_update_RecentChat( $dbh, $requestinfo["opID"], $ces, 0 ) ;
						}
					}
				}
				else if ( $requestinfo["initiated"] || $requestinfo["status"] )
					Chat_remove_Request( $dbh, $requestinfo["requestID"] ) ;
			}
		}
		else if ( isset( $requestinfo["requestID"] ) && !$requestinfo["status"] )
		{
			if ( $isop && ( $requestinfo["opID"] != $isop ) )
			{
				if ( $requestinfo["op2op"] )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

					Chat_remove_itr_RequestByCes( $dbh, $requestinfo["ces"] ) ;
				}
			}
			else
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

				if ( $vclick ) { Chat_update_RequestLogValue( $dbh, $ces, "status_msg", 3 ) ; }
				Ops_put_itr_OpReqStat( $dbh, $requestinfo["deptID"], 0, "message", 1 ) ;
				Chat_remove_itr_RequestByCes( $dbh, $requestinfo["ces"] ) ;
			}
		}

		if ( !$unload )
		{
			if ( is_file( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ; }
			if ( $ces ) { clear_istyping( $ces ) ; }
			Footprints_update_FootprintUniqueValue( $dbh, $vis_token, "chatting", 0 ) ;
		}

		if ( $widget )
		{
			database_mysql_close( $dbh ) ;
			$image_dir = "$CONF[DOCUMENT_ROOT]/pics/icons/pixels" ;
			$image_path = "$image_dir/1x1.gif" ;
			Header( "Content-type: image/GIF" ) ;
			Header( "Content-Transfer-Encoding: binary" ) ;
			if ( !isset( $VALS['OB_CLEAN'] ) || ( $VALS['OB_CLEAN'] == 'on' ) ) { ob_clean(); flush(); }
			readfile( $image_path ) ;
			exit ;
		}
		else
			$json_data = "json_data = { \"status\": 1, \"ces\": \"$ces\" };" ;
	}
	else
		$json_data = "json_data = { \"status\": 0 };" ;

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	print "$json_data" ;
	exit ;

	function clear_istyping( $ces )
	{
		global $CONF ;
		if ( $ces )
		{
			$dir_files = glob( $CONF["TYPE_IO_DIR"]."/$ces"."*", GLOB_NOSORT ) ;
			$total_dir_files = count( $dir_files ) ;
			if ( $total_dir_files )
			{
				for ( $c = 0; $c < $total_dir_files; ++$c )
				{
					if ( $dir_files[$c] && is_file( $dir_files[$c] ) ) { unlink( $dir_files[$c] ) ; }
				}
			}
		}
	}
?>