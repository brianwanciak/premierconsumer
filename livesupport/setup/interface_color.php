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
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$lang = Util_Format_Sanatize( Util_Format_GetVar( "lang" ), "ln" ) ;

	if ( !isset( $CONF["lang"] ) ) { $CONF["lang"] = "english" ; } if ( !$lang ) { $lang = Util_Format_Sanatize( $CONF["lang"], "ln" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/lang_packs/$lang.php" ) ;

	$error = "" ;

	if ( $action == "update" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/put.php" ) ;

		$TXT_LIVECHAT = Util_Format_Sanatize( Util_Format_GetVar( "TXT_LIVECHAT" ), "notags" ) ;
		$TXT_CHAT_WELCOME = Util_Format_Sanatize( Util_Format_GetVar( "TXT_CHAT_WELCOME" ), "notags" ) ;
		$TXT_CHAT_WELCOME_SUBTEXT = Util_Format_Sanatize( Util_Format_GetVar( "TXT_CHAT_WELCOME_SUBTEXT" ), "notags" ) ;
		$TXT_DEPARTMENT = Util_Format_Sanatize( Util_Format_GetVar( "TXT_DEPARTMENT" ), "notags" ) ;
		$TXT_CHAT_SELECT_DEPT = Util_Format_Sanatize( Util_Format_GetVar( "TXT_CHAT_SELECT_DEPT" ), "notags" ) ;

		$lang_db = Lang_get_Lang( $dbh, $lang ) ; $db_lang_hash = Array() ;
		if ( isset( $lang_db["lang"] ) )
			$db_lang_hash = unserialize( $lang_db["lang_vars"] ) ;
		$LANG_TEMP = array_merge( $LANG, $db_lang_hash ) ;

		if ( ( $LANG_TEMP["TXT_LIVECHAT"] == $TXT_LIVECHAT ) && ( $LANG_TEMP["CHAT_WELCOME"] == $TXT_CHAT_WELCOME ) && ( $LANG_TEMP["CHAT_WELCOME_SUBTEXT"] == $TXT_CHAT_WELCOME_SUBTEXT ) && ( $LANG_TEMP["TXT_DEPARTMENT"] == $TXT_DEPARTMENT ) && ( $LANG_TEMP["CHAT_SELECT_DEPT"] == $TXT_CHAT_SELECT_DEPT ) )
		{
			// do not save since nothing changed.  but display a success message
		}
		else
		{
			$LANG = Array() ;
			$LANG["TXT_LIVECHAT"] = $TXT_LIVECHAT ;
			$LANG["CHAT_WELCOME"] = $TXT_CHAT_WELCOME ;
			$LANG["CHAT_WELCOME_SUBTEXT"] = $TXT_CHAT_WELCOME_SUBTEXT ;
			$LANG["TXT_DEPARTMENT"] = $TXT_DEPARTMENT ;
			$LANG["CHAT_SELECT_DEPT"] = $TXT_CHAT_SELECT_DEPT ;

			if ( !Lang_put_Lang( $dbh, $lang, serialize( $LANG ) ) )
				$error = "Error saving values." ;
			else
			{
				$lang_db = Array() ; $lang_db["lang"] = $lang ;
			}
		}
	}
	else if ( $action == "revert" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Lang/remove.php" ) ;

		Lang_remove_Lang( $dbh, $lang ) ;
	}
	else
	{
		$lang_db = Lang_get_Lang( $dbh, $lang ) ;
		if ( isset( $lang_db["lang"] ) )
		{
			$db_lang_hash = unserialize( $lang_db["lang_vars"] ) ;
			$LANG = array_merge( $LANG, $db_lang_hash ) ;
		}
	}
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

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
<script type="text/javascript" src="../js/jquery_md5.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "interface" ) ;

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>

		init_cover() ;
	});

	function select_lang( thelang )
	{
		location.href = "interface_lang.php?ses=<?php echo $ses ?>&lang="+thelang ;
	}

	function close_view() { } // dummy function needed for preview close
	function view_preview( theflag )
	{
		if ( theflag )
		{
			if ( !$('#numbers_cover').is(':visible') )
			{
				$('#numbers_cover').show() ;
			}
		}
		else
		{
			document.getElementById('iframe_widget_embed').contentWindow.preview_text( strip_tags( $('#TXT_LIVECHAT').val() ), strip_tags( $('#TXT_CHAT_WELCOME').val() ), strip_tags( $('#TXT_CHAT_WELCOME_SUBTEXT').val() ), strip_tags( $('#TXT_DEPARTMENT').val() ), strip_tags( $('#TXT_CHAT_SELECT_DEPT').val() ) ) ;
			$('#phplive_widget_embed_iframe').fadeOut("fast").fadeIn("fast") ;
			$('#numbers_cover').hide() ;
		}
	}

	function do_update()
	{
		$('#form_txt').submit() ;
	}

	function do_reset()
	{
		$('#form_txt').trigger("reset") ;
		view_preview(0) ;
		view_preview(1) ;
	}

	function do_revert()
	{
		if ( confirm( "Clear current text and revert to default text?" ) )
		{
			var unique = unixtime() ;
			location.href = "interface_lang.php?ses=<?php echo $ses ?>&action=revert&lang=<?php echo $lang ?>&"+unique ;
		}
	}

	function init_cover()
	{
		var pos = $('#iframe_widget_embed').position() ;
		var top = pos.top + 3 ;
		var left = pos.left ;
		$('#numbers_cover').css({'top': top, 'left': left}) ;
	}

	function strip_tags( thetext )
	{
		var tmp = document.createElement("DIV") ;
		tmp.innerHTML = thetext ;
		return tmp.textContent || tmp.innerText || "" ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='interface_themes.php?ses=<?php echo $ses ?>'">Themes</div>
			<div class="op_submenu" onClick="location.href='interface.php?ses=<?php echo $ses ?>&jump=logo'">Logo</div>
			<div class="op_submenu" onClick="location.href='interface.php?ses=<?php echo $ses ?>&jump=charset'">Character Set</div>
			<?php if ( phpversion() >= "5.1.0" ): ?><div class="op_submenu" onClick="location.href='interface.php?ses=<?php echo $ses ?>&jump=time'">Time Zone</div><?php endif; ?>
			<div class="op_submenu_focus" id="menu_lang">Language Text</div>
			<div class="op_submenu" onClick="location.href='interface.php?ses=<?php echo $ses ?>&jump=screen'">Login Screen</div>
			<div style="clear: both"></div>
		</div>

		<form method="POST" action="interface_lang.php?submit" enctype="multipart/form-data" id="form_txt">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="jump" id="jump" value="lang">
		<input type="hidden" name="ses" value="<?php echo $ses ?>">
		<input type="hidden" name="MAX_FILE_SIZE" value="50000">
		<div style="margin-top: 25px;">
			<table cellspacing=0 cellpadding=2 border=0 width="100%" style="margin-top: 15px;">
			<tr>
				<td></td>
				<td style="padding-left: 50px; padding-bottom: 5px;">
					<div>
						<select name="lang" id="lang" onChange="select_lang(this.value)">
						<?php
							$dir_langs = opendir( "$CONF[DOCUMENT_ROOT]/lang_packs/" ) ;

							$langs = Array() ;
							while ( $lang_ = readdir( $dir_langs ) )
								$langs[] = $lang_ ;
							closedir( $dir_langs ) ;
							
							sort( $langs, SORT_STRING ) ;
							for ( $c = 0; $c < count( $langs ); ++$c )
							{
								$lang_ = $langs[$c] ;

								if ( preg_match( "/[a-z]/i", $lang_ ) )
								{
									$lang_temp = preg_replace( "/(.php)/", "", $lang_ ) ;
									$lang_display = ucwords( $lang_temp ) ;
									$selected = $selected_display = "" ;
									if ( $lang == $lang_temp ) { $selected = "selected" ; $selected_display = " -" ; }

									print "<option value=\"$lang_temp\" $selected>$lang_display$selected_display</option>" ;
								}
							}
						?>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td valign="top" width="400">
					<div id='phplive_widget_embed_iframe' style='width: 400px; height: <?php echo $VARS_CHAT_HEIGHT_WIDGET ?>px; -moz-border-radius: 5px; border-radius: 5px;'>
						<iframe id='iframe_widget_embed' name='iframe_widget_embed' style='width: 100%; height: 100%; -moz-border-radius: 5px; border-radius: 5px; border: 0px;' src='../phplive_embed.php?marquee_test=preview&lang=<?php echo $lang ?>' scrolling='no' border=0 frameborder=0></iframe>
					</div>
				</td>
				<td valign="top" width="100%" style="padding-left: 15px;">
					<div>
						<table cellspacing=0 cellpadding=5 border=0 width="100%">
						<tr>
							<td width="22"><img src="../pics/icons/numbers/1.png" width="22" height="22" border="0" alt=""></td>
							<td><input type="text" style="width: 90%;" maxlength="35" name="TXT_LIVECHAT" id="TXT_LIVECHAT" onFocus="view_preview(1)" value="<?php echo $LANG["TXT_LIVECHAT"] ?>"></td>
						</tr>
						<tr>
							<td width="22"><img src="../pics/icons/numbers/2.png" width="22" height="22" border="0" alt=""></td>
							<td><input type="text" style="width: 90%;" maxlength="165" name="TXT_CHAT_WELCOME" id="TXT_CHAT_WELCOME" onFocus="view_preview(1)" value="<?php echo $LANG["CHAT_WELCOME"] ?>"></td>
						</tr>
						<tr>
							<td width="22"><img src="../pics/icons/numbers/3.png" width="22" height="22" border="0" alt=""></td>
							<td><input type="text" style="width: 90%;" maxlength="255" name="TXT_CHAT_WELCOME_SUBTEXT" id="TXT_CHAT_WELCOME_SUBTEXT" onFocus="view_preview(1)" value="<?php echo $LANG["CHAT_WELCOME_SUBTEXT"] ?>"></td>
						</tr>
						<tr>
							<td width="22"><img src="../pics/icons/numbers/4.png" width="22" height="22" border="0" alt=""></td>
							<td><input type="text" style="width: 90%;" maxlength="165" name="TXT_DEPARTMENT" id="TXT_DEPARTMENT" onFocus="view_preview(1)" value="<?php echo $LANG["TXT_DEPARTMENT"] ?>"></td>
						</tr>
						<tr>
							<td width="22"><img src="../pics/icons/numbers/5.png" width="22" height="22" border="0" alt=""></td>
							<td><input type="text" style="width: 90%;" maxlength="55" name="TXT_CHAT_SELECT_DEPT" id="TXT_CHAT_SELECT_DEPT" onFocus="view_preview(1)" value="<?php echo $LANG["CHAT_SELECT_DEPT"] ?>"></td>
						</tr>
						<tr>
							<td width="22">&nbsp;</td>
							<td style="padding-top: 25px;">
								<div><span class="info_box"><img src="../pics/icons/arrow_left.png" width="16" height="15" border="0" alt=""> <a href="JavaScript:void(0)" onClick="view_preview(0)">view how it looks</a></span></div>
								<div style="margin-top: 35px;">
									<button type="button" class="btn" onClick="do_update()">Update Text</button> &nbsp; &nbsp; <button type="button" class="btn" onClick="do_reset()">Reset</button> &nbsp; &nbsp; <?php if ( isset( $lang_db["lang"] ) ): ?>or <a href="JavaScript:void(0)" onClick="do_revert()">revert to default</a><?php endif ; ?>
								</div>

								<div style="margin-top: 45px;">
									<div class="edit_title">Leave a Message Offline Text</div>
									<div style="margin-top: 5px;">
										<table cellspacing=0 cellpadding=0 border=0>
										<tr>
											<td><img src="../pics/icons/info.png" width="14" height="14" border="0" alt=""></td>
											<td style="padding-left: 5px;">The Offline text can be updated for each department <a href="depts.php?ses=<?php echo $ses ?>&ftab=msg">Offline Msg</a> area.</td>
										</tr>
										</table>
									</div>
								</div>
							</td>
						</tr>
						</table>
					</div>
				</td>
			</tr>
			</table>
		</div>
		</form>

		<div id="numbers_cover" style="position: absolute; top: 0px; left; 0px; width: 400px; height: <?php echo $VARS_CHAT_HEIGHT_WIDGET ?>px; background: url( ../pics/setup/numbers_cover.png ) no-repeat; background-position: 0px 0px; z-Index: 100;">&nbsp;</div>

<?php include_once( "./inc_footer.php" ) ?>
