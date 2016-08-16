<?php
	if ( defined( 'API_Chat_remove_itr' ) ) { return ; }
	define( 'API_Chat_remove_itr', true ) ;

	FUNCTION Chat_remove_itr_RequestByCes( &$dbh,
						$ces )
	{
		if ( $ces == "" )
			return false ;

		LIST( $ces ) = database_mysql_quote( $dbh, $ces ) ;

		$query = "DELETE FROM p_requests WHERE ces = '$ces'" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}

	FUNCTION Chat_remove_itr_OldRequests( &$dbh )
	{
		global $CONF ;
		global $VALS ;
		global $VARS_EXPIRED_REQS ;
		global $VARS_MAX_EMBED_SESSIONS ;
		$now = time() ;
		$expired = $now - $VARS_EXPIRED_REQS ;

		$expired_requests = $expired_requests_hash = $file_ces = Array() ;
		$dir_files = glob( $CONF["CHAT_IO_DIR"].'/*.txt', GLOB_NOSORT ) ;
		$total_dir_files = count( $dir_files ) ;
		if ( $total_dir_files )
		{
			for ( $c = 0; $c < $total_dir_files; ++$c )
			{
				$ces = str_replace( "$CONF[CHAT_IO_DIR]", "", $dir_files[$c] ) ;
				$ces = preg_replace( "/[\\/]|(.txt)/", "", $ces ) ;
				if ( $ces ) { $file_ces[$ces] = 1 ; }
			}
		}

		$query = "SELECT SQL_NO_CACHE * FROM p_requests WHERE ( updated < $expired OR vupdated < $expired ) AND op2op = 0" ;
		database_mysql_query( $dbh, $query ) ;
		if ( $dbh[ 'ok' ] )
		{
			if ( database_mysql_nresults( $dbh ) || $total_dir_files )
			{
				while( $data = database_mysql_fetchrow( $dbh ) )
				{
					$expired_requests[] = $data ;
					$expired_requests_hash[$data["ces"]] = 1 ;
				}

				if ( !defined( 'API_Chat_get' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;
				if ( !defined( 'API_Chat_get_itr' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;

				foreach ( $file_ces as $this_ces => $the_flag )
				{
					if ( !isset( $expired_requests_hash[$this_ces] ) )
					{
						$requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $this_ces ) ;
						if ( !isset( $requestinfo["ces"] ) )
						{ $expired_requests[] = Chat_get_RequestHistCesInfo( $dbh, $this_ces ) ; }
					}
				}
				if ( count( $expired_requests ) )
				{
					if ( !defined( 'API_Chat_Util' ) )
					include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
					if ( !defined( 'API_Chat_put' ) )
						include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put_itr.php" ) ;
					if ( !defined( 'API_Chat_remove_itr' ) )
						include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;
					if ( !defined( 'API_Depts_get' ) )
						include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

					$lang = $CONF["lang"] ; $deptinfo = Array() ; $deptvars = Array() ; $prev_deptid = 1111111111 ; // start things off
					for ( $c = 0; $c < count( $expired_requests ); ++$c )
					{
						$request = $expired_requests[$c] ;
						$ces = $request["ces"] ; $ip = $request["ip"] ; $deptid = $request["deptID"] ;
						$vis_token = isset( $request["md5_vis_"] ) ? $request["md5_vis_"] : $request["md5_vis"] ;
						$trans_file = "$ces.txt" ;

						if ( $prev_deptid != $deptid )
						{
							$prev_deptid = $deptid ;
							$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
							$deptvars = Depts_get_DeptVars( $dbh, $deptid ) ;
							if ( $deptinfo["lang"] ) { $lang = $deptinfo["lang"] ; }
							include( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($lang, "ln").".php" ) ;
						}

						$query = "UPDATE p_footprints_u SET chatting = 0 WHERE md5_vis = '$vis_token'" ;
						database_mysql_query( $dbh, $query ) ;

						if ( is_file( "$CONF[CHAT_IO_DIR]/$trans_file" ) )
						{
							$string_disconnect = "<div class='cl'><disconnected><d6>".$LANG["CHAT_NOTIFY_DISCONNECT"]."</div>" ;
							UtilChat_AppendToChatfile( $trans_file, $string_disconnect ) ;
							$filename_op = $ces."-$request[opID]" ; UtilChat_AppendToChatfile( "$filename_op.text", $string_disconnect ) ;
							if ( isset( $request["t_vses"] ) ) { $max_vses = ( $request["t_vses"] > $VARS_MAX_EMBED_SESSIONS ) ? $VARS_MAX_EMBED_SESSIONS : $request["t_vses"] ; }
							else { $max_vses = $VARS_MAX_EMBED_SESSIONS ; }
							for ( $c2 = 1; $c2 <= $max_vses; ++$c2 )
							{
								$filename_vis = $ces."-0"."_".$c2 ;
								UtilChat_AppendToChatfile( "$filename_vis.text", $string_disconnect ) ;
							}

							$output = UtilChat_ExportChat( $trans_file ) ;
							if ( isset( $output[0] ) )
							{
								$formatted = $output[0] ; $plain = $output[1] ;

								$fsize = strlen( $formatted ) ;
								$requestinfo = Chat_get_RequestHistCesInfo( $dbh, $ces ) ;

								if ( $requestinfo["status"] )
								{
									if ( Chat_put_itr_Transcript( $dbh, $ces, $requestinfo["status"], $requestinfo["created"], $now, $requestinfo["deptID"], $requestinfo["opID"], $requestinfo["initiated"], $requestinfo["op2op"], 0, $fsize, $requestinfo["vname"], $requestinfo["vemail"], $requestinfo["ip"], $vis_token, $requestinfo["question"], $formatted, $plain, $deptinfo, $deptvars ) )
										Chat_remove_itr_RequestByCes( $dbh, $ces ) ;
								}
								else if ( is_file( "$CONF[CHAT_IO_DIR]/$ces.txt" ) ) { unlink( "$CONF[CHAT_IO_DIR]/$ces.txt" ) ; }
							}
							if ( $vis_token && is_file( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ) { unlink( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ; }
						}
					}
					$query = "DELETE FROM p_requests WHERE ( updated < $expired OR vupdated < $expired ) AND op2op = 0" ;
					database_mysql_query( $dbh, $query ) ;
				}
			}

			$dir_files = glob( $CONF["CHAT_IO_DIR"].'/*.text', GLOB_NOSORT ) ;
			$total_dir_files = count( $dir_files ) ;
			if ( $total_dir_files )
			{
				for ( $c = 0; $c < $total_dir_files; ++$c )
				{
					$modtime = filemtime( $dir_files[$c] ) ;
					if ( $modtime < ( $now - (60*15) ) )
					{
						if ( $dir_files[$c] && is_file( $dir_files[$c] ) ) { unlink( $dir_files[$c] ) ; }
					}
				}
			}
		} return true ;
	}

	FUNCTION Chat_remove_itr_ExpiredOp2OpRequests( &$dbh )
	{
		global $VARS_EXPIRED_OP2OP ;
		$expired_op2op = time() - $VARS_EXPIRED_OP2OP ;

		$query = "DELETE FROM p_requests WHERE updated < $expired_op2op AND vupdated < $expired_op2op AND op2op <> 0" ;
		database_mysql_query( $dbh, $query ) ;

		return true ;
	}
?>
