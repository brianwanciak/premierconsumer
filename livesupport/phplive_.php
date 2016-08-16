<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	include_once( "./web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Vars/get.php" ) ;

	$postembed = Util_Format_Sanatize( Util_Format_GetVar( "postembed" ), "n" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$opid = Util_Format_Sanatize( Util_Format_GetVar( "opid" ), "n" ) ;
	$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;
	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "lns" ) ;
	$auto_pop = Util_Format_Sanatize( Util_Format_GetVar( "auto_pop" ), "n" ) ;
	$popout = Util_Format_Sanatize( Util_Format_GetVar( "popout" ), "n" ) ;
	$vname = Util_Format_Sanatize( Util_Format_GetVar( "vname" ), "ln" ) ;
	$vemail = Util_Format_Sanatize( Util_Format_GetVar( "vemail" ), "e" ) ;
	$vsubject = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "vsubject" ), "htmltags" ) ) ;
	$question = Util_Format_Sanatize( Util_Format_GetVar( "vquestion" ), "htmltags" ) ;
	$onpage = rawurldecode( Util_Format_Sanatize( Util_Format_GetVar( "onpage" ), "url" ) ) ;  $onpage = ( $onpage ) ? $onpage : "" ;
	$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "title" ) ; $title = ( $title ) ? $title : "" ;
	$resolution = Util_Format_Sanatize( Util_Format_GetVar( "win_dim" ), "ln" ) ;
	$widget = Util_Format_Sanatize( Util_Format_GetVar( "widget" ), "n" ) ;
	$embed = Util_Format_Sanatize( Util_Format_GetVar( "embed" ), "n" ) ;
	$custom = Util_Format_Sanatize( Util_Format_GetVar( "custom" ), "url" ) ;
	$token = Util_Format_Sanatize( Util_Format_GetVar( "token" ), "ln" ) ;
	if ( ( !isset( $CONF['cookie'] ) || ( isset( $CONF['cookie'] ) && ( $CONF['cookie'] == "on" ) ) ) && !isset( $_COOKIE["phplive_vid"] ) ) { setcookie( "phplive_vid", Util_Format_RandomString(10), time()+(60*60*24*180), "/" ) ; }
	$dept_themes = ( isset( $VALS["THEMES"] ) ) ? unserialize( $VALS["THEMES"] ) : Array() ;
	if ( !$theme && isset( $dept_themes[$deptid] ) && $deptid ) { $theme = $dept_themes[$deptid] ; }
	else if ( !$theme ) { $theme = $CONF["THEME"] ; }
	else if ( $theme && !is_file( "$CONF[DOCUMENT_ROOT]/themes/$theme/style.css" ) ) { $theme = $CONF["THEME"] ; }

	$now = time() ; $lang = ( isset( $CONF["lang"] ) ) ? $CONF["lang"] : "english" ; $dev = 0 ;
	$salt = md5( $CONF["SALT"] ) ;
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $ip, $vis_token ) = Util_IP_GetIP( $token ) ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;

	$vemail = ( !$vemail ) ? "null" : $vemail ;
	if ( preg_match( "/$ip/", $VALS["CHAT_SPAM_IPS"] ) )
	{
		$custom_temp = rawurlencode( $custom ) ;
		$url_redirect = "phplive_m.php?ces=$ces&deptid=$deptid&theme=$theme&embed=$embed&vname=$vname&vemail=$vemail&vquestion=&onpage=".urlencode( Util_Format_URL( $onpage ) )."&custom=$custom_temp&" ;
		if ( $postembed )
		{
			$url_redirect = rawurlencode( $url_redirect ) ;
			$json_data = "json_data = { \"status\": 0, \"url_redirect\": \"$url_redirect\" };" ;
			print $json_data ; exit ;
		} else { HEADER( "location: $url_redirect" ) ; }
	}
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

	/* fetch direct additonal check BEGIN */
	if ( $opid )
	{
		$opinfo_next = Ops_get_OpInfoByID( $dbh, $opid ) ;
		if ( !isset( $opinfo_next["opID"] ) ) { $opid = 0 ; unset( $opinfo_next ) ; }
	}
	if ( $deptid || $opid )
	{
		if ( isset( $opinfo_next ) )
		{
			$op_depts = Ops_get_OpDepts( $dbh, $opinfo_next["opID"] ) ;
			$deptid_found = 0 ;
			for ( $c = 0; $c < count( $op_depts ); ++$c )
			{
				if ( ( $op_depts[$c]["deptID"] == $deptid ) && $op_depts[$c]["status"] ) { $deptid_found = 1 ; break ; }
			}
			if ( !$deptid_found )
			{
				for ( $c = 0; $c < count( $op_depts ); ++$c )
				{
					if ( $op_depts[$c]["status"] ) { $deptid = $op_depts[$c]["deptID"] ; $deptid_found = 1 ; break ; }
				}
				if ( !$deptid_found ) { $opid = 0 ; unset( $opinfo_next ) ; }
			}
		}
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
		if ( !isset( $deptinfo["deptID"] ) ) { $deptid = 0 ; }
	}
	/* fetch direct additonal check END */
	if ( $deptid && $vname )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Email.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_ext.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/mapp/API/Util_MAPP.php" ) ;

		if ( $popout ) { $requestinfo = Chat_get_itr_RequestCesInfo( $dbh, $ces ) ; }
		else if ( $embed ) { $requestinfo = Chat_get_itr_RequestIPInfo( $dbh, $ip, $vis_token ) ; }
		if ( $deptinfo["smtp"] )
		{
			$smtp_array = unserialize( Util_Functions_itr_Decrypt( $CONF["SALT"], $deptinfo["smtp"] ) ) ;
		}
		if ( $deptinfo["lang"] ) { $CONF["lang"] = $deptinfo["lang"] ; }
		$lang = Util_Format_Sanatize( $CONF["lang"], "ln" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;

		if ( isset( $requestinfo["requestID"] ) )
		{
			$vname = $requestinfo["vname"] ;
			$vemail = $requestinfo["vemail"] ;
			$question = $requestinfo["question"] ;
		}
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions_ext.php" ) ;
			$ces = Util_Functions_ext_GenerateCes( $dbh ) ;
			$vname_orig = $vname ;
			$question = preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", preg_replace( "/\"/", "&quot;", $question ) ) ;
			$question_sms = preg_replace( "/<br>/", " ", $question ) ;
			$question_sms = ( strlen( $question_sms ) > 100 ) ? substr( $question_sms, 0, 100 ) . "..." : $question_sms ;
		}

		$sim_ops = "" ;
		$mapp_array = ( isset( $VALS["MAPP"] ) && $VALS["MAPP"] ) ? unserialize( $VALS["MAPP"] ) : Array() ;
		if ( ( $deptinfo["rtype"] < 3 ) || $opid )
		{
			if ( isset( $requestinfo["opID"] ) && !isset( $opinfo_next ) ) { $opinfo_next = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ; }
			else if ( !isset( $opinfo_next ) ) { $opinfo_next = Ops_get_NextRequestOp( $dbh, $deptid, $deptinfo["rtype"], "" ) ; }
			if ( !isset( $opinfo_next["opID"] ) )
			{
				database_mysql_close( $dbh ) ;
				$custom_temp = rawurlencode( $custom ) ;
				$url_redirect = "phplive_m.php?ces=$ces&chat=1&pause=1&deptid=$deptid&token=$token&theme=$theme&embed=$embed&vname=$vname&vemail=$vemail&vquestion=".rawurlencode($question)."&title=".rawurlencode($title)."&onpage=".rawurlencode( Util_Format_URL( $onpage ) )."&custom=$custom_temp&" ;
				if ( $postembed )
				{
					$url_redirect = rawurlencode( $url_redirect ) ;
					$json_data = "json_data = { \"status\": 0, \"url_redirect\": \"$url_redirect\" };" ;
					print $json_data ; exit ;
				}
				else { HEADER( "location: $url_redirect" ) ; }
			}
			else if ( !$opid ) { $opid = $opinfo_next["opID"] ; }

			if ( !isset( $requestinfo["opID"] ) )
			{
				if ( $opinfo_next["mapp"] )
				{
					if ( is_file( "$CONF[TYPE_IO_DIR]/$opid.mapp" ) )
					{
						$question_mapp = ( $question ) ? $question : "[ $LANG[TXT_LIVECHAT] ]" ;
						if ( isset( $mapp_array[$opid] ) ) { $arn = $mapp_array[$opid]["a"] ; $platform = $mapp_array[$opid]["p"] ; }
						if ( isset( $arn ) && $arn ) { Util_MAPP_Publish( $opid, "new_request", $platform, $arn, $question_mapp ) ; }
					}
				}
				else if ( !$opinfo_next["mapp"] && ( $opinfo_next["sms"] == 1 ) )
					Util_Email_SendEmail( $opinfo_next["name"], $opinfo_next["email"], $vname_orig, base64_decode( $opinfo_next["smsnum"] ), "Chat Request", $question_sms, "sms" ) ;
			}
		}
		else
		{
			$opid = 1111111111 ; $sim_ops = "" ;
			$opinfo_next = Array( "rate" => 0, "sms" => 0 ) ;
			if ( !isset( $requestinfo["requestID"] ) )
			{
				$sim_operators = Depts_get_DeptOps( $dbh, $deptid, 1 ) ;
				$total_sim_ops = count( $sim_operators ) ;
				for ( $c = 0; $c < $total_sim_ops; ++$c )
				{
					$operator = $sim_operators[$c] ;
					$sim_opid = $operator["opID"] ;
					$sim_ops .= "$sim_opid-" ;
					if ( !isset( $requestinfo["opID"] ) )
					{
						if ( $operator["mapp"] )
						{
							if ( is_file( "$CONF[TYPE_IO_DIR]/$sim_opid.mapp" ) )
							{
								$question_mapp = ( $question ) ? $question : "[ $LANG[TXT_LIVECHAT] ]" ;
								if ( isset( $mapp_array[$sim_opid] ) ) { $arn = $mapp_array[$sim_opid]["a"] ; $platform = $mapp_array[$sim_opid]["p"] ; }
								if ( isset( $arn ) && $arn ) { Util_MAPP_Publish( $sim_opid, "new_request", $platform, $arn, $question_mapp ) ; }
							}
						}
						else if ( $operator["sms"] == 1 )
							Util_Email_SendEmail( $operator["name"], $operator["email"], $vname_orig, base64_decode( $operator["smsnum"] ), "Chat Request", $question_sms, "sms" ) ;
					}
				}
			}
		}

		$vses = $t_vses = 1 ; $connected = $created_embed = 0 ; $connected_trans = $text = "" ;
		if ( isset( $requestinfo["requestID"] ) )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;

			$requestid = $requestinfo["requestID"] ;
			$t_vses = $requestinfo["t_vses"] ;
			$vses = $t_vses + 1 ;
			Chat_update_RequestValue( $dbh, $requestid, "t_vses", $vses ) ;

			if ( $vses > $VARS_MAX_EMBED_SESSIONS ) { $vses = $vses - $VARS_MAX_EMBED_SESSIONS ; }
			function get_diff( $x, $y ) { return $x-$y ; }
			$diff = get_diff( $vses, $VARS_MAX_EMBED_SESSIONS ) ;
			while( $diff > 0 )
			{
				$vses = $diff ;
				$diff = get_diff( $diff, $VARS_MAX_EMBED_SESSIONS ) ;
			}

			if ( $requestinfo["status"] && is_file( "$CONF[CHAT_IO_DIR]/$ces.txt" ) )
			{
				$connected = 1 ;
				$created_embed = $requestinfo["created"] ;
	
				$rid = "0_$vses" ;
				$filename = $ces."-".$rid ;

				if ( is_file( "$CONF[CHAT_IO_DIR]/$filename.text" ) )
					unlink( "$CONF[CHAT_IO_DIR]/$filename.text" ) ;

				$chat_file = "$CONF[CHAT_IO_DIR]/$ces.txt" ;
				$text = "" ;
				if ( is_file( $chat_file ) )
				{
					$trans_raw = file( $chat_file ) ;
					$text = addslashes( preg_replace( "/\"/", "&quot;", $trans_raw[0] ) ) ;
					$text = preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $text ) ;
				}
			}
		}
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;

			$concurrent_requests = Chat_get_IPTotalRequests( $dbh, $ip, "requests" ) ;
			if ( $concurrent_requests < $VARS_MAX_IP_CHAT_REQUESTS )
			{
				$referinfo = Footprints_get_IPRefer( $dbh, $vis_token ) ;
				$marketid = ( isset( $referinfo["marketID"] ) && $referinfo["marketID"] ) ? $referinfo["marketID"] : 0 ;

				$refer = ( isset( $referinfo["refer"] ) ) ? $referinfo["refer"] : "" ;
				$vis_token_embed = ( $embed ) ? $vis_token : "" ;

				$requestid = Chat_put_Request( $dbh, $deptid, $opid, 0, $widget, 0, $vses, $os, $browser, $ces, $resolution, $vname, $vemail, $ip, $vis_token_embed, $vis_token, $onpage, $title, $question, $marketid, $refer, $custom, $auto_pop, $sim_ops ) ;
			}
			else
			{
				database_mysql_close( $dbh ) ;
				$custom_temp = rawurlencode( $custom ) ;
				$url_redirect = "phplive_m.php?ces=$ces&chat=1&pause=1&deptid=$deptid&theme=$theme&embed=$embed&vname=$vname&vemail=$vemail&vquestion=".rawurlencode( $question )."&onpage=".rawurlencode( Util_Format_URL( $onpage ) )."&custom=$custom_temp&" ;
				if ( $postembed )
				{
					$url_redirect = rawurlencode( $url_redirect ) ;
					$json_data = "json_data = { \"status\": 0, \"url_redirect\": \"$url_redirect\" };" ;
					print $json_data ; exit ;
				} else { HEADER( "location: $url_redirect" ) ; }
			}
		}

		if ( $requestid )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/put_itr.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Marquee/get.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/update.php" ) ;
			include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/update.php" ) ;

			Footprints_update_FootprintUniqueValue( $dbh, $vis_token, "chatting", 1 ) ;
			if ( isset( $requestinfo["requestID"] ) && !$requestinfo["status"] && $requestinfo["initiated"] )
			{
				$opid = $requestinfo["opID"] ;
				Chat_update_RequestValue( $dbh, $requestinfo["requestID"], "status", 1 ) ;
				Chat_update_RequestLogValue( $dbh, $requestinfo["ces"], "status", 1 ) ;
				Ops_put_itr_OpReqStat( $dbh, $deptid, $opid, "initiated_", 1 ) ;
				$filename = $ces."-".$opid ;
				$text = "<widget><idle_start><div class='ca'><b>$vname</b> ".$LANG["CHAT_NOTIFY_JOINED"]."</div></idle_start></widget>" ;
				UtilChat_AppendToChatfile( "$ces.txt", $text ) ;
				if ( $opid != 1111111111 ) { UtilChat_AppendToChatfile( "$filename.text", $text ) ; }
			}
			else if ( !isset( $requestinfo["requestID"] ) )
			{
				include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/put.php" ) ;
				IPs_put_IP( $dbh, $ip, $vis_token, $deptid, 0, 1, 0, 1, 0, 0, $now ) ;
				Chat_put_ReqLog( $dbh, $requestid ) ;
				Footprints_update_FootprintUniqueValue( $dbh, $vis_token, "requests", "requests + 1" ) ;

				if ( !isset( $CONF["cookie"] ) || ( $CONF["cookie"] == "on" ) )
				{
					if ( $vname_orig != "null" ) { setcookie( "phplive_vname", $vname_orig, $now+60*60*24*365 ) ; }
					if ( $vemail != "null" ) { setcookie( "phplive_vemail", $vemail, $now+60*60*24*365 ) ; }
				}
				if ( $deptinfo["rtype"] < 3 )
				{
					Chat_put_RstatsLog( $dbh, $ces, 0, $deptid, $opid ) ;
					Ops_put_itr_OpReqStat( $dbh, $deptid, $opid, "requests", 1 ) ;
				}
				else
				{
					Ops_put_itr_OpReqStat( $dbh, $deptid, 0, "requests", 1 ) ;
					for ( $c = 0; $c < count( $sim_operators ); ++$c )
					{
						$operator = $sim_operators[$c] ;
						Chat_put_RstatsLog( $dbh, $ces, 0, $deptid, $operator["opID"] ) ;
						Ops_put_itr_OpReqStat( $dbh, 0, $operator["opID"], "requests", 1 ) ;
					}
				}
				$text = ( $question ) ? "<div class='ca'><i>".$question."</i></div>" : "" ;
				UtilChat_AppendToChatfile( "$ces.txt", $text ) ;

				if ( $postembed )
				{
					$json_data = "json_data = { \"status\": 1, \"deptid\": $deptid, \"ces\": \"$ces\" };" ;
					print $json_data ; exit ;
				}
			}

			// reset auto initiate timer since visitor requested chat
			$initiate_array = ( isset( $VALS["auto_initiate"] ) && $VALS["auto_initiate"] ) ? unserialize( html_entity_decode( $VALS["auto_initiate"] ) ) : Array() ;
			$auto_initiate_reset = ( isset( $initiate_array["reset"] ) ) ? $initiate_array["reset"] : 60*60 ;
			$reset = 60*60*24*$auto_initiate_reset ;
			IPs_update_IpValue( $dbh, $vis_token, "i_initiate", $now + $reset ) ;

			$dept_vars = Depts_get_DeptVars( $dbh, $deptid ) ;
			$dept_idle = ( isset( $dept_vars["idle_v"] ) ) ? $dept_vars["idle_v"] : 0 ;
			$marquees = Marquee_get_DeptMarquees( $dbh, $deptid ) ;
			$marquee_string = "" ;
			for ( $c = 0; $c < count( $marquees ); ++$c )
			{
				$marquee = $marquees[$c] ;
				$snapshot = preg_replace( "/'/", "&#39;", preg_replace( "/\"/", "&quot;", $marquee["snapshot"] ) ) ;
				$message = preg_replace( "/'/", "&#39;", preg_replace( "/\"/", "", $marquee["message"] ) ) ;

				$marquee_string .= "marquees[$c] = '$snapshot' ; marquees_messages[$c] = '$message' ; " ;
			}
			if ( !count( $marquees ) )
				$marquee_string = "marquees[0] = '' ; marquees_messages[0] = '' ; " ;

			$stars_five = Util_Functions_Stars( ".", 5 ) ; $stars_four = Util_Functions_Stars( ".", 4 ) ; $stars_three = Util_Functions_Stars( ".", 3 ) ; $stars_two = Util_Functions_Stars( ".", 2 ) ; $stars_one = Util_Functions_Stars( ".", 1 ) ;

			$email_display = ( $vemail != "null" ) ? $vemail : "" ; $survey = "" ;
			$div_email = "<div class='cl'><table cellspacing=0 cellpadding=0 border=0 width='100%'><tr><td nowrap>$LANG[TXT_EMAIL] : &nbsp;</td><td width='100%'><input type='text' class='input_text vcomment' style='width: 95%;' malength='160' id='vemail' name='vemail' value='$email_display'></td></tr><tr><td colspan=2 style='padding-top: 5px;' align='right'><input type='button' id='btn_email' value='$LANG[CHAT_BTN_EMAIL_TRANS]' onClick='send_email()'></td></tr></table></div>" ;
			$div_rate = "<div class='cl'><div class='ctitle'>".$LANG["CHAT_NOTIFY_RATE"]."</div>
				<table cellspacing=0 cellpadding=0 border=0 width='100%'>
				<tr>
					<td width='100'>
						<table cellspacing=0 cellpadding=2 border=0 style='padding-top: 10px; padding-bottom: 10px;'>
						<tr><td><input type='radio' name='rating' id='rating_5' value=5 onClick='submit_survey(this, survey_texts)'></td><td style='padding-left: 2px;'>$stars_five</td></tr>
						<tr><td><input type='radio' name='rating' id='rating_4' value=4 onClick='submit_survey(this, survey_texts)'></td><td style='padding-left: 2px;'>$stars_four</td></tr>
						<tr><td><input type='radio' name='rating' id='rating_3' value=3 onClick='submit_survey(this, survey_texts)'></td><td style='padding-left: 2px;'>$stars_three</td></tr>
						<tr><td><input type='radio' name='rating' id='rating_2' value=2 onClick='submit_survey(this, survey_texts)'></td><td style='padding-left: 2px;'>$stars_two</td></tr>
						<tr><td><input type='radio' name='rating' id='rating_1' value=1 onClick='submit_survey(this, survey_texts)'></td><td style='padding-left: 2px;'>$stars_one</td></tr>
						</table>
					</td>
					<td>
						Comment :<div><textarea rows='3' style='width: 90%; resize: vertical;' maxlength='255' id='vcomment' class='input_text vcomment'></textarea></div>
						<div><input type='button' id='btn_comment' value='$LANG[TXT_SUBMIT]' onClick='send_comment()'></div>
					</td>
				</tr>
				</table></div>" ;
			$div_rate = preg_replace( "/(\r\n)|(\n)|(\r)/", "", $div_rate ) ;
			if ( $deptinfo["temail"] && !$deptinfo["emailt_bcc"] ) { $survey .= $div_email ; }

			$socials = Vars_get_Socials( $dbh, $deptid ) ;
			if ( !count( $socials ) && $deptid )
				$socials = Vars_get_Socials( $dbh, 0 ) ;
			$socials_string = "" ;
			foreach ( $socials as $social => $data )
			{
				if ( $data["status"] )
					$socials_string .= "<a href=\"$data[url]\" target=\"_blank\" title=\"$data[tooltip]\" alt=\"$data[tooltip]\"><img src=\"themes/$theme/social/$social.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"\"></a> &nbsp; &nbsp;" ;
			}
		} else { ErrorHandler( 603, "Chat session did not create.  $dbh[query]<br>$dbh[error].", $PHPLIVE_FULLURL, 0, Array() ) ; }
	}
	else
	{
		$onpage = rawurlencode( Util_Format_URL( $onpage ) ) ;
		database_mysql_close( $dbh ) ;

		$url_redirect = "phplive.php?d=$deptid&token=$token&onpage=$onpage&embed=$embed&theme=$theme&" ;
		if ( $postembed )
		{
			$url_redirect = rawurlencode( $url_redirect ) ;
			$json_data = "json_data = { \"status\": 0, \"url_redirect\": \"$url_redirect\" };" ;
			print $json_data ; exit ;
		}
		else { HEADER( "location: $url_redirect" ) ; exit ; }
	}

	$dept_emo = ( isset( $VALS["EMOS"] ) ) ? unserialize( $VALS["EMOS"] ) : Array() ;
	$addon_emo = 0 ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) )
	{
		if ( isset( $dept_emo[$deptid] ) && $dept_emo[$deptid] ) { $addon_emo = 1 ; }
		else if ( isset( $dept_emo[0] ) && $dept_emo[0] ) { $addon_emo = 1 ; }
	}
	$autolinker_js_file = ( isset( $VARS_JS_AUTOLINK_FILE ) && ( ( $VARS_JS_AUTOLINK_FILE == "min" ) || ( $VARS_JS_AUTOLINK_FILE == "src" ) ) ) ? "autolinker_$VARS_JS_AUTOLINK_FILE.js" : "autolinker_min.js" ;
	include_once( "./inc_cache.php" ) ;
