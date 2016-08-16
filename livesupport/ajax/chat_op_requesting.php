<?php
	/************************************************
	/* as of v.4.5.9.1
	/*    - this new new file that combines both chat_requesting.php and chat_chatting.php
	/*      to optimize performance by reducing ping to server
	/*      NOTE: chat_requesting.php and chat_chatting.php is no longer in use as of v.4.5.9.1
	*/
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* status DB request: -1 ended by action taken, 0 waiting pick-up, 1 picked up, 2 transfer */
	$microtime = ( function_exists( "gettimeofday" ) ) ? 1 : 0 ;
	$process_start = ( $microtime ) ? microtime(true) : time() ;
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "a" ), "ln" ) ;
	$q_cces = Util_Format_Sanatize( Util_Format_GetVar( "qcc" ), "a" ) ;
	if ( !isset( $CONF['foot_log'] ) ) { $CONF['foot_log'] = "on" ; } if ( !isset( $CONF['icon_check'] ) ) { $CONF['icon_check'] = "on" ; }
	$json_status = 0 ; $json_request = $json_chatting = $json_error = "" ;
	if ( $action == "rq" )
	{
		if ( !isset( $_COOKIE["phplive_opID"] ) || !$_COOKIE["phplive_opID"] )
			$json_status = -1 ;
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_itr.php" ) ;

			$now = time() ;
			$opid = Util_Format_Sanatize( $_COOKIE["phplive_opID"], "n" ) ;
			$prev_status = Util_Format_Sanatize( Util_Format_GetVar( "ps" ), "n" ) ;
			$c_requesting = Util_Format_Sanatize( Util_Format_GetVar( "cr" ), "n" ) ;
			$traffic = Util_Format_Sanatize( Util_Format_GetVar( "t" ), "n" ) ;
			$mapp = Util_Format_Sanatize( Util_Format_GetVar( "m" ), "n" ) ;
			$q_ces = Util_Format_Sanatize( Util_Format_GetVar( "qc" ), "a" ) ;
			$q_ces_hash = Array() ;

			for ( $c = 0; $c < count( $q_ces ); ++$c ) { $ces = $q_ces[$c] ; $q_ces_hash[$ces] = 1 ; }
			if ( !( $c_requesting % $VARS_CYCLE_CLEAN ) )
			{
				$vars = Util_Format_Get_Vars( $dbh ) ;
				if ( $vars["ts_clean"] <= ( $now - ( $VARS_CYCLE_CLEAN * 2 ) ) )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;
					include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove_itr.php" ) ;
					include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_itr.php" ) ;

					Util_Format_Update_TimeStamp( $dbh, "clean", $now ) ;
					Footprints_remove_itr_Expired_U( $dbh ) ;
					Chat_remove_itr_ExpiredOp2OpRequests( $dbh ) ;
					Chat_remove_itr_OldRequests( $dbh ) ;
					Ops_update_itr_IdleOps( $dbh ) ;
				}
			}
			else if ( !( $c_requesting % ($VARS_CYCLE_CLEAN+1) ) )
			{
				$query = "UPDATE p_operators SET lastactive = $now WHERE opID = $opid" ;
				database_mysql_query( $dbh, $query ) ;
				$query = "UPDATE p_requests SET updated = $now WHERE ( opID = $opid OR op2op = $opid OR opID = 1111111111 ) AND ( status = 0 OR status = 1 OR status = 2 )" ;
				database_mysql_query( $dbh, $query ) ;
			}

			$total_traffics = ( $traffic && ( $CONF['icon_check'] == "on" ) ) ? Footprints_get_itr_TotalFootprints_U( $dbh ) : 0 ;
			$query = "SELECT * FROM p_requests WHERE ( opID = $opid OR op2op = $opid OR opID = 1111111111 ) AND ( status = 0 OR status = 1 OR status = 2 ) ORDER BY created ASC" ;
			database_mysql_query( $dbh, $query ) ;

			$requests_temp = Array() ;
			if ( $dbh[ 'ok' ] )
			{
				while ( $data = database_mysql_fetchrow( $dbh ) ) { $requests_temp[] = $data ; }
			} $requests = Array() ;
			for ( $c = 0; $c < count( $requests_temp ); ++$c )
			{
				$data = $requests_temp[$c] ;
				if ( ( $data["status"] == 2 ) && ( $data["op2op"] == $opid ) )
				{
					if ( $data["tupdated"] < ( time() - $VARS_TRANSFER_BACK ) )
						include_once( "$CONF[DOCUMENT_ROOT]/ops/inc_chat_transfer.php" ) ;
				}
				else
				{
					// sim ops filter for declined
					if ( !preg_match( "/(^|-)($opid-)/", $data["sim_ops_"] ) ) { $requests[] = $data ; }
				}
			}
			$json_status = 1 ;
			$json_request = "\"traffics\": $total_traffics, \"requests\": [  " ;
			for ( $c = 0; $c < count( $requests ); ++$c )
			{
				$req = $requests[$c] ;
				$os = $VARS_OS[$req["os"]] ;
				$browser = $VARS_BROWSER[$req["browser"]] ;
				$title = preg_replace( "/\"/", "&quot;", $req["title"] ) ;
				$question = preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", preg_replace( "/\"/", "&quot;", $req["question"] ) ) ;
				$onpage = preg_replace( "/hphp/i", "http", $req["onpage"] ) ;
				$refer_raw = preg_replace( "/hphp/i", "http", $req["refer"] ) ;
				$str_snap = ( $mapp ) ? 35 : 50 ;
				$refer_snap = ( strlen( $refer_raw ) > $str_snap ) ? substr( $refer_raw, 0, ($str_snap-5) ) . "..." : $refer_raw ;
				$custom = $req["custom"] ;

				// if status is 2 then it's a transfer call... keep original visitor name
				if ( ( $req["status"] != 2 ) && $req["op2op"] )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

					if ( $opid == $req["op2op"] ) { $opinfo = Ops_get_OpInfoByID( $dbh, $req["opID"] ) ; }
					else { $opinfo = Ops_get_OpInfoByID( $dbh, $req["op2op"] ) ; }
					$vname = $opinfo["name"] ; $vemail = $opinfo["email"] ;
				}
				else { $vname = $req["vname"] ; $vemail = $req["vemail"] ; }

				if ( ( $req["status"] == 1 ) && ( $req["opID"] == 1111111111 ) )
				{
					$req["status"] = 0 ;
					$query = "UPDATE p_requests SET status = 0 WHERE requestID = $req[requestID]" ;
					database_mysql_query( $dbh, $query ) ;
				}

				if ( isset( $q_ces_hash[$req["ces"]] ) )
					$json_request .= "{ \"rid\": $req[requestID], \"ces\": \"$req[ces]\", \"did\": $req[deptID], \"tv\": $req[t_vses], \"status\": $req[status], \"vup\": \"$req[vupdated]\" }," ;
				else
				{
					$country = strtolower( $req["country"] ) ;
					$json_request .= "{ \"rid\": $req[requestID], \"ces\": \"$req[ces]\", \"created\": \"$req[created]\", \"did\": $req[deptID], \"opid\": $req[opID], \"op2op\": $req[op2op], \"tv\": $req[t_vses], \"vname\": \"$vname\", \"status\": $req[status], \"auto_pop\": $req[auto_pop], \"initiated\": $req[initiated], \"os\": \"$os\", \"browser\": \"$browser\", \"requests\": \"$req[requests]\", \"resolution\": \"$req[resolution]\", \"vemail\": \"$vemail\", \"ip\": \"$req[ip]\", \"vis_token\": \"$req[md5_vis_]\", \"onpage\": \"$onpage\", \"title\": \"$title\", \"question\": \"$question\", \"marketid\": \"$req[marketID]\", \"refer_raw\": \"$refer_raw\", \"refer_snap\": \"$refer_snap\", \"custom\": \"$custom\", \"vup\": \"$req[vupdated]\", \"country\": \"$country\" }," ;
				}
			} $json_request = substr_replace( $json_request, "", -1 ) ;
			$json_request .= "	] " ;
		}
	}
	if ( count( $q_cces ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ;

		$ces = Util_Format_Sanatize( Util_Format_GetVar( "c" ), "ln" ) ;
		$isop = Util_Format_Sanatize( Util_Format_GetVar( "o" ), "n" ) ;
		$isop_ = Util_Format_Sanatize( Util_Format_GetVar( "o_" ), "n" ) ;
		$isop__ = Util_Format_Sanatize( Util_Format_GetVar( "o__" ), "n" ) ;
		$c_chatting = Util_Format_Sanatize( Util_Format_GetVar( "ch" ), "n" ) ;
		$q_chattings = Util_Format_Sanatize( Util_Format_GetVar( "qch" ), "a" ) ;
		$q_isop_ = Util_Format_Sanatize( Util_Format_GetVar( "qo_" ), "a" ) ;
		$q_isop__ = Util_Format_Sanatize( Util_Format_GetVar( "qo__" ), "a" ) ;
		$mapp = Util_Format_Sanatize( Util_Format_GetVar( "mp" ), "n" ) ;
		$realtime = Util_Format_Sanatize( Util_Format_GetVar( "r" ), "n" ) ;
		$fline = Util_Format_Sanatize( Util_Format_GetVar( "f" ), "n" ) ;
		$t_vses = Util_Format_Sanatize( Util_Format_GetVar( "t" ), "n" ) ;

		if ( ( $isop && $isop_ ) && ( $isop == $isop_ ) ) { $iid = $isop__ ; }
		else if ( $isop && $isop_ ) { $iid = $isop_ ; }
		else { $iid = $isop_ ; }
		$filename = $ces.$iid ;
		$istyping = ( is_file( "$CONF[TYPE_IO_DIR]/$filename.txt" ) && !$realtime ) ? 1 : 0 ;
		$json_status = 1 ;
		$json_chatting = "\"istyping\": $istyping, \"chats\": [  " ;
		for ( $c = 0; $c < count( $q_cces ); ++$c )
		{
			$ces = Util_Format_Sanatize( $q_cces[$c], "lns" ) ;
			$chatting = Util_Format_Sanatize( $q_chattings[$c], "n" ) ;

			if ( ( $isop && $q_isop_[$c] ) && ( $isop == $q_isop_[$c] ) ) { $rid = $q_isop__[$c] ; }
			else if ( $isop && $q_isop_[$c] ) { $rid = $q_isop_[$c] ; }
			else
			{
				if ( $isop ) { $rid = $isop ; }
				else { $rid = $isop."_".$t_vses ; }
			}
			$filename = $ces."-".$rid ;
			if ( !$chatting )
				$chat_file = "$CONF[CHAT_IO_DIR]/$ces.txt" ;
			else
				$chat_file = "$CONF[CHAT_IO_DIR]/$filename.text" ;

			if ( is_file( $chat_file ) )
			{
				$trans_raw = file( $chat_file ) ;
				$trans = explode( "<>", implode( "", $trans_raw ) ) ;
				$file_lines = 0 ;
				if ( !$chatting )
				{
					$file_lines = count( $trans ) - 1 ;
					$text = preg_replace( "/\"/", "&quot;", implode( "<>", array_slice( $trans, $fline, $file_lines-$fline ) ) ) ;
				}
				else
					$text = addslashes( preg_replace( "/\"/", "&quot;", implode( "<>", $trans ) ) ) ;
				$text = preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $text ) ;

				$json_chatting .= "{ \"ces\": \"$ces\", \"fline\": $file_lines, \"text\": \"$text\" }," ;

				if ( is_file( "$CONF[CHAT_IO_DIR]/$filename.text" ) )
					unlink( "$CONF[CHAT_IO_DIR]/$filename.text" ) ;
			}
			else if ( !is_file( "$CONF[CHAT_IO_DIR]/$ces.txt" ) )
			{
				$json_chatting .= "{ \"ces\": \"$ces\", \"text\": \"<div class='cl'><disconnected><d5>".$LANG["CHAT_NOTIFY_DISCONNECT"]."</div>\" }," ;
			}

			if ( !$isop && ( !( $c_chatting % $VARS_CYCLE_VUPDATE ) ) && !$realtime )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
				$requestid = Util_Format_Sanatize( Util_Format_GetVar( "rq" ), "n" ) ;
				$mobile = Util_Format_Sanatize( Util_Format_GetVar( "mo" ), "n" ) ;

				$vupdated = ( $mobile ) ? time() + $VARS_MOBILE_CHAT_BUFFER : time() ;
				Chat_update_RequestValue( $dbh, $requestid, "vupdated", $vupdated ) ;
				if ( $mapp ) { Chat_update_RequestValue( $dbh, $requestid, "updated", $vupdated ) ; }
			}
			else if ( !$isop && ( !( $c_chatting % $VARS_CYCLE_CLEAN ) ) && !$realtime )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;

				$now = time() ; $vars = Util_Format_Get_Vars( $dbh ) ;
				if ( $vars["ts_clean"] <= ( $now - ( $VARS_CYCLE_CLEAN * 2 ) ) )
				{
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;
					Util_Format_Update_TimeStamp( $dbh, "clean", $now ) ;
					Chat_remove_itr_OldRequests( $dbh ) ;
				}
			}
		} $json_chatting = substr_replace( $json_chatting, "", -1 ) ; $json_chatting .= "	] " ;
	}
	if ( isset( $dbh ) && isset( $dbh['con'] ) ) { database_mysql_close( $dbh ) ; }

	$process_end = ( $microtime ) ? microtime(true) : time() ;
	$pd = $process_end - $process_start ; if ( !$pd ) { $pd = 0.001 ; }
	$pd = str_replace( ",", ".", $pd ) ;

	if ( $json_request ) { $json_request .= ", " ; }
	if ( $json_chatting ) { $json_chatting .= ", " ; }
	$json_data = "json_data = { \"status\": $json_status, $json_request $json_chatting pd: $pd, \"error\": \"$json_error\" };" ;
	$json_data = preg_replace( "/\r\n/", "", $json_data ) ; $json_data = preg_replace( "/\t/", "", $json_data ) ; print "$json_data" ;
	exit ;
?>