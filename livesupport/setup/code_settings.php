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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_IP.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Vars/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/IPs/get.php" ) ;

	if ( !isset( $VALS["POPOUT"] ) ) { $VALS["POPOUT"] = "on" ; }
	if ( !isset( $VALS["DEPT_NAME_VIS"] ) ) { $VALS["DEPT_NAME_VIS"] = "off" ; }

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$now = time() ;
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
	var global_popout = "<?php echo ( isset( $VALS["POPOUT"] ) && $VALS["POPOUT"] ) ? $VALS["POPOUT"] : "on" ; ?>" ;
	var global_dept_name_vis = "<?php echo $VALS["DEPT_NAME_VIS"] ?>" ;

	var phplive_browser = navigator.appVersion ; var phplive_mime_types = "" ;
	var phplive_display_width = screen.availWidth ; var phplive_display_height = screen.availHeight ; var phplive_display_color = screen.colorDepth ; var phplive_timezone = new Date().getTimezoneOffset() ;
	if ( navigator.mimeTypes.length > 0 ) { for (var x=0; x < navigator.mimeTypes.length; x++) { phplive_mime_types += navigator.mimeTypes[x].description ; } }
	var phplive_browser_token = phplive_md5( phplive_display_width+phplive_display_height+phplive_display_color+phplive_timezone+phplive_browser+phplive_mime_types ) ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "html" ) ;
	});

	function confirm_popout( thepopout )
	{
		if ( global_popout != thepopout )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=update_popout&value="+thepopout+"&"+unixtime(),
				success: function(data){
					global_popout = thepopout ;
					do_alert( 1, "Success" ) ;
				}
			});
		}
	}

	function confirm_dept_name_vis( the_dept_name_vis )
	{
		if ( global_dept_name_vis != the_dept_name_vis )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=update_dept_name_vis&value="+the_dept_name_vis+"&"+unixtime(),
				success: function(data){
					global_dept_name_vis = the_dept_name_vis ;
					do_alert( 1, "Success" ) ;
				}
			});
		}
	}

	function do_redirect()
	{
		location.href = "code_invite.php?ses=<?php echo $ses ?>&token="+phplive_browser_token ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='code.php?ses=<?php echo $ses ?>'">HTML Code</div>
			<div class="op_submenu_focus">Settings</div>
			<div class="op_submenu" onClick="do_redirect()">Automatic Chat Invitation</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			<form>
			<div style="text-align: justify;" id="settings_misc_settings">
				<div style="float: left; min-height: 160px; width: 45%" class="info_info">
					<div style="font-size: 14px; font-weight: bold;">Embed Chat Popout</div>

					<div style="margin-top: 15px;">(default is On) If <a href="icons.php?ses=<?php echo $ses ?>&jump=settings">embed chat</a> is enabled, the popout feature allows the visitor to open the embed chat in a new pop up window when clicking the popout icon <img src="../pics/icons/win_pop.png" width="16" height="16" border="0" alt="">.  By switching "Off" the embed chat popout, the popout icon <img src="../pics/icons/win_pop.png" width="16" height="16" border="0" alt=""> will not be visible.  "Off" will also remove the print icon <img src="../themes/default/printer.png" width="16" height="16" border="0" alt=""> during a chat session.</div>
					<div style="margin-top: 15px;">
						<div class="info_good" style="float: left; width: 60px; padding: 3px; cursor: pointer" onclick="$('#popout_on').prop('checked', true);confirm_popout('on');"><input type="radio" name="popout" id="popout_on" value="on" <?php echo ( $VALS["POPOUT"] != "off" ) ? "checked" : "" ?>> On</div>
						<div class="info_error" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#popout_off').prop('checked', true);confirm_popout('off');"><input type="radio" name="popout" id="popout_off" value="off" <?php echo ( $VALS["POPOUT"] == "off" ) ? "checked" : "" ?>> Off</div>
						<div style="clear: both;"></div>
					</div>
				</div>
				<div style="float: left; margin-left: 2px; min-height: 160px; width: 45%;" class="info_info">
					<div style="font-size: 14px; font-weight: bold;">Department Name Visible for One Department</div>

					<div style="margin-top: 15px;">(default is Off) Set the system to display or not to display the department name on the chat request window for the <a href="./code.php?ses=<?php echo $ses ?>">Department Specific HTML Code</a> or if only one department has been created.</div>
					<div style="margin-top: 15px;">
						<div class="info_good" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#dept_name_vis_on').prop('checked', true);confirm_dept_name_vis('on');"><input type="radio" name="dept_name_vis" id="dept_name_vis_on" value="on" <?php echo ( $VALS["DEPT_NAME_VIS"] != "off" ) ? "checked" : "" ?>> On</div>
						<div class="info_error" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;" onclick="$('#dept_name_vis_off').prop('checked', true);confirm_dept_name_vis('off');"><input type="radio" name="dept_name_vis" id="dept_name_vis_off" value="off" <?php echo ( $VALS["DEPT_NAME_VIS"] == "off" ) ? "checked" : "" ?>> Off</div>
						<div style="clear: both;"></div>
					</div>
				</div>
				<div style="clear: both;"></div>
			</div>
			</form>
		</div>

<?php include_once( "./inc_footer.php" ) ?>