?>
<?php include_once( "./inc_doctype.php" ) ?>
<?php if ( isset( $CONF["KEY"] ) && ( $CONF["KEY"] == md5($KEY."-c615") ) ): ?><?php else: ?>
<!--
********************************************************************
* PHP Live! (c) OSI Codes Inc.
* www.phplivesupport.com
********************************************************************
-->
<?php endif ; ?>
<head>
<title> <?php echo $LANG["CHAT_WELCOME"] ?> </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>">
<?php include_once( "./inc_meta_dev.php" ) ; ?>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">

<link rel="Stylesheet" href="./themes/<?php echo $theme ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="./js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/jquery.tools.min.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/jquery_md5.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/modernizr.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="./js/<?php echo $autolinker_js_file ?>?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	"use strict" ;
	var base_url = "." ; var base_url_full = "<?php echo $CONF["BASE_URL"] ?>" ;
	var isop = 0 ; var isop_ = 11111111111 ; var isop__ = 0 ;
	var cname = "<?php echo $vname ?>" ; var cemail = "<?php echo $vemail ?>" ;
	var ces = "<?php echo $ces ?>" ;
	var st_typing, st_flash_console ;
	var si_title, si_typing, si_chat_body_resize, si_textarea ;
	var deptid = <?php echo $deptinfo["deptID"] ?> ;
	var temail = <?php echo $deptinfo["temail"] ?> ;
	var rtype = <?php echo $deptinfo["rtype"] ?> ;
	var rtime = <?php echo $deptinfo["rtime"] ?> ;
	var rloop = <?php echo ( $deptinfo["rloop"] ) ? $deptinfo["rloop"] : 1 ; ?> ;
	var chat_sound = 1 ; var console_blink_r = 0 ;
	var title_orig = document.title ;
	var si_counter = 0 ;
	var focused = 1 ;
	var widget = 0 ; var embed = <?php echo $embed ?> ;
	var wp = 0 ;
	var mobile = <?php echo $mobile ?> ; var mapp = 0 ;
	var sound_new_text = "default" ;
	var sound_volume = 1 ;
	var salt = "<?php echo $salt ?>" ;
	var theme = "<?php echo $theme ?>" ;
	var vclick = 0 ;
	var unload = 0 ;
	var socials = <?php echo ( $socials_string ) ? 1 : 0 ; ?> ;

	var marquees = new Array(), marquees_messages = new Array() ;
	var marquee_index = 0 ;
	var addon_emo = <?php echo $addon_emo ?> ;

	var loaded = 0 ;
	var newwin_print ;
	var survey_texts = new Array("<?php echo $LANG["CHAT_SURVEY_THANK"] ?>", "<?php echo $LANG["CHAT_CLOSE"] ?>") ;
	var survey = "<?php echo $survey ?>" ; var survey_rate = "<?php echo $div_rate ?>" ;
	var phplive_mobile = 0 ;
	var phplive_userAgent = navigator.userAgent || navigator.vendor || window.opera ;
	if ( phplive_userAgent.match( /iPad/i ) || phplive_userAgent.match( /iPhone/i ) || phplive_userAgent.match( /iPod/i ) )
	{
		if ( phplive_userAgent.match( /iPad/i ) ) { phplive_mobile = 0 ; }
		else { phplive_mobile = 1 ; }
	}
	else if ( phplive_userAgent.match( /Android/i ) ) { phplive_mobile = 2 ; }

	var phplive_browser = navigator.appVersion ; var phplive_mime_types = "" ;
	var phplive_display_width = screen.availWidth ; var phplive_display_height = screen.availHeight ; var phplive_display_color = screen.colorDepth ; var phplive_timezone = new Date().getTimezoneOffset() ;
	if ( navigator.mimeTypes.length > 0 ) { for (var x=0; x < navigator.mimeTypes.length; x++) { phplive_mime_types += navigator.mimeTypes[x].description ; } }
	var phplive_browser_token = phplive_md5( phplive_display_width+phplive_display_height+phplive_display_color+phplive_timezone+phplive_browser+phplive_mime_types ) ;
	var autolinker = new Autolinker( { newWindow: true, stripPrefix: false } ) ;

	var chats = new Object ;
	chats[ces] = new Object ;
	chats[ces]["requestid"] = <?php echo $requestid ?> ;
	chats[ces]["vname"] = cname ;
	chats[ces]["trans"] = "<xo><div class=\"ca\"><?php echo ( $question ) ? "<div class=\'info_box\'><i>".preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", preg_replace( "/\"/", "&quot;", $question ) )."</i></div>" : "" ; ?><div style=\"margin-top: 10px;\"><?php echo addslashes( $deptinfo["msg_greet"] ) ?><div style=\"margin-top: 10px;\"><img src=\"themes/<?php echo $theme ?>/loading_bar.gif\" border=\"0\" alt=\"\"></div></div></div></xo>".vars().emos() ;
	chats[ces]["status"] = 0 ;
	chats[ces]["disconnected"] = 0 ;
	chats[ces]["tooslow"] = 0 ;
	chats[ces]["op2op"] = 0 ;
	chats[ces]["t_ses"] = <?php echo $vses ?> ;
	chats[ces]["deptid"] = <?php echo $deptid ?> ;
	chats[ces]["opid"] = 0 ;
	chats[ces]["opid_orig"] = 0 ;
	chats[ces]["oname"] = "" ;
	chats[ces]["mapp"] = 0 ;
	chats[ces]["ip"] = "<?php echo $ip ?>" ;
	chats[ces]["vis_token"] = "<?php echo $vis_token ?>" ;
	chats[ces]["chatting"] = 0 ;
	chats[ces]["survey"] = 0 ;
	chats[ces]["rate"] = 0 ;
	chats[ces]["timer"] = <?php echo ( isset( $requestinfo["ces"] ) ) ? $requestinfo["created"] : time() ?> ;
	chats[ces]["istyping"] = 0 ;
	chats[ces]["disconnect_click"] = 0 ;
	chats[ces]["idle"] = ( mobile ) ? <?php echo $dept_idle ?>*60+<?php echo $VARS_MOBILE_CHAT_BUFFER ?> : <?php echo $dept_idle ?>*60 ;
	chats[ces]["idle_counter"] = 0 ;
	chats[ces]["idle_counter_pause"] = chats[ces]["idle_alert"] = 0 ;

	var audio_supported = HTML5_audio_support() ;
	var mp3_support = ( typeof( audio_supported["mp3"] ) != "undefined" ) ? 1 : 0 ;

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;

		<?php echo $marquee_string ?>

		$("body").show() ;
		loaded = 1 ;
		init_divs(0) ;
		init_disconnects() ;
		init_disconnect() ;

		if ( <?php echo $connected ?> )
		{
			chats[ces]["chatting"] = 1 ;
			chats[ces]["trans"] = init_timestamps( "<?php echo $text ?>" ) ;
			$('#chat_body').empty().html( chats[ces]["trans"] ) ;
		}
		else { $('#chat_body').empty().html( chats[ces]["trans"] ) ; }
		if ( addon_emo ) { $('#span_emoticons').show() ; }
		init_scrolling() ;
		init_marquees() ;
		init_typing() ;
		textarea_listen() ;

		document.getElementById('iframe_chat_engine').contentWindow.location.href = "./ops/p_engine.php?ces=<?php echo $ces ?>" ;

		if ( typeof( parent.chat_connected ) != "undefined" )
		{
			parent.chat_connected = 1 ;
		}
	});
	$(window).resize(function() {
		if ( !mobile ) { init_divs(1) ; init_div_profile() ; }
	});

	<?php if ( !$embed && !$dev ): ?>window.onbeforeunload = function() { return unload_disconnect( ces ) ; }<?php endif ; ?>

	$(window).focus(function() {
		input_focus() ;
	});
	$(window).blur(function() {
		focused = 0 ;
	});

	function unload_disconnect( theces )
	{
		unload = 1 ;
		disconnect(1, 0, theces) ;
		return "<?php echo $LANG["CHAT_CLOSE"] ?>?" ;
	}

	function init_disconnects()
	{
		// to fix div text not udating if covered by invisible layer image on parent (embed chat)
		var width = $('#info_disconnect').outerWidth() ;
		var width_embed = $('#info_disconnect_embed').outerWidth() ;
		var height = $('#info_disconnect').outerHeight() ;
		var height_embed = $('#info_disconnect_embed').outerHeight() ;

		if ( width_embed > width ) { $('#info_disconnect').css({'width': width_embed}) ; }
		if ( height_embed > height ) { $('#info_disconnect').css({'height': height_embed}) ; }

		$('#info_disconnect').addClass("info_disconnect") ;
		$('#info_disconnect_embed').addClass("info_disconnect") ;
	}

	function init_connect( thejson_data )
	{
		init_connect_doit( thejson_data ) ;
	}

	function init_connect_doit( thejson_data )
	{
		isop_ = thejson_data.opid ;
		chats[ces]["status"] = thejson_data.status_request ;
		chats[ces]["oname"] = thejson_data.name ;
		chats[ces]["opid"] = thejson_data.opid ;
		chats[ces]["opid_orig"] = thejson_data.opid ;
		chats[ces]["mapp"] = thejson_data.mapp ;
		chats[ces]["rate"] = thejson_data.rate ;
		chats[ces]["timer"] = ( parseInt( chats[ces]["chatting"] ) ) ? chats[ces]["timer"] : unixtime() ;
		chats[ces]["trans"] = chats[ces]["trans"].replace( /<xo>(.*)<\/xo>/, "" ) ;

		init_idle( ces ) ;

		var transcript = chats[ces]["trans"] ;
		$('#chat_body').empty().html( transcript.emos() ) ;
		$('#chat_vname').empty().html( chats[ces]["oname"] ) ;
		$('textarea#input_text').val( "" ) ;
		init_scrolling() ;
		init_textarea() ;
		if ( !mobile ) { $('#input_text').focus() ; }

		// visible check because fadeIn effects init_resize_chat_body() interval
		if ( ( thejson_data.profile != "" ) && !$('#chat_profile_pic').is(':visible') )
		{
			$('#chat_profile_name').html( chats[ces]["oname"] ) ;
			$('#chat_profile_pic_img').html( "<img src='"+thejson_data.profile+"' width='55' height='55' border='0' alt='' class='profile_pic_img'>" ) ;
			$('#chat_profile_pic').fadeIn("slow") ;

			var chat_body_height = $('#chat_body').height() - 75 ; $('#chat_body').css({'height': chat_body_height}) ;
			setTimeout(function(){ init_resize_chat_body( chat_body_height ) ; }, 5000) ; // delay for fadeIn() to finish for seamless display
			init_scrolling() ;
		}
		$('#options_print').show() ;
		init_timer() ;
	}

	function init_div_profile()
	{
		if ( $('#chat_profile_pic').is(':visible') )
		{
			var chat_body_height = $('#chat_body').height() - 75 ; $('#chat_body').css({'height': chat_body_height}) ;
			if ( typeof( si_chat_body_resize ) != "undefined" ) { clearInterval( si_chat_body_resize ) ; si_chat_body_resize = undeefined ; }
			init_resize_chat_body( chat_body_height ) ;
		}
	}

	function init_chats()
	{
		unload = 0 ; // reset unload flag
	}

	function init_resize_chat_body( chat_body_height )
	{
		// constantly resize the chat body because on some browsers it reverts to previous defined
		// when browser window is placed in background a while (unless chat ended and survey displayed)
		if ( typeof( si_chat_body_resize ) != "undefined" ) { clearInterval( si_chat_body_resize ) ; si_chat_body_resize = undeefined ; }
		si_chat_body_resize = setInterval(function(){
			var chat_body_height_temp = $('#chat_body').height() ;
			if ( ( chat_body_height_temp != chat_body_height ) && !chats[ces]["survey"] ) { $('#chat_body').css({'height': chat_body_height}) ; init_scrolling() ; }
		}, 200) ;
	}

	function cleanup_disconnect( theces )
	{
		// visitor disconnects
		// - disconnected by operator located at global_chat.js update_ces() through parsing
		if ( !chats[theces]["disconnected"] && chats[theces]["status"] && !unload )
		{
			if ( parseInt( chats[theces]["idle_counter"] ) != -1 ) { $('#idle_timer_notice').hide() ; }
			if ( typeof( chats[theces]["idle_si"] ) != "undefined" ) { clearInterval( chats[theces]["idle_si"] ) ; chats[theces]["idle_si"] = undeefined ; }

			chats[theces]["disconnected"] = unixtime() ;
			var text = "<div class='cl'><?php echo $LANG["CHAT_NOTIFY_VDISCONNECT"] ?></div>" ;
			if ( !chats[theces]["status"] )
			{
				// clear it out so the loading image is not shown
				$('#chat_body').empty() ;
				chats[theces]["trans"] = "" ;
			}

			add_text( theces, text ) ;
			init_textarea() ;
			document.getElementById('iframe_chat_engine').contentWindow.stopit(0) ;

			window.onbeforeunload = null ;
			if ( typeof( parent.chat_disconnected ) != "undefined" )
				parent.chat_disconnected = 1 ;

			if ( chats[theces]["status"] || ( chats[theces]["status"] == 2 ) )
				chat_survey() ;
			else
				leave_a_mesg() ;
		}
	}

	function disconnect_complete()
	{
		if ( ( typeof( ces ) != "undefined" ) && chats[ces]["disconnected"] && $('#chat_input').is(':visible') )
		{
			var chat_body_height = $('#chat_body').height() + $('#chat_input').height() ;
			$('#chat_input').hide() ;
			$('#chat_btn').hide() ;
			$('#chat_body').css({'height': chat_body_height}) ;
		}
	}

	function leave_a_mesg()
	{
		<?php if ( $vsubject ): ?>var vsubject = encodeURIComponent( "<?php echo $vsubject ?>" ) ;<?php else: ?>var vsubject = "" ;<?php endif ; ?>

		window.onbeforeunload = null ;
		var url = base_url_full+"/phplive_m.php?ces=<?php echo $ces ?>&chat=1&deptid=<?php echo $deptid ?>&token="+phplive_browser_token+"&theme=<?php echo $theme ?>&embed=<?php echo $embed ?>&vname=<?php echo $vname ; ?>&vemail=<?php echo $vemail ?>&vsubject="+vsubject+"&vquestion=<?php echo rawurlencode( $question ) ?>&onpage=<?php echo rawurlencode( Util_Format_URL( $onpage ) ) ?>&disconnect_click="+chats[ces]["disconnect_click"]+"&vclick="+vclick+"&custom=<?php echo $custom ?>&" ;

		if ( embed ) { parent.leave_a_message( url ); }
		else { location.href = url ; }
	}

	function send_email()
	{
		if ( !$('#vemail').val() )
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_EMAIL"] ?>" ) ;
		else if ( !check_email( $('#vemail').val() ) )
			do_alert( 0, "<?php echo $LANG["CHAT_JS_INVALID_EMAIL"] ?>" ) ;
		else
		{
			$('#btn_email').attr( "disabled", true ) ;
			$('#vemail').attr( "disabled", true ) ;

			var json_data = new Object ;
			var unique = unixtime() ;
			var vname = "<?php echo $vname ?>" ;
			var vemail = $('#vemail').val() ;

			$.ajax({
			type: "POST",
			url: "phplive_m.php",
			data: "&action=send_email_trans&trans=1&ces=<?php echo $ces ?>&opid="+chats[ces]["opid"]+"&deptid="+chats[ces]["deptid"]+"&token="+phplive_browser_token+"&vname="+vname+"&vemail="+vemail+"&"+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					do_alert( 1, "<?php echo $LANG["CHAT_JS_EMAIL_SENT"] ?>" ) ;
				}
				else
				{
					do_alert( 0, json_data.error ) ;
					$('#btn_email').attr( "disabled", false ) ;
					$('#vemail').attr( "disabled", false ) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Could not connect to server.  Please try again. [e551]" ) ;
				$('#btn_email').attr( "disabled", false ) ;
				$('#vemail').attr( "disabled", false ) ;
			} });
		}
	}

	function send_comment()
	{
		var json_data = new Object ;
		var unique = unixtime() ;
		var message = encodeURIComponent( $('#vcomment').val() ) ;

		if ( !message )
		{
			do_alert( 0, "<?php echo $LANG["CHAT_JS_BLANK_COMMENT"] ?>" ) ;
		}
		else
		{
			$('#btn_comment').attr( "disabled", true ) ;
			$('#vcomment').attr( "disabled", true ) ;

			$.ajax({
			type: "POST",
			url: "ajax/chat_actions_rating.php",
			data: "&action=comment&deptid="+chats[ces]["deptid"]+"&vis_token="+chats[ces]["vis_token"]+"&ces=<?php echo $ces ?>&message="+message+"&"+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					do_alert( 1, "<?php echo $LANG["CHAT_COMMENT_THANK"] ?>" ) ;
				}
				else
				{
					do_alert( 0, json_data.error ) ;
					$('#btn_comment').attr( "disabled", false ) ;
					$('#vcomment').attr( "disabled", false ) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Could not connect to server.  Please try again. [e554]" ) ;
				$('#btn_comment').attr( "disabled", false ) ;
				$('#vcomment').attr( "disabled", false ) ;
			} });
		}
	}

	function toggle_chat_sound( thetheme )
	{
		if ( chat_sound )
		{
			chat_sound = 0 ;
			console_blink_r = 1 ;
		}
		else
		{
			chat_sound = 1 ;
			console_blink_r = 0 ;
		}
		print_chat_sound_image( thetheme ) ;
	}

	function toggle_show_disconnect( theflag )
	{
		if ( theflag ) { $('#info_disconnect').show() ; }
		else { $('#info_disconnect').hide() ; }
	}

	function close_idle_div()
	{
		if ( parseInt( chats[ces]["idle_counter"] ) == -1 )
			$('#idle_timer_notice').hide() ;
	}
