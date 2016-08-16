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
	$error = "" ; $jump = "themes" ;

	$op_sounds = isset( $VALS["op_sounds"] ) ? unserialize( $VALS["op_sounds"] ) : Array() ;
	if ( isset( $op_sounds[$opinfo["opID"]] ) ) { $op_sounds_vals = $op_sounds[$opinfo["opID"]] ; $opinfo["sound1"] = $op_sounds_vals[0] ; $opinfo["sound2"] = $op_sounds_vals[1] ; } else { $opinfo["sound1"] = "default" ; $opinfo["sound2"] = "default" ; }

	if ( $action == "update_theme" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update.php" ) ;
		$theme = Util_Format_Sanatize( Util_Format_GetVar( "theme" ), "ln" ) ;

		if ( !Ops_update_OpValue( $dbh, $opinfo["opID"], "theme", $theme ) )
			$error = "Error in updating theme." ;
		else
			$opinfo["theme"] = $theme ;
	}
	else if ( $action == "update_sound" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
		$sound1 = Util_Format_Sanatize( Util_Format_GetVar( "sound1" ), "ln" ) ;
		$sound2 = Util_Format_Sanatize( Util_Format_GetVar( "sound2" ), "ln" ) ;

		$op_sounds[$opinfo["opID"]] = Array( $sound1, $sound2 ) ;
		Util_Vals_WriteToFile( "op_sounds", serialize( $op_sounds ) ) ;
		$opinfo["sound1"] = $sound1 ; $opinfo["sound2"] = $sound2 ;
		
		$jump = "sounds" ;
	}
	else
		$error = "invalid action" ;
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
<script type="text/javascript" src="../js/global_chat.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery.tools.min.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/modernizr.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	"use strict" ;
	var base_url = ".." ; var base_url_full = "<?php echo $CONF["BASE_URL"] ?>" ;
	var mobile = 1 ;
	var mapp = 1 ;
	var sound_volume = parent.sound_volume ;

	var theme = "<?php echo $opinfo["theme"] ?>" ;
	var audio_supported = HTML5_audio_support() ;
	var mp3_support = ( typeof( audio_supported["mp3"] ) != "undefined" ) ? 1 : 0 ;

	$(document).ready(function()
	{
		reset_mapp_div_height() ;
		$('#canned_wrapper').show() ;

		toggle_menu_info( "<?php echo $jump ?>" ) ;

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>

		if ( ( typeof( parent.isop ) != "undefined" ) && ( ( "<?php echo $action ?>" == "update_theme" ) || ( "<?php echo $action ?>" == "update_sound" ) ) )
			parent.reload_console(0) ;

		if ( parent.chat_sound ) { $('#r_sound_1').prop('checked', true) ; }
		else { $('#r_sound_0').prop('checked', true) ; }

		parent.init_extra_loaded() ;
	});

	function toggle_menu_info( themenu )
	{
		var divs = Array( "themes", "sounds" ) ;

		for ( var c = 0; c < divs.length; ++c )
		{
			$('#div_settings_'+divs[c]).hide() ;
			$('#menu_settings_'+divs[c]).removeClass('menu_traffic_info_focus').addClass('menu_traffic_info') ;
		}

		demo_sound1(0) ;
		$('#div_settings_'+themenu).show() ;
		$('#menu_settings_'+themenu).removeClass('menu_traffic_info').addClass('menu_traffic_info_focus') ;
	}

	function confirm_theme( thetheme, thethumb )
	{
		if ( theme != thetheme )
		{
			$('#theme_'+thetheme).prop('checked', true) ;
			$('#div_theme_thumb').html( "<div style=\"background: url( "+thethumb+" ); background-position: top left; width: 85px; height: 54px; -moz-border-radius: 5px; border-radius: 5px;\">&nbsp;</div>") ;
			$('#div_confirm').show() ;
		}
	}

	function update_theme( thetheme )
	{
		location.href = 'mapp_themes.php?action=update_theme&ses=<?php echo $ses ?>&theme='+thetheme ;
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
			$('#theme_<?php echo $opinfo["theme"] ?>').prop('checked', true) ;
			$('#div_confirm').hide() ;
		}
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

	function update_sound_doit()
	{
		var sound1 = $('#sound1').val() ;
		var sound2 = $('#sound2').val() ;

		location.href = 'mapp_themes.php?ses=<?php echo $ses ?>&action=update_sound&sound1='+sound1+'&sound2='+sound2 ;
	}

	function update_sound( thevalue )
	{
		if ( thevalue != parent.chat_sound )
		{
			parent.chat_sound = thevalue ;
			if ( thevalue ) { $('#r_sound_1').prop('checked', true) ; }
			else { $('#r_sound_0').prop('checked', true) ; }

			do_alert( 1, "Update Success" ) ;
		}
	}
//-->
</script>
</head>
<body style="">

