<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/*
	// status json route: -1 no request, 0 same op route, 1 request accepted, 2 new op route, 10 leave a message
	*/
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "a" ), "ln" ) ;

	if ( $action == "routing" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;

		$ces = Util_Format_Sanatize( Util_Format_GetVar( "c" ), "ln" ) ;
		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "d" ), "n" ) ;
		$c_routing = Util_Format_Sanatize( Util_Format_GetVar( "cr" ), "n" ) ;
		$rtype = Util_Format_Sanatize( Util_Format_GetVar( "r" ), "n" ) ;
		$rtime = Util_Format_Sanatize( Util_Format_GetVar( "rt" ), "n" ) ;
		$rloop = Util_Format_Sanatize( Util_Format_GetVar( "rl" ), "n" ) ;
		$loop = Util_Format_Sanatize( Util_Format_GetVar( "l" ), "n" ) ;

		$requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $ces ) ;
		if ( !isset( $requestinfo["requestID"] ) )
			$json_data = "json_data = { \"status\": 10 };" ;
		else
		{
			if ( $requestinfo["status"] && ( $requestinfo["opID"] != 1111111111 ) )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
				$opinfo = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ; $profile_src = "" ;
				if ( $opinfo["pic"] && ( isset( $VALS['PROFILE'] ) && ( $VALS['PROFILE'] == 1 ) ) )
				{
					if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
					else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
					$profile_src = Util_Upload_GetLogo( "profile", $opinfo["opID"] ) ;
				}
				$json_data = "json_data = { \"status\": 1, \"status_request\": $requestinfo[status], \"requestid\": $requestinfo[requestID], \"initiated\": $requestinfo[initiated], \"name\": \"$opinfo[name]\", \"rate\": $opinfo[rate], \"deptid\": $deptid, \"opid\": $opinfo[opID], \"email\": \"$opinfo[email]\", \"profile\": \"$profile_src\", \"mapp\": \"$opinfo[mapp]\" };" ;
			}
			else
			{
				// vupdated is used for routing UNTIL chat is accepted then it is used
				// for visitor's callback updated time
				$rupdated = $requestinfo["vupdated"] + $rtime ;
				if ( time() <= $rupdated )
					$json_data = "json_data = { \"status\": 0 };" ;
				else
				{
					// no looping for simultaneous routing
					if ( $requestinfo["opID"] == 1111111111 )
					{
						include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;
						include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

						$sim_ops = Util_Format_ExplodeString( "-", $requestinfo["sim_ops"] ) ;
						$sim_ops_ = Util_Format_ExplodeString( "-", $requestinfo["sim_ops_"] ) ;
						for ( $c = 0; $c < count( $sim_ops ); ++$c )
						{
							$found = 0 ;
							for ( $c2 = 0; $c2 < count( $sim_ops_ ); ++$c2 )
							{
								if ( $sim_ops[$c] == $sim_ops_[$c2] )
									$found = 1 ;
							}
							if ( !$found ) { Ops_put_itr_OpReqStat( $dbh, $requestinfo["deptID"], $sim_ops[$c], "declined", 1 ) ; }
						}

						// leave a message
						Ops_put_itr_OpReqStat( $dbh, $deptid, 0, "message", 1 ) ;
						Chat_remove_itr_RequestByCes( $dbh, $requestinfo["ces"] ) ;
						$json_data = "json_data = { \"status\": 10 };" ;
					}
					else
					{
						include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update_itr.php" ) ;
						include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
						include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;

						if ( $loop == 1 ) { Ops_put_itr_OpReqStat( $dbh, $deptid, $requestinfo["opID"], "declined", 1 ) ; }
						$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
						$opinfo_next = Ops_get_NextRequestOp( $dbh, $deptid, $rtype, $requestinfo["rstring"] ) ;
						if ( isset( $opinfo_next["opID"] ) )
						{
							$opid = $opinfo_next["opID"] ;
							$mapp_opid = ( $opinfo_next["mapp"] && is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) ) ? $opid : 0 ;
							Chat_update_itr_RouteChat( $dbh, $requestinfo["requestID"], $requestinfo["ces"], $opinfo_next["opID"], $opinfo_next["sms"],  " $requestinfo[rstring]-$opinfo_next[opID]" ) ;
							if ( ( $opinfo_next["sms"] == 1 ) || $mapp_opid ) { include_once( "$CONF[DOCUMENT_ROOT]/ajax/inc_request_sms.php" ) ; }

							// don't log trasfer chats on total stats of requests
							if ( ( $requestinfo["status"] != 2 ) && ( $loop == 1 ) )
							{
								include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put.php" ) ;
								if ( !$c_routing )
									Ops_put_itr_OpReqStat( $dbh, $deptid, $opinfo_next["opID"], "requests", 1 ) ;
								else
									Ops_put_itr_OpReqStat( $dbh, 0, $opinfo_next["opID"], "requests", 1 ) ;
								Chat_put_RstatsLog( $dbh, $requestinfo["ces"], 0, $deptid, $opinfo_next["opID"] ) ;
							}
							$json_data = "json_data = { \"status\": 2 };" ;
						}
						else
						{
							if ( $loop < $rloop )
							{
								Chat_update_itr_ResetChat( $dbh, $requestinfo["requestID"], $ces ) ;
								$json_data = "json_data = { \"status\": 2, \"reset\": 1 };" ;
							}
							else
							{
								include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put.php" ) ;
								include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

								// on stats db the leave a message is not op specific, just use the current opID to track
								// requests that went to leave a messge
								Ops_put_itr_OpReqStat( $dbh, $deptid, 0, "message", 1 ) ;
								Chat_remove_itr_RequestByCes( $dbh, $requestinfo["ces"] ) ;
								$json_data = "json_data = { \"status\": 10 };" ;
							}
						}
					}
				}
			}
		}
	}

	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

	print "$json_data" ;
	exit ;
?>