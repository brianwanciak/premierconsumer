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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;  if ( !$theme ) { $theme = $CONF["THEME"] ; }
	$error = "" ;

	$dept_themes = ( isset( $VALS["THEMES"] ) && $VALS["THEMES"] ) ? unserialize( $VALS["THEMES"] ) : Array() ;
	$departments = Depts_get_AllDepts( $dbh ) ;

	if ( $action == "update_theme" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;

		if ( is_dir( "$CONF[DOCUMENT_ROOT]/themes/$theme/" ) )
		{
			if ( $deptid )
			{
				$update_vals = 0 ;
				if ( ( $deptid && isset( $dept_themes[$deptid] ) && ( $dept_themes[$deptid] == $theme ) ) || ( isset( $CONF["THEME"] ) && ( $CONF["THEME"] == $theme ) ) ) {
					if ( isset( $dept_themes[$deptid] ) ) { unset( $dept_themes[$deptid] ) ; $update_vals = 1 ; }
				}
				else { $dept_themes[$deptid] = $theme ; }
				if ( count( $dept_themes ) || $update_vals ) { $error = ( Util_Vals_WriteToFile( "THEMES", serialize( $dept_themes ) ) ) ? "" : "Could not write to vals file. [e2]" ; }
			}
			else
			{
				$error = ( Util_Vals_WriteToConfFile( "THEME", $theme ) ) ? "" : "Could not write to config file. [e4]" ;
				if ( !$error )
				{
					$update_vals = 0 ;
					foreach ( $dept_themes as $the_deptid => $this_theme )
					{
						if ( $theme == $this_theme ) { unset( $dept_themes[$the_deptid] ) ; $update_vals = 1 ; }
					}
					if ( count( $dept_themes ) || $update_vals ) { $error = ( Util_Vals_WriteToFile( "THEMES", serialize( $dept_themes ) ) ) ? "" : "Could not write to vals file. [e7]" ; }
				}
			}
		}
		else { $error = "Invalid theme." ; }
	}

	$themes_js = "" ;
	foreach ( $dept_themes as $key => $value )
		$themes_js .= "themes[$key] = '$value' ; " ;
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
	var deptid = <?php echo $deptid ?> ;
	var primary_theme = "<?php echo $CONF["THEME"] ?>" ;
	var global_theme = "<?php echo $theme ?>" ;
	var themes = new Object ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "interface" ) ;

		eval( "<?php echo $themes_js ?>" ) ;

		switch_dept( deptid ) ;
		<?php if ( $action && !$error ): ?>do_alert( 1, "Update Success" ) ; setTimeout( function(){ $('#div_alert_wrapper').fadeOut("slow") ; }, 3000 ) ;<?php endif ; ?>
	});

	function switch_dept( thedeptid )
	{
		var theme ;
		var dept_name = $("#deptid option:selected").text() ;

		deptid = thedeptid ;

		if ( typeof( themes[deptid] ) != "undefined" ) { theme = themes[deptid] ; }
		else { theme = primary_theme ; }

		$('#div_themes').find('*').each( function () {
			var div_name = this.id ;
			if ( div_name.indexOf( "span_" ) == 0 )
				$('#'+div_name).removeClass('info_box').addClass('info_neutral') ;
		}) ;
		$('#span_'+theme).removeClass('info_neutral').addClass('info_box') ;
		$('#theme_'+theme).prop('checked', true) ;
		$('#div_thumb_'+theme).fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;

		if ( dept_name == "Primary Theme" )
			$('#div_information').html( "<span style='font-size: 16px; font-weight: bold;'>Primary</span> chat window theme for <a href='./code.php?ses=<?php echo $ses ?>&deptid="+deptid+"'>All Departments HTML Code</a>" ) ;
		else
			$('#div_information').html( "<span style='font-size: 16px; font-weight: bold;'>"+dept_name+"</span> chat window theme for <a href='./code.php?ses=<?php echo $ses ?>&deptid="+deptid+"'>Department Specific HTML Code</a>" ) ;
		global_theme = theme ;
	}

	function confirm_theme( thetheme, thethumb )
	{
		if ( global_theme != thetheme )
		{
			var height = $(document).height() ;

			$('#theme_'+thetheme).prop('checked', true) ;
			$('#div_theme_thumb').html( "<div style=\"background: url( "+thethumb+" ); background-position: top left; width: 85px; height: 54px; -moz-border-radius: 5px; border-radius: 5px;\">&nbsp;</div>") ;

			$('body').css({'overflow': 'hidden'}) ;
			$('#div_confirm').css({'height': height+'px'}).show() ;
			$('#div_confirm_body').center().show() ;
		}
	}

	function update_theme( thetheme )
	{
		location.href = "interface_themes.php?ses=<?php echo $ses ?>&action=update_theme&deptid="+deptid+"&theme="+thetheme ;
	}

	function update_theme_pre( theflag )
	{
		if ( theflag )
		{
			var theme = $('input:radio[name=theme]:checked').val() ;
			update_theme( theme ) ;
		}
		else
		{
			$('#theme_'+global_theme).prop('checked', true) ;

			$('#div_confirm').hide() ;
			$('#div_confirm_body').hide() ;
			$('body').css({'overflow': 'visible'}) ;
		}
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu_focus">Themes</div>
			<div class="op_submenu" onClick="location.href='interface.php?ses=<?php echo $ses ?>&jump=logo'">Logo</div>
			<div class="op_submenu" onClick="location.href='interface.php?ses=<?php echo $ses ?>&jump=charset'">Character Set</div>
			<?php if ( phpversion() >= "5.1.0" ): ?><div class="op_submenu" onClick="location.href='interface.php?ses=<?php echo $ses ?>&jump=time'">Time Zone</div><?php endif; ?>
			<div class="op_submenu" onClick="location.href='interface_lang.php?ses=<?php echo $ses ?>'" id="menu_lang">Language Text</div>
			<div class="op_submenu" onClick="location.href='interface.php?ses=<?php echo $ses ?>&jump=screen'">Login Screen</div>
			<div style="clear: both"></div>
		</div>

		<form>
		<div style="margin-top: 25px;">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td valign="top" width="50%">
					<div style="text-shadow: none;">
						<form>
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td>
								<div>
									<select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_dept( this.value )">
									<option value="0">Primary Theme</option>
									<?php
										if ( count( $departments ) > 1 )
										{
											for ( $c = 0; $c < count( $departments ); ++$c )
											{
												$department = $departments[$c] ;
												$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
												if ( $department["name"] != "Archive" )
													print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
											}
										}
									?>
									</select>
								</div>
							</td>
							<td style="padding-left: 25px;">
								<div id="div_information"></div>
							</td>
						</tr>
						</table>

						<div id="div_themes" style="margin-top: 5px;">
							<div id="div_alert_wrapper" style="margin-top: 25px;"><span id="div_alert"></span></div>
							<table cellspacing=0 cellpadding=2 border=0 width="100%" style="margin-top: 25px;">
							<tr>
								<td>
									<?php
										$dir_themes = opendir( "$CONF[DOCUMENT_ROOT]/themes/" ) ;

										$themes = Array() ;
										while ( $theme = readdir( $dir_themes ) )
											$themes[] = $theme ;
										closedir( $dir_themes ) ;

										sort( $themes, SORT_STRING ) ;
										for ( $c = 0; $c < count( $themes ); ++$c )
										{
											$theme = $themes[$c] ;
											$path_thumb = ( is_file( "../themes/$theme/thumb.png" ) ) ? "../themes/$theme/thumb.png" : "../pics/screens/thumb_theme_blank.png" ;

											if ( preg_match( "/[a-z]/i", $theme ) && ( $theme != "initiate" ) )
												print "<div class=\"li_op round\" style=\"padding: 5px; width: 150px; margin-bottom: 15px;\"><div id=\"div_thumb_$theme\" style=\"background: url( $path_thumb ); background-position: top left; height: 100px; -moz-border-radius: 5px; border-radius: 5px;\"><span style=\"cursor: pointer;\" onClick=\"confirm_theme('$theme', '$path_thumb')\" id=\"span_$theme\"><input type=\"radio\" name=\"theme\" id=\"theme_$theme\" value=\"$theme\"> $theme</span></div><div style=\"background: #FAFAFA; border: 1px solid #D6D6D6; padding: 5px; color: #6D6D71; cursor: pointer;\" class=\"round_bottom\" onClick=\"preview_theme('$theme', $VARS_CHAT_WIDTH, $VARS_CHAT_HEIGHT, 0 )\"><img src=\"../pics/icons/arrow_right.png\" width=\"16\" height=\"15\" border=\"0\" alt=\"\"> click to preview</div></div>" ;
										}
									?>
									<div style="clear: both;"></div>
								</td>
							</tr>
							</table>
						</div>
						</form>
					</div>
				</td>
			</tr>
			</table>
		</div>
		</form>

<div id="div_confirm" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">&nbsp;</div>
<div id="div_confirm_body" class="info_info" style="display: none; position: absolute; width: 350px; margin: 0 auto; top: 100px; z-index: 21;">
	<div class="info_box" style="padding: 25px;">
		<table cellspacing=0 cellpadding=0 border=0>
		<tr>
			<td><div id="div_theme_thumb" class="li_mapp round" style="width: 85px; height: 54px;"></div><div class="clear:both;"></div></td>
			<td style="padding-left: 15px;">
				<div id="confirm_title">Select this theme?</div>
				<div style="margin-top: 15px;"><button type="button" onClick="update_theme_pre(1)" class="input_button" class="btn">Yes</button> &nbsp; &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="update_theme_pre(0)">cancel</span></div>
			</td>
		</tr>
		</table>
	</div>
</div>

<?php include_once( "./inc_footer.php" ) ?>