<div id="canned_wrapper" style="display: none; height: 100%; overflow: hidden;">
	<table cellspacing=0 cellpadding=0 border=0 width="100%"><tr><td class="t_tl"></td><td class="t_tm"></td><td class="t_tr"></td></tr>
	<tr>
		<td class="t_ml"></td><td class="t_mm">
			<div id="canned_container" style="padding-bottom: 100px; overflow: auto;">

				<div style="">
					<div id="menu_settings_themes" class="menu_traffic_info_focus" onClick="toggle_menu_info('themes')">Themes</div>
					<div id="menu_settings_sounds" class="menu_traffic_info" onClick="toggle_menu_info('sounds')">Sounds</div>
					<div style="clear: both;"></div>
				</div>

				<div style="margin-top: 25px;">
					<div id="div_settings_themes" style="display: none;">
						<form>
						<table cellspacing=0 cellpadding=2 border=0 width="100%" style="">
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

										$checked = "" ;
										if ( $opinfo["theme"] == $theme )
											$checked = "checked" ;

										$path_thumb = ( is_file( "../themes/$theme/thumb.png" ) ) ? "../themes/$theme/thumb.png" : "../pics/screens/thumb_theme_blank.png" ;

										if ( preg_match( "/[a-z]/i", $theme ) && ( $theme != "initiate" ) )
											print "<div class=\"li_mapp round\" style=\"width: 85px; margin-bottom: 15px;\"><div style=\"background: url( $path_thumb ); background-position: top left; height: 54px; -moz-border-radius: 5px; border-radius: 5px; cursor: pointer;\" onClick=\"confirm_theme('$theme', '$path_thumb')\"><input type=\"radio\" name=\"theme\" id=\"theme_$theme\" value=\"$theme\" $checked> <span class=\"info_mapp_neutral\">$theme</span></div></div>" ;
									}
								?>
								<div style="clear: both;"></div>
							</td>
						</tr>
						</table>
						</form>
					</div>

					<div id="div_settings_sounds" style="display: none;">
						<form>
						New chat request:
						<div>
							<table cellspacing=0 cellpadding=2 border=0 style="">
							<tr>
								<td class="td_dept_td"><div style=""><select name="sound1" id="sound1" style="" onChange="demo_sound1(1)">
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
								<td class="td_dept_td" style="padding-left: 15px;"><div style="cursor: pointer;" onClick="demo_sound1(1)" class="li_mapp round"><img src="../pics/icons/bell_start.png" width="16" height="16" border="0" alt="play sound" title="play sound" id="img_sound1"></div></td>
								<td class="td_dept_td" style="padding-left: 15px;"><div style="cursor: pointer;" onClick="demo_sound1(0)" class="li_mapp round"><img src="../pics/icons/bell_stop.png" width="16" height="16" border="0" alt="stop sound" title="stop sound" id="img_sound1"></div></td>
							</tr>
							</table>
						</div>

						<div style="margin-top: 25px;">New chat response:</div>
						<div>
							<table cellspacing=0 cellpadding=2 border=0 style="">
							<tr>
								<td class="td_dept_td"><div style="padding-top: 5px;"><select name="sound2" id="sound2" style="" onChange="demo_sound2()">
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
								<td class="td_dept_td" style="padding-left: 15px;"><div style="cursor: pointer;" onClick="demo_sound2()" class="li_mapp round"><img src="../pics/icons/bell_start.png" width="16" height="16" border="0" alt="play sound" title="play sound" id="img_sound2"></div></td>
								<td class="td_dept_td">&nbsp;</td>
							</tr>
							</table>
						</div>

						<div style="padding-top: 35px;"><input type="button" value="Update Sound Alerts" onClick="update_sound_doit()"></div>
						</form>


						<div style="margin-top: 35px;">
							<div>
								<div class="info_mapp_good" style="float: left; width: 100px; cursor: pointer;" onClick="update_sound(1)"><input type="radio" name="r_sound" id="r_sound_1" value=1> Sound On</div>
								<div class="info_mapp_error" style="float: left; margin-left: 10px; width: 100px; cursor: pointer;" onClick="update_sound(0)"><input type="radio" name="r_sound" id="r_sound_0" value=0> Sound Off</div>
							</div>
							<div style="clear: both;"></div>
						</div>
					</div>
				</div>

			</div>
		</td><td class="t_mr"></td>
	</tr>
	<tr><td class="t_bl"></td><td class="t_bm"></td><td class="t_br"></td></tr>
	</table>
</div>

<div id="div_confirm" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">
	<div id="div_confirm_body" class="info_info" style="position: relative; width: 350px; margin: 0 auto; top: 100px;">
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
</div>

<div id="sounds" style="display: none; position: absolute; width: 1px; height: 1px; overflow: hidden; opacity:0.0; filter:alpha(opacity=0);">
	<span id="div_sounds_new_request"></span>
	<span id="div_sounds_new_text"></span>
	<audio id='div_sounds_audio_new_request'></audio>
	<audio id='div_sounds_audio_new_text'></audio>
</div>

</body>
</html>
<?php
	if ( isset( $dbh ) && $dbh['con'] )
		database_mysql_close( $dbh ) ;
?>
