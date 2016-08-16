<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	$auth = Util_Format_Sanatize( Util_Format_GetVar( "auth" ), "ln" ) ;
	$id = Util_Format_Sanatize( Util_Format_GetVar( "id" ), "n" ) ;
	$wp = Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) ;

	if ( $auth == "setup" )
	{
		if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses, $id ) ){ ErrorHandler( 602, "Invalid setup session.", $PHPLIVE_FULLURL, 0, Array() ) ; }
		$theme = $CONF["THEME"] ;
	}
	else
	{
		if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses, $id, $wp ) ){ ErrorHandler( 602, "Invalid setup session.", $PHPLIVE_FULLURL, 0, Array() ) ; }
		$theme = $opinfo["theme"] ;
	}
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Notes/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$ces = Util_Format_Sanatize( Util_Format_GetVar( "ces" ), "ln" ) ;
	$back = Util_Format_Sanatize( Util_Format_GetVar( "back" ), "n" ) ;
	$mapp = Util_Format_Sanatize( Util_Format_GetVar( "mapp" ), "n" ) ;
	$realtime = Util_Format_Sanatize( Util_Format_GetVar( "realtime" ), "n" ) ;

	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	LIST( $os, $browser ) = Util_Format_GetOS( $agent ) ;
	$mobile = ( $os == 5 ) ? 1 : 0 ;
	$error = "" ;
	$noteinfo = Array() ; $rating_stars = "" ;

	$transcript = Chat_ext_get_Transcript( $dbh, $ces ) ;
	$requestinfo = Chat_get_RequestHistCesInfo( $dbh, $ces ) ;
	if ( !isset( $requestinfo["ces"] ) && isset( $transcript["ces"] ) )
	{
		$requestinfo = Array() ;
		$requestinfo["ces"] = $transcript["ces"] ;
		$requestinfo["created"] = $transcript["created"] ;
		$requestinfo["ended"] = $transcript["ended"] ;
		$requestinfo["status"] = 1 ;
		$requestinfo["initiated"] = $transcript["initiated"] ;
		$requestinfo["deptID"] = $transcript["deptID"] ;
		$requestinfo["opID"] = $transcript["opID"] ;
		$requestinfo["op2op"] = $transcript["op2op"] ;
		$requestinfo["marketID"] = 0 ;
		$requestinfo["os"] = 4 ;
		$requestinfo["browser"] = 6 ;
		$requestinfo["resolution"] = "&nbsp;" ;
		$requestinfo["vname"] = $transcript["vname"] ;
		$requestinfo["vemail"] = $transcript["vemail"] ;
		$requestinfo["ip"] = $transcript["ip"] ;
		$requestinfo["sim_ops"] = "" ;
		$requestinfo["agent"] = "&nbsp;" ;
		$requestinfo["onpage"] = "" ;
		$requestinfo["title"] = "" ;
		$requestinfo["custom"] = "" ;
		$requestinfo["md5_vis"] = $transcript["md5_vis"] ;
		$requestinfo["question"] = $transcript["question"] ;
		$noteinfo = ( $transcript["noteID"] ) ? Notes_get_NoteInfo( $dbh, $transcript["noteID"] ) : Array() ;
		$rating_stars = Util_Functions_Stars( "..", $transcript["rating"] ) ;
	}
	else if ( !isset( $requestinfo["ces"] ) && !isset( $transcript["ces"] ) )
	{
		$requestinfo = Array() ;
		$requestinfo["ces"] = "invalid" ;
		$requestinfo["created"] = 0 ;
		$requestinfo["ended"] = 0 ;
		$requestinfo["status"] = 1 ;
		$requestinfo["initiated"] = 0 ;
		$requestinfo["deptID"] = "invalid" ;
		$requestinfo["opID"] = "invalid" ;
		$requestinfo["op2op"] = 0 ;
		$requestinfo["marketID"] = 0 ;
		$requestinfo["os"] = 4 ;
		$requestinfo["browser"] = 6 ;
		$requestinfo["resolution"] = "&nbsp;" ;
		$requestinfo["vname"] = "invalid" ;
		$requestinfo["vemail"] = "invalid" ;
		$requestinfo["ip"] = "invalid" ;
		$requestinfo["sim_ops"] = "" ;
		$requestinfo["agent"] = "&nbsp;" ;
		$requestinfo["onpage"] = "" ;
		$requestinfo["title"] = "" ;
		$requestinfo["custom"] = "" ;
		$requestinfo["md5_vis"] = "invalid" ;
		$requestinfo["question"] = "invalid" ;
		$transcript["formatted"] = "" ;
		$transcript["opID"] = 0 ;
		$transcript["deptID"] = 0 ;
		$transcript["created"] = 0 ;
		$transcript["ended"] = 0 ;
	}
	else if ( isset( $transcript["ces"] ) )
	{
		$noteinfo = ( $transcript["noteID"] ) ? Notes_get_NoteInfo( $dbh, $transcript["noteID"] ) : Array() ;
		$rating_stars = Util_Functions_Stars( "..", $transcript["rating"] ) ;
	}

	if ( !$realtime || $requestinfo["ended"] )
	{
		$realtime = 0 ; // set it to zero since it ended, not realtime anymore
		$formatted = preg_replace( "/(\r\n)|(\r)|(\n)/", "<br>", preg_replace( "/\"/", "&quot;", $transcript["formatted"] ) ) ;
		$requestinfo["vname"] = Util_Format_Sanatize( $requestinfo["vname"], "v" ) ;
		$opinfo_ = Ops_get_OpInfoByID( $dbh, $transcript["opID"] ) ;
		$deptinfo = Depts_get_DeptInfo( $dbh, $transcript["deptID"] );
		$duration = $transcript["ended"] - $transcript["created"] ;
		if ( $duration < 60 )
			$duration = 60 ;
		$duration = Util_Format_Duration( $duration ) ;
	}
	else if ( isset( $requestinfo["opID"] ) )
	{
		$opinfo_ = Ops_get_OpInfoByID( $dbh, $requestinfo["opID"] ) ;
		$deptinfo = Depts_get_DeptInfo( $dbh, $requestinfo["deptID"] );
		$formatted = "<div class='ca' style='font-size: 14px; font-weight: bold;'><img src='../themes/$theme/chats.png' width='16' height='16' border='0' alt=''> Real-time Chat Session View</div>" ;
		$duration = "" ;
	}
	$profile_pic = "<div id='chat_profile_pic'>Chat ID: $ces</div>" ;
	if ( $opinfo_["pic"] && ( isset( $VALS['PROFILE'] ) && ( $VALS['PROFILE'] == 1 ) ) )
	{
		if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
		else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
		$profile_pic_url = Util_Upload_GetLogo( "profile", $opinfo_["opID"] ) ;
		$profile_pic = "<div id='chat_profile_pic' style='margin-bottom: 5px;'><table cellspacing=0 cellpadding=0 border=0><tr><td valign='top' width='55'><div id='chat_profile_pic_img'><img src='$profile_pic_url' width='55' height='55' border=0 class='profile_pic_img'></div></td><td valign='top' style='padding-left: 15px; min-width: 220px;'><div style='font-weight: bold; font-size: 14px;' id='chat_profile_name'>$opinfo_[name]</div><div style='margin-top: 4px;'>$deptinfo[name]</div><div style='margin-top: 4px;' class=''>Chat ID: $ces</div></div></td></tr></table></div>" ;
	}
	$visitorid_string = ( !$mapp ) ? "<tr><td nowrap>Visitor ID: </td><td>$requestinfo[md5_vis]</td></tr>" : "" ;
	$comments_string = ( isset( $noteinfo["message"] ) ) ? "<tr><td nowrap><table cellspacing=0 cellpadding=0 border=0><tr><td>Comment </td><td> &nbsp;<img src='../themes/$theme/info_flag.gif' width='10' height='10' border='0' alt='' class='info_neutral'> </td></tr></table></td><td><i>".preg_replace( "/(\r\n)|(\n)|(\r)/", "", nl2br( $noteinfo["message"] ) )."</i></td></tr>" : "" ;
	$rating_string = ( $rating_stars ) ? "<tr><td nowrap>Rating: </td><td>$rating_stars</td></tr>" : "" ;
	$profile_pic .= "<div class='ca'><table cellspacing=0 cellpadding=2 border=0>$visitorid_string$rating_string$comments_string</table></div>" ;

	$CONF['lang'] = ( $deptinfo["lang"] ) ? $deptinfo["lang"] : $CONF["lang"] ;
	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ;

	if ( isset( $requestinfo["os"] ) )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_ext.php" ) ;

		$os = $VARS_OS[$requestinfo["os"]] ;
		$browser = $VARS_BROWSER[$requestinfo["browser"]] ;

		$onpage_title = preg_replace( "/\"/", "&quot;", $requestinfo["title"] ) ;
		$onpage_title_raw = $onpage_title ;
		$onpage_title_snap = ( strlen( $onpage_title_raw ) > 20 ) ? substr( $onpage_title_raw, 0, 40 ) . "..." : $onpage_title_raw ;
		$onpage_raw = preg_replace( "/hphp/i", "http", $requestinfo["onpage"] ) ;
		$onpage_snap = ( strlen( $onpage_raw ) > 20 ) ? substr( $onpage_raw, 0, 40 ) . "..." : $onpage_raw ;

		$referinfo = Footprints_get_IPRefer( $dbh, $requestinfo["md5_vis"] ) ;
		$refer_raw = ( isset( $referinfo["refer"] ) && $referinfo["refer"] ) ? preg_replace( "/((http)|(https)):\/\/(www.)/", "", preg_replace( "/hphp/i", "http", $referinfo["refer"] ) ) : "" ;
		$refer_snap = ( strlen( $refer_raw ) > 20 ) ? substr( $refer_raw, 0, 40 ) . "..." : $refer_raw ;

		if ( $requestinfo["marketID"] )
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/get_itr.php" ) ;
			$marketinfo = Marketing_get_itr_MarketingByID( $dbh, $requestinfo["marketID"] ) ;
		}
	}
	
	$deptid = $requestinfo["deptID"] ;
	$dept_emo = ( isset( $VALS["EMOS"] ) ) ? unserialize( $VALS["EMOS"] ) : Array() ;
	$addon_emo = 0 ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/emoticons.php" ) )
	{
		if ( isset( $dept_emo[$deptid] ) && $dept_emo[$deptid] ) { $addon_emo = 1 ; }
		else if ( isset( $dept_emo[0] ) && $dept_emo[0] ) { $addon_emo = 1 ; }
	}
	$message_body = preg_replace( "/%%visitor%%/", "$requestinfo[vname]", $deptinfo["msg_email"] ) ;
	$message_body = preg_replace( "/%%operator%%/", "$opinfo_[name]", $message_body ) ;
	$message_body = preg_replace( "/%%op_email%%/", "$opinfo_[email]", $message_body ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> <?php echo ( $realtime ) ? "Chat Session" : "Transcript" ; ?> </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=<?php echo $LANG["CHARSET"] ?>"> 
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/<?php echo $theme ?>/style.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/winapp.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var view = 1 ; // flag used in global_chat.js for minor formatting of divs
	var base_url = ".." ; var base_url_full = "<?php echo $CONF["BASE_URL"] ?>" ;
	var ces = "<?php echo $ces ?>" ;
	var wp = <?php echo $wp ?> ;
	var realtime = <?php echo ( $realtime ) ? $realtime : 0 ; ?> ;
	var widget ;
	var mobile = <?php echo $mobile ?> ; var mapp = <?php echo $mapp ?> ;
	var isop = 0 ;
	var profile_pic_enabled = 1 ;

	var addon_emo = <?php echo $addon_emo ?> ;

	var st_realtime ;
	var c_chatting = 0 ;
	var chats = new Object ;
	chats[ces] = new Object ;
	chats[ces]["requestid"] = 0 ;
	chats[ces]["status"] = <?php echo ( isset( $requestinfo["os"] ) ) ? $requestinfo["status"] : 0 ; ?> ;
	chats[ces]["op2op"] = <?php echo ( isset( $requestinfo["os"] ) ) ? $requestinfo["op2op"] : 0 ; ?> ;
	chats[ces]["initiated"] = <?php echo ( isset( $requestinfo["os"] ) ) ? $requestinfo["initiated"] : 0 ; ?> ;
	chats[ces]["disconnected"] = 0 ;
	chats[ces]["mapp"] = 0 ;
	chats[ces]["fline"] = 0 ;
	chats[ces]["trans"] = "" ;
	chats[ces]["timer"] = <?php echo ( isset( $requestinfo["os"] ) ) ? $requestinfo["created"] : 0 ; ?> ;

	$(document).ready(function()
	{
		init_divs(0) ;

		var transcript = "<?php echo $formatted ?>" ;
		$('#chat_body').html( "<?php echo $profile_pic ?>"+init_timestamps( transcript.emos() ) ) ;

		$('#btn_email').attr( "disabled", false ) ; // reset it... firefox caches disabled
		init_req() ;

		if ( realtime )
		{
			init_timer() ;
			chatting() ;
		}
		$('window').focus() ;

		//$('#table_info tr:nth-child(1n)').addClass('chat_info_tr_traffic_row') ;

		if ( mapp ) { init_external_url() ; }
		else if ( !<?php echo $requestinfo["created"] ?> ) { $('#div_options').hide() ; }
	});

	if ( !mapp )
	{
		// iOS resizes on various events, even CSS resize (skip $mapp)
		$(window).resize(function() { init_divs(1) ; });
	}

	function init_external_url()
	{
		$("a").click(function(){
			var temp_url = $(this).attr( "href" ) ;
			if ( !temp_url.match( /javascript/i ) )
			{
				parent.parent.external_url = temp_url ;
				return false ;
			}
		});
	}

	function init_chat_body_height( thewidth, theheight )
	{
		var chat_body_width = thewidth - 15 ;
		var chat_body_height = theheight - 25 ;

		$('#chat_input').hide() ;
		$('#chat_body').css({'overflow-x': 'hidden', 'width': chat_body_width, 'height': chat_body_height }) ;
	}

	function init_req()
	{
		if ( !realtime )
			$('#chat_vtimer').html( "<?php echo $duration ; ?>" ) ;
	}

	function toggle_info( theforce )
	{
		if ( theforce )
		{
			if ( $('#table_email').is(':visible') ) { toggle_info(0) ; }
		}
		else { $('#table_email').hide() ; $('#table_info').show() ; }
	}

	function toggle_email()
	{
		$('#table_info').hide() ;
		$('#table_email').show() ;
		$('#btn_email').attr( "disabled", false ) ;
	}

	function send_email()
	{
		if ( !$('#vemail').val() )
			do_alert( 0, "Blank Email is invalid." ) ;
		else if ( !$('#message').val() )
			do_alert( 0, "Blank Message is invalid." ) ;
		else
		{
			$('#btn_email').attr( "disabled", true ) ;

			var json_data = new Object ;
			var unique = unixtime() ;
			var deptid = $('#deptid').val() ;
			var vname = "<?php echo ( isset( $requestinfo["os"] ) ) ? $requestinfo["vname"] : "" ; ?>" ;
			var vemail = $('#vemail').val() ;
			var subject = encodeURIComponent( "Chat Transcript: "+vname ) ;
			var message =  encodeURIComponent( $('#message').val() ) ;

			$('#chat_button_start').blur() ;

			$.ajax({
			type: "POST",
			url: "../phplive_m.php",
			data: "action=send_email&ces=<?php echo $ces ?>&trans=1&opid=<?php echo isset( $opinfo_["opID"] ) ? $opinfo_["opID"] : 0 ; ?>&deptid="+deptid+"&vname="+vname+"&vemail="+vemail+"&vsubject="+subject+"&vquestion="+message+"&unique="+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					toggle_info(0) ;
					do_alert( 1, "Transcript emailed to: "+vemail ) ;
				}
				else
				{
					do_alert( 0, json_data.error ) ;
					$('#btn_email').attr( "disabled", false ) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error sending transcript.  Please reload the page and try again." ) ;
			} });
		}
	}

	function chatting()
	{
		var json_data = new Object ;
		var start = 0 ;
		var q_ces = q_chattings = q_cids = "" ;

		q_ces += "qcc[]="+ces+"&" ;
		q_chattings += "qch[]=0&" ;

		if ( typeof( st_chatting ) != "undefined" )
		{
			clearTimeout( st_chatting ) ;
			st_chatting = undeefined ;
		}

		if ( !chats[ces]["disconnected"] )
		{
			var unique = unixtime() ;
			$.ajax({
			type: "GET",
			url: "../ajax/chat_op_requesting.php",
			data: "c="+ces+"&ch="+c_chatting+"&r="+realtime+"&"+q_ces+q_chattings+"&f="+chats[ces]["fline"]+"&"+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					for ( var c = 0; c < json_data.chats.length; ++c )
						realtime_update_ces( json_data.chats[c] ) ;

					if ( typeof( st_chatting ) == "undefined" )
						st_realtime = setTimeout(function(){ chatting() ; }, <?php echo ( $VARS_JS_REQUESTING * 1000 ) ?>) ;
				}
			},
			error:function (xhr, ajaxOptions, thrownError){
			} });
			++c_chatting ;
		}
		else
		{
			if ( typeof( st_chatting ) != "undefined" )
			{
				clearTimeout( st_chatting ) ;
				st_chatting = undeefined ;
			}
		}
	}

	function realtime_update_ces( thejson_data )
	{
		var thisces = thejson_data["ces"] ;
		var append_text = thejson_data["text"] ;

		if ( ( typeof( chats[thisces] ) != "undefined" ) && ( parseInt( chats[thisces]["fline"] ) != parseInt( thejson_data["fline"] ) ) )
		{
			chats[thisces]["fline"] = thejson_data["fline"] ;
			chats[thisces]["trans"] += append_text ;

			// parse for flags before doing functions
			if ( ( thejson_data["text"].indexOf( "<disconnected>" ) != -1 ) )
			{
				chats[thisces]["disconnected"] = unixtime() ;
				$('#req_rating').html( "<img src='../themes/<?php echo $theme ?>/loading_fb.gif' width='16' height='11' border='0' alt=''> disconnecting..." ) ;
				setTimeout(function(){ $('#req_rating').html( "[ <a href=\"JavaScript:void(0)\" onClick=\"location.reload()\">view rating</a> ]" ) ; }, 10000) ;
			}

			$('#chat_body').append( init_timestamps( append_text.emos() ) ) ;
			init_scrolling() ;

			//if ( chat_sound )
			//	play_sound( 0, "new_text", "new_text" ) ;
		}
	}

