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

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$url = Util_Format_Sanatize( Util_Format_GetVar( "url" ), "url" ) ;

	if ( ( $action == "pre_register" ) && $url )
	{
		if ( function_exists( "curl_init" ) && function_exists( "curl_exec" ) )
		{
			$url = urlencode( $url ) ;
			$request = curl_init( "https://chat.phplivesupport.com/mapp/Util/system_validate.php" ) ;
			curl_setopt( $request, CURLOPT_RETURNTRANSFER, true ) ;
			curl_setopt( $request, CURLOPT_CUSTOMREQUEST, "POST") ;
			curl_setopt( $request, CURLOPT_POSTFIELDS, Array( "u"=>"$url" ) ) ;
			if ( !isset( $VARS_SET_VERIFYPEER ) || ( $VARS_SET_VERIFYPEER == 1 ) )
			{
				curl_setopt( $request, CURLOPT_SSL_VERIFYPEER, true ) ;
				curl_setopt( $request, CURLOPT_CAINFO, "$CONF[DOCUMENT_ROOT]/mapp/API/cacert.pem" ) ;
			}
			else { curl_setopt( $request, CURLOPT_SSL_VERIFYPEER, false ) ; }
			$response = curl_exec( $request ) ;
			$curl_errno = curl_errno( $request ) ;
			$status = curl_getinfo( $request, CURLINFO_HTTP_CODE ) ;
			curl_close( $request ) ;

			if ( ( $response == 0 ) && !$curl_errno ) { $error = "X-Frame-Options detected.  For more information please visit the <a href='http://www.phplivesupport.com/r.php?r=xframe' target='_blank' style='color: #FFFFFF;'>X-Frame-Options documentation</a>." ; $json_data = "json_data = { \"status\": 0, \"error\": \"$error\" };" ; }
			else if ( $curl_errno == 35 ) { $json_data = "json_data = { \"status\": 0, \"error\": \"OpenSSL upgrade is required.  <a href='https://www.openssl.org' target='_blank' style='color: #FFFFFF;'>Open SSL</a> must be v.0.9.8o or greater.\" };" ; }
			else if ( $response == 1 ) { $json_data = "json_data = { \"status\": 1 };" ; }
			else if ( $curl_errno ) { $json_data = "json_data = { \"status\": 0, \"error\": \"CURL error: $curl_errno\" };" ; }
			else { $json_data = "json_data = { \"status\": 0, \"error\": \"Invalid URL.\" };" ; }
		}
		else
		{
			$json_data = "json_data = { \"status\": 0, \"error\": \"Server PHP does not support <a href='http://php.net/manual/en/book.curl.php' target='_blank' style='color: #FFFFFF;'>cURL</a>.  Contact your server admin to enable PHP cURL support to utilize the Mobile App feature.  Also check the 'curl_exec' function is not disabled in the php.ini file.\" };" ;
		}
		database_mysql_close( $dbh ) ;
		print "$json_data" ;
		exit ;
	}
	else if ( $action == "update_idle_hours" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
		$hours = Util_Format_Sanatize( Util_Format_GetVar( "hours" ), "n" ) ; if ( !$hours ) { $hours = 10 ; }

		Util_Vals_WriteToFile( "MOBILE_EXPIRED_OPS", $hours ) ;
		$json_data = "json_data = { \"status\": 1 };" ;
		database_mysql_close( $dbh ) ;
		print "$json_data" ;
		exit ;
	}

	$mapp_key = isset( $CONF['MAPP_KEY'] ) ? $CONF['MAPP_KEY'] : "" ;
	$kpr = substr( $mapp_key, 0, 5 ) ; $kpo = substr( $mapp_key, 5, strlen( $mapp_key ) ) ;
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
		toggle_menu_setup( "settings" ) ;

		var url = location.href ;
		url = url.replace( /\/mapp\/settings(.*)$/, "" ) ;
		url = url.replace( /\/$/, "" ) ;

		$('#url').val( url ) ;
		$('#phplive_url').html( url ) ;

		var kpo = phplive_md5(url).substring(10, 15) ;
		if ( ( '<?php echo $mapp_key ?>' == '' ) || ( '<?php echo $kpo ?>' != kpo ) )
			$('#div_register').show() ;
		else
		{
			$('#div_kpr').html( '<?php echo $kpr ?>' ) ;
			$('#div_kpo').html( '<?php echo $kpo ?>' ) ;

			$('#div_register').hide() ;
			$('#div_activated').show() ;
		}
	});

	function init_register()
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var url = $('#url').val() ;

		$('#div_alert').hide() ;
		$('#btn_register').attr( "disabled", true ) ;

		$.ajax({
		type: "POST",
		url: "./settings.php",
		data: "action=pre_register&url="+encodeURIComponent(url)+"&ses=<?php echo $ses ?>&"+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
				do_register() ;
			else
			{
				$('#btn_register').attr( "disabled", false ) ;
				do_alert_div( "..", 0, json_data.error ) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			$('#btn_register').attr( "disabled", false ) ;
			do_alert_div( "..", 0, "Could not establish connection to begin the Mobile App registration.  Please try again." ) ;
		} });
	}

	function do_register()
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var url = $('#url').val() ;

		$.ajax({
		type: "POST",
		url: "https://chat.phplivesupport.com/mapp/Util/system_register.php",
		data: "url="+encodeURIComponent(url)+"&key=<?php echo $KEY ?>&"+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
				do_save_code( json_data.kpr, json_data.kpo ) ;
			else
			{
				$('#btn_register').attr( "disabled", false ) ;
				do_alert_div( "..", 0, json_data.error ) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			$('#btn_register').attr( "disabled", false ) ;
			do_alert_div( "..", 0, "Could not establish connection to the Registration server.  The server may be in the process of being updated.  Please try again." ) ;
		} });
	}

	function do_save_code( thekpr, thekpo )
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var thismkey = thekpr+thekpo ;

		$('#btn_register').attr( "disabled", true ) ;

		$.ajax({
		type: "POST",
		url: "../ajax/setup_actions_.php",
		data: "action=save_mapp_key&mkey="+thismkey+"&ses=<?php echo $ses ?>&"+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				$('#div_kpr').html( thekpr ) ;
				$('#div_kpo').html( thekpo ) ;

				$('#div_register').hide() ;
				$('#div_activated').show() ;
			}
			else
			{
				$('#btn_register').attr( "disabled", false ) ;
				do_alert_div( "..", 0, json_data.error ) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			$('#btn_register').attr( "disabled", false ) ;
			do_alert_div( "..", 0, "Could not save Activation Code.  Please try again." ) ;
		} });
	}

	function update_idle_hours()
	{
		var unique = unixtime() ;
		var json_data = new Object ;
		var hours = $('#idle_hours').val() ;

		$('#btn_update').attr( "disabled", true ) ;

		$.ajax({
		type: "POST",
		url: "./settings.php",
		data: "action=update_idle_hours&hours="+hours+"&ses=<?php echo $ses ?>&"+unique,
		success: function(data){
			eval( data ) ;

			$('#btn_update').attr( "disabled", false ) ;
			if ( json_data.status )
			{
				do_alert( 1, "Success" ) ;
			}
			else
			{
				do_alert( 0, "Could not save settings.  Please try again." ) ;
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			$('#btn_register').attr( "disabled", false ) ;
			do_alert( 0, "Could not process request.  Please try again." ) ;
		} });
	}
//-->
</script>
</head>
<?php include_once( "../setup/inc_header.php" ) ?>

			<div class="op_submenu_wrapper">
				<div class="op_submenu" onClick="location.href='../setup/settings.php?ses=<?php echo $ses ?>&jump=eips'" id="menu_eips">Excluded IPs</div>
				<div class="op_submenu" onClick="location.href='../setup/settings.php?ses=<?php echo $ses ?>&jump=sips'" id="menu_sips">Blocked IPs</div>
				<div class="op_submenu" onClick="location.href='../setup/settings.php?ses=<?php echo $ses ?>&jump=cookie'" id="menu_cookie">Cookies</div>
				<div class="op_submenu_focus" id="menu_system"><img src="../pics/icons/mobile.png" width="12" height="12" border="0" alt=""> Mobile App</div>
				<?php if ( $admininfo["adminID"] == 1 ): ?><div class="op_submenu" onClick="location.href='../setup/settings.php?ses=<?php echo $ses ?>&jump=profile'" id="menu_profile"><img src="../pics/icons/key.png" width="12" height="12" border="0" alt=""> Setup Profile</div><?php endif ; ?>
				<div class="op_submenu" onClick="location.href='../setup/system.php?ses=<?php echo $ses ?>'" id="menu_cookie">System</div>
				<div style="clear: both"></div>
			</div>

			<div id="div_register" style="display: none; margin-top: 25px;">
				To utilize the <a href="http://www.phplivesupport.com/r.php?r=mapp" target="_blank">Mobile Application</a> for Android and iOS, a registration of your PHP Live! system is required.  Registration is required to verify the software License Key and also to generate the mobile app activation key.

				<div style="margin-top: 25px;" class="info_info">
					<div style="">Your PHP Live! URL:</div>
					<div style="margin-top: 15px; font-size: 32px; font-weight: bold; color: #3FABC1; text-shadow: 1px 1px #FFFFFF;"><span id="phplive_url"></span></div>
				</div>

				<div style="display: none; margin-top: 25px;" class="info_error" id="div_alert"></div>

				<div style="margin-top: 15px;"><img src="../pics/icons/info.png" width="14" height="14" border="0" alt=""> The domain <big><b><?php echo $_SERVER["HTTP_HOST"] ?></b></big> must be accessible on the internet.  It cannot be an internal IP address or internal private domain.</div>
				<div id="div_btn_register" style="margin-top: 25px;">
					<form>
					<input type="hidden" name="url" id="url" value="">
					<button type="button" onClick="init_register()" id="btn_register" class="btn">Register for Mobile App</button>
					</form>
				</div>
			</div>

			<div id="div_activated" style="display: none; margin-top: 25px;">
				<span class="info_box"><img src="../pics/icons/check.png" width="16" height="16" border="0" alt=""> Your PHP Live! system has been registered for Mobile App.</span>

				<div style="margin-top: 25px;" class="info_info">
					Provide the following Activation Code to your operators.  The Activation Code will need to be entered on the <a href="http://www.phplivesupport.com/r.php?r=mapp" target="_blank">Mobile Application</a> for Android and iOS.

					<div style="margin-top: 25px; font-size: 18px; font-weight: bold;"><img src="../pics/icons/mobile.png" width="16" height="16" border="0" alt=""> Activation Code: <span id="div_kpr" class="info_box"></span> - <span id="div_kpo" class="info_box"></span></div>

					<div style="margin-top: 25px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> <b>Reminder:</b> Enable the <a href="../setup/ops.php?ses=<?php echo $ses ?>">"Mobile App Access" for each operator</a> so they can login from the mobile application.</div>
				</div>

				<div style="margin-top: 25px;" class="info_info">
					<div class="edit_title">Mobile App Automatic Idle Offline</div>
					<div style="margin-top: 5px;">Automatically set mobile <i>Online</i> operators to <span style="font-weight: bold; color: #D6453D;">Offline</span> status if the operator has <span style="font-weight: bold; color: #D6453D;">not accessed (opened) the mobile application</span> greater then
						<select name="idle_hours" id="idle_hours">
						<?php
							$VARS_MOBILE_EXPIRED_OPS = ( isset( $VALS["MOBILE_EXPIRED_OPS"] ) ) ? $VALS["MOBILE_EXPIRED_OPS"] : 10 ;
							for( $c = 5; $c <= 48; ++$c )
							{
								$selected = "" ;
								if ( $VARS_MOBILE_EXPIRED_OPS == $c )
									$selected = "selected" ;
								print "<option value=\"$c\" $selected>$c</option>" ;
							}
						?></select> hours. &nbsp;  <button type="button" onClick="update_idle_hours()" class="btn" id="btn_update">Update</button>
					</div>
				</div>

			</div>

<?php include_once( "../setup/inc_footer.php" ) ?>