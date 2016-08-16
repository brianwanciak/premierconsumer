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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Vars/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$error = "" ;
	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;

	if ( $action == "submit" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Vars/put.php" ) ;

		$sm_fb_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_fb_tip" ), "ln" ) ;
		$sm_tw_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_tw_tip" ), "ln" ) ;
		$sm_yt_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_yt_tip" ), "ln" ) ;
		$sm_li_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_li_tip" ), "ln" ) ;
		$sm_gl_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_gl_tip" ), "ln" ) ;
		$sm_in_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_in_tip" ), "ln" ) ;
		$sm_pi_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_pi_tip" ), "ln" ) ;
		$sm_tu_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_tu_tip" ), "ln" ) ;
		$sm_rss_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_rss_tip" ), "ln" ) ;
		$sm_ot_tip = Util_Format_Sanatize( Util_Format_GetVar( "sm_ot_tip" ), "ln" ) ;

		$sm_fb_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_fb_url" ), "base_url" ) ;
		$sm_tw_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_tw_url" ), "base_url" ) ;
		$sm_yt_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_yt_url" ), "base_url" ) ;
		$sm_li_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_li_url" ), "base_url" ) ;
		$sm_gl_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_gl_url" ), "base_url" ) ;
		$sm_in_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_in_url" ), "base_url" ) ;
		$sm_pi_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_pi_url" ), "base_url" ) ;
		$sm_tu_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_tu_url" ), "base_url" ) ;
		$sm_rss_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_rss_url" ), "base_url" ) ;
		$sm_ot_url = Util_Format_Sanatize( Util_Format_GetVar( "sm_ot_url" ), "base_url" ) ;

		Vars_put_Social( $dbh, $deptid, "facebook", 1, $sm_fb_tip, $sm_fb_url ) ;
		Vars_put_Social( $dbh, $deptid, "twitter", 1, $sm_tw_tip, $sm_tw_url ) ;
		Vars_put_Social( $dbh, $deptid, "youtube", 1, $sm_yt_tip, $sm_yt_url ) ;
		Vars_put_Social( $dbh, $deptid, "linkedin", 1, $sm_li_tip, $sm_li_url ) ;
		Vars_put_Social( $dbh, $deptid, "gplus", 1, $sm_gl_tip, $sm_gl_url ) ;
		Vars_put_Social( $dbh, $deptid, "instagram", 1, $sm_in_tip, $sm_in_url ) ;
		Vars_put_Social( $dbh, $deptid, "pinterest", 1, $sm_pi_tip, $sm_pi_url ) ;
		Vars_put_Social( $dbh, $deptid, "tumblr", 1, $sm_tu_tip, $sm_tu_url ) ;
		Vars_put_Social( $dbh, $deptid, "rss", 1, $sm_rss_tip, $sm_rss_url ) ;
		Vars_put_Social( $dbh, $deptid, "other", 1, $sm_ot_tip, $sm_ot_url ) ;
	}
	else if ( $action == "reset" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Vars/remove.php" ) ;

		Vars_remove_DeptSocials( $dbh, $deptid ) ;
	}

	$socials = Vars_get_Socials( $dbh, $deptid ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	if ( $deptid )
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
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
	var sms = Array( "fb", "tw", "yt", "li", "gl", "in", "pi", "tu", "rss", "ot" ) ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "extras" ) ;
		show_div( "marketing" ) ;

		<?php if ( $action && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
		<?php if ( ( $action == "reset" ) && !$error ): ?>do_alert( 1, "Reset Success" ) ;<?php endif ; ?>
	});

	function tcolor_focus( thediv )
	{
		$( '*', 'body' ).each( function(){
			var div_name = this.id ;
			if ( div_name.indexOf( "tcolor_li_" ) != -1 )
				$(this).css( { "border": "1px solid #C2C2C2" } ) ;
		} );

		if ( thediv != undefined )
		{
			$( "#color" ).val( thediv ) ;
			$( "#tcolor_li_"+thediv ).css( { "border": "1px solid #444444" } ) ;
		}
	}

	function do_edit( themarketid, theskey, thename, thecolor )
	{
		$( "input#marketid" ).val( themarketid ) ;
		$( "input#skey" ).val( theskey ) ;
		$( "input#name" ).val( thename ) ;
		tcolor_focus( thecolor ) ;
		location.href = "#a_edit" ;
	}

	function do_delete( themarketid )
	{
		if ( confirm( "Delete this campaign?  All data will be lost." ) )
			location.href = "marketing.php?ses=<?php echo $ses ?>&action=delete&marketid="+themarketid ;
	}

	function do_submit()
	{
		var execute = 1 ;
		for ( var c = 0; c < sms.length; ++c )
		{
			if ( !check_sm_vals( sms[c] ) )
			{
				execute = 0 ;
				break ;
			}
		}

		if ( execute )
			$('#theform').submit() ;
	}

	function check_sm_vals( thesm )
	{
		var enabled = $('#sm_'+thesm).is(':checked') ;
		var tooltip = $('#sm_'+thesm+'_tip').val() ;
		var url = $('#sm_'+thesm+'_url').val() ;
		var url_ok = ( url && url.match( /(http:\/\/)|(https:\/\/)/i ) ) ? 1 : 0 ;

		if ( enabled )
		{
			if ( !tooltip || !url )
			{
				$('#sm_'+thesm).prop('checked', false) ;
				do_alert( 0, "Please provide the Tooltip and the Social Media URL." ) ;
				return false ;
			}
			else if ( !url_ok )
			{
				$('#sm_'+thesm).prop('checked', false) ;
				do_alert( 0, "URL should begin with http:// or https:// protocol." ) ;
				return false ;
			}
		}
		
		return true ;
	}

	function launch_sm( thesm )
	{
		var unique = unixtime() ;
		var url = $('#sm_'+thesm+'_url').val() ;
		var url_ok = ( url && url.match( /(http:\/\/)|(https:\/\/)/i ) ) ? 1 : 0 ;

		if ( !url )
			do_alert( 0, "Please provide the Social Media URL." ) ;
		else if ( !url_ok )
			do_alert( 0, "URL should begin with http:// or https:// protocol." ) ;
		else
			window.open(url, unique, 'scrollbars=yes,menubar=yes,resizable=1,location=yes,toolbar=yes,status=1') ;
	}

	function switch_dept( theobject )
	{
		location.href = "marketing.php?ses=<?php echo $ses ?>&deptid="+theobject.value+"&"+unixtime() ;
	}

	function reset_sm( thedeptid )
	{
		if ( confirm( "Clear current links and use Global Default?" ) )
			location.href = "./marketing.php?ses=<?php echo $ses ?>&action=reset&deptid="+thedeptid ;
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<?php include_once( "./inc_menu.php" ) ; ?>

		<div style="margin-top: 25px;">
			<div class="op_submenu_focus">Social Media</div>
			<div class="op_submenu2" onClick="location.href='marketing_marquee.php?ses=<?php echo $ses ?>'">Chat Footer Marquee</div>
			<?php if ( is_file( "../addons/announceit/announceit.php" ) ): ?><div class="op_submenu2" onClick="location.href='../addons/announceit/announceit.php?ses=<?php echo $ses ?>'">Announce It</div><?php endif ?>
			<div class="op_submenu2" onClick="location.href='marketing_click.php?ses=<?php echo $ses ?>'">Campaign Tracking</div>
			<div class="op_submenu2" onClick="location.href='reports_marketing.php?ses=<?php echo $ses ?>'">Report: Campaign Clicks</div>
			<!-- <div class="op_submenu" onClick="location.href='marketing_ga.php?ses=<?php echo $ses ?>'">Google Analytics</div> -->
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			The social media icons will be visible on the visitor chat window <b>during a chat session</b>.  Provide both the "Tooltip" and the "URL" to enable.

			<form method="POST" action="marketing.php?submit" id="form_theform">
			<select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000; margin-top: 15px;" OnChange="switch_dept( this )">
			<option value="0">All Departments</option>
			<?php
				for ( $c = 0; $c < count( $departments ); ++$c )
				{
					$department = $departments[$c] ;

					if ( $department["name"] != "Archive" )
					{
						$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
						print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
					}
				}
			?>
			</select>
			</form>
		</div>

		<div style="margin-top: 25px;">
			<form action="marketing.php?submit" method="POST" id="theform">
			<input type="hidden" name="action" value="submit">
			<input type="hidden" name="ses" value="<?php echo $ses ?>">
			<input type="hidden" name="deptid" value="<?php echo $deptid ?>">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td width="100"><div class="td_dept_header">&nbsp;</div></td>
				<td><div class="td_dept_header">Tooltip (example: Follow us on Twitter!)</div></td>
				<td><div class="td_dept_header">Social Media URL</div></td>
			</tr>
			<tr>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/facebook.png" width="16" height="16" border="0" alt=""> Facebook</td>
				<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="100" name="sm_fb_tip" id="sm_fb_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["facebook"]["tooltip"] ) && $socials["facebook"]["tooltip"] ) ? $socials["facebook"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="45" maxlength="255" name="sm_fb_url" id="sm_fb_url" value="<?php echo ( isset( $socials["facebook"]["url"] ) && $socials["facebook"]["url"] ) ? $socials["facebook"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('fb')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/twitter.png" width="16" height="16" border="0" alt=""> Twitter</td>
				<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="100" name="sm_tw_tip" id="sm_tw_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["twitter"]["tooltip"] ) && $socials["twitter"]["tooltip"] ) ? $socials["twitter"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="45" maxlength="255" name="sm_tw_url" id="sm_tw_url" value="<?php echo ( isset( $socials["twitter"]["url"] ) && $socials["twitter"]["url"] ) ? $socials["twitter"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('tw')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/youtube.png" width="16" height="16" border="0" alt=""> YouTube</td>
				<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="100" name="sm_yt_tip" id="sm_yt_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["youtube"]["tooltip"] ) && $socials["youtube"]["tooltip"] ) ? $socials["youtube"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="45" maxlength="255" name="sm_yt_url" id="sm_yt_url" value="<?php echo ( isset( $socials["youtube"]["url"] ) && $socials["youtube"]["url"] ) ? $socials["youtube"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('yt')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/linkedin.png" width="16" height="16" border="0" alt=""> LinkedIn</td>
				<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="100" name="sm_li_tip" id="sm_li_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["linkedin"]["tooltip"] ) && $socials["linkedin"]["tooltip"] ) ? $socials["linkedin"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="45" maxlength="255" name="sm_li_url" id="sm_li_url" value="<?php echo ( isset( $socials["linkedin"]["url"] ) && $socials["linkedin"]["url"] ) ? $socials["linkedin"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('li')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/gplus.png" width="16" height="16" border="0" alt=""> Google Plus</td>
				<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="100" name="sm_gl_tip" id="sm_gl_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["gplus"]["tooltip"] ) && $socials["gplus"]["tooltip"] ) ? $socials["gplus"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="45" maxlength="255" name="sm_gl_url" id="sm_gl_url" value="<?php echo ( isset( $socials["gplus"]["url"] ) && $socials["gplus"]["url"] ) ? $socials["gplus"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('gl')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/instagram.png" width="16" height="16" border="0" alt=""> Instagram</td>
				<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="100" name="sm_in_tip" id="sm_in_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["instagram"]["tooltip"] ) && $socials["instagram"]["tooltip"] ) ? $socials["instagram"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="45" maxlength="255" name="sm_in_url" id="sm_in_url" value="<?php echo ( isset( $socials["instagram"]["url"] ) && $socials["instagram"]["url"] ) ? $socials["instagram"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('in')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/pinterest.png" width="16" height="16" border="0" alt=""> Pinterest</td>
				<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="100" name="sm_pi_tip" id="sm_pi_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["pinterest"]["tooltip"] ) && $socials["pinterest"]["tooltip"] ) ? $socials["pinterest"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="45" maxlength="255" name="sm_pi_url" id="sm_pi_url" value="<?php echo ( isset( $socials["pinterest"]["url"] ) && $socials["pinterest"]["url"] ) ? $socials["pinterest"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('pi')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/tumblr.png" width="16" height="16" border="0" alt=""> Tumblr</td>
				<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="100" name="sm_tu_tip" id="sm_tu_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["tumblr"]["tooltip"] ) && $socials["tumblr"]["tooltip"] ) ? $socials["tumblr"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="45" maxlength="255" name="sm_tu_url" id="sm_tu_url" value="<?php echo ( isset( $socials["tumblr"]["url"] ) && $socials["tumblr"]["url"] ) ? $socials["tumblr"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('tu')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/rss.png" width="16" height="16" border="0" alt=""> RSS</td>
				<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="100" name="sm_rss_tip" id="sm_rss_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["rss"]["tooltip"] ) && $socials["rss"]["tooltip"] ) ? $socials["rss"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="45" maxlength="255" name="sm_rss_url" id="sm_rss_url" value="<?php echo ( isset( $socials["rss"]["url"] ) && $socials["rss"]["url"] ) ? $socials["rss"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('rss')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" nowrap><img src="../pics/icons/social/other.png" width="16" height="16" border="0" alt=""> Other</td>
				<td class="td_dept_td"><input type="text" class="input" size="35" maxlength="100" name="sm_ot_tip" id="sm_ot_tip" onKeyPress="return noquotes(event)" value="<?php echo ( isset( $socials["other"]["tooltip"] ) && $socials["other"]["tooltip"] ) ? $socials["other"]["tooltip"] : "" ; ?>"></td>
				<td class="td_dept_td"><input type="text" class="input" size="45" maxlength="255" name="sm_ot_url" id="sm_ot_url" value="<?php echo ( isset( $socials["other"]["url"] ) && $socials["other"]["url"] ) ? $socials["other"]["url"] : "" ; ?>"> &nbsp; <span style="">&middot; <a href="JavaScript:void(0)" onClick="launch_sm('ot')">visit</a></span></td>
			</tr>
			<tr>
				<td class="td_dept_td" colspan="3">
					<input type="button" value="Update" onClick="do_submit()" class="btn"> &nbsp; 
					<?php
						if ( !count( $socials ) && $deptid ):
							print " &bull; currently using <a href=\"marketing.php?ses=$ses\">Global Default</a>" ;
						elseif ( count( $socials ) && $deptid ):
							print " &bull; reset to use <a href=\"JavaScript:void(0)\" onClick=\"reset_sm( $deptid )\">Global Default</a>" ;
						endif ;
					?>
				</td>
			</tr>
			</table>
			</form>
		</div>

<?php include_once( "./inc_footer.php" ) ?>
