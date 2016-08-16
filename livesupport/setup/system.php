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

	// permission checking
	$perm_web = is_writable( "$CONF[CONF_ROOT]" ) ;
	$perm_conf = is_writeable( "$CONF[CONF_ROOT]/config.php" ) ;
	$perm_chats = is_writeable( $CONF["CHAT_IO_DIR"] ) ;
	$perm_initiate = is_writeable( $CONF["TYPE_IO_DIR"] ) ;
	$perm_patches = is_writeable( "$CONF[CONF_ROOT]/patches" ) ;
	$disabled_functions = ini_get( "disable_functions" ) ;
	$ini_open_basedir = ini_get("open_basedir") ;
	$ini_safe_mode = ini_get("safe_mode") ;
	$safe_mode = preg_match( "/on/i", $ini_safe_mode ) ? 1 : 0 ;

	$created = date( "M j, Y", $admininfo["created"] ) ;
	$diff = time() - $admininfo["created"] ; $days_running = round( $diff/(60*60*24) ) ;
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

<script type="text/javascript">
<!--
	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "settings" ) ;
		fetch_admins() ;
	});

	function generate_admin()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$('#btn_generate').attr( "disabled", true ) ;

		$.ajax({
		type: "POST",
		url: "../ajax/setup_actions.php",
		data: "action=generate_setup_admin&ses=<?php echo $ses ?>&"+unique,
		success: function(data){
			eval( data ) ;

			$('#btn_generate').attr( "disabled", false ) ;
			if ( json_data.status )
			{
				do_alert( 1, "Account Created" ) ;
				fetch_admins() ;
			}
			else
				do_alert( 0, json_data.error ) ;
		},
		error:function (xhr, ajaxOptions, thrownError){
			$('#btn_generate').attr( "disabled", false ) ;
			alert( "Could not connect to server.  Try reloading this page." ) ;
		} });
	}

	function fetch_admins()
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		$.ajax({
		type: "POST",
		url: "../ajax/setup_actions.php",
		data: "action=fetch_setup_admins&ses=<?php echo $ses ?>&"+unique,
		success: function(data){
			eval( data ) ;

			if ( json_data.status )
			{
				var admin_string = "<table cellspacing=0 cellpadding=0 border=0 width='100%'><tr><td width=\"14\" class=\"td_dept_td\">&nbsp;</td><td width=\"30\" class=\"td_dept_td\"><b>Login</b></td><td width=\"30\" class=\"td_dept_td\"><b>Password</b></td><td class=\"td_dept_td\"><b>Created</b></td><td class=\"td_dept_td\"><b>Accessed</b></td></tr>" ;
				for ( var c = 0; c < json_data.admins.length; ++c )
				{
					var admin = json_data.admins[c] ;
					var password = admin["password"] ;
					var delete_option = "<img src=\"../pics/icons/delete.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"delete\" title=\"delete\" style=\"cursor: pointer;\" onClick=\"delete_admin("+admin["adminid"]+")\">" ;

					admin_string += "<tr><td class=\"td_dept_td\">"+delete_option+"</td><td class=\"td_dept_td\">"+admin["login"]+"</td><td class=\"td_dept_td\">"+password+"</td><td class=\"td_dept_td\" nowrap>"+admin["created"]+"</td><td class=\"td_dept_td\" nowrap>"+admin["lastactive"]+"</td></tr>" ;
				}
				admin_string += "</table>" ;
				
				$('#div_admins').html( admin_string ) ;
			}
			else
			{
			}
		},
		error:function (xhr, ajaxOptions, thrownError){
			alert( "Could not connect to server.  Try reloading this page." ) ;
		} });
	}

	function delete_admin( theadminid )
	{
		var unique = unixtime() ;
		var json_data = new Object ;

		if ( confirm( "Really delete this Admin?" ) )
		{
			$.ajax({
			type: "POST",
			url: "../ajax/setup_actions.php",
			data: "action=delete_setup_admin&ses=<?php echo $ses ?>&adminid="+theadminid+"&"+unique,
			success: function(data){
				eval( data ) ;

				$('#btn_generate').attr( "disabled", false ) ;
				if ( json_data.status )
				{
					do_alert( 1, "Account Deleted" ) ;
					fetch_admins() ;
				}
				else
					do_alert( 0, json_data.error ) ;
			},
			error:function (xhr, ajaxOptions, thrownError){
				alert( "Could not connect to server.  Try reloading this page." ) ;
			} });
		}
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

			<div class="op_submenu_wrapper">
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=eips'" id="menu_eips">Excluded IPs</div>
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=sips'" id="menu_sips">Blocked IPs</div>
				<div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=cookie'" id="menu_cookie">Cookies</div>
				<?php if ( is_file( "$CONF[DOCUMENT_ROOT]/mapp/settings.php" ) && ( $admininfo["adminID"] == 1 ) ): ?><div class="op_submenu" onClick="location.href='../mapp/settings.php?ses=<?php echo $ses ?>'" id="menu_system"><img src="../pics/icons/mobile.png" width="12" height="12" border="0" alt=""> Mobile App</div><?php endif ; ?>
				<?php if ( $admininfo["adminID"] == 1 ): ?><div class="op_submenu" onClick="location.href='settings.php?ses=<?php echo $ses ?>&jump=profile'" id="menu_profile"><img src="../pics/icons/key.png" width="12" height="12" border="0" alt=""> Setup Profile</div><?php endif ; ?>
				<div class="op_submenu_focus" id="menu_system">System</div>
				<div style="clear: both"></div>
			</div>

			<div style="margin-top: 25px;">
				<form>
				<div style="float: left; width: 750px;">

					<div class="info_info">
						<table cellspacing=0 cellpadding=5 border=0 width="100%">
						<tr>
							<td nowrap><b>License Key:</b> <?php echo $KEY ?>
							<td width="100%" align="right" style="padding-left: 25px;"></td>
						</tr>
						<tr>
							<td colspan=2>
								<div class="info_neutral" style="">
									<table cellspacing=0 cellpadding=2 border=0>
									<tr>
										<td width="150">PHP Live! <span class="info_box">v.<?php echo $VERSION ?></span></td><td><img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck&v=<?php echo base64_encode( $VERSION ) ?>" target="new">check for new version</a></td>
									</tr>
									</table>
								</div>
								<?php
									if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/VERSION.php" ) ):
									include_once( "$CONF[DOCUMENT_ROOT]/addons/smtp/API/VERSION.php" ) ;
								?>
								<div class="info_neutral" style="margin-top: 15px;">
									<table cellspacing=0 cellpadding=2 border=0>
									<tr>
										<td width="150"><a href="../addons/smtp/smtp.php?ses=<?php echo $ses ?>">SMTP addon</a> <span class="info_box">v.<?php echo $SMTP_VERSION ?></span></td><td><img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck_smtp&v=<?php echo base64_encode( $SMTP_VERSION ) ?>" target="new">check for new version</a></td>
									</tr>
									</table>
								</div>
								<?php endif ; ?>
								<?php
									if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/geo_data/VERSION.php" ) ):
									include_once( "$CONF[DOCUMENT_ROOT]/addons/geo_data/VERSION.php" ) ;
								?>
								<div class="info_neutral" style="margin-top: 15px;">
									<table cellspacing=0 cellpadding=2 border=0>
									<tr>
										<td width="150"><a href="extras_geo.php?ses=<?php echo $ses ?>">GeoIP addon</a> <span class="info_box">v.<?php echo $VERSION_GEO ?></span></td><td><img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck_geo&v=<?php echo base64_encode( $VERSION_GEO ) ?>" target="new">check for new version</a></td>
									</tr>
									</table>
								</div>
								<?php endif ; ?>
								<?php
									if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/VERSION.php" ) ):
									include_once( "$CONF[DOCUMENT_ROOT]/addons/emoticons/VERSION.php" ) ;
								?>
								<div class="info_neutral" style="margin-top: 15px;">
									<table cellspacing=0 cellpadding=2 border=0>
									<tr>
										<td width="150"><a href="../addons/emoticons/emo.php?ses=<?php echo $ses ?>">Emoticons addon</a> <span class="info_box">v.<?php echo $EMO_VERSION ?></span></td><td><img src="../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck_emo&v=<?php echo base64_encode( $EMO_VERSION ) ?>" target="new">check for new version</a></td>
									</tr>
									</table>
								</div>
								<?php endif ; ?>
							</td>
						</tr>
						</table>
					</div>

					<?php if ( $admininfo["status"] != -1 ): ?>
					<div style="margin-top: 25px; min-height: 150px; max-height: 250px; text-align: justify;" class="info_info">
						<div style="float: left; width: 200px; margin-right: 15px;">
							<div class="edit_title">Temporary Admins</div>
							<div style="margin-top: 15px;">At this time, temporary setup admins have access to all the setup options.</div>
							<div style="margin-top: 15px;"><button type="button" onClick="generate_admin()" id="btn_generate" class="btn">Generate Temp Admin</button></div>
						</div>
						<div style="float: left; width: 450px;">
							<div style="margin-top: 15px; min-height: 145px; max-height: 245px; overflow-x: hidden; overflow-y: auto;" id="div_admins"></div>
						</div>
						<div style="clear: both;"></div>
					</div>
					<?php endif ; ?>

				</div>
				<div style="float: left; border: 0px solid transparent; margin-left: 25px; text-align: right;">
					<?php if ( $admininfo["adminID"] == 1 ): ?>
					System Installed on:
					<div style="margin-top: 5px; margin-bottom: 90px; font-size: 16px;">
						<?php echo $created ?>
						<div style="margin-top: 5px; font-size: 12px;">(<?php echo ( $days_running ) ? $days_running : 1 ; ?> days)</div>
					</div>
					<?php endif ; ?>

					<div style=""><a href="db.php?ses=<?php echo $ses ?>">View Database</a></div>
					<div style="margin-top: 15px;">
						<a href="http://php.net/manual/en/reserved.constants.php" target=_blank>PHP_INT_MAX</a>
						<div style="font-weight: bold; margin-top: 10px;" class="info_neutral"><?php echo PHP_INT_MAX; ?></div>
					</div>
				</div>
				<div style="clear:both;"></div>
				</form>

			</div>

<?php include_once( "./inc_footer.php" ) ?>