//-->
</script>
</head>
<body>

<div id="chat_canvas" style="min-height: 100%; width: 100%;"></div>
<div style="position: absolute; top: 2px; padding: 10px; z-Index: 2; overflow: auto;">
	<div id="chat_body" style="overflow: auto;<?php echo ( $mobile || $mapp ) ? " padding: 5px;": "" ; ?>"></div>
	<div id="chat_input" style="margin-top: 8px; padding: 5px; -moz-border-radius: 5px; border-radius: 5px;">
		
		<?php if ( isset( $requestinfo["os"] ) && !$mapp ): ?>
		<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_info">
		<tr>
			<td width="200" valign="top">
				<div class="chat_info_td_traffic" style="font-weight: bold;"><table cellspacing=0 cellpadding=0 border=0><tr><td style="padding-right: 5px;"><span id="chat_vtimer"></span></td><td><span id="req_rating"><?php echo $rating_stars ?></span></td></tr></table></div>
				<div class="chat_info_td_traffic"><b><?php echo ( $requestinfo["resolution"] ) ? $requestinfo["resolution"] : "" ; ?></b> &nbsp; <img src="../themes/<?php echo $theme ?>/os/<?php echo $os ?>.png" width="14" height="14" border="0"  title="<?php echo $os ?>" alt="<?php echo $os ?>" style="cursor: help;"> &nbsp; <img src="../themes/<?php echo $theme ?>/browsers/<?php echo $browser ?>.png" width="14" height="14" border="0" title="<?php echo $browser ?>" alt="<?php echo $browser ?>" style="cursor: help;"></div>

				<?php if ( ( ( isset( $opinfo ) && $opinfo["viewip"] ) && !$requestinfo["op2op"] ) || isset( $admininfo ) ): ?>
				<div class="chat_info_td_traffic"><b><?php echo $requestinfo["ip"] ?></b></div>
				<?php endif ; ?>

				<div style="max-height: 55px; overflow: auto;">
				<?php
					if ( $requestinfo["custom"] )
					{
						$customs = explode( "-cus-", $requestinfo["custom"] ) ;
						for ( $c = 0; $c < count( $customs ); ++$c )
						{
							$custom_var = $customs[$c] ;
							if ( $custom_var && preg_match( "/-_-/", $custom_var ) )
							{
								LIST( $cus_name, $cus_val ) = explode( "-_-", $custom_var ) ;
								if ( $cus_val )
								{
									if ( preg_match( "/^http/", $cus_val ) )
									{
										$cus_val_snap = ( strlen( $cus_val ) > 40 ) ? substr( $cus_val, 0, 15 ) . "..." . substr( $cus_val, -15, strlen( $cus_val ) ) : $cus_val ;
										print "<div class=\"chat_info_td_traffic\"><b>$cus_name:</b> <span title=\"$cus_val\" alt=\"$cus_val\"><a href=\"$cus_val\" target=_blank>$cus_val_snap</a></span></div>" ;
									}
									else
										print "<div class=\"chat_info_td_traffic\"><b>$cus_name:</b> $cus_val</div>" ;
								}
							}
						}
					}
				?>
				</div>
			</td>
			<td valign="top">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td width="50" nowrap><div class="chat_info_td_traffic" style="padding-left: 10px;"><span class="text_trans_view_td">Visitor:</span></div></td>
					<td><div class="chat_info_td_traffic"><b><?php echo $requestinfo["vname"] ?></b> <?php echo ( $requestinfo["vemail"] && ( ( $requestinfo["vemail"] != "null" ) && ( $requestinfo["vemail"] != "invalid" ) ) ) ? "<a href='mailto:$requestinfo[vemail]' target='new'>$requestinfo[vemail]</a>" : "" ; ?></div></td>
				</tr>
				<tr>
					<td><div class="chat_info_td_traffic" style="padding-left: 10px;"><span class="text_trans_view_td">Operator:</span></div></td>
					<td><div class="chat_info_td_traffic"><?php echo ( $requestinfo["initiated"] ) ? "<img src=\"../themes/$CONF[THEME]/info_initiate.gif\" width=\"10\" height=\"10\" border=\"0\" alt=\"\" title=\"Operator Initiated Chat\" alt=\"Operator Initiated Chat\" style=\"cursor: help;\"> " : "" ; ?><b><?php echo ( $requestinfo["op2op"] ) ? "Operator to Operator Chat" : $opinfo_["name"] ; ?></b> <?php echo "<a href='mailto:$opinfo_[email]' target='new'>$opinfo_[email]</a>" ?></div></td>
				</tr>
				<tr>
					<td><div class="chat_info_td_traffic" style="padding-left: 10px;"><span class="text_trans_view_td">Department:</span></div></td>
					<td><div class="chat_info_td_traffic"><b><?php echo $deptinfo["name"] ; ?></b></div></td>
				</tr>
				<tr>
					<td><div class="chat_info_td_traffic" style="padding-left: 10px;"><span class="text_trans_view_td">On Page:</span></div></td>
					<td><div class="chat_info_td_traffic" title="<?php echo $onpage_raw ?>" alt="<?php echo $onpage_raw ?>"><b><a href="<?php echo ( $onpage_raw == "livechatimagelink" ) ? "JavaScript:void(0)" : $onpage_raw ; ?>" target="_blank"><?php echo $onpage_title_snap ?></a></b></div></td>
				</tr>
				<tr>
					<td><div class="chat_info_td_traffic" style="padding-left: 10px;"><span class="text_trans_view_td">Refer:</span></div></td>
					<td><div class="chat_info_td_traffic" title="<?php echo $refer_raw ?>" alt="<?php echo $refer_raw ?>"><b><a href="<?php echo $refer_raw ?>" target="_blank"><?php echo $refer_snap ?></a></b></div></td>
				</tr>
				</table>
			</td>
		</tr>
		<?php if ( !$realtime ): ?>
		<tr>
			<td colspan="2">
				<div class="info_neutral" style="margin-top: 10px;" id="div_options">
					<div style="float: left;">Options: </div>
					<div style="float: left; margin-left: 15px; cursor: pointer" onClick="toggle_email()"><img src="../themes/<?php echo $theme ?>/email.png" width="16" height="16" border="0" alt="email transcript" title="email transcript"></div>

					<?php if ( !$back ): ?>
					<div style="float: left; margin-left: 15px; cursor: pointer;" onClick="do_print('<?php echo $ces ?>', <?php echo $requestinfo["deptID"] ?>, <?php echo $requestinfo["opID"] ?>, <?php echo $VARS_CHAT_WIDTH ?>, <?php echo $VARS_CHAT_HEIGHT ?> )"><img src="../themes/<?php echo $theme ?>/printer.png" width="16" height="16" border="0" alt="print" title="print"></div>
					<?php endif ; ?>

					<div style="float: right; text-align: right;">created: <b><?php echo date( "M j, Y, g:i a", $requestinfo["created"] ) ; ?></b></div>
					<div style="clear: both;"></div>
				</div>
			</td>
		</tr>
		<?php endif ; ?>
		</table>

		<?php elseif ( !$mapp ): ?>

		<div class="info_box">Transcript does not exist or is no longer available.</a>.</div>

		<?php endif ; ?>

	</div>
