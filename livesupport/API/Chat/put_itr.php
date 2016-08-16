<?php
	if ( defined( 'API_Chat_put_itr' ) ) { return ; }
	define( 'API_Chat_put_itr', true ) ;

	FUNCTION Chat_put_itr_Transcript( &$dbh,
					$ces,
					$status,
					$created,
					$ended,
					$deptid,
					$opid,
					$initiated,
					$op2op,
					$rating,
					$fsize,
					$vname,
					$vemail,
					$ip,
					$vis_token,
					$question,
					$formatted,
					$plain,
					$deptinfo,
					$deptvars )
	{
		if ( ( $ces == "" ) || ( $deptid == "" ) || ( $opid == "" ) || ( $fsize == "" )
			|| ( $ended == "" ) || ( $vname == "" ) || ( $ip == "" )
			|| ( $vis_token == "" ) || ( $formatted == "" ) || ( $plain == "" ) )
			return false ;

		global $CONF ; global $VALS ;
		global $VARS_EXPIRED_TFOOT ;
		if ( !defined( 'API_Util_Email' ) )
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Email.php" ) ;

		LIST( $ces, $status, $created, $ended, $deptid, $opid, $initiated, $op2op, $rating, $fsize, $vname, $vemail, $ip, $vis_token, $question, $formatted, $plain ) = database_mysql_quote( $dbh, $ces, $status, $created, $ended, $deptid, $opid, $initiated, $op2op, $rating, $fsize, $vname, $vemail, $ip, $vis_token, $question, $formatted, $plain ) ;

		$query = "SELECT * FROM p_transcripts WHERE ces = '$ces' LIMIT 1" ;
		database_mysql_query( $dbh, $query ) ;
		$transcript = database_mysql_fetchrow( $dbh ) ;

		$trans_exists = 1 ;
		if ( !isset( $transcript["ces"] ) )
		{
			$trans_exists = 0 ;
			if ( !defined( 'API_Chat_get' ) )
				include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;

			// get initiated value from log because during transfer it resets the initiate flag
			$requestinfo_log = Chat_get_RequestHistCesInfo( $dbh, $ces ) ;
			$initiated = ( isset( $requestinfo_log["initiated"] ) ) ? $requestinfo_log["initiated"] : $initiated ;

			$query = "INSERT INTO p_transcripts VALUES ( '$ces', $created, $ended, $deptid, $opid, $initiated, $op2op, $rating, 0, $fsize, 0, '$vname', '$vemail', '$ip', '$vis_token', '$question', '$formatted', '$plain' )" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "UPDATE p_req_log SET ended = $ended WHERE ces = '$ces'" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "UPDATE p_requests SET ended = $ended WHERE ces = '$ces'" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "UPDATE p_refer SET archive = 1 WHERE md5_vis = '$vis_token' AND archive = 0" ;
			database_mysql_query( $dbh, $query ) ;

			$archive_expired = time() - (60*60*24*$VARS_EXPIRED_TFOOT) ;
			$query = "DELETE FROM p_footprints WHERE created < $archive_expired AND archive = 1" ;
			database_mysql_query( $dbh, $query ) ;
			$query = "UPDATE p_footprints SET archive = 1 WHERE md5_vis = '$vis_token' AND archive = 0" ; // to retain transcript data for few months
			database_mysql_query( $dbh, $query ) ;
		}
		else if ( $created == "null" ) { $formatted = $transcript["formatted"] ; }
		else { $formatted = false ; }
		if ( is_file( "$CONF[CHAT_IO_DIR]/$ces.txt" ) ) { unlink( "$CONF[CHAT_IO_DIR]/$ces.txt" ) ; }

		if ( $dbh['ok'] && $formatted && isset( $deptinfo["temail"] ) )
		{
			if ( $status && ( $deptinfo["temail"] || $deptinfo["temaild"] || $deptinfo["emailt"] ) && ( $vemail != "null" ) )
			{
				if ( $deptinfo["smtp"] )
				{
					if ( !defined( 'API_Util_Functions_itr' ) )
						include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
					$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;
				}

				$query = "SELECT * FROM p_operators WHERE opID = '$opid' LIMIT 1" ;
				database_mysql_query( $dbh, $query ) ;
				$opinfo = database_mysql_fetchrow( $dbh ) ;

				$lang = $CONF["lang"] ;
				if ( $deptinfo["lang"] ) { $lang = $deptinfo["lang"] ; }
				include( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($lang, "ln").".php" ) ;

				$subject_visitor = $LANG["TRANSCRIPT_SUBJECT"]." $opinfo[name]" ;
				$subject_department = $LANG["TRANSCRIPT_SUBJECT"]." $vname <$vemail>" ;
				$message_footer = "Visitor: $vname $vemail\r\nOperator: $opinfo[name] $opinfo[email]\r\n\r\nChat ID: $ces" ;

				$message_trans = preg_replace( "/%%visitor%%/", $vname, $deptinfo["msg_email"] ) ;
				$message_trans = preg_replace( "/%%operator%%/", $opinfo["name"], $message_trans ) ;
				$message_trans = preg_replace( "/%%op_email%%/", $opinfo["email"], $message_trans ) ;
				$message_trans = preg_replace( "/%%transcript%%/", preg_replace( "/\\$/", "-dollar-", stripslashes( $formatted ) ), $message_trans ) ;

				if ( isset( $deptvars["trans_f_dept"] ) && $deptvars["trans_f_dept"] ) { $from_name = $deptinfo["name"] ; $from_email = $deptinfo["email"] ; }
				else { $from_name = $opinfo["name"] ; $from_email = $opinfo["email"] ; }
				if ( ( $created == "null" ) && $vemail )
					Util_Email_SendEmail( $from_name, $from_email, $vname, $vemail, $subject_visitor, $message_trans."\r\n-------------------------------------\r\n$message_footer\r\n-------------------------------------\r\n", "trans" ) ;
				if ( !$trans_exists )
				{
					$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
					if ( $opinfo["mapp"] && is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) )
					{
						include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;
						if ( isset( $mapp_array[$opid] ) ) { $arn = $mapp_array[$opid]["a"] ; $platform = $mapp_array[$opid]["p"] ; }
						if ( isset( $arn ) && $arn ) { Util_MAPP_Publish( $opid, "new_text", $platform, $arn, "$vname [".$LANG["TXT_DISCONNECT"]."]" ) ; }
					}

					if ( $deptinfo["emailt"] && $deptinfo["emailt_bcc"] )
					{
						Util_Email_SendEmail( $from_name, $from_email, $vname, $vemail, $subject_visitor, $message_trans."\r\n-------------------------------------\r\n$message_footer\r\n-------------------------------------\r\n", "trans", Array($deptinfo["emailt"]) ) ;
					}
					else if ( $deptinfo["emailt"] )
					{
						Util_Email_SendEmail( $from_name, $from_email, $deptinfo["name"], $deptinfo["emailt"], $subject_department, $message_trans."\r\n-------------------------------------\r\n$message_footer\r\n-------------------------------------\r\n", "trans" ) ;
					}

					if ( $deptinfo["temaild"] )
					{
						Util_Email_SendEmail( $from_name, $from_email, $deptinfo["name"], $deptinfo["email"], $subject_department, $message_trans."\r\n-------------------------------------\r\n$message_footer\r\n-------------------------------------\r\n", "trans" ) ;
					}
				}
			}
			return true ;
		}
		else if ( $trans_exists ) { return true ; }
		return false ;
	}
?>