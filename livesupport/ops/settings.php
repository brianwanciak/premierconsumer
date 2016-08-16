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
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "n" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$wp = Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) ;
	$auto = Util_Format_Sanatize( Util_Format_GetVar( "auto" ), "n" ) ;
	$jump = Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ; if ( !$jump ) { $jump = "pic" ; }
	$agent = isset( $_SERVER["HTTP_USER_AGENT"] ) ? $_SERVER["HTTP_USER_AGENT"] : "&nbsp;" ;
	$menu = "settings" ; $error = "" ;

	if ( $action == "update" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_File.php" ) ;

		LIST( $error, $filename ) = Util_Upload_File( "profile", $opinfo["opID"] ) ;
	}
	else if ( $action == "clear" )
	{
		$dir_files = glob( $CONF["CONF_ROOT"]."/profile_$opinfo[opID].*", GLOB_NOSORT ) ;
		$total_dir_files = count( $dir_files ) ;
		if ( $total_dir_files )
		{
			for ( $c = 0; $c < $total_dir_files; ++$c )
			{
				if ( $dir_files[$c] && is_file( $dir_files[$c] ) ) { unlink( $dir_files[$c] ) ; }
			}
		}
	}
	else if ( $action == "update_password" )
	{
		$cpass = Util_Format_Sanatize( Util_Format_GetVar( "cpass" ), "ln" ) ;
		$npass = Util_Format_Sanatize( Util_Format_GetVar( "npass" ), "ln" ) ;
		$vnpass = Util_Format_Sanatize( Util_Format_GetVar( "vnpass" ), "ln" ) ;

		if ( $cpass != md5( $opinfo["password"] ) )
			$error = "Current password is invalid." ;
		else if ( $vnpass != md5( $npass ) )
			$error = "Verify password does not match." ;
		else
		{
			include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
			Ops_update_OpValue( $dbh, $opinfo["opID"], "password", $npass ) ;
		} $action = "success" ; $jump = "password" ;
	}
	else if ( $action == "update_nsleep" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;
		Ops_update_OpVarValue( $dbh, $opinfo["opID"], "nsleep", $value ) ; database_mysql_close( $dbh ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
		print $json_data ; exit ;
	}
	else if ( $action == "update_shorts" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;
		Ops_update_OpVarValue( $dbh, $opinfo["opID"], "shorts", $value ) ; database_mysql_close( $dbh ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
		print $json_data ; exit ;
	}
	else if ( $action == "success" )
	{
		// sucess action is an indicator to show the success alert as well
		// as bypass the reloading of the operator console
	}
	else { $error = "invalid action" ; }

	$opvars = Ops_get_OpVars( $dbh, $opinfo["opID"] ) ;
	$auto_login_enabled = ( isset( $_COOKIE["phplive_auto_login_token"] ) && $_COOKIE["phplive_auto_login_token"] ) ? 1 : 0 ;
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
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery_md5.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/dn.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var opwin ;
	var menu ;
	var dn = dn_check() ;
	var embed = 0 ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu_op() ;
		toggle_menu_op( "<?php echo $menu ?>" ) ;
		show_div( "<?php echo $jump ?>" ) ;

		<?php if ( $action && $error ): ?>
		do_alert_div( "..", 0, "<?php echo $error ?>" ) ;
		<?php elseif ( $action ): ?>
		do_alert(1, "Success" ) ;
		<?php endif ; ?>

		if ( ( typeof( parent.isop ) != "undefined" ) && ( ( "<?php echo $action ?>" == "update" ) || ( "<?php echo $action ?>" == "clear" ) ) && ( "<?php echo $error ?>" == "" ) )
			parent.reload_console(0) ;
		else if ( typeof( parent.isop ) != "undefined" ) { parent.init_extra_loaded() ; }
	});

	function show_div( thediv )
	{
		var divs = Array( "pic", "shorts", "auto", "password", "sleep" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#op_'+divs[c]).hide() ;
			$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('#op_'+thediv).show() ;
		$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function update_password()
	{
		if ( $('#cpass_temp').val() == "" )
			do_alert( 0, "Please provide the current password." ) ;
		else if ( $('#npass_temp').val() == "" )
			do_alert( 0, "Please provide the new password." ) ;
		else if ( $('#npass_temp').val() != $('#vnpass_temp').val() )
			do_alert( 0, "New password does not match." ) ;
		else
		{
			$('#cpass').val( phplive_md5( phplive_md5( $('#cpass_temp').val() ) ) ) ;
			$('#npass').val( phplive_md5( $('#npass_temp').val() ) ) ;
			$('#vnpass').val( phplive_md5( phplive_md5( $('#npass_temp').val() ) ) ) ;
			$('#theform').submit() ;
		}
	}

	function update_auto_login( thevalue )
	{
		var json_data = new Object ;

		$.ajax({
			type: "POST",
			url: "../index.php",
			data: "action=update_auto_login&value="+thevalue+"&"+unixtime(),
			success: function(data){
				eval(data) ;

				if ( json_data.status )
					do_alert( 1, "Success" ) ;
				else
					do_alert( 0, "Error processing request.  Please try again." ) ;
			}
		});
	}

	function update_nsleep( thevalue )
	{
		var json_data = new Object ;

		$.ajax({
			type: "POST",
			url: "settings.php",
			data: "action=update_nsleep&value="+thevalue+"&ses=<?php echo $ses ?>&"+unixtime(),
			success: function(data){
				eval(data) ;

				if ( json_data.status )
				{
					if ( typeof( parent.isop ) != "undefined" )
						parent.reload_console(0) ;
					else
						do_alert( 1, "Success" ) ;
				}
				else
					do_alert( 0, "Error processing request.  Please try again." ) ;
			}
		});
	}

	function update_shorts( thevalue )
	{
		var json_data = new Object ;

		$.ajax({
			type: "POST",
			url: "settings.php",
			data: "action=update_shorts&value="+thevalue+"&ses=<?php echo $ses ?>&"+unixtime(),
			success: function(data){
				eval(data) ;

				if ( json_data.status )
				{
					if ( typeof( parent.isop ) != "undefined" )
						parent.reload_console(0) ;
					else
						do_alert( 1, "Success" ) ;
				}
				else
					do_alert( 0, "Error processing request.  Please try again." ) ;
			}
		});
	}

	function confirm_clear()
	{
		if ( confirm( "Really clear this operator profile picture and use Global Default?" ) )
		{
			location.href = "settings.php?ses=<?php echo $ses ?>&action=clear&"+unixtime() ;
		}
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ); ?>

		<div id="op_title" class="edit_title" style="margin-bottom: 15px;"></div>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="show_div('pic')" id="menu_pic">Profile Picture</div>
			<div class="op_submenu" onClick="show_div('shorts')" id="menu_shorts">Chat Session Shortcuts</div>
			<div class="op_submenu" onClick="show_div('auto')" id="menu_auto">Automatic Login</div>
			<div class="op_submenu" onClick="show_div('sleep')" id="menu_sleep">Computer Sleep Lock</div>
			<div class="op_submenu" onClick="show_div('password')" id="menu_password"><img src="../pics/icons/key.png" width="12" height="12" border="0" alt=""> Password</div>
			<div style="clear: both"></div>
		</div>

		<div id="op_pic" style="display: none; margin-top: 15px;">
			<img src="../pics/icons/vcard.png" width="16" height="16" border="0" alt=""> Your Operator Profile Picture

			<div style="margin-top: 15px;">
				<img src="<?php print Util_Upload_GetLogo( "profile", $opinfo["opID"] ) ?>" width="55" height="55" border=0 style="border: 1px solid #DFDFDF;" class="round"> &nbsp; &nbsp;

				<?php if ( $opinfo["opID"] && ( Util_Upload_GetLogo( "profile", 0 ) != Util_Upload_GetLogo( "profile", $opinfo["opID"] ) ) ): ?>
				<img src="../pics/icons/reset.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="confirm_clear()">clear operator profile picture and use the default image</a>
				<?php endif ; ?>
			</div>
			<div style="margin-top: 15px;">
				<?php if ( $opinfo["pic"] && ( isset( $VALS['PROFILE'] ) && ( $VALS['PROFILE'] == 1 ) ) ): ?>
				<div><span class="info_good">Your profile picture will be displayed to the visitor during a chat session.</span></div>
				<?php else: ?>
				<div><span class="info_error">Your profile picture is not visible to the visitor.  To update the visible setting, please contact the Setup Admin.</span></div>
				<?php endif ; ?>

				<div id="div_alert" style="display: none; margin-top: 15px; margin-bottom: 25px;"></div>
				<?php if ( isset( $opvars["pic_edit"] ) && $opvars["pic_edit"] ): ?>
				<div style="margin-top: 15px;">
					<form method="POST" action="settings.php?submit" enctype="multipart/form-data">
					<input type="hidden" name="action" value="update">
					<input type="hidden" name="ses" value="<?php echo $ses ?>">
					<input type="hidden" name="MAX_FILE_SIZE" value="20000">
					<div id="div_alert" style="display: none; margin-top: 15px; margin-bottom: 25px;"></div>
					<div style="margin-top: 15px;">* profile picture should be <b>55 pixels width</b> and <b>55 pixels height</b>.  The image will be automatcially scaled to fit the dimensions.</div>
					<div style="margin-top: 15px;">
						<input type="file" name="profile" size="30"><p>
						<input type="submit" value="Upload Picture" style="margin-top: 10px;" class="btn">
					</div>
					</form>
				</div>
				<?php else: ?>
				<div style="margin-top: 15px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> To update your profile picture and the visible setting, please contact the Setup Admin.</div>
				<?php endif ; ?>
			</div>
		</div>

		<div id="op_shorts" style="display: none; margin-top: 15px;">
			<img src="../pics/icons/code_doc.png" width="16" height="16" border="0" alt=""> Chat Session Shortcuts

			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td valign="top" width="250">
						<div style="text-align: justify;">Enable chat session shortcut commands that will process various actions.  Shortcut commands are to be entered at the operator console textarea during a chat session.  Shortcut commands begin with a forward slash (/) character.</div>
						<div style="margin-top: 10px;">
							<div class="info_good" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#shorts_on').prop('checked', true);update_shorts(1);"><input type="radio" name="shorts" id="shorts_on" value=1 <?php echo ( isset( $opvars["shorts"] ) && $opvars["shorts"] ) ? "checked" : "" ?> > On</div>
							<div class="info_error" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#shorts_off').prop('checked', true);update_shorts(0);"><input type="radio" name="shorts" id="shorts_off" value=0 <?php echo ( !isset( $opvars["shorts"] ) || !$opvars["shorts"] ) ? "checked" : "" ?>> Off</div>
							<div style="clear: both;"></div>
						</div>
					</td>
					<td valign="top" style="padding-left: 50px;">
						<div><span class="info_box">Current list of shortcut commands:</span></div>
						<div style="margin-top: 10px;">
							<table cellspacing=1 cellpadding=5 border=0>
							<tr>
								<td style="background: #E1E1E1; font-weight: bold;"> /close </td>
								<td style="background: #F7F7F7;">close (disconnect) the current chat session</td>
							</tr>
							<tr>
								<td style="background: #E1E1E1; font-weight: bold;"> /exit </td>
								<td style="background: #F7F7F7;">close (disconnect) the current chat session</td>
							</tr>
							<tr>
								<td style="background: #E1E1E1; font-weight: bold;"> /n </td>
								<td style="background: #F7F7F7;">toggle to the next chat session</td>
							</tr>
							<tr>
								<td style="background: #E1E1E1; font-weight: bold;"> /t </td>
								<td style="background: #F7F7F7;">toggle to the next chat session</td>
							</tr>
							<tr>
								<td style="background: #E1E1E1; font-weight: bold;"> /nolink </td>
								<td style="background: #F7F7F7;">(always enabled) do not autolink URLs</td>
							</tr>
							</table>
						</div>
					</td>
				</tr>
				</table>
			</div>
		</div>

		<div id="op_auto" style="display: none; margin-top: 15px;">
			<img src="../pics/icons/vcard.png" width="16" height="16" border="0" alt=""> Automatic Login

			<div style="margin-top: 15px;">
				Automatically login to your Operator area without providing the login credentials on the signin screen.

				<div style="margin-top: 10px;">
					<div class="info_good" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#auto_login_on').prop('checked', true);update_auto_login(1);"><input type="radio" name="auto_login" id="auto_login_on" value=1 <?php echo ( $auto_login_enabled ) ? "checked" : "" ?> > On</div>
					<div class="info_error" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#auto_login_off').prop('checked', true);update_auto_login(0);"><input type="radio" name="auto_login" id="auto_login_off" value=0 <?php echo ( !$auto_login_enabled ) ? "checked" : "" ?>> Off</div>
					<div style="clear: both;"></div>
				</div>
			</div>
		</div>

		<div id="op_sleep" style="display: none; margin-top: 15px;">
			<img src="../pics/icons/computer.png" width="16" height="16" border="0" alt=""> Computer Sleep Lock

			<div style="margin-top: 15px;">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td valign="top" width="250">
						<div style="text-align: justify;">Prevent the computer from powering down to "system sleep".  For most modern computers, "system sleep" mode may pause network connections and web browser processes.  This will cause the operator console to go offline and unable to receive new chat requests.  It is recommended to keep this setting to "On".</div>

						<?php if ( !preg_match( "/(MSIE 6)|(MSIE 7)|(MSIE 8)/i", $agent ) ): ?>
						<div style="margin-top: 10px;">
							<div class="info_good" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#nsleep_on').prop('checked', true);update_nsleep(1);"><input type="radio" name="nsleep" id="nsleep_on" value=1 <?php echo ( $opvars["nsleep"] ) ? "checked" : "" ?> > On</div>
							<div class="info_error" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#nsleep_off').prop('checked', true);update_nsleep(0);"><input type="radio" name="nsleep" id="nsleep_off" value=0 <?php echo ( !$opvars["nsleep"] ) ? "checked" : "" ?>> Off</div>
							<div style="clear: both;"></div>
						</div>
						<?php else: ?>
						<div style="margin-top: 10px;"><span class="info_error">Sleep Lock is not supported for this browser.</span></div>
						<?php endif ; ?>

					</td>
					<td valign="top" style="padding-left: 50px;">
						<div><span class="info_box"><i>Sleep Lock</i> will have the following lock/prevent behaviors depending on the brower type:</span></div>
						<div style="margin-top: 10px;">
							<table cellspacing=1 cellpadding=5 border=0 width="100%">
							<tr>
								<td bgColor="#E1E1E1" align="center"><b>Browser</b></td>
								<td bgColor="#E1E1E1" align="center">Screen Saver</td>
								<td bgColor="#E1E1E1" align="center">Screen Shutdown</td>
								<td bgColor="#E1E1E1" align="center">Sleep Power Down</td>
							</tr>
							<tr>
								<td bgColor="#F7F7F7"><img src="../themes/default/browsers/Chrome.png" width="16" height="16" border="0" alt="Chrome" title="Chrome"> Chrome</td>
								<td bgColor="#F7F7F7" align="center"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""></td>
								<td bgColor="#F7F7F7" align="center"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""></td>
								<td bgColor="#F7F7F7" align="center"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""></td>
							</tr>
							<tr>
								<td bgColor="#F7F7F7"><img src="../themes/default/browsers/Firefox.png" width="16" height="16" border="0" alt="Firefox" title="Firefox"> Firefox</td>
								<td bgColor="#F7F7F7" align="center"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""></td>
								<td bgColor="#F7F7F7" align="center"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""></td>
								<td bgColor="#F7F7F7" align="center"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""></td>
							</tr>
							<tr>
								<td bgColor="#F7F7F7"><img src="../themes/default/browsers/IE.png" width="16" height="16" border="0" alt="IE (all)" title="IE (all)"> IE (9+)</td>
								<td bgColor="#F7F7F7" align="center">&nbsp;</td>
								<td bgColor="#F7F7F7" align="center">&nbsp;</td>
								<td bgColor="#F7F7F7" align="center"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""></td>
							</tr>
							<tr>
								<td bgColor="#F7F7F7"><img src="../themes/default/browsers/Safari.png" width="16" height="16" border="0" alt="Safari" title="Safari"> Safari</td>
								<td bgColor="#F7F7F7" align="center"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""></td>
								<td bgColor="#F7F7F7" align="center">&nbsp;</td>
								<td bgColor="#F7F7F7" align="center"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""></td>
							</tr>
							</table>
						</div>
					</td>
				</tr>
				</table>
			</div>
		</div>

		<div id="op_password" style="display: none; margin-top: 15px;">
			<?php if ( $action && $error ): ?>
				<div id="div_error" class="info_error" style="margin-bottom: 10px;"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt="">  <?php echo $error ?></div>
			<?php endif; ?>

			<form method="POST" action="settings.php?submit" id="theform">
			<input type="hidden" name="action" value="update_password">
			<input type="hidden" name="console" value="<?php echo $console ?>">
			<input type="hidden" name="auto" value="<?php echo $auto ?>">
			<input type="hidden" name="wp" value="<?php echo $wp ?>">
			<input type="hidden" name="ses" value="<?php echo $ses ?>">
			<input type="hidden" name="cpass" id="cpass" value="">
			<input type="hidden" name="npass" id="npass" value="">
			<input type="hidden" name="vnpass" id="vnpass" value="">
			<img src="../pics/icons/key.png" width="16" height="16" border="0" alt=""> Operator Account Password

			<div style="margin-top: 15px;">Current Password</div>
			<input type="password" class="input" name="cpass_temp" id="cpass_temp" size="30" maxlength="50" value="">

			<div style="margin-top: 15px;">New Password</div>
			<input type="password" class="input" name="npass_temp" id="npass_temp" size="30" maxlength="50" value="" onKeyPress="return noquotes(event)"><div style="font-size: 10px;">* letters and numbers</div>

			<div style="margin-top: 15px;">Verify New Password</div>
			<input type="password" class="input" name="vnpass_temp" id="vnpass_temp" size="30" maxlength="50" value="" onKeyPress="return noquotes(event)"><div style="font-size: 10px;">* letters and numbers</div>

			<div style="margin-top: 10px;"><input type="button" value="Update Password" onClick="update_password()" class="btn"></div>
			</form>
		</div>

<?php include_once( "./inc_footer.php" ); ?>