</div>

<div id="table_email" style="display: none; position: absolute; width: 100%; bottom: 10px; z-Index: 5" class="info_content">
	<div style="padding: 15px;">
	<form>
	<input type="hidden" name="deptid" id="deptid" value="<?php echo $requestinfo["deptID"] ?>">
	<table cellspacing=0 cellpadding=0 border=0 width="100%">
	<tr>
		<td style="">To Email:<br><input type="text" class="input_text" name="vmail" id="vemail" size="38" maxlength="160" style="width: 95%;" value="<?php echo ( $requestinfo["vemail"] != "null" ) ? $requestinfo["vemail"] : "" ; ?>" onKeyPress="return justemails(event)"></td>
	</tr>
	<tr><td style="height: 5px;"></td></tr>
	<tr>
		<td colspan=2>Message:<br><textarea class="input_text" rows="7" style="width: 95%; resize: none;" wrap="virtual" id="message"><?php echo $message_body ?>

[Chat ID: <?php echo $requestinfo["ces"] ?>]
</textarea></td>
		</tr>
		<tr><td style="height: 15px;"></td></tr>
		<tr><td align="center"><input type="button" id="btn_email" value="Email Transcript" onClick="send_email()" class="input_button"> &nbsp; &nbsp; &nbsp; <a href="JavaScript:void(0)" onClick="toggle_info(0)">cancel</a></td></tr>
		</table>
		</form>
	</div>
</div>

<?php if ( $back && !$mapp ): ?>
<div class="info_disconnect" style="position: absolute; top: 0px; right: 0px; z-Index: 101;" onClick="history.go(-1)"><img src="../themes/<?php echo $theme ?>/close_extra.png" width="14" height="14" border="0" alt=""> back to transcript list</div>
<?php elseif ( $mapp ): ?>
<div class="info_disconnect" style="position: absolute; top: 0px; right: 0px; z-Index: 101;" onClick="parent.close_transcript('<?php echo $ces ?>')"><img src="../themes/<?php echo $theme ?>/close_extra.png" width="14" height="14" border="0" alt=""> close transcript &nbsp;</div>
<?php endif ; ?>

</body>
</html>
<?php database_mysql_close( $dbh ) ; ?>