<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$akey = Util_Format_Sanatize( Util_Format_GetVar( "akey" ), "ln" ) ; $jkey = Util_Format_Sanatize( Util_Format_GetVar( "jkey" ), "ln" ) ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$token = Util_Format_Sanatize( Util_Format_GetVar( "token" ), "ln" ) ;
	if ( ( !isset( $CONF['cookie'] ) || ( isset( $CONF['cookie'] ) && ( $CONF['cookie'] == "on" ) ) ) && !isset( $_COOKIE["phplive_vid"] ) ) { setcookie( "phplive_vid", Util_Format_RandomString(10), time()+(60*60*24*180), "/" ) ; }
	$image_dir = realpath( "$CONF[DOCUMENT_ROOT]/pics/icons/pixels" ) ;

	if ( $akey ) { $jkey = md5( $akey ) ; }

	if ( $jkey && isset( $CONF["API_KEY"] ) && ( $jkey == md5( $CONF["API_KEY"] ) ) )
	{
		if ( !isset( $CONF['SQLTYPE'] ) ) { $CONF['SQLTYPE'] = "SQL.php" ; }
		else if ( $CONF['SQLTYPE'] == "mysql" ) { $CONF['SQLTYPE'] = "SQL.php" ; }

		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_itr.php" ) ;

		Ops_update_itr_IdleOps( $dbh ) ;
		$total_ops = Ops_get_itr_AnyOpsOnline( $dbh, $deptid ) ;

		if ( $action == "js" )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;

			LIST( $ip, $vis_token ) = Util_IP_GetIP( $token ) ;
			$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? substr( $_SERVER["HTTP_USER_AGENT"], 0, 255 ) : "&nbsp;" ;

			$file_name = "online_$deptid.info" ;
			if ( $total_ops )
			{
				if ( !is_file( "$CONF[CHAT_IO_DIR]/$file_name" ) )
					touch( "$CONF[CHAT_IO_DIR]/$file_name" ) ;
			}
			else
			{
				if ( is_file( "$CONF[CHAT_IO_DIR]/$file_name" ) )
					unlink( "$CONF[CHAT_IO_DIR]/$file_name" ) ;
			}

			if ( $ip && preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) ) { $image_path = "$image_dir/3x3.gif" ; }
			else if ( $total_ops ) { $image_path = "$image_dir/1x1.gif" ; }
			else { $image_path = "$image_dir/2x2.gif" ; }

			// override for auto invite or current embed sessions
			$auto_invite = ( is_file( "$CONF[TYPE_IO_DIR]/$vis_token.txt" ) ) ? 1 : 0 ;

			$requestinfo = Chat_get_itr_RequestIPInfo( $dbh, $ip, $vis_token ) ;
			if ( isset( $requestinfo["ces"] ) && $total_ops )
			{
				if ( $requestinfo["status"] ) { $image_path = "$image_dir/8x8.gif" ; }
				else if ( !$requestinfo["initiated"] ) { $image_path = "$image_dir/10x10.gif" ; }
				else { $image_path = "$image_dir/4x4.gif" ; }
			}
			else if ( isset( $requestinfo["ces"] ) )
			{
				if ( $requestinfo["status"] ) { $image_path = "$image_dir/9x9.gif" ; }
				else if ( !$requestinfo["initiated"] ) { $image_path = "$image_dir/11x11.gif" ; }
				else { $image_path = "$image_dir/5x5.gif" ; }
			}
			else if ( $auto_invite && $total_ops ) { $image_path = "$image_dir/6x6.gif" ; }
			else if ( $auto_invite ) { $image_path = "$image_dir/7x7.gif" ; }

			Header( "Content-type: image/GIF" ) ;
			Header( "Content-Transfer-Encoding: binary" ) ;
			if ( !isset( $VALS['OB_CLEAN'] ) || ( $VALS['OB_CLEAN'] == 'on' ) ) { ob_clean(); flush(); }
			readfile( $image_path ) ;
		}
		else
		{
			if ( $total_ops ) { print "1" ; }
			else { print "0" ; }
		}
		database_mysql_close( $dbh ) ; exit ;
	}
	else { print "Invalid API Key." ; }
?>