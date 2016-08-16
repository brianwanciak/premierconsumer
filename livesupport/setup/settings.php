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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Hash.php" ) ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_File.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$https = "" ;
	if ( isset( $_SERVER["HTTP_CF_VISITOR"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_CF_VISITOR"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTP_X_FORWARDED_PROTO"] ) && preg_match( "/(https)/i", $_SERVER["HTTP_X_FORWARDED_PROTO"] ) ) { $https = "s" ; }
	else if ( isset( $_SERVER["HTTPS"] ) && preg_match( "/(on)/i", $_SERVER["HTTPS"] ) ) { $https = "s" ; }

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$jump = ( Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) : "eips" ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;

	if ( !isset( $CONF["cookie"] ) ) { $CONF["cookie"] = "on" ; }

	$error = "" ;

	$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	LIST( $your_ip, $null ) = Util_IP_GetIP( "" ) ;

	$cookie_off = ( $CONF["cookie"] == "off" ) ? "checked" : "" ;
	$cookie_on = ( $cookie_off == "checked" ) ? "" : "checked" ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$timezones = Util_Hash_Timezones() ;
	$vars = Util_Format_Get_Vars( $dbh ) ;
	$charset = ( isset( $vars["char_set"] ) && $vars["char_set"] ) ? unserialize( $vars["char_set"] ) : Array(0=>"UTF-8") ;
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
	var global_cookie = "<?php echo $CONF["cookie"] ?>" ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "settings" ) ;

		fetch_eips() ;
		fetch_sips() ;
		show_div( "<?php echo $jump ?>" ) ;

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
		<?php if ( $action && $error ): ?>do_alert_div( "..", 0, "<?php echo $error ?>" ) ;<?php endif ; ?>
	});

	function fetch_eips()
	{
		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=eips&"+unixtime(),
			success: function(data){
				print_eips( data ) ;
			}
		});
	}

	function fetch_sips()
	{
		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=sips&"+unixtime(),
			success: function(data){
				print_sips( data ) ;
			}
		});
	}

	function print_eips( thedata )
	{
		var json_data = new Object ;

		eval( thedata ) ;
		if ( typeof( json_data.ips ) != "undefined" )
		{
			var ip_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\">" ;
			for ( var c = 0; c < json_data.ips.length; ++c )
			{
				var ip = json_data.ips[c]["ip"] ;
				var ip_ = ip.replace( /\./g, "" ) ;

				ip_string += "<tr id=\"tr_eip_"+ip_+"\"><td class=\"td_dept_td\" width=\"14\"><div id=\"eip_"+ip_+"\"><a href=\"JavaScript:void(0)\" onClick=\"remove_eip( '"+ip+"' )\"><img src=\"../pics/icons/delete.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\"></a></div></td><td class=\"td_dept_td\">"+ip+"</td></tr>" ;
			}
			if ( !c )
				ip_string += "<tr><td class=\"td_dept_td\">Blank results.</td></tr>" ;
		}
		ip_string += "</table>" ;
		$('#eips').html( ip_string ) ;
	}

	function print_sips( thedata )
	{
		var json_data = new Object ;

		eval( thedata ) ;
		if ( typeof( json_data.ips ) != "undefined" )
		{
			var ip_string = "<table cellspacing=0 cellpadding=0 border=0 width=\"100%\">" ;
			for ( var c = 0; c < json_data.ips.length; ++c )
			{
				var ip = json_data.ips[c]["ip"] ;
				var ip_ = ip.replace( /\./g, "" ) ;

				ip_string += "<tr id=\"tr_sip_"+ip_+"\"><td class=\"td_dept_td\" width=\"14\"><div id=\"sip_"+ip_+"\"><a href=\"JavaScript:void(0)\" onClick=\"remove_sip( '"+ip+"' )\"><img src=\"../pics/icons/delete.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\"></a></div></td><td class=\"td_dept_td\">"+ip+"</td></tr>" ;
			}
			if ( !c )
				ip_string += "<tr><td class=\"td_dept_td\">Blank results.</td></tr>" ;
		}
		ip_string += "</table>" ;
		$('#sips').html( ip_string ) ;
	}

	function add_eip()
	{
		var ip = $('#ip_exclude').val().replace( /[^0-9.]/g, "" ) ;
		$('#ip_exclude').val( ip ) ;

		if ( !ip )
			do_alert( 0, "Blank IP field is invalid." ) ;
		else
		{
			var json_data = new Object ;
			$('#btn_eip').attr( "disabled", true ) ;
			$('#img_loading_eip').show() ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=add_eip&ip="+ip+"&"+unixtime(),
				success: function(data){
					eval(data) ;
					if ( json_data.status )
					{
						// timeout is due to possible server cache settings
						setTimeout( function(){
							$('#img_loading_eip').hide() ;
							$('#btn_eip').attr( "disabled", false ) ;
							fetch_eips() ;
							do_alert( 1, "Success" ) ;
						}, 3000 ) ;
					}
					else
					{
						$('#img_loading_eip').hide() ;
						$('#btn_eip').attr( "disabled", false ) ;
						do_alert( 0, "IP ("+ip+") already excluded." ) ;
					}
					$('#ip_exclude').val('') ;
				}
			});
		}
	}

	function remove_eip( theip )
	{
		var theip_ = theip.replace( /\./g, "" ) ;
		$('#eips').hide() ;
		$('#img_loading_eip').show() ;

		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=remove_eip&ip="+theip+"&"+unixtime(),
			success: function(data){
				// timeout is due to possible server cache settings
				setTimeout( function(){
					$('#tr_eip_'+theip_).remove() ;
					$('#eips').show() ;
					$('#img_loading_eip').hide() ;
					do_alert( 1, "Success" ) ;
				}, 3000 ) ;
			}
		});
	}

	function add_sip()
	{
		var ip = $('#ip_spam').val().replace( /[^0-9.]/g, "" ) ;
		$('#ip_spam').val( ip ) ;

		if ( !ip )
			do_alert( 0, "Blank IP field is invalid." ) ;
		else
		{
			var json_data = new Object ;
			$('#btn_sip').attr( "disabled", true ) ;
			$('#img_loading_sip').show() ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=add_sip&ip="+ip+"&"+unixtime(),
				success: function(data){
					eval(data) ;
					if ( json_data.status )
					{
						// timeout is due to possible server cache settings
						setTimeout( function(){
							$('#img_loading_sip').hide() ;
							$('#btn_sip').attr( "disabled", false ) ;
							fetch_sips() ;
							do_alert( 1, "Success" ) ;
						}, 3000 ) ;
					}
					else
					{
						$('#img_loading_sip').hide() ;
						$('#btn_sip').attr( "disabled", false ) ;
						do_alert( 0, "IP ("+ip+") already reported as spam." ) ;
					}
					$('#ip_spam').val('') ;
				}
			});
		}
	}

	function remove_sip( theip )
	{
		var theip_ = theip.replace( /\./g, "" ) ;
		$('#sips').hide() ;
		$('#img_loading_sip').show() ;

		$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "ses=<?php echo $ses ?>&action=remove_sip&ip="+theip+"&"+unixtime(),
			success: function(data){
				// timeout is due to possible server cache settings
				setTimeout( function(){
					$('#tr_sip_'+theip_).remove() ;
					$('#sips').show() ;
					$('#img_loading_sip').hide() ;
					do_alert( 1, "Success" ) ;
				}, 3000 ) ;
			}
		});
	}

	function show_div( thediv )
	{
		var divs = Array( "eips", "sips", "cookie", "profile" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#settings_'+divs[c]).hide() ;
			$('#menu_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu') ;
		}

		$('input#jump').val( thediv ) ;
		$('#settings_'+thediv).show() ;
		$('#menu_'+thediv).removeClass('op_submenu').addClass('op_submenu_focus') ;
	}

	function switch_dept( theobject )
	{
		location.href = "settings.php?ses=<?php echo $ses ?>&deptid="+theobject.value ;
	}

	function update_profile()
	{
		execute = 1 ;
		var inputs = Array( "email", "login" ) ;

		if ( !check_email( $('#email').val() ) ){ do_alert( 0, "Email format is invalid. (example: you@domain.com)" ) ; execute = 0 ; }

		if ( $('#npassword').val() || $('#vpassword').val() )
		{
			if ( $('#npassword').val() != $('#vpassword').val() ){ do_alert( 0, "New Password and Verify Password does not match." ) ; execute = 0 ; }
		}

		if ( execute ){ update_profile_doit() ; } ;
	}

	function update_profile_doit()
	{
		var json_data = new Object ;
		var unique = unixtime() ;

		var email = $('#email').val() ;
		var login = $('#login').val() ;
		var npassword = phplive_md5( $('#npassword').val() ) ;
		var vpassword = phplive_md5( $('#vpassword').val() ) ;
		var md5_password = phplive_md5( npassword+vpassword ) ;

		$.ajax({
		type: "POST",
		url: "../ajax/setup_actions.php",
		data: "action=update_profile&ses=<?php echo $ses ?>&email="+email+"&login="+login+"&npassword="+npassword+"&vpassword="+vpassword+"&md5_password="+md5_password+"&"+unique,
		success: function(data){
			eval( data ) ;
			if ( json_data.status )
			{
				$('#npassword').val('') ;
				$('#vpassword').val('') ;
				do_alert( 1, "Success" ) ;
			}
			else
				do_alert( 0, json_data.error ) ;

		},
		error:function (xhr, ajaxOptions, thrownError){
			do_alert( 0, "Connection to server was lost.  Please reload the page." ) ;
		} });
	}

	function confirm_change( theflag )
	{
		if ( global_cookie != theflag )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=update_cookie&value="+theflag+"&"+unixtime(),
				success: function(data){
					global_cookie = theflag ;
					do_alert( 1, "Success" ) ;
				}
			});
		}
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="show_div('eips')" id="menu_eips">Excluded IPs</div>
			<div class="op_submenu" onClick="show_div('sips')" id="menu_sips">Blocked IPs</div>
			<div class="op_submenu" onClick="show_div('cookie')" id="menu_cookie">Cookies</div>
			<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/mapp/settings.php" ) && ( $admininfo["adminID"] == 1 ) ): ?><div class="op_submenu" onClick="location.href='../mapp/settings.php?ses=<?php echo $ses ?>'" id="menu_system"><img src="../pics/icons/mobile.png" width="12" height="12" border="0" alt=""> Mobile App</div><?php endif ; ?>
			<?php if ( $admininfo["adminID"] == 1 ): ?><div class="op_submenu" onClick="show_div('profile')" id="menu_profile"><img src="../pics/icons/key.png" width="12" height="12" border="0" alt=""> Setup Profile</div><?php endif ; ?>
			<div class="op_submenu" onClick="location.href='system.php?ses=<?php echo $ses ?>'" id="menu_system">System</div>
			<div style="clear: both"></div>
		</div>

		<form method="POST" action="settings.php?submit" enctype="multipart/form-data">
		<input type="hidden" name="action" value="update">
		<input type="hidden" name="jump" id="jump" value="">
		<input type="hidden" name="ses" value="<?php echo $ses ?>">

		<div style="display: none; margin-top: 25px; text-align: justify;" id="settings_eips">
			To avoid misleading page views when developing a site, exclude internal or company IP from being counted towards the overall footprint report.  Excluded IPs will not be visible on the traffic monitor. URL footprints of Excluded IPs will not be stored in the database and will not count towars the overall <a href="reports_traffic.php?ses=<?php echo $ses ?>">footprint report</a>.

			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top" nowrap style="padding-right: 25px;">
						<div class="info_info">
							<div>Your IP: <big><b><?php echo $your_ip ?></b></big></div>
							<div style="margin-top: 15px;"><input type="text" name="ip_exclude" id="ip_exclude" size="20" maxlength="45" onKeyPress="return numbersonly(event)"></div>
							<div style="margin-top: 25px;"><input type="button" onClick="add_eip()" value="Add Exclude IP" class="btn" id="btn_eip"></div>
						</div>
					</td>
					<td valign="top" width="100%">
						<div><div class="td_dept_header">Current Excluded IPs: &nbsp; <img src="../pics/loading_ci.gif" width="14" height="14" border="0" alt="" id="img_loading_eip" style="display: none;"></div></div>
						<div id="eips" style=""></div>
					</td>
				</tr>
				</table>
			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="settings_sips">
			Blocked IPs will always see an OFFLINE status icon.  Operators can specify an IP to block during a chat session or you can provide an IP address here.  Blocked IPs will still display on the traffic monitor.

			<div style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top" nowrap style="padding-right: 25px;">
						<div class="info_info">
							<div>Example: <big><b>123.456.789.101</b></big></div>
							<div style="margin-top: 15px;"><input type="text" name="ip_spam" id="ip_spam" size="20" maxlength="45" onKeyPress="return numbersonly(event)"></div>
							<div style="margin-top: 25px;"><input type="button" onClick="add_sip()" value="Add IP to Block" class="btn" id="btn_sip"></div>
						</div>
					</td>
					<td valign="top" width="100%">
						<div><div class="td_dept_header">Current Blocked IPs: &nbsp; <img src="../pics/loading_ci.gif" width="14" height="14" border="0" alt="" id="img_loading_sip" style="display: none;"></div></div>
						<div id="sips" style="max-height: 300px; overflow-y: auto; overflow-x: hidden;"></div>
					</td>
				</tr>
				</table>
			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="settings_cookie">
			Switch on/off the use of cookies on the visitor chat window.  The cookies provide convenience for the visitor and does not affect the actual chat functions.
			<div style="margin-top: 25px;" class="info_info">
				Cookies set by the system on the visitor chat request window:
				<li style="margin-top: 5px;"> <code>phplive_vname</code> - The visitor's name
				<li> <code>phplive_vemail</code> - The visitor's email address
			</div>

			<div style="margin-top: 25px;">
				<div class="li_op round" style="cursor: pointer;" onclick="$('#cookie_on').prop('checked', true);confirm_change('on');"><input type="radio" name="cookie" id="cookie_on" value="on" <?php echo $cookie_on ?>> Set cookies</div>
				<div class="li_op round" style="cursor: pointer;" onclick="$('#cookie_off').prop('checked', true);confirm_change('off');"><input type="radio" name="cookie" id="cookie_off" value="off" <?php echo $cookie_off ?>> Do not set cookies</div>
				<div style="clear: both;"></div>
			</div>
		</div>

		<div style="display: none; margin-top: 25px;" id="settings_profile">
			Update the setup admin contact email address and the password to this setup area.  The admin email is used for the Setup Admin "forgot password" recovery and other system related features.

			<div style="margin-top: 25px;">
				<input type="hidden" name="login" id="login" value="<?php echo $admininfo["login"] ?>">
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td class="td_dept_td" width="120">Setup Admin Email</td>
					<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="50" name="email" id="email" value="<?php echo $admininfo["email"] ?>" onKeyPress="return justemails(event)" value=""></td>
				</tr>
				<tr>
					<td colspan="4" style="padding-top: 15px;">
						<div style="font-size: 14px; font-weight: bold;">Update Password (optional)</div>
						<div style="margin-top: 15px;">
							<table cellspacing=0 cellpadding=4 border=0>
							<tr> 
								<td class="td_dept_td" width="120">New Password</td> 
								<td class="td_dept_td"><input type="password" class="input" size="35" maxlength="50" id="npassword" onKeyPress="return noquotes(event)"></td> 
							</tr>
							<tr>
								<td class="td_dept_td" width="120">Verify Password</td> 
								<td class="td_dept_td"><input type="password" class="input" size="35" maxlength="50" id="vpassword" onKeyPress="return noquotes(event)"></td> 
							</tr>
							</table>
						</div>
					</td>
				</tr>
				<tr> 
					<td></td> 
					<td class="td_dept_td"><input type="button" value="Update Profile" id="btn_submit" onClick="update_profile()" class="btn"></td> 
				</tr> 
				</table>
			</div>
		</div>

		</form>

<?php include_once( "./inc_footer.php" ) ?>
