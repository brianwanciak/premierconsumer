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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "n" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$wp = Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) ;
	$auto = Util_Format_Sanatize( Util_Format_GetVar( "auto" ), "n" ) ;
	$menu = "notifications" ;
	$jump = Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ; if ( !$jump ) { $jump = "sounds" ; }
	$error = "" ;

	$opvars = Ops_get_OpVars( $dbh, $opinfo["opID"] ) ;
	$op_sounds = isset( $VALS["op_sounds"] ) ? unserialize( $VALS["op_sounds"] ) : Array() ;
	if ( isset( $op_sounds[$opinfo["opID"]] ) ) { $op_sounds_vals = $op_sounds[$opinfo["opID"]] ; $opinfo["sound1"] = $op_sounds_vals[0] ; $opinfo["sound2"] = $op_sounds_vals[1] ; } else { $opinfo["sound1"] = "default" ; $opinfo["sound2"] = "default" ; }

	$sound_on = ( !isset( $opvars["sound"] ) || ( isset( $opvars["sound"] ) && $opvars["sound"] ) ) ? "checked" : "" ;
	$sound_off = ( $sound_on == "checked" ) ? "" : "checked" ;
	$blink_on = ( !isset( $opvars["blink"] ) || ( isset( $opvars["blink"] ) && !$opvars["blink"] ) ) ? "" : "checked" ;
	$blink_off = ( $blink_on == "checked" ) ? "" : "checked" ;
	$blink_r_on = ( !isset( $opvars["blink_r"] ) || ( isset( $opvars["blink_r"] ) && !$opvars["blink_r"] ) ) ? "" : "checked" ;
	$blink_r_off = ( $blink_r_on == "checked" ) ? "" : "checked" ;
	$dn_always_on = ( !isset( $opvars["dn_always"] ) || ( isset( $opvars["dn_always"] ) && $opvars["dn_always"] ) ) ? "checked" : "" ;
	$dn_always_off = ( $dn_always_on == "checked" ) ? "" : "checked" ;

	if ( $action == "update_sound" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
		$sound1 = Util_Format_Sanatize( Util_Format_GetVar( "sound1" ), "ln" ) ;
		$sound2 = Util_Format_Sanatize( Util_Format_GetVar( "sound2" ), "ln" ) ;

		$op_sounds[$opinfo["opID"]] = Array( $sound1, $sound2 ) ;
		Util_Vals_WriteToFile( "op_sounds", serialize( $op_sounds ) ) ;
		$opinfo["sound1"] = $sound1 ; $opinfo["sound2"] = $sound2 ;
		
		$jump = "sounds" ;
	}
	else if ( $action == "mreset" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;

		$now = time()-60 ;
		Ops_update_OpValue( $dbh, $opinfo["opID"], "sms", $now ) ;
		Ops_update_OpValue( $dbh, $opinfo["opID"], "smsnum", "" ) ;
		$opinfo["sms"] = $now ; $opinfo["smsnum"] = "" ;

		$action = "success" ;
	}
	else if ( $action == "success" )
	{
		// sucess action is an indicator to show the success alert as well
		// as bypass the reloading of the operator console
	}
	else
		$error = "invalid action" ;

	$smsnum = Array() ;
	$tok = strtok( base64_decode( $opinfo["smsnum"] ), '@' ) ;
	while ( $tok !== false ) { $smsnum[] = $tok ; $tok = strtok( '@' ) ; }
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
	var base_url = ".." ; // needed for function play_sound()
	var focused = 1 ; // needed for function play_sound() and dn_show()
	var dn = dn_check() ;
	var dn_always = 1 ; // always show since demo notification
	var dn_enabled_response = <?php echo ( isset( $opvars["dn_response"] ) ) ? $opvars["dn_response"] : 0 ; ?> ;
	var dn_enabled_request = <?php echo ( isset( $opvars["dn_request"] ) ) ? $opvars["dn_request"] : 0 ; ?> ;
	var dn_counter = 0 ;
	var smsnum ;
	var smsreset = 0 ;
	var st_sound ;
	var sound_volume = ( typeof( parent.isop ) != "undefined" ) ? parent.sound_volume : 1 ;
	var wp = <?php echo $wp ?> ;
	var embed = 0 ;
	var mobile = 0 ;

	var global_sound = "<?php echo ( $sound_on ) ? 1 : 0 ; ?>" ;
	var global_blink = "<?php echo ( $blink_on ) ? 1 : 0 ; ?>" ;
	var global_blink_r = "<?php echo ( $blink_r_on ) ? 1 : 0 ; ?>" ;
	var global_dn_always = "<?php echo ( $dn_always_on ) ? 1 : 0 ; ?>" ;

	var audio_supported = HTML5_audio_support() ;
	var mp3_support = ( typeof( audio_supported["mp3"] ) != "undefined" ) ? 1 : 0 ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu_op() ;
		toggle_menu_op( "notifications" ) ;
		show_div( "<?php echo $jump ?>" ) ;

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
		<?php if ( ( $opinfo["sms"] == 1 ) || ( $opinfo["sms"] == 2 ) ): ?>do_sms_enabled( <?php echo $opinfo["sms"] ?> ) ; smsnum = "<?php echo $smsnum[0] ?>@<?php echo $smsnum[1] ?>" ; $('#sma_reset').show() ;<?php endif ; ?>

		<?php if ( $action && !$error && ( $action != "update_password" ) ): ?>
		if ( ( typeof( parent.isop ) != "undefined" ) && ( "<?php echo $action ?>" != "success" ) )
			parent.reload_console(0) ;
		<?php else: ?>
		if ( dn_enabled_response ) { $('#dn_enabled_response_off').hide() ; $('#dn_enabled_response_on').show() ; }
		if ( audio_supported && ( typeof( parent.isop ) != "undefined" ) ) { $("input[name=volume][value='"+sound_volume+"']").prop("checked", true) ; $('#tr_sound_volume').show() ; }

		var dn_browser = dn_check_browser() ;
		if ( ( dn_browser == "null" ) && !wp )
			$('#dn_unavailable').show() ;
		else
		{
			var dn = dn_check() ;

			if ( ( dn == -1 ) && ( dn_browser == "firefox" ) ) { $('#dn_firefox').show() ; }
			else
			{
				if ( !dn )
				{
					$('#dn_enabled').show() ;

					if ( dn_enabled_request ) { $('#dn_enabled_on').show() ; }
					else { $('#dn_enabled_off').show() ; }
				}
				else if ( dn == 2 ) { $('#dn_disabled').show() ; }
				else { $('#dn_request').show() ; }
			}
		}

		if ( typeof( parent.isop ) != "undefined" ) { parent.init_extra_loaded() ; }
		<?php endif ; ?>
	});

	function show_div( thediv )
	{
		var divs = Array( "sounds", "dn", "sms" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#op_'+divs[c]).hide() ;
			$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('#op_'+thediv).show() ;
		$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function demo_sound1( theflag )
	{
		var sound = $('#sound1').val() ;

		clear_sound('new_request') ;
		if ( theflag )
			play_sound(1, 'new_request', 'new_request_'+sound) ;
	}

	function demo_sound2()
	{
		var sound = $('#sound2').val() ;

		clear_sound('new_request') ;
		play_sound(0, 'new_text', 'new_text_'+sound) ;
	}

	function update_sound()
	{
		var sound1 = $('#sound1').val() ;
		var sound2 = $('#sound2').val() ;

		location.href = 'notifications.php?ses=<?php echo $ses ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&console=<?php echo $console ?>&action=update_sound&sound1='+sound1+'&sound2='+sound2 ;
	}

	function sms_send()
	{
		var json_data = new Object ;
		var phonenum = $('#phonenum').val().replace( /[^0-9a-z_\-\.]/g, "" )  ; $('#phonenum').val( phonenum ) ;
		var carrier = encodeURIComponent( $('#carrier').val() ) ;

		$('#btn_sms_send').attr('disabled', true) ;

		if ( !phonenum || !carrier )
		{
			$('#btn_sms_send').attr('disabled', false) ;
			do_alert( 0, "Blank input is invalid." ) ;
		}
		else if ( !check_email( phonenum+"@"+carrier ) )
		{
			$('#btn_sms_send').attr('disabled', false) ;
			do_alert( 0, "Carrier (gateway) format is invalid." ) ;
		}
		else if ( !smsreset && ( smsnum == phonenum+"@"+carrier ) )
		{
			$('#btn_sms_send').attr('disabled', false) ;
			do_alert( 1, "Your mobile has already been verified." ) ;
		}
		else
		{
			if ( !smsreset && smsnum && ( smsnum != phonenum+"@"+carrier ) )
			{
				if ( !confirm( "You are about to change your SMS information.  Are you sure?" ) )
				{
					$('#btn_sms_send').attr('disabled', false) ;
					$('#phonenum').val( "<?php echo isset( $smsnum[0] ) ? $smsnum[0] : "" ; ?>" ) ;
					$('#carrier').val( "<?php echo isset( $smsnum[1] ) ? $smsnum[1] : "" ; ?>" ) ;
					return false ;
				}
			}

			$.ajax({
				type: "POST",
				url: "../ajax/chat_actions_op_ext.php",
				data: "ses=<?php echo $ses ?>&action=sms_send&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&console=<?php echo $console ?>&phonenum="+phonenum+"&carrier="+carrier+"&"+unixtime(),
				success: function(data){
					eval(data) ;

					setTimeout( function(){ $('#btn_sms_send').attr('disabled', false) ; }, 5000 ) ;
					if ( json_data.status )
					{
						smsreset = 1 ;
						smsnum = phonenum+"@"+carrier ;
						$('#sms_enabled').hide() ;
						do_alert( 1, "Verification code sent to "+smsnum ) ;
					}
					else
						do_alert( 0, json_data.error ) ;
				}
			});
		}
	}

	function sms_verify()
	{
		var json_data = new Object ;
		var code = $('#code').val().replace( /[^0-9]/g, "" )  ; $('#code').val( code ) ;

		$('#btn_sms_verify').attr('disabled', true) ;

		if ( !parseInt( code ) )
		{
			$('#btn_sms_verify').attr('disabled', false) ;
			do_alert( 0, "Blank verification code is invalid." ) ;
		}
		else
		{
			$.ajax({
				type: "POST",
				url: "../ajax/chat_actions_op_ext.php",
				data: "ses=<?php echo $ses ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&console=<?php echo $console ?>&action=sms_verify&code="+code+"&"+unixtime(),
				success: function(data){
					eval(data) ;

					setTimeout( function(){ $('#btn_sms_verify').attr('disabled', false) ; }, 5000 ) ;
					if ( json_data.status )
					{
						if ( typeof( json_data.error ) == "undefined" )
							location.href = "./notifications.php?action=success&jump=sms&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&console=<?php echo $console ?>&ses=<?php echo $ses ?>" ;
						else
						{
							$('#code').val("") ;
							do_alert( 1, json_data.error ) ;
						}
					}
					else
						do_alert( 0, json_data.error ) ;
				}
			});
		}
	}

	function sms_toggle( theflag )
	{
		var json_data = new Object ;

		$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_ext.php",
			data: "ses=<?php echo $ses ?>&action=sms_verify&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&console=<?php echo $console ?>&code="+theflag+"&"+unixtime(),
			success: function(data){
				eval(data) ;

				if ( json_data.status )
				{
					if ( theflag == 1 ){ $('#sms_enabled_off').hide() ; $('#sms_enabled_on').show() ; }
					else { $('#sms_enabled_on').hide() ; $('#sms_enabled_off').show() ; }
				}
				else
					do_alert( 0, json_data.error ) ;
			}
		});
	}

	function reset_sms()
	{
		if ( confirm( "You are about to clear your SMS information.  Are you sure?" ) )
			location.href = "./notifications.php?action=mreset&jump=sms&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&console=<?php echo $console ?>&ses=<?php echo $ses ?>" ;
	}

	function do_sms_enabled( theflag )
	{
		if ( theflag == 1 ){ $('#sms_enabled_off').hide() ; $('#sms_enabled_on').show() ; }
		else { $('#sms_enabled_on').hide() ; $('#sms_enabled_off').show() ; }
		$('#code').val("") ;
		$('#sms_enabled').show() ;
		$('#sms_send').hide() ;
		$('#sma_reset').show() ;
	}

	function do_sms_edit()
	{
		if ( $('#sms_send').is(':visible') )
		{
			$('#icon_sms_arrow').attr( 'src', '../pics/icons/arrow_grey.png' ) ;
			$('#sms_send').hide() ;
		}
		else
		{
			$('#icon_sms_arrow').attr( 'src', '../pics/icons/arrow_grey_bottom.png' ) ;
			$('#sms_send').show() ;
		}
	}

	function dn_toggle( theflag )
	{
		var json_data = new Object ;

		$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_ext.php",
			data: "ses=<?php echo $ses ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&console=<?php echo $console ?>&action=dn_toggle&dn="+theflag+"&"+unixtime(),
			success: function(data){
				eval(data) ;

				if ( json_data.status )
				{
					if ( theflag == 1 ){ $('#dn_enabled_off').hide() ; $('#dn_enabled_on').show() ; }
					else { $('#dn_enabled_on').hide() ; $('#dn_enabled_off').show() ; }

					if ( typeof( parent.dn_enabled_request ) != "undefined" )
						parent.dn_enabled_request = theflag ;
				}
				else
					do_alert( 0, "Error: Could not update DN value." ) ;
			}
		});
	}

	function dn_toggle_response( theflag )
	{
		var json_data = new Object ;

		$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_ext.php",
			data: "ses=<?php echo $ses ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&console=<?php echo $console ?>&action=dn_toggle_response&dn="+theflag+"&"+unixtime(),
			success: function(data){
				eval(data) ;

				if ( json_data.status )
				{
					if ( theflag == 1 ){ $('#dn_enabled_response_off').hide() ; $('#dn_enabled_response_on').show() ; }
					else { $('#dn_enabled_response_on').hide() ; $('#dn_enabled_response_off').show() ; }

					if ( typeof( parent.dn_enabled_response ) != "undefined" )
						parent.dn_enabled_response = theflag ;
				}
				else
					do_alert( 0, "Error: Could not update DN response value." ) ;
			}
		});
	}

	function toggle_console_sound( thevalue )
	{
		if ( parseInt( global_sound ) != parseInt( thevalue ) )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/chat_actions_op_ext.php",
				data: "ses=<?php echo $ses ?>&action=console_sound&value="+thevalue+"&"+unixtime(),
				success: function(data){
					eval(data) ;

					if ( json_data.status )
					{
						global_sound = thevalue ;
						if ( typeof( parent.chat_sound ) != "undefined" )
						{
							parent.chat_sound = global_sound ;
							parent.print_chat_sound_image( parent.theme ) ;
						}
						if ( !parseInt( global_sound ) && !parseInt( global_blink ) ) { toggle_console_blink( 1, 0 ) ; $('#console_blink_on').prop('checked', true) ; }
						else if ( parseInt( global_sound ) ) { $('#div_console_blink_alert').hide() ; }
						do_alert( 1, "Success" ) ;
					}
					else
						do_alert( 0, "Error: Could not update value.  Please try again." ) ;
				}
			});
		}
	}

	function toggle_console_blink( thevalue, thealert )
	{
		if ( parseInt( global_blink ) != parseInt( thevalue ) )
		{
			if ( ( !parseInt( global_sound ) && !parseInt( thevalue ) ) || !thealert )
			{
				$('#div_console_blink_alert').fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
				$('#console_blink_on').prop('checked', true) ;
			}

			if ( !parseInt( global_sound ) && !thevalue ) {}
			else
			{
				var json_data = new Object ;

				$.ajax({
					type: "POST",
					url: "../ajax/chat_actions_op_ext.php",
					data: "ses=<?php echo $ses ?>&action=console_blink&value="+thevalue+"&"+unixtime(),
					success: function(data){
						eval(data) ;

						if ( json_data.status )
						{
							global_blink = thevalue ;
							if ( typeof( parent.isop ) != "undefined" ) { parent.console_blink = thevalue ; }
							if ( thealert ) { do_alert( 1, "Success" ) ; }
						}
						else
							do_alert( 0, "Error: Could not update value.  Please try again." ) ;
					}
				});
			}
		}
	}

	function toggle_console_blink_r( thevalue, thealert )
	{
		if ( parseInt( global_blink_r ) != parseInt( thevalue ) )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/chat_actions_op_ext.php",
				data: "ses=<?php echo $ses ?>&action=console_blink_r&value="+thevalue+"&"+unixtime(),
				success: function(data){
					eval(data) ;

					if ( json_data.status )
					{
						global_blink_r = thevalue ;
						if ( typeof( parent.isop ) != "undefined" ) { parent.console_blink_r = thevalue ; }
						if ( thealert ) { do_alert( 1, "Success" ) ; }
					}
					else
						do_alert( 0, "Error: Could not update value.  Please try again." ) ;
				}
			});
		}
	}

	function toggle_dn_always( thevalue )
	{
		if ( parseInt( global_dn_always ) != parseInt( thevalue ) )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/chat_actions_op_ext.php",
				data: "ses=<?php echo $ses ?>&action=dn_always&value="+thevalue+"&"+unixtime(),
				success: function(data){
					eval(data) ;

					if ( json_data.status )
					{
						global_dn_always = thevalue ;
						if ( typeof( parent.isop ) != "undefined" ) { parent.dn_always = thevalue ; }
						do_alert_div( base_url, 1, "Success" ) ;
						setTimeout( function(){ $('#div_alert').fadeOut("fast") }, 1500 ) ;
					}
					else
						do_alert( 0, "Error: Could not update value.  Please try again." ) ;
				}
			});
		}
		else
			do_alert( 1, "Success" ) ;
	}

	function update_volume( thevalue )
	{
		var sound = $('#sound1').val() ;

		sound_volume = thevalue ;
		if ( typeof( parent.isop ) != "undefined" ) { parent.sound_volume = sound_volume ; }

		$("input[name=volume][value='"+thevalue+"']").prop("checked", true) ;

		clear_sound('new_request') ;
		play_sound(0, 'new_request', 'new_request_'+sound) ;

		do_alert( 1, "Update Success" ) ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ); ?>

		<div id="op_title" class="edit_title" style="margin-bottom: 15px;"></div>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="show_div('sounds')" id="menu_sounds">Sound Alerts and Console Blink</div>
			<div class="op_submenu" onClick="show_div('dn')" id="menu_dn">Desktop Notification</div>
			<?php if ( $opinfo["sms"] ): ?><div class="op_submenu" onClick="show_div('sms')" id="menu_sms">SMS</div><?php endif ; ?>
			<div style="clear: both"></div>
		</div>

		<div id="op_sounds" style="display: none; margin-top: 15px;">
			<img src="../pics/icons/bell_start.png" width="16" height="16" border="0"> If the operator console is open, refresh the console window for the new sound to take affect.

			<table cellspacing=0 cellpadding=0 border=0 width="100%" style="margin-top: 25px;">
			<tr>
				<td valign="top" width="275">
					<div class="info_info">
						<div style=""><img src="../pics/icons/bell_start.png" width="16" height="16" border="0" alt=""> Chat request and response sound alert</div>
						<div style="margin-top: 10px;">
							<div class="info_good" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#console_sound_on').prop('checked', true);toggle_console_sound(1);"><input type="radio" name="console_sound" id="console_sound_on" value=1 <?php echo $sound_on ?>> On</div>
							<div class="info_error" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#console_sound_off').prop('checked', true);toggle_console_sound(0);"><input type="radio" name="console_sound" id="console_sound_off" value=0 <?php echo $sound_off ?>> Off</div>
							<div style="clear: both;"></div>
						</div>
					</div>
					<div style="margin-top: 15px;" class="info_info">
						<div>
							<div id="div_console_blink_alert" class="info_box" style="display: none; margin-bottom: 15px;">Console automatically set to "blink" if sound alert is off.</div>
							<div>
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td><img src="../pics/icons/blink.png" width="16" height="16" border="0" alt=""></td>
									<td style="padding-left: 5px;">Blink the operator console window for new <b>chat requests</b>.</td>
								</tr>
								</table>
							</div>
							<div style="margin-top: 10px;">
								<div class="info_good" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#console_blink_on').prop('checked', true);toggle_console_blink(1, 1);"><input type="radio" name="console_blink" id="console_blink_on" value=1  <?php echo $blink_on ?>> On</div>
								<div class="info_error" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#console_blink_off').prop('checked', true);toggle_console_blink(0, 1);"><input type="radio" name="console_blink" id="console_blink_off" value=0 <?php echo $blink_off ?>> Off</div>
								<div style="clear: both;"></div>
							</div>
						</div>

						<div style="margin-top: 25px;">
							<div>
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td><img src="../pics/icons/blink.png" width="16" height="16" border="0" alt=""></td>
									<td style="padding-left: 5px;">Blink the operator console window for new <b>chat responses</b>.</td>
								</tr>
								</table>
							</div>
							<div style="margin-top: 10px;">
								<div class="info_good" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#console_blink_r_on').prop('checked', true);toggle_console_blink_r(1, 1);"><input type="radio" name="console_blink_r" id="console_blink_r_on" value=1  <?php echo $blink_r_on ?>> On</div>
								<div class="info_error" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#console_blink_r_off').prop('checked', true);toggle_console_blink_r(0, 1);"><input type="radio" name="console_blink_r" id="console_blink_r_off" value=0 <?php echo $blink_r_off ?>> Off</div>
								<div style="clear: both;"></div>
							</div>
						</div>
					</div>
				</td>
				<td valign="top" style="padding-left: 50px;">
					<form>
					<table cellspacing=0 cellpadding=2 border=0 style="">
					<tr>
						<td class="td_dept_td">New chat request: </td>
						<td class="td_dept_td"><div style="margin-left: 15px;"><select name="sound1" id="sound1" style="" onChange="demo_sound1(1)">
							<?php
								$dir_sounds = opendir( "$CONF[DOCUMENT_ROOT]/media/" ) ;

								$sounds = $sounds_filter = Array() ;
								while ( $sound = readdir( $dir_sounds ) )
									$sounds[] = $sound ;
								closedir( $dir_sounds ) ;
								
								sort( $sounds, SORT_STRING ) ;
								for ( $c = 0; $c < count( $sounds ); ++$c )
								{
									$sound = $sounds[$c] ;

									if ( preg_match( "/[a-z]/i", $sound ) && preg_match( "/^new_request_/i", $sound ) )
									{
										$sound_temp = preg_replace( "/(new_request_)|(.swf)|(.mp3)/", "", $sound ) ;
										if ( !isset( $sounds_filter[$sound_temp] ) )
										{
											$sounds_filter[$sound_temp] = 1 ;
											$sound_display = ucwords( preg_replace( "/_/", " ", $sound_temp ) ) ;
											$selected = "" ;
											if ( $opinfo["sound1"] == $sound_temp )
												$selected = "selected" ;

											print "<option value=\"$sound_temp\" $selected>$sound_display</option>" ;
										}
									}
								}
							?>
							</select></div>
						</td>
						<td class="td_dept_td"><div style="margin-left: 15px; cursor: pointer;" onClick="demo_sound1(1)"><img src="../pics/icons/bell_start.png" width="16" height="16" border="0" alt="play sound" title="play sound" id="img_sound1"></div></td>
						<td class="td_dept_td"><div style="margin-left: 15px; cursor: pointer;" onClick="demo_sound1(0)"><img src="../pics/icons/bell_stop.png" width="16" height="16" border="0" alt="stop sound" title="stop sound" id="img_sound1"></div></td>
					</tr>
					<tr>
						<td class="td_dept_td"><div style="padding-top: 5px;">New chat response: </div></td>
						<td class="td_dept_td"><div style="padding-top: 5px; margin-left: 15px;"><select name="sound2" id="sound2" style="" onChange="demo_sound2()">
							<?php
								$dir_sounds = opendir( "$CONF[DOCUMENT_ROOT]/media/" ) ;

								$sounds = $sounds_filter = Array() ;
								while ( $sound = readdir( $dir_sounds ) )
									$sounds[] = $sound ;
								closedir( $dir_sounds ) ;

								sort( $sounds, SORT_STRING ) ;
								for ( $c = 0; $c < count( $sounds ); ++$c )
								{
									$sound = $sounds[$c] ;

									if ( preg_match( "/[a-z]/i", $sound ) && preg_match( "/^new_text_/i", $sound ) )
									{
										$sound_temp = preg_replace( "/(new_text_)|(.swf)|(.mp3)/", "", $sound ) ;
										if ( !isset( $sounds_filter[$sound_temp] ) )
										{
											$sounds_filter[$sound_temp] = 1 ;
											$sound_display = ucwords( preg_replace( "/_/", " ", $sound_temp ) ) ;
											$selected = "" ;
											if ( $opinfo["sound2"] == $sound_temp )
												$selected = "selected" ;

											print "<option value=\"$sound_temp\" $selected>$sound_display</option>" ;
										}
									}
								}
							?>
							</select></div>
						</td>
						<td class="td_dept_td"><div style="margin-left: 15px; cursor: pointer;" onClick="demo_sound2()"><img src="../pics/icons/bell_start.png" width="16" height="16" border="0" alt="play sound" title="play sound" id="img_sound2"></div></td>
						<td class="td_dept_td">&nbsp;</td>
					</tr>
					<tr>
						<td></td>
						<td colspan=2 class="td_dept_td" style="border-bottom: 0px;"><div style="margin-left: 15px; padding-top: 15px;"><input type="button" value="Update Sound Alerts" onClick="update_sound()" class="btn"></div></td>
					</tr>
					<tr><td colspan="4" style="padding: 15px;">&nbsp;</td></tr>
					<tr id="tr_sound_volume" style="display: none;">
						<td class="td_dept_td_blank"><div style="">Sound Alert Volume: </div></td>
						<td class="td_dept_td_blank" colspan=5><div style="margin-left: 15px;">
							<div>
								<div class="li_op round" style="cursor: pointer;" onclick="$('#vol_1').prop('checked', true);update_volume(1);"><input type="radio" name="volume" id="vol_1" value="1"> 100%</div>
								<div class="li_op round" style="cursor: pointer;" onclick="$('#vol_75').prop('checked', true);update_volume(0.09);"><input type="radio" name="volume" id="vol_75" value="0.09"> 75%</div>
								<div class="li_op round" style="cursor: pointer;" onclick="$('#vol_50').prop('checked', true);update_volume(0.06);"><input type="radio" name="volume" id="vol_50" value="0.06"> 50%</div>
								<div class="li_op round" style="cursor: pointer;" onclick="$('#vol_25').prop('checked', true);update_volume(0.03);"><input type="radio" name="volume" id="vol_25" value="0.03"> 25%</div>
								<div style="clear: both;"></div>
							</div>
						</div></td>
					</tr>
					</table>
					</form>
				</td>
			</tr>
			</table>
		</div>

		<div id="op_sms" style="display: none; margin-top: 15px;">
			<img src="../pics/icons/mobile.png" width="16" height="16" border="0" alt=""> Receive new chat request SMS alerts on your mobile. This is a notification only. The operator console should remain opened on the computer.

			<form>
			<table cellspacing=0 cellpadding=2 border=0 width="100%" style="margin-top: 25px;">
			<tr>
				<td>
					<div id="sms_enabled" style="display: none; margin-bottom: 25px;">
						<div class="info_good edit_title" id="sms_enabled_on" style="display: none; text-align: center;">SMS notification alert is on.  <button type="button" onClick="sms_toggle(2)" class="btn">Switch Off</button></div>
						<div class="info_error edit_title" id="sms_enabled_off" style="display: none; text-align: center;">SMS notification alert is off.  <button type="button" onClick="sms_toggle(1)" class="btn">Switch On</button></div>
						<div style="margin-top: 15px;"><img src="../pics/icons/arrow_grey.png" width="16" height="16" border="0" alt="" id="icon_sms_arrow"> <a href="JavaScript:void(0)" onClick="do_sms_edit()">Your SMS information</a></div>
					</div>
					<div id="sms_send">
						<table cellspacing=0 cellpadding=0 border=0 width="100%">
						<tr>
							<td width="60%" valign="top">
								<div style="font-size: 14px; font-weight: bold;">Step 1.</div>
								<div style="margin-top: 5px;">The format is <span style="font-style: italic;">mobile_number@SMS_gateway</span> (example: 5551234567@txt.att.net)</div>
								<div style="margin-top: 15px;">
									<table cellspacing=0 cellpadding=2 border=0 width="100%">
									<tr>
										<td>mobile #<br><input type="text" class="input" size="15" maxlength="65" id="phonenum" name="phonenum" onKeyPress="return logins(event)" value="<?php echo isset( $smsnum[0] ) ? $smsnum[0] : "" ?>"></td>
										<td><span style="font-size: 10px; color: #FFFFFF;">@</span><br>@</td>
										<td nowrap>carrier (SMS gateway)<br><input type="text" class="input" size="15" maxlength="40" id="carrier" id="carrier" value="<?php echo isset( $smsnum[1] ) ? $smsnum[1] : "" ?>"> &nbsp; <input type="button" value="Send Verification" onClick="sms_send()" id="btn_sms_send" class="btn"></td>
									</tr>
									<tr>
										<td colspan=3>
											<div style="margin-top: 15px;">Verification of your mobile device is required.  There is a 1 minute delay between each verification attempts.  If you do not receive the verification code within 5-10 minutes, double check the mobile number and the carrier values for accuracy.  <a href="https://www.google.com/search?q=List%20of%20Email%20to%20SMS%20Gateways" target="_blank">(Google search: list of email to SMS gateways)</a>
											<div id="sma_reset" style="display: none; margin-top: 15px; text-decoration: underline; cursor: pointer;" onClick="reset_sms()">
													Clear and reset the SMS information.
												</div>
											</div>
										</td>
									</tr>
									</table>
								</div>
							</td>
							<td width="5%">&nbsp;</td>
							<td width="35%" valign="top">
								<div style="font-size: 14px; font-weight: bold;">Step 2.</div>
								<div style="margin-top: 5px;">Provide the verification code to activate.</div>
								<div style="margin-top: 15px;">
									<table cellspacing=0 cellpadding=2 border=0>
									<tr>
										<td>verification code<br><input type="text" class="input" size="11" maxlength="15" id="code" id="code" onKeyPress="return logins(event)"> <input type="button" value="Verify" onClick="sms_verify()" id="btn_sms_verify" class="btn"></td>
									</tr>
									</table>
								</div>
							</td>
						</tr>
						</table>
					</div>
					<div id="sms_verify" style="display: none;">
					</div>
				</td>
			</tr>
			</table>
			</form>
		</div>

		<div id="op_dn" style="display: none; margin-top: 15px;">
			<img src="../pics/icons/comp.png" width="16" height="16" border="0" alt=""> Display a new chat request/response notification on the desktop.  Desktop notification is currently available for <a href="https://www.google.com/chrome" target="new">Google Chrome</a> and <a href="http://www.firefox.com" target="new">Firefox</a>.

			<form>
			<table cellspacing=0 cellpadding=2 border=0 width="100%" style="margin-top: 25px;">
			<tr>
				<td >
					<div id="dn_unavailable" style="display: none;">Desktop Notification is not supported for this browser type.  Consider using Google Chrome of Firefox browser if you'd like to utilize the new chat request Desktop Notification popup alert.</div>
					<div id="dn_request" style="display: none;">
						<div class="info_box"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""> Good news!  Desktop notification is supported for this browser.</div>

						<div style="margin-top: 15px;">To enable the new chat request desktop notification, click on the "Request Notification" button.  When alerted to "Allow" or "Deny" the request, click the "Allow" option.</div>

						<div style="margin-top: 15px;"><input type="button" onClick="dn_pre_request()" value="Request Notification" class="btn"></div>
					</div>
					<div id="dn_firefox" style="display: none;">
						<div class="info_box"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""> Good news!  Desktop notification is supported for this browser.</div>

						<div style="margin-top: 15px;">However, the system has detected an outdated Firefox version.  An upgrade of the browser is needed.  After the browser upgrade, visit this area again to enable the feature. <a href="http://www.firefox.com" target="new">Firefox.com</a></div>
					</div>
					<div id="dn_enabled" style="display: none;">
						<table cellspacing=0 cellpadding=0 border=0 width="100%">
						<tr>
							<td valign="top" style="width: 250px;">
								<div style="" class="info_info">
									<div id="div_alert" style="display: none; margin-bottom: 15px;"></div>
									<div class="info_neutral">
										<table cellspacing=0 cellpadding=2 border=0>
										<tr>
											<td><input type="radio" name="dn_always" id="dn_always_on" value=1 <?php echo $dn_always_on ?> onClick="toggle_dn_always(1, 1)"></td>
											<td>Always display the Desktop Notification</td>
										</tr>
										</table>
									</div>
									<div class="info_neutral" style="margin-top: 15px;">
										<table cellspacing=0 cellpadding=2 border=0>
										<tr>
											<td><input type="radio" name="dn_always" id="dn_always_off" value=0 <?php echo $dn_always_off ?> onClick="toggle_dn_always(0, 1)"></td>
											<td>Only display the Desktop Notification if the operator console window is out of focus</td>
										</tr>
										</table>
									</div>
								</div>
							</td>
							<td valign="top" style="padding-left: 50px;">
								<div class="info_good edit_title" id="dn_enabled_on" style="display: none; text-align: center;">Chat <span class="info_box">Request</span> Notification Alert is on. <button type="button" onClick="dn_toggle(0)" class="btn">Switch Off</button></div>

								<div class="info_error edit_title" id="dn_enabled_off" style="display: none; text-align: center;">Chat <span class="info_box">Request</span> Notification Alert is off.  <button type="button" onClick="dn_toggle(1)" class="btn">Switch On</button></div>

								<div style="margin-top: 5px;"><a href="JavaScript:void(0)" onClick="dn_show( 'new_chat', '<?php echo time() ?>', 'Demo Visitor', 'This is a demo chat request question.', 45000 )">Click to display a demo desktop notification.</a></div>

								<div style="margin-top: 25px;">
									<div class="info_good edit_title" id="dn_enabled_response_on" style="display: none; text-align: center;">Chat <span class="info_box">Response</span> Notification Alert is on. <button type="button" onClick="dn_toggle_response(0)" class="btn">Switch Off</button></div>
									<div class="info_error edit_title" id="dn_enabled_response_off" style="text-align: center;">Chat <span class="info_box">Response</span> Notification Alert is off. <button type="button" onClick="dn_toggle_response(1)" class="btn">Switch On</button></div>
								</div>
							</td>
						</tr>
						</table>
					</div>
					<div id="dn_disabled" style="display: none;">
						<span class="info_error">Desktop notification request was denied.</span>

						<div style="margin-top: 25px;">To re-request the desktop notification access, you'll want to refer to the <a href="http://www.phplivesupport.com/help_desk.php?title=Reset_Desktop_Notification_Settings&docid=18" target="new">Reset Desktop Notification Settings</a> documentation.  After the settings have been reset, <a href="./notifications.php?jump=dn&console=<?php echo $console ?>&ses=<?php echo $ses ?>&<?php echo time() ?>">reload this page</a> to re-request permission.</div>
					</div>
				</td>
			</tr>
			</table>
			</form>
		</div>

<?php include_once( "./inc_footer.php" ); ?>
