<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else if ( $action == "accept" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;

		$requestid = Util_Format_Sanatize( Util_Format_GetVar( "requestid" ), "n" ) ;
		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "lns" ) ;
		$t_vses = Util_Format_Sanatize( Util_Format_GetVar( "t_vses" ), "n" ) ;
		$opid_cookie = Util_Format_Sanatize( $_COOKIE["phplive_opID"], "n" ) ;
		$tooslow = 0 ;

		$requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $ces ) ;
		if ( !isset( $requestinfo["status"] ) || ( $requestinfo["vupdated"] == 1 ) || ( ( $requestinfo["vupdated"] < ( time() - $VARS_EXPIRED_REQS ) ) && !$requestinfo["op2op"] ) || ( $requestinfo["status"] && ( $requestinfo["opID"] != $opid_cookie ) ) )
			$tooslow = 1 ;
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;

			$opinfo = ( $requestinfo["opID"] != 1111111111 ) ? Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) : Ops_get_OpInfoByID( $dbh, $opid_cookie ) ;
			Ops_update_OpValue( $dbh, $opid_cookie, "lastrequest", time() ) ;
			Chat_update_AcceptChat( $dbh, $requestinfo["requestID"], $opid_cookie, $requestinfo["status"], $requestinfo["op2op"] ) ;

			$lang = $CONF["lang"] ;
			$deptinfo = Depts_get_DeptInfo( $dbh, $requestinfo["deptID"] ) ;
			if ( $deptinfo["lang"] ) { $lang = $deptinfo["lang"] ; }
			include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($lang, "ln").".php" ) ;

			// if transferred, keep the same created time (status 2 is transferred)
			if ( $requestinfo["status"] != 2 )
			{
				if ( ( $opid_cookie != $requestinfo["opID"] ) && ( $requestinfo["opID"] != 1111111111 ) )
					$tooslow = 1 ;
				else
				{
					if ( $requestinfo["opID"] == 1111111111 )
					{
						Chat_update_RequestValue( $dbh, $requestid, "opID", $opid_cookie ) ;
						Chat_update_RequestLogValue( $dbh, $ces, "opID", $opid_cookie ) ;
					}
					if ( !$requestinfo["initiated"] )
					{
						Chat_update_RstatsLogValue( $dbh, $ces, $opid_cookie, "status", 1 ) ;
						Ops_put_itr_OpReqStat( $dbh, $requestinfo["deptID"], $opid_cookie, "taken", 1 ) ;
					}

					Chat_update_RequestValue( $dbh, $requestid, "created", time() ) ;
					Chat_update_RequestLogValue( $dbh, $ces, "created", time() ) ;
	
					$text = "<div class='ca'><b>$opinfo[name]</b> ".$LANG["CHAT_NOTIFY_JOINED"]."</div>" ;
					UtilChat_AppendToChatfile( "$ces.txt", $text ) ;

					if ( $requestinfo["op2op"] && ( $requestinfo["status"] != 2 ) )
					{
						$filename = $ces."-".$requestinfo["op2op"];
						UtilChat_AppendToChatfile( "$filename.text", $text ) ;

						$filename = $ces."-".$requestinfo["opID"] ;
						UtilChat_AppendToChatfile( "$filename.text", $text ) ;
					}
					else if ( $requestinfo["opID"] && ( $requestinfo["opID"] != 1111111111 ) )
					{
						// when the chat transcript was transferred back to original operator
						$filename = $ces."-0_".$requestinfo["opID"] ;
						UtilChat_AppendToChatfile( "$filename.text", "<idle_restart>".$text."<idle_restart>" ) ;
					}
				}
			}
			else
			{
				// reset the op2op as it was used for the original opID for transfer back
				Chat_update_RequestValue( $dbh, $requestid, "op2op", 0 ) ;

				$text = "<idle_restart><div class='ca'><b>$opinfo[name]</b> ".$LANG["CHAT_NOTIFY_JOINED"]."</div></idle_restart>" ;
				UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
				$max_vses = ( $t_vses > $VARS_MAX_EMBED_SESSIONS ) ? $VARS_MAX_EMBED_SESSIONS : $t_vses ;
				for ( $c = 1; $c <= $max_vses; ++$c )
				{
					$filename = $ces."-0"."_".$c ;
					UtilChat_AppendToChatfile( "$filename.text", $text ) ;
				}
			}
			Chat_update_RequestLogValue( $dbh, $ces, "status", 1 ) ;
		}
		if ( $tooslow ) { $json_data = "json_data = { \"status\": 1, \"tooslow\": 1 };" ; }
		else { $json_data = "json_data = { \"status\": 1, \"tooslow\": 0 };" ; }
	}
	else { $json_data = "json_data = { \"status\": 0 };" ; }

	if ( isset( $dbh ) && $dbh['con'] ) { database_mysql_close( $dbh ) ; }
	print "$json_data" ; exit ;
?>