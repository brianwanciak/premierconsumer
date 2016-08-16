<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Footprints/remove_itr.php" ) ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }

	$error = "" ;
	$traffic = 1 ;
	$theme = "default" ;

	if ( !isset( $CONF['foot_log'] ) ) { $CONF['foot_log'] = "on" ; }
	if ( !isset( $CONF["icon_check"] ) ) { $CONF["icon_check"] = "on" ; }

	Footprints_remove_itr_Expired_U( $dbh ) ;
	Chat_remove_itr_ExpiredOp2OpRequests( $dbh ) ;
	Ops_update_itr_IdleOps( $dbh ) ;
	$total_visitors = Footprints_get_itr_TotalFootprints_U( $dbh ) ;
	$operators = Ops_get_AllOps( $dbh ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Online Status Monitor </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var newwin ;
	var refresh_counter = 25 ;
	var si_refresh, st_rd ;

	$(document).ready(function()
	{
		$("body").css({'background': '#F1F1F1'}) ;
		//$('#table_trs tr:nth-child(2n+3)').addClass('td_dept_td2') ;

		var refresh_counter_temp = refresh_counter ;
		si_refresh = setInterval(function(){
			if ( refresh_counter_temp <= 0 )
				location.href = "tools_op_status.php?ses=<?php echo $ses ?>&pop=1&"+unixtime() ;
			else
			{
				$('#refresh_counter').html( pad( refresh_counter_temp, 2 ) ) ;
				--refresh_counter_temp ;
			}
		}, 1000) ;
	});
	$(window).resize(function() { });

	function open_window( theurl )
	{
		window.open( theurl, "PHP Live! Setup", "scrollbars=yes,menubar=yes,resizable=1,location=yes,status=1" ) ;
	}

	function open_transcript( theces, theopid )
	{
		var url = "../ops/op_trans_view.php?ses=<?php echo $ses ?>&ces="+theces+"&id="+theopid+"&auth=setup&"+unixtime() ;
		
		newwin = window.open( url, theces, "scrollbars=yes,menubar=no,resizable=1,location=no,width=<?php echo $VARS_CHAT_WIDTH+50 ?>,height=<?php echo $VARS_CHAT_HEIGHT ?>,status=0" ) ;
		if ( newwin )
			newwin.focus() ;
	}

	function remote_disconnect( theopid, thelogin )
	{
		if ( confirm( "Remote disconnect operator console ("+thelogin+")?" ) )
		{
			var unique = unixtime() ;
			var json_data = new Object ;

			clearInterval( si_refresh ) ;
			$('#op_login').html( thelogin ) ;
			$('#remote_disconnect_notice').show() ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=remote_disconnect&opid="+theopid+"&"+unixtime(),
				success: function(data){
					eval( data ) ;

					if ( json_data.status )
						check_op_status( theopid ) ;
					else
					{
						$('#remote_disconnect_notice').hide() ;
						do_alert( 0, "Could not remote disconnect console.  Please try again." ) ;
					}
				}
			});
		}
	}

	function check_op_status( theopid )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( typeof( st_rd ) != "undefined" ) { clearTimeout( st_rd ) ; }

		$.ajax({
		type: "POST",
		url: "../wapis/status_op.php",
		data: "opid="+theopid+"&jkey=<?php echo md5( $CONF['API_KEY'] ) ?>&"+unique,
		success: function(data){
			eval( data ) ;

			if ( !parseInt( json_data.status ) )
			{
				if ( window.opener != null && !window.opener.closed )
					window.opener.location.href = 'index.php?ses=<?php echo $ses ?>' ;

				location.href = 'tools_op_status.php?ses=<?php echo $ses ?>&pop=1' ;
			}
			else
				st_rd = setTimeout( function(){ check_op_status( theopid ) ; }, 2000 ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Lost connection to server.  Please reload the page and try again." ) ;
		} });
	}
//-->
</script>
</head>
<body style="">

