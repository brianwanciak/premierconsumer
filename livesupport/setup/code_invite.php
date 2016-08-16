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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/API/Util_Extra_Pre.php" ) ) { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_.php" ) ; }
	else { include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload.php" ) ; }
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Upload_File.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get_itr.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Vars/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$token = Util_Format_Sanatize( Util_Format_GetVar( "token" ), "ln" ) ;
	$jump = ( Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ) ? Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) : "main" ;
	$jump = Util_Format_Sanatize( Util_Format_GetVar( "jump" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$now = time() ; $error = "" ; $display = 0 ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$ops_assigned = 0 ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$ops = Depts_get_DeptOps( $dbh, $department["deptID"] ) ;
		if ( count( $ops ) )
			$ops_assigned = 1 ;
	}
	$deptinfo = Array() ;
	if ( $deptid )
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;

	$total_ops = Ops_get_TotalOps( $dbh ) ;
	$total_ops_online = Ops_get_itr_AnyOpsOnline( $dbh, $deptid ) ;

	if ( ( $action == "clear" ) && ( isset( $CONF['icon_initiate'] ) && $CONF['icon_initiate'] ) )
	{
		$error = ( Util_Vals_WriteToConfFile( "icon_initiate", "" ) ) ? "" : "Could not write to config file." ;
	}
	else if ( $action == "update_image" )
	{
		LIST( $error, $filename ) = Util_Upload_File( "icon_initiate", 0 ) ;
		if ( !$error )
		{
			database_mysql_close( $dbh ) ;
			HEADER( "location: code_invite.php?ses=$ses&action=update" ) ;
			exit ;
		}
	}
	else if ( $action == "update_criteria" )
	{
		$onoff = Util_Format_Sanatize( Util_Format_GetVar( "onoff" ), "n" ) ;
		$duration = Util_Format_Sanatize( Util_Format_GetVar( "duration" ), "n" ) ;
		$andor = Util_Format_Sanatize( Util_Format_GetVar( "andor" ), "n" ) ;
		$footprints = Util_Format_Sanatize( Util_Format_GetVar( "footprints" ), "n" ) ;
		$reset = Util_Format_Sanatize( Util_Format_GetVar( "reset" ), "n" ) ;
		$pos = Util_Format_Sanatize( Util_Format_GetVar( "pos" ), "n" ) ;
		$exin = Util_Format_Sanatize( Util_Format_GetVar( "exin" ), "ln" ) ;
		$exclude = preg_replace( "/[;*\/:\[\]\"\']/", "", stripslashes( Util_Format_Sanatize( Util_Format_GetVar( "exclude" ), "ln" ) ) ) ;

		if ( !$onoff )
			$error = ( Util_Vals_WriteToFile( "auto_initiate", serialize( Array() ) ) ) ? "" : "Could not write to vals file." ;
		else
		{
			$exclude_array = explode( ",", $exclude ) ;
			$exclude_string = "" ;
			for ( $c = 0; $c < count( $exclude_array ); ++$c )
			{
				$temp = preg_replace( "/ +/", "", $exclude_array[$c] ) ;
				if ( $temp )
					$exclude_string .= "$temp," ;
			}

			if ( $exclude_string ) { $exclude_string = substr_replace( $exclude_string, "", -1 ) ; }

			$initiate = Array() ;
			$initiate["duration"] = $duration ;
			$initiate["andor"] = $andor ;
			$initiate["footprints"] = $footprints ;
			$initiate["reset"] = $reset ;
			$initiate["exclude"] = $exclude_string ;
			$initiate["pos"] = $pos ;
			$initiate["exin"] = $exin ;

			$initiate_serialize = serialize( $initiate ) ;
			$admininfo["initiate"] = $initiate_serialize ;

			if ( $andor )
			{
				$error = ( Util_Vals_WriteToFile( "auto_initiate", htmlentities( $initiate_serialize ) ) ) ? "" : "Could not write to vals file." ;
				if ( !$error )
				{
					database_mysql_close( $dbh ) ;
					HEADER( "location: code_invite.php?ses=$ses&action=update&jump=auto&jump=criteria" ) ;
					exit ;
				}
			}
		}
	}

	$initiate = ( isset( $VALS["auto_initiate"] ) && $VALS["auto_initiate"] ) ? unserialize( html_entity_decode( $VALS["auto_initiate"] ) ) : Array() ;
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
	var phplive_browser = navigator.appVersion ; var phplive_mime_types = "" ;
	var phplive_display_width = screen.availWidth ; var phplive_display_height = screen.availHeight ; var phplive_display_color = screen.colorDepth ; var phplive_timezone = new Date().getTimezoneOffset() ;
	if ( navigator.mimeTypes.length > 0 ) { for (var x=0; x < navigator.mimeTypes.length; x++) { phplive_mime_types += navigator.mimeTypes[x].description ; } }
	var phplive_browser_token = phplive_md5( phplive_display_width+phplive_display_height+phplive_display_color+phplive_timezone+phplive_browser+phplive_mime_types ) ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "html" ) ;

		if ( "<?php echo $jump ?>" == "criteria" ) { show_subdiv( "criteria" ) ; }

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;
		<?php elseif ( $action && $error ): ?>do_alert_div( "..", 0, "<?php echo $error ?>" ) ;
		<?php endif ; ?>

		if ( <?php echo isset( $initiate["exin"] ) ? 1 : 0 ; ?> ) { toggle_onoff( 1 ) ; }
		else { toggle_onoff( 0 ) ; }
	});

	function show_subdiv( thediv )
	{
		var divs = Array( "image", "criteria" ) ;
		for ( var c = 0; c < divs.length; ++c )
		{
			$('#div_sub_'+divs[c]).hide() ;
			$('#menu_sub_'+divs[c]).removeClass('op_submenu_focus').addClass('op_submenu2') ;
		}

		$('#div_sub_'+thediv).show() ;
		$('#menu_sub_'+thediv).removeClass('op_submenu2').addClass('op_submenu_focus') ;
	}

	function toggle_onoff( theflag )
	{
		if ( theflag )
		{
			$('#div_on').show() ;
		}
		else
		{
			$('#div_on').hide() ;
		}
	}

	function confirm_clear()
	{
		if ( confirm( "Really reset the chat invitation image?" ) )
		{
			location.href = "code_invite.php?ses=<?php echo $ses ?>&action=clear&<?php echo $now ?>" ;
		}
	}

	function do_redirect()
	{
		location.href = "code_invite_live.php?ses=<?php echo $ses ?>&token="+phplive_browser_token ;
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<?php if ( !count( $departments ) ): ?>
		<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Create a <a href="depts.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Department</a> to continue.</span>
		<?php elseif ( !$total_ops ): ?>
		<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Create an <a href="ops.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Operator</a> to continue.</span>
		<?php elseif ( !$ops_assigned ): ?>
		<span class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> <a href="ops.php?ses=<?php echo $ses ?>&jump=assign" style="color: #FFFFFF;">Assign an operator to a department</a> to continue.</span>
		<?php
			else:
			$display = 1 ;
		?>
		<?php endif ; ?>

		<?php if ( $display ): ?>
		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='code.php?ses=<?php echo $ses ?>'">HTML Code</div>
			<div class="op_submenu" onClick="location.href='code_settings.php?ses=<?php echo $ses ?>'">Settings</div>
			<div class="op_submenu_focus" id="menu_code_auto">Automatic Chat Invitation</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			<div>
				<div id="menu_sub_image" class="op_submenu_focus" onClick="show_subdiv('image')">Chat Invitation Image</div>
				<div id="menu_sub_criteria" class="op_submenu2" onClick="show_subdiv('criteria')">Invitation Criteria</div>
				<div id="menu_sub_live" class="op_submenu2" onClick="do_redirect()">Demo Invitation</div>
				<div style="clear: both"></div>
			</div>

			<div style="margin-top: 25px;">

				<div id="div_sub_image">
					<form method="POST" action="code_invite.php?submit" enctype="multipart/form-data">
					<input type="hidden" name="action" value="update_image">
					<input type="hidden" name="token" value="<?php echo $token ?>">
					<input type="hidden" name="jump" value="image">
					<input type="hidden" name="ses" value="<?php echo $ses ?>">
					<input type="hidden" name="MAX_FILE_SIZE" value="200000">

					<table cellspacing=0 cellpadding=0 border=0>
					<tr>
						<td valign="top" width="270">
							<div style="background: url( ../themes/initiate/bg_trans.png ) repeat; padding: 10px;" class="round">
								<div>Current Chat Invitation Image</div>
								<div style="margin-top: 10px;"><img src="<?php echo Util_Upload_GetInitiate( 0 ) ; ?>" width="250" height="160" border="0" alt="" class="round"></div>
							</div>
						</td>
						<td valign="top" style="padding-left: 30px;">
							<div class="edit_title">Upload New Chat Invite Image</div>
							<div style="margin-top: 15px;"><img src="../pics/icons/info.png" width="10" height="10" border="0" alt=""> image should be <b>250 pixels width</b> and <b>160 pixels height</b></div>
							<div style="margin-top: 5px;"><img src="../pics/icons/info.png" width="10" height="10" border="0" alt=""> the "close" (x) portion is part of the image</div>
							<div style="margin-top: 5px;"><img src="../pics/icons/info.png" width="10" height="10" border="0" alt=""> maximum image size of 200 kb</div>
							<div id="div_alert" style="display: none; margin-top: 15px;"></div>
							<div style="margin-top: 25px;">
								<input type="file" name="icon_initiate" size="30"><p>
								<div style="margin-top: 25px;">
									<input type="submit" value="Upload Image" class="btn">
									<?php if ( isset( $CONF['icon_initiate'] ) && $CONF['icon_initiate'] ): ?> &nbsp; &nbsp; <img src="../pics/icons/reset.png" width="16" height="16" border="0" alt=""> <a href="JavaScript:void(0)" onClick="confirm_clear()">reset the chat invitation image to the original</a><?php endif ; ?>
								</div>
							</div>
						</td>
					</tr>
					</table>
					</form>
				</div>
				<div id="div_sub_criteria" style="display: none;">
					<div>On webpages containing the <a href="./code.php?ses=<?php echo $ses ?>">HTML Code</a>, automatically display a chat invitation image to the visitor when certain criterias are met AND when an operator is Online.</div>

					<div style="margin-top: 15px;">
						<form method="POST" action="code_invite.php?submit" enctype="multipart/form-data">
						<input type="hidden" name="action" value="update_criteria">
						<input type="hidden" name="token" value="<?php echo $token ?>">
						<input type="hidden" name="jump" value="criteria">
						<input type="hidden" name="ses" value="<?php echo $ses ?>">
						<input type="hidden" name="MAX_FILE_SIZE" value="200000">
						<div>
							<table cellspacing=0 cellpadding=0 border=0>
							<tr>
								<td><div class="tab_form_title">On / Off</div></td>
								<td style="padding-left: 10px;">
									<div>
										<div class="info_good" style="float: left; width: 60px; padding: 3px; cursor: pointer;" onClick="$('#onoff_on').prop('checked', true);toggle_onoff(1);"><input type="radio" name="onoff" id="onoff_on" value="1" onClick="toggle_onoff(1)" <?php echo isset( $initiate["exin"] ) ? "checked" : "" ; ?>> On</div>
										<div class="info_error" style="float: left; margin-left: 10px; width: 60px; padding: 3px; cursor: pointer;"  onClick="$('#onoff_off').prop('checked', true);toggle_onoff(0);"><input type="radio" name="onoff" id="onoff_off" value="0" onClick="toggle_onoff(0)" <?php echo !isset( $initiate["exin"] ) ? "checked" : "" ; ?>> Off</div>
										<div style="clear: both;"></div>
									</div>
								</td>
							</tr>
							</table>
						</div>
						<div id="div_on" style="display: none;">
							<div style="margin-top: 25px;">
								<table cellspacing=0 cellpadding=0 border=0 width="100%">
								<tr>
									<td nowrap><div class="tab_form_title">Criteria</div></td>
									<td width="100%" style="padding-left: 10px;">
										On the same page for at least <select name="duration">
										<?php
											$options = Array(
												15 => "15 seconds",
												30 => "30 seconds",
												60 => "1 minute",
												90 => "1 min 30 sec",
												120 => "2 minutes",
												150 => "2 min 30 sec",
												180 => "3 minutes",
												300 => "5 minutes"
											) ;
											foreach ( $options as $numeric => $value )
											{
												$selected = "" ;
												if ( isset( $initiate["duration"] ) && ( $initiate["duration"] == $numeric ) )
													$selected = "selected" ;

												print "<option value='$numeric' $selected>$value</option>" ;
											}
										?></select> 
										<select name="andor"><option value="1" <?php echo ( isset( $initiate["andor"] ) && ( $initiate["andor"] == 1 ) ) ? "selected" : "" ?> >or</option><option value="2" <?php echo ( isset( $initiate["andor"] ) && ( $initiate["andor"] == 2 ) ) ? "selected" : "" ?> >and</option></select>
										has viewed <select name="footprints">
										<?php
											for( $c = 1; $c <= 25; ++$c )
											{
												$selected = "" ;
												if ( isset( $initiate["footprints"] ) && ( $initiate["footprints"] == $c ) )
													$selected = "selected" ;

												print "<option value=\"$c\" $selected>$c</option>" ;
											}
										?></select> page views
									</td>
								</tr>
								</table>
							</div>
							<div style="margin-top: 25px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td><div class="tab_form_title">Invite Display Position</div></td>
									<td style="padding-left: 10px;">
										<select name="pos" id="pos" style="background: #D5E6FD;">
											<option value="1" <?php echo ( !isset( $initiate["pos"] ) || ( $initiate["pos"] == 1 ) ) ? "selected" : "" ; ?> >Left</option>
											<option value="2" <?php echo ( isset( $initiate["pos"] ) && ( $initiate["pos"] == 2 ) ) ? "selected" : "" ; ?> >Right</option>
											<option value="3" <?php echo ( isset( $initiate["pos"] ) && ( $initiate["pos"] == 3 ) ) ? "selected" : "" ; ?> >Bottom Left</option>
											<option value="4" <?php echo ( isset( $initiate["pos"] ) && ( $initiate["pos"] == 4 ) ) ? "selected" : "" ; ?> >Bottom Right</option>
											<option value="100" <?php echo ( isset( $initiate["pos"] ) && ( $initiate["pos"] == 100 ) ) ? "selected" : "" ; ?> >Center of the page</option>
										</select>
									</td>
								</tr>
								</table>
							</div>
							<div style="margin-top: 25px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td valign="top"><div class="tab_form_title">Criteria Reset</div></td>
									<td valign="top" style="padding-left: 10px;">
										<div style="">After the chat invitation has been displayed OR when the chat request window is opened, reset the automatic chat invitation criteria and display the invite again after 
											<select name="reset">
											<?php
												for( $c = 1; $c <= 48; ++$c )
												{
													$selected = "" ;
													if ( isset( $initiate["reset"] ) && ( $initiate["reset"] == $c ) )
														$selected = "selected" ;
													print "<option value=\"$c\" $selected>$c</option>" ;
												}
											?></select> hours (* the reset duration is to prevent the chat invitation from displaying during a possible chat session)
										</div>
									</td>
								</tr>
								</table>
							</div>
							<div style="margin-top: 25px;">
								<table cellspacing=0 cellpadding=0 border=0>
								<tr>
									<td valign="top"><div class="tab_form_title">Exclude or Include</div></td>
									<td valign="top" style="padding-left: 10px;">
										<div style="">
											<select name="exin" id="exin" style="width: 100%;">
												<option value="exclude" <?php echo ( isset( $initiate["exin"] ) && ( $initiate["exin"] == "exclude" ) ) ? "selected" : "" ; ?> >EXCLUDE automatic chat invitation for URLs that contain:</option>
												<option value="include" <?php echo ( isset( $initiate["exin"] ) && ( $initiate["exin"] == "include" ) ) ? "selected" : "" ; ?>>INCLUDE automatic chat invitation for only the URLs that contain:</option>
											</select>
										</div>

										<div style="margin-top: 5px;">
											<ul>
												<li> Separate the values with a comma (,) without spaces</li>
												<li> Provide the page itself and not the full URL. Example: <code>purchase.php, checkout.html, trial.asp</code></li>
												<li> Full domain name can be provided.  Example: <code>mysite1.com, mysite3.com</code></li>
												<li> Quotes ("), forward slash (/), back slash (\) and other special characters will be omitted.</li>
											</ul>

											<div style="margin-top: 5px;"><input type="text" class="input" size="65" name="exclude" id="exclude" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $initiate["exclude"] ) && $initiate["exclude"] ) ? $initiate["exclude"] : "" ; ?>"></div>
										</div>
									</td>
								</tr>
								</table>
							</div>
						</div>

						<div style="margin-top: 25px;">
							<table cellspacing=0 cellpadding=0 border=0>
							<tr>
								<td><div class="tab_form_title" style="background: #FFFFFF; border: 1px solid #FFFFFF; text-align: left; font-weight: normal;">&nbsp;</div></td>
								<td style="padding-left: 10px;">
									<input type="submit" value="Save Settings" class="btn">
								</td>
							</tr>
							</table>
						</div>
						</form>

					</div>
				</div>

			</div>
		</div>
		<?php endif ; ?>

<?php include_once( "./inc_footer.php" ) ?>