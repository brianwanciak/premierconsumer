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
	var global_auto_login = parseInt( <?php echo $auto_login_enabled ?> ) ;
	var global_mapp_c = parent.mapp_c ;

	$(document).ready(function()
	{
		$.ajaxSetup({ cache: false }) ;

		reset_mapp_div_height() ;
		$('#canned_wrapper').show() ;

		$('#r_mapp_c_'+global_mapp_c).prop('checked', true) ;
	});

	function toggle_menu_info( themenu )
	{
		var divs = Array( "login", "mappc" ) ;

		for ( var c = 0; c < divs.length; ++c )
		{
			$('#div_settings_'+divs[c]).hide() ;
			$('#menu_settings_'+divs[c]).removeClass('menu_traffic_info_focus').addClass('menu_traffic_info') ;
		}

		$('#div_settings_'+themenu).show() ;
		$('#menu_settings_'+themenu).removeClass('menu_traffic_info').addClass('menu_traffic_info_focus') ;
	}

	function update_auto_login( theflag )
	{
		if ( global_auto_login != theflag )
		{
			var json_data = new Object ;
			var unique = unixtime() ;

			$('#r_auto_login_'+theflag).prop('checked', true) ;
			$.ajax({
				type: "POST",
				url: "../index.php",
				data: "action=update_auto_login&value="+theflag+"&"+unique,
				success: function(data){
					eval(data) ;

					if ( json_data.status )
					{
						global_auto_login = theflag ;
						do_alert( 1, "Success" ) ;
					}
					else
					{
						$('#r_auto_login_'+global_auto_login).prop('checked', true) ;
						do_alert( 0, "Error processing automatic login.  Please try again." ) ;
					}
				}
			});
		}
	}

	function update_mapp_confirm( theflag )
	{
		if ( global_mapp_c != theflag )
		{
			var json_data = new Object ;
			var unique = unixtime() ;

			$('#r_mapp_c_'+theflag).prop('checked', true) ;
			$.ajax({
				type: "POST",
				url: "./ajax/mapp_actions_op.php",
				data: "action=update_mapp_c&value="+theflag+"&"+unique,
				success: function(data){
					eval(data) ;

					if ( json_data.status )
					{
						parent.mapp_c = theflag ;
						global_mapp_c = theflag ;
						do_alert( 1, "Success" ) ;
					}
					else
					{
						$('#r_mapp_c_'+global_mapp_c).prop('checked', true) ;
						do_alert( 0, "Error processing notification confirmation.  Please try again." ) ;
					}
				}
			});
		}
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
					<div id="menu_settings_login" class="menu_traffic_info_focus" onClick="toggle_menu_info('login')">Automatic Login</div>
					<div id="menu_settings_mappc" class="menu_traffic_info" onClick="toggle_menu_info('mappc')">Minimize Confirmation</div>
					<div style="clear: both;"></div>
				</div>

				<div id="div_settings_login" style="margin-top: 25px;">
					<div>
						<div style="font-size: 14px; font-weight: bold;">Automatic Login</div>
						<div style="margin-top: 5px; text-align: justify;">Automatically login to your Operator area without providing the login credentials on the signin screen.</div>
						<div style="margin-top: 10px;">
							<div class="info_mapp_good" style="float: left; width: 100px; cursor: pointer;" onClick="update_auto_login(1)"><input type="radio" name="r_auto_login" id="r_auto_login_1" value=1 <?php echo ( $auto_login_enabled ) ? "checked" : "" ?> > On</div>
							<div class="info_mapp_error" style="float: left; margin-left: 10px; width: 100px; cursor: pointer;" onClick="update_auto_login(0)"><input type="radio" name="r_auto_login" id="r_auto_login_0" value=0 <?php echo ( !$auto_login_enabled ) ? "checked" : "" ?> > Off</div>
						</div>
						<div style="clear: both;"></div>
					</div>
				</div>
				<div id="div_settings_mappc" style="display: none; margin-top: 25px;">
					<div>
						<div style="font-size: 14px; font-weight: bold;">Mobile App Minimize Confirmation</div>
						<div style="margin-top: 5px; text-align: justify;">Display a confirmation push notification when the mobile app is placed in the background to confirm the server has received the request to process push notifications for new chats or responses.</div>
						<div style="margin-top: 10px;">
							<div class="info_mapp_good" style="float: left; width: 100px; cursor: pointer;" onClick="update_mapp_confirm(1)"><input type="radio" name="r_mapp_c" id="r_mapp_c_1" value=1> On</div>
							<div class="info_mapp_error" style="float: left; margin-left: 10px; width: 100px; cursor: pointer;" onClick="update_mapp_confirm(0)"><input type="radio" name="r_mapp_c" id="r_mapp_c_0" value=0> Off</div>
						</div>
						<div style="clear: both;"></div>
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