<div id="ops_list" style="padding: 10px; padding-bottom: 65px;">
	<div style="padding: 5px; color: #A5A5A5; text-shadow: 1px 1px #FFFFFF;">Window will refresh in <span id="refresh_counter">25</span> seconds.</div>
	
	<div class="info_error" style="display: none; margin-top: 10px;" id="div_error_dc">Could not connect to server.  Try reloading the window to re-establish connection.</div>
	<div style="margin-top: 10px;">
		<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_trs">
		<tr>
			<td width="180" class="td_dept_header"><a href="JavaScript:void(0)" onClick="open_window('<?php echo $CONF["BASE_URL"] ?>/setup/ops.php?ses=<?php echo $ses ?>')">Operator</a></td>
			<td width="50" class="td_dept_header">Status</td>
			<td width="30" class="td_dept_header"><a href="JavaScript:void(0)" onClick="open_window('<?php echo $CONF["BASE_URL"] ?>/setup/reports_chat.php?ses=<?php echo $ses ?>')">Chats</a></td>
			<td class="td_dept_header" style="">Recent Rating</td>
		</tr>
		<?php
			for ( $c = 0; $c < count( $operators ); ++$c )
			{
				$operator = $operators[$c] ;

				$status = ( $operator["status"] ) ? "<b>Online</b>" : "Offline" ;
				$status_img = ( $operator["status"] ) ? "<img src=\"../pics/icons/bulb.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"online\" title=\"online\">" : "<img src=\"../pics/icons/bulb_off.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"offline\" title=\"offline\">" ;
				$tr_style = ( $operator["status"] ) ? "background: #AFFF9F;" : "" ;
				$style = ( $operator["status"] ) ? "cursor: pointer" : "" ;
				$js = ( $operator["status"] ) ? "onClick=\"remote_disconnect($operator[opID], '$operator[login]')\"" : "" ;
				$mapp_online_icon = ( $operator["mapp"] && $operator["status"] ) ? " &nbsp; <img src=\"../pics/icons/mobile.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"logged in on mobile\" title=\"logged in on mobile\" style=\"cursor: help;\">" : "" ;

				$lastactive = ( $operator["lastactive"] ) ? date( "M j, Y g:i a", $operator["lastactive"] ) : "&nbsp;" ;

				$profile_image = Util_Upload_GetLogo( "profile", $operator["opID"] ) ;
				$requests = Chat_get_OpTotalRequests( $dbh, $operator["opID"] ) ;
				$rating = Util_Functions_Stars( "..", $operator["rating"] ) ;
				$rating_string = ( $operator["ces"] ) ? "<div>$rating</div><div style=\"margin-top: 5px; font-size: 10px; cursor: pointer;\" class=\"info_neutral\" onClick=\"open_transcript('$operator[ces]', $operator[opID])\">Chat ID: $operator[ces]</div>" : $rating ;

				print "<tr style=\"$tr_style\"><td class=\"td_dept_td\" width=\"180\"><table cellspacing=0 cellpadding-0 border=0><tr><td><img src=\"$profile_image\" width=\"35\" height=\"35\" border=\"0\" alt=\"\" style=\"border: 1px solid #DFDFDF;\" class=\"round\"></td><td style=\"padding-left: 5px;\">$operator[name]</td></tr></table></td><td class=\"td_dept_td\" width=\"50\" style=\"$style\" nowrap $js>$status_img$mapp_online_icon</td><td class=\"td_dept_td\" width=\"30\"><a href=\"JavaScript:void(0)\" onClick=\"open_window('$CONF[BASE_URL]/setup/reports_chat_active.php?ses=$ses')\">$requests</a></td><td class=\"td_dept_td\">$rating_string</td></tr>" ;
			}
			if ( $c == 0 )
				print "<tr><td colspan=7 class=\"td_dept_td\">Blank results.</td></tr>" ;
		?>
		</table>
	</div>
</div>


<div id="remote_disconnect_notice" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">
	<div style="padding-top: 150px; text-align: center;"><span class="info_error" style="">Disconnecting console [ <span id="op_login"></span> ].  Just a moment... <img src="../pics/loading_fb.gif" width="16" height="11" border="0" alt=""></span></div>
</div>


<?php if ( ( $CONF['icon_check'] == "on" ) && $traffic ): ?>
<div style="position: fixed; bottom: 0px; width: 100%; background: #FFFFFF; border-top: 1px solid #D6D6D6; color: #6D6D71;">
	<div id="total_visitors" style="padding: 10px;">
		Website visitors: <span class="info_box"><?php echo $total_visitors ?></span>
	</div>
</div>
<?php endif ; ?>

</body>
</html>
<?php
	if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;
?>