//-->
</script>
</head>
<body style="display: none;">

<div id="chat_canvas" style="min-height: 100%; width: 100%;"></div>
<div style="position: absolute; top: 2px; padding: 10px; z-Index: 2;" onClick="clear_flash_console();">
	<div id="chat_body_header" style="display: none;"></div>
	<div id="chat_profile_pic" style="display: none; margin-bottom: 5px;">
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td valign="top" width="55"><div id="chat_profile_pic_img"><img src="pics/profile.png" width="55" height="55" border="0" alt="" class="profile_pic_img"></div></td>
			<td valign="top" style="padding-left: 15px; min-width: 220px;">
				<div style="">
					<div style="font-weight: bold; font-size: 14px;" id="chat_profile_name">Operator Name</div>
					<div style="margin-top: 4px;"><?php echo $deptinfo["name"] ?></div>
					<div style="margin-top: 4px;" class="">Chat ID: <?php echo $ces ?></div>
				</div>
			</td>
		</tr>
		</table>
	</div>
	<div id="chat_body" style="overflow: auto;<?php echo ( $mobile ) ? " padding: 5px;": "" ; ?>" onClick="close_misc()"></div>
	<div id="chat_options" style="padding: 5px;">
		<div style="height: 16px;">
			<div id="options_print" style="display: none; float: left; white-space: nowrap;">
				<?php if ( !$mobile ): ?>
					<span><img src="./themes/<?php echo $theme ?>/sound_on.png" width="16" height="16" border="0" alt="" onClick="toggle_chat_sound('<?php echo $theme ?>')" id="chat_sound" title="<?php echo $LANG["CHAT_SOUND"] ?>" alt="<?php echo $LANG["CHAT_SOUND"] ?>" style="cursor: pointer;"></span>
				<?php endif ; ?>
				<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) ; } ?>
				<?php if ( !$mobile && ( !isset( $VALS["POPOUT"] ) || ( $VALS["POPOUT"] != "off" ) ) ): ?>
					<span style="padding-left: 15px;"><img src="./themes/<?php echo $theme ?>/printer.png" width="16" height="16" border="0" alt="" onClick="do_print(ces, <?php echo $deptinfo["deptID"] ?>, 0, <?php echo $VARS_CHAT_WIDTH ?>, <?php echo $VARS_CHAT_HEIGHT ?>)" title="<?php echo $LANG["CHAT_PRINT"] ?>" alt="<?php echo $LANG["CHAT_PRINT"] ?>" style="cursor: pointer;"></span>
				<?php endif ; ?>
				<span id="chat_vtimer_wrapper" style="position: relative; top: -2px; padding-left: 15px;"><input type="text" style="text-align: center; font-weight: normal;" value="00:00" id="chat_vtimer" size=8 maxlength=10 readonly class="input_timer"></span>
				<span id="chat_processing" style="display: none; padding-left: 15px;"><img src="./themes/<?php echo $theme ?>/loading_chat.gif" width="16" height="16" border="0" alt="loading..." title="loading..."></span>
				<span id="chat_vname" style="position: relative; top: -2px; padding-left: 15px;"></span>
				<span id="chat_vistyping" style="display: none; position: relative; top: -2px;">&nbsp;<?php echo $LANG["TXT_TYPING"] ?></span>
			</div>
			<div style="clear: both;"></div>
		</div>
	</div>
	<div id="chat_input" style="margin-top: 8px;">
		<textarea id="input_text" rows="3" style="padding: 2px; height: 75px; resize: none;" wrap="virtual" onKeyup="input_text_listen(event);" onKeydown="input_text_typing(event);" onFocus="clear_flash_console();" disabled="disabled"><?php echo $LANG["TXT_CONNECTING"] ?></textarea>
	</div>
	<div style="margin-top: 5px;">
		<?php echo $socials_string ?>
	</div>
