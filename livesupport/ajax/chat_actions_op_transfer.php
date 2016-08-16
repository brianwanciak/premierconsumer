<?php
	$NO_CACHE = 1 ; include_once( "../inc_cache.php" ) ;
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( !isset( $_COOKIE["phplive_opID"] ) )
		$json_data = "json_data = { \"status\": -1 };" ;
	else if ( $action == "transfer" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;

		$requestid = Util_Format_Sanatize( Util_Format_GetVar( "requestid" ), "n" ) ;
		$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
		$deptname = Util_Format_Sanatize( Util_Format_GetVar( "deptname" ), "ln" ) ;
		$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
		$opname = Util_Format_Sanatize( Util_Format_GetVar( "opname" ), "ln" ) ;
		$t_vses = Util_Format_Sanatize( Util_Format_GetVar( "t_vses" ), "n" ) ;
		$opid_cookie = Util_Format_Sanatize( $_COOKIE["phplive_opID"], "n" ) ;

		$lang = $CONF["lang"] ;
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
		$opinfo = Ops_get_OpInfoByID( $dbh, $opid ) ;
		if ( $deptinfo["lang"] ) { $lang = $deptinfo["lang"] ; }
		include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($lang, "ln").".php" ) ;

		$text = "<idle_pause><div class='ca'>".$LANG["CHAT_TRANSFER"]." <b><top>$opname</top><!--opid:$opinfo[opID]--><!--mapp:$opinfo[mapp]--></b>.<div style='margin-top: 10px;'><div class='ctitle'>".$LANG["TXT_CONNECTING"]."</div></div></div></idle_pause>" ;

		Chat_update_TransferChat( $dbh, $ces, $opid_cookie, $deptid, $opid ) ;

		UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
		$max_vses = ( $t_vses > $VARS_MAX_EMBED_SESSIONS ) ? $VARS_MAX_EMBED_SESSIONS : $t_vses ;
		for ( $c = 1; $c <= $max_vses; ++$c )
		{
			$filename = $ces."-0"."_".$c ;
			UtilChat_AppendToChatfile( "$filename.text", $text ) ;
		}
		$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
		if ( $opinfo["mapp"] && is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;
			if ( isset( $mapp_array[$opid] ) ) { $arn = $mapp_array[$opid]["a"] ; $platform = $mapp_array[$opid]["p"] ; }
			if ( isset( $arn ) && $arn ) { Util_MAPP_Publish( $opid, "new_request", $platform, $arn, "[ Transfer Chat ]" ) ; }
		}
		$json_data = "json_data = { \"status\": 1 };" ;
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