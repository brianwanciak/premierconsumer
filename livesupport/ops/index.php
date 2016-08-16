<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../web/config.php" ) ){ HEADER("location: ../setup/install.php") ; exit ; }
	include_once( "../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$opinfo = Util_Security_AuthOp( $dbh, $ses ) ){ ErrorHandler( 602, "Invalid operator session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/remove.php" ) ;

	/***** [ BEGIN ] BASIC CLEANUP *****/
	$now = time() ;
	$dir_files = glob( $CONF["CHAT_IO_DIR"].'/*.t*', GLOB_NOSORT ) ;
	$total_dir_files = count( $dir_files ) ;
	if ( $total_dir_files )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/Util.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/put_itr.php" ) ;
		include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

		for ( $c = 0; $c < $total_dir_files; ++$c )
		{
			$file = $dir_files[$c] ;
			$modtime = filemtime( $file ) ;
			if ( $modtime < ( $now - (60*60*14) ) )
			{
				if ( is_file( $file ) )
				{
					preg_match( "/(.*?)\.txt/", $file, $matches ) ;
					if ( isset( $matches[1] ) )
					{
						$ces = $matches[1] ;
						$requestinfo = Chat_get_RequestHistCesInfo( $dbh, $ces ) ;

						if ( isset( $requestinfo["ces"] ) && !$requestinfo["ended"] )
						{
							$deptinfo = Depts_get_DeptInfo( $dbh, $requestinfo["deptID"] ) ;
							$deptvars = Depts_get_DeptVars( $dbh, $requestinfo["deptID"] ) ;

							Chat_remove_itr_RequestByCes( $dbh, $ces ) ;
							$CONF["lang"] = ( isset( $CONF["lang"] ) && $CONF["lang"] ) ? $CONF["lang"] : "english" ;
							include( "$CONF[DOCUMENT_ROOT]/lang_packs/".Util_Format_Sanatize($CONF["lang"], "ln").".php" ) ;
							$string_disconnect = "<div class='cl'><disconnected><d6>".$LANG["CHAT_NOTIFY_DISCONNECT"]."</div>" ;
							UtilChat_AppendToChatfile( $ces.".txt", $string_disconnect ) ;

							$output = UtilChat_ExportChat( $ces.".txt" ) ;
							if ( isset( $output[0] ) )
							{
								$formatted = $output[0] ; $plain = $output[1] ;
								$fsize = strlen( $formatted ) ;
								if ( $requestinfo["status"] )
								{
									Chat_put_itr_Transcript( $dbh, $ces, $requestinfo["status"], $requestinfo["created"], $modtime, $requestinfo["deptID"], $requestinfo["opID"], $requestinfo["initiated"], $requestinfo["op2op"], 0, $fsize, $requestinfo["vname"], $requestinfo["vemail"], $requestinfo["ip"], $requestinfo["md5_vis"], $requestinfo["question"], $formatted, $plain, $deptinfo, $deptvars ) ;
								}
							}
						}
					} unlink( $file ) ;
				}
			}
		}
	}
	Ops_remove_CleanStats( $dbh ) ;
	/***** [ END ] BASIC CLEANUP *****/

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "n" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$menu = Util_Format_Sanatize( Util_Format_GetVar( "menu" ), "ln" ) ;
	$wp = Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) ;
	$auto = Util_Format_Sanatize( Util_Format_GetVar( "auto" ), "n" ) ;
	$menu = ( $menu ) ? $menu : "go" ;
	$error = "" ;
	$theme = "default" ;

	$op_depts = Ops_get_OpDepts( $dbh, $opinfo["opID"] ) ;
	$opvars = Ops_get_OpVars( $dbh, $opinfo["opID"] ) ;

	if ( $action == "update_theme" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;

		if ( !Ops_update_OpValue( $dbh, $opinfo["opID"], "theme", $theme ) )
			$error = "Error in updating theme." ;
		else
			$opinfo["theme"] = $theme ;
		
		$menu = "themes" ;
	}
	else if ( $action == "success" )
	{
		// sucess action is an indicator to show the success alert as well
		// as bypass the reloading of the operator console
	}
	else
		$error = "invalid action" ;

	$query = "SELECT SUM(rateit) AS rateit, SUM(ratings) AS ratings FROM p_rstats_ops WHERE opID = '$opinfo[opID]'" ;
	database_mysql_query( $dbh, $query ) ; $data = database_mysql_fetchrow( $dbh ) ;
	$overall = ( isset( $data["rateit"] ) && $data["rateit"] ) ? round( $data["ratings"]/$data["rateit"] ) : 0 ;

	$query = "SELECT SUM(taken) AS total FROM p_rstats_ops WHERE opID = '$opinfo[opID]'" ;
	database_mysql_query( $dbh, $query ) ; $data = database_mysql_fetchrow( $dbh ) ;
	$chats_accepted = ( isset( $data["total"] ) ) ? $data["total"] : 0 ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Operator </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery.tools.min.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery_md5.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/dn.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/modernizr.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var opwin ;
	var menu ;
	var theme = "<?php echo $opinfo["theme"] ?>" ;
	var base_url = ".." ; // needed for function play_sound()
	var embed = 0 ;

	var audio_supported = HTML5_audio_support() ;
	var mp3_support = ( typeof( audio_supported["mp3"] ) != "undefined" ) ? 1 : 0 ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		$('#op_launch_btn_popup').on('mouseover mouseout', function(event) {
			$('#op_launch_btn_popup').toggleClass('op_launch_btn_focus') ;
		});
		$('#op_launch_btn_tab').on('mouseover mouseout', function(event) {
			$('#op_launch_btn_tab').toggleClass('op_launch_btn_focus') ;
		});

		init_menu_op() ;
		init_div_confirm() ;
		toggle_menu_op( "<?php echo $menu ?>" ) ;

		if ( !<?php echo count( $op_depts ) ?> ) { $('#no_dept').show() ; }

		<?php if ( $action && !$error ): ?>do_alert( 1, "Update Success" ) ; setTimeout( function(){ $('#div_alert_wrapper').fadeOut("slow") ; }, 3000 ) ;<?php endif ; ?>
		
		$('#div_thumb_'+theme).fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;

		if ( ( typeof( parent.isop ) != "undefined" ) && ( "<?php echo $action ?>" == "update_theme" ) )
			parent.reload_console(0) ;

		toggle_status(0) ;
		if ( typeof( parent.isop ) != "undefined" ) { parent.init_extra_loaded() ; }
	});

	function init_div_confirm()
	{
	}

	function launchit()
	{
		var open_status = $('#open_status').val() ;
		var open_win_popup = 1 ;
		var screen_width = screen.width ;
		var screen_height = screen.height ;
		var url = "operator.php?ses=<?php echo $ses ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&console=<?php echo $console ?>&pop=1&open_status="+open_status+"&"+unixtime() ;

		var console_width ;
		if ( screen_width > 1200 ) { console_width = 1100 }
		else if ( screen_width > 800 ) { console_width = 940 ; }
		else { console_width = 700 ; }
		var console_height = ( screen_height > 1000 ) ? 690 : 600 ;

		if ( !<?php echo count( $op_depts ) ?> )
			$('#no_dept').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
		else
		{
			if ( typeof( opwin ) == "undefined" )
			{
				if ( open_win_popup )
					opwin = window.open( url, "<?php echo $ses ?>", "scrollbars=yes,menubar=no,resizable=1,location=no,width="+console_width+",height="+console_height+",status=0" ) ;
				else
					opwin = window.open( url, "<?php echo $ses ?>" ) ;
			}
			else if ( opwin.closed )
			{
				if ( open_win_popup )
					opwin = window.open( url, "<?php echo $ses ?>", "scrollbars=yes,menubar=no,resizable=1,location=no,width="+console_width+",height="+console_height+",status=0" ) ;
				else
					opwin = window.open( url, "<?php echo $ses ?>" ) ;
			}

			if ( opwin )
			{
				opwin.focus() ;
			}
		}

		return true ;
	}

	function confirm_theme( thetheme, thethumb )
	{
		if ( theme != thetheme )
		{
			var height = $(document).height() ;

			$('#theme_'+thetheme).prop('checked', true) ;
			$('#div_theme_thumb').html( "<div style=\"background: url( "+thethumb+" ); background-position: top left; width: 85px; height: 54px; -moz-border-radius: 5px; border-radius: 5px;\">&nbsp;</div>") ;

			$('body').css({'overflow': 'hidden'}) ;
			$('#div_confirm').css({'height': height+'px'}).show() ;
			$('#div_confirm_body').center().show() ;
		}
	}

	function update_theme( thetheme )
	{
		location.href = 'index.php?console=<?php echo $console ?>&ses=<?php echo $ses ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&action=update_theme&theme='+thetheme ;
	}

	function update_theme_pre( theflag )
	{
		if ( theflag )
		{
			var theme = $('input:radio[name=theme]:checked').val() ;
			update_theme( theme ) ;
		}
		else
		{
			$('#theme_<?php echo $opinfo["theme"] ?>').prop('checked', true) ;

			$('#div_confirm').hide() ;
			$('#div_confirm_body').hide() ;
			$('body').css({'overflow': 'visible'}) ;
		}
	}

	function toggle_win_option( theflag )
	{
		if ( theflag == "popup" )
		{
			$('#open_win_popup').prop('checked', true) ;
			$('#op_launch_btn_tab').hide() ;
			$('#op_launch_btn_popup').show() ;
		}
		else
		{
			$('#open_win_tab').prop('checked', true) ;
			$('#op_launch_btn_popup').hide() ;
			$('#op_launch_btn_tab').show() ;
		}
	}

	function toggle_status( thestatus )
	{
		if ( parseInt( thestatus ) )
			$('#div_status').removeClass('info_good').addClass('info_error') ;
		else
			$('#div_status').removeClass('info_error').addClass('info_good') ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ); ?>

		<div id="op_title" class="edit_title" style="margin-bottom: 15px;"></div>

		<div id="op_go" style="margin: 0 auto;">
			<div id="no_dept" class="info_error" style="display: none; margin-bottom: 15px;"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Contact the Setup Admin to assign this account to a department.  Once assigned, <a href="./?ses=<?php echo $ses ?>&<?php echo time() ?>" style="color: #FFFFFF;">refresh</a> this page to continue.</div>

			<div>
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td>
						<div style="width: 290px;">
							<div style="border-bottom: 1px solid #CFD2D5; padding-bottom: 15px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td><div style="margin-top: 15px;"><a href="settings.php?ses=<?php echo $ses ?>"><img src="<?php print Util_Upload_GetLogo( "profile", $opinfo["opID"] ) ?>" width="55" height="55" border=0 style="border: 1px solid #DFDFDF;" class="round"></a></div></td>
									<td style="padding-left: 15px;">
										<div class="edit_title" style="font-weight: normal;">Chat Operator</div>
										<div style="margin-top: 5px;"><?php echo $opinfo["name"] ?></div>
										<div style="margin-top: 5px;"><?php echo $opinfo["email"] ?></div>
									</td>
								</tr>
								</table>
							</div>

							<div style="margin-top: 15px;"><img src="../pics/icons/chats.png" width="16" height="16" border="0" alt=""> <a href="reports.php?ses=<?php echo $ses ?>">Total Chats Accepted</a>: <?php echo $chats_accepted ?></div>
							<div style="margin-top: 15px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td><img src="../pics/icons/flag_blue.png" width="16" height="16" border="0" alt=""> <a href="transcripts.php?ses=<?php echo $ses ?>">Overall Rating</a>:</td>
									<td style="padding-left: 5px;"> <?php echo Util_Functions_Stars( "..", $overall ) ; ?></td>
								</tr>
								</table>
							</div>
						</div>
					</td>
					<td valign="bottom" style="padding-left: 25px;">
						<div id="div_status">
							<table cellspacing=0 cellpadding=0 border=0>
							<tr>
								<td>Open operator console with status :</td>
								<td>&nbsp; <select id="open_status" name="open_status" onChange="toggle_status(this.value)"><option value=0 selected>Online</option><option value=1>Offline</option></select></td>
							</tr>
							</table>
						</div>

						<div id="op_launch_btn_popup" style="margin-top: 35px; border: 1px solid #049BD8; padding: 10px; font-size: 18px; font-weight: bold; color: #FFFFFF; text-shadow: 1px 1px #049BD8; text-align: center; cursor: pointer;" onClick="launchit()" class="op_launch_btn round"><img src="../pics/icons/pointer.png" width="16" height="16" border="0" alt=""> Click to open Operator Console to accept visitor chat requests.</div>
					</td>
				</tr>
				</table>
			</div>
		</div>

		<div id="op_themes" style="display: none; margin: 0 auto;">
			<img src="../pics/icons/themes.png" width="16" height="16" border="0" alt=""> If the operator console is open, refresh the console window for the new theme to take affect.

			<div id="div_alert_wrapper" style="margin-top: 25px;"><span id="div_alert"></span></div>
			<form>
			<table cellspacing=0 cellpadding=2 border=0 width="100%" style="margin-top: 25px;">
			<tr>
				<td>
					<?php
						$dir_themes = opendir( "$CONF[DOCUMENT_ROOT]/themes/" ) ;

						$themes = Array() ;
						while ( $theme = readdir( $dir_themes ) )
							$themes[] = $theme ;
						closedir( $dir_themes ) ;

						sort( $themes, SORT_STRING ) ;
						for ( $c = 0; $c < count( $themes ); ++$c )
						{
							$theme = $themes[$c] ;
							$checked = ( $opinfo["theme"] == $theme ) ? "checked" : "" ;
							$class = ( $checked ) ? "info_box" : "info_neutral" ;
							$path_thumb = ( is_file( "../themes/$theme/thumb.png" ) ) ? "../themes/$theme/thumb.png" : "../pics/screens/thumb_theme_blank.png" ;

							if ( preg_match( "/[a-z]/i", $theme ) && ( $theme != "initiate" ) )
								print "<div class=\"li_op round\" style=\"padding: 5px; width: 150px; margin-bottom: 15px;\"><div id=\"div_thumb_$theme\" style=\"background: url( $path_thumb ); background-position: top left; height: 100px; -moz-border-radius: 5px; border-radius: 5px;\"><span class=\"$class\" style=\"cursor: pointer;\" onClick=\"confirm_theme('$theme', '$path_thumb')\" id=\"span_$theme\"><input type=\"radio\" name=\"theme\" id=\"theme_$theme\" value=\"$theme\" $checked> $theme</span></div></div>" ;
						}
					?>
					<div style="clear: both;"></div>
				</td>
			</tr>
			</table>
			</form>
		</div>

<div id="div_confirm" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">&nbsp;</div>
<div id="div_confirm_body" class="info_info" style="display: none; position: absolute; width: 350px; margin: 0 auto; top: 100px; z-index: 21;">
	<div class="info_box" style="padding: 25px;">
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td><div id="div_theme_thumb" class="li_mapp round" style="width: 85px; height: 54px;"></div><div class="clear:both;"></div></td>
			<td style="padding-left: 15px;">
				<div id="confirm_title">Select this theme?</div>
				<div style="margin-top: 15px;"><button type="button" onClick="update_theme_pre(1)" class="input_button" class="btn">Yes</button> &nbsp; &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="update_theme_pre(0)">cancel</span></div>
			</td>
		</tr>
		</table>
	</div>
</div>

<?php include_once( "./inc_footer.php" ); ?>