</div>

<div id="chat_btn" style="position: absolute; z-Index: 10;">
	<button id="input_btn" type="button" class="input_button" style="<?php echo ( $mobile ) ? "" : "width: 104px; height: 45px; font-size: 14px; font-weight: bold;" ?> padding: 6px;" OnClick="add_text_prepare(1)" disabled="disabled"><?php echo $LANG["TXT_SUBMIT"] ?></button>
	<div id="sounds" style="width: 1px; height: 1px; overflow: hidden; opacity:0.0; filter:alpha(opacity=0);">
		<span id="div_sounds_new_text"></span>
		<audio id='div_sounds_audio_new_text'></audio>
	</div>
</div>

<div style="display: none;"><iframe id="iframe_chat_engine" name="iframe_chat_engine" style="position: absolute; width: 100%; border: 0px; bottom: -50px; height: 20px;" src="about:blank" scrolling="no" frameBorder="0"></iframe></div>

<div id="info_disconnect" style="position: absolute; top: 0px; right: 0px; text-align: center; z-Index: 102;" onClick="disconnect(0, 1, undeefined, 1);"><img src="./themes/<?php echo $theme ?>/close_extra.png" width="14" height="14" border="0" alt=""> <span id="info_disconnect_text"><?php echo $LANG["TXT_DISCONNECT"] ?></span></div>

<div id="idle_timer_notice" class="info_content" style="display: none; position: absolute; top: 40px; left: 25px; width: 310px; padding: 10px; z-index: 10;" onClick="close_idle_div()">
	<div style="font-weight: bold; font-size: 14px;"><?php echo $LANG["CHAT_NOTIFY_IDLE_TITLE"] ?></div>
	<div style="margin-top: 10px;"><?php echo $LANG["CHAT_NOTIFY_IDLE_AUTO_DISCONNECT"] ?> <span class="info_neutral" id="idle_countdown">60</span> <?php echo $LANG["TXT_SECONDS"] ?>.</div>
</div>

<?php if ( !$mobile ): ?>
<div id="chat_footer" style="position: relative; width: 100%; margin-top: -28px; height: 28px; padding-top: 7px; padding-left: 15px; z-Index: 10;"></div>
<?php endif ; ?>

</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>
