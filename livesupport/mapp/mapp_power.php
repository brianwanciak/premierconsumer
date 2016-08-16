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

	$profile_pic_url = Util_Upload_GetLogo( "profile", $opinfo["opID"] ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Operator </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8"> 
<?php include_once( "../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../themes/<?php echo $opinfo["theme"] ?>/style.css?<?php echo $VERSION ?>">
<link rel="Stylesheet" href="../mapp/css/mapp.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../mapp/js/mapp.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery_md5.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	"use strict" ;
	var base_url = ".." ; var base_url_full = "<?php echo $CONF["BASE_URL"] ?>" ;
	var global_status = 1 ;

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;

		update_network_img(5) ;
		reset_mapp_div_height() ;
		$('#canned_wrapper').show() ;
	});

	function toggle_menu_info( themenu )
	{
		var divs = Array( "profile", "network" ) ;

		for ( var c = 0; c < divs.length; ++c )
		{
			$('#div_settings_'+divs[c]).hide() ;
			$('#menu_settings_'+divs[c]).removeClass('menu_traffic_info_focus').addClass('menu_traffic_info') ;
		}

		$('#div_settings_'+themenu).show() ;
		$('#menu_settings_'+themenu).removeClass('menu_traffic_info').addClass('menu_traffic_info_focus') ;
	}

	function toggle_network_infolog()
	{
		if ( $('#chat_info_wrapper_network_info').is(':visible') )
		{
			$('#chat_info_wrapper_network_info').hide() ;
			$('#chat_info_wrapper_network_log').show() ;
		}
		else
		{
			$('#chat_info_wrapper_network_log').hide() ;
			$('#chat_info_wrapper_network_info').show() ;
		}
	}

	function update_status( theflag )
	{
		if ( global_status != theflag )
		{
			parent.location.href = "../logout.php?action=logout&arn="+parent.arn+"&auto=1&mapp=1&menu=operator&"+unixtime() ;
		}
	}

	function update_network_img( theflag )
	{
		if ( theflag == 5 )
			$('#chat_network_img').css({'background-position': '0px bottom'}) ;
		else if ( theflag == 4 )
			$('#chat_network_img').css({'background-position': '0px -152px'}) ;
		else if ( theflag == 3 )
			$('#chat_network_img').css({'background-position': '0px -114px'}) ;
		else if ( theflag == 2 )
			$('#chat_network_img').css({'background-position': '0px -76px'}) ;
		else if ( theflag == 1 )
			$('#chat_network_img').css({'background-position': '0px -38px'}) ;
		else
			$('#chat_network_img').css({'background-position': '0px 0px'}) ;
	}

	function update_network_log( thecounter, thestring )
	{
		$('#chat_info_network_info tbody tr:nth-child(2)').after( thestring ) ;
		$('#div_network_his_'+thecounter).show() ;
	}
//-->
</script>
</head>
<body style="">

<div id="canned_wrapper" style="display: none; height: 100%; overflow: auto;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td class="t_tl"></td><td class="t_tm"></td><td class="t_tr"></td></tr>
	<tr>
		<td class="t_ml"></td><td class="t_mm">
			<div id="canned_container" style="overflow: auto;">

				<div style="">
					<div id="menu_settings_profile" class="menu_traffic_info_focus" onClick="toggle_menu_info('profile')">Profile</div>
					<div id="menu_settings_network" class="menu_traffic_info" onClick="toggle_menu_info('network')">Network Status</div>
					<div style="clear: both;"></div>
				</div>

				<div id="div_settings_profile" style="margin-top: 25px;">
					<div class="info_box">
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><img src="<?php echo $profile_pic_url ?>" width="55" height="55" border=0 class="profile_pic_img"></td>
							<td style="padding-left: 15px;">
								<div style="font-weight: bold; font-size: 14px;" id="chat_profile_name"><?php echo $opinfo["name"] ?></div>
								<div style="margin-top: 5px;">
									<?php if ( $opinfo["pic"] && ( isset( $VALS['PROFILE'] ) && ( $VALS['PROFILE'] == 1 ) ) ): ?>
									<div>Your profile picture will be displayed to the visitor during a chat session.</div>
									<?php else: ?>
									<div>Your profile picture is not visible to the visitor.</div>
									<?php endif ; ?>
								</div>
							</td>
						</tr>
						</table>

						<div style="margin-top: 15px; font-size: 18px; font-weight: bold;">Online/Offline Status</div>
						<div style="margin-top: 5px;">
							<div class="info_mapp_good" style="float: left; width: 60px; cursor: pointer;"onClick="update_status(1)"><input type="radio" name="r_status" id="r_status_1" value="on" checked> Online</div>
							<div class="info_mapp_error" style="float: left; margin-left: 10px; width: 140px; cursor: pointer;" onClick="update_status(0)"><input type="radio" name="r_status" id="r_status_0" value="off"> Logout and Go Offline</div>
							<div style="clear: both;"></div>
						</div>
					</div>

					<div style="margin-top: 25px; text-align: justify;">
						If the interface goes out of sync or some areas are not functional, try refreshing the operator console to reset the layout.
						<div style="margin-top: 15px;"><button type="button" onClick="parent.reload_console(0);">Refresh Operator Console</button></div>
					</div>
				</div>
				<div id="div_settings_network" style="display: none; margin-top: 25px;">
					<div id="chat_network_img" style="width: 50px; height: 38px;" title="network status" alt="network status"></div>

					<div style="margin-top: 15px; padding-bottom: 100px;">
						<div id="chat_info_wrapper_network_info">
							<table cellspacing=0 cellpadding=2 border=0 width="100%" id="chat_info_network_info">
							<tbody>
							<tr><td colspan=3 class="chat_info_td"><div class="info_box">
								<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td width="60%">Network Status</td></tr></table>
							</div></td></tr>
							<tr>
								<td class="chat_info_td_h" width="26%"><b>Network</b><div style="font-size: 10px;">(seconds)</div></td>
								<td class="chat_info_td_h" width="50%"><b>Server Response</b><div style="font-size: 10px;">(seconds)</div></td>
								<td class="chat_info_td_h" width="24%"><b>Total</b><div style="font-size: 10px;">(seconds)</div></td>
							</tr>
							</tbody>
							</table>
						</div>
						<div id="chat_info_wrapper_network_log" style="display: none;">
							<table cellspacing=0 cellpadding=2 border=0 width="100%" id="chat_info_network_log">
							<tbody>
							<tr><td colspan=3 class="chat_info_td"><div class="info_box">
								<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td width="60%"><span onClick="toggle_network_infolog()" style="text-decoration: underline; cursor: pointer;">Network Status</span> | Log</td><td width="40%" align="right"><button type="button" style="" onClick="open_network_status()">close</button></td></tr></table>
							</div></td></tr>
							<tr>
								<td class="chat_info_td_h" width="26%"><b>Time</b></td>
								<td class="chat_info_td_h" width="50%"><b>&nbsp;</b></td>
								<td class="chat_info_td_h" width="24%"><b>Error Code</b></td>
							</tr>
							</tbody>
							</table>
						</div>
					</div>
				</div>

			</div>
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>
</div>

</body>
</html>
<?php
	if ( isset( $dbh ) && $dbh['con'] )
		database_mysql_close( $dbh ) ;
?>
