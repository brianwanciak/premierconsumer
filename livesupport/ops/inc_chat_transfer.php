<?php
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$lang = $CONF["lang"] ;
	$deptinfo = Depts_get_DeptInfo( $dbh, $data["deptID"] ) ;
	if ( $deptinfo["lang"] ) { $lang = $deptinfo["lang"] ; }
	$lang = Util_Format_Sanatize($lang, "ln") ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ) {
		include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;
		$text = "<c615><restart_router><d4><div class='ca'>".$LANG["CHAT_TRANSFER_TIMEOUT"]."</div></c615>" ;
	}
	else { $text = "<c615><restart_router><d4><div class='ca'>Transfer timed out.  Connecting to previous operator...</div></c615>" ; }
	UtilChat_AppendToChatfile( "$data[ces].txt", $text ) ;

	$max_vses = ( $data["t_vses"] > $VARS_MAX_EMBED_SESSIONS ) ? $VARS_MAX_EMBED_SESSIONS : $data["t_vses"] ;
	for ( $c = 1; $c <= $max_vses; ++$c )
	{
		$filename = $data["ces"]."-0"."_".$c ;
		UtilChat_AppendToChatfile( "$filename.text", $text ) ;
	}
	$query = "UPDATE p_requests SET tupdated = 0, status = 0, vupdated = $now, opID = $data[op2op], op2op = 0 WHERE ces = '$data[ces]'" ;
	database_mysql_query( $dbh, $query ) ;
?>