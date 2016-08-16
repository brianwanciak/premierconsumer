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

	$error = "" ;

	include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;

	if ( $action == "submit" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/put.php" ) ;

		$marketid = Util_Format_Sanatize( Util_Format_GetVar( "marketid" ), "n" ) ;
		$skey = Util_Format_Sanatize( Util_Format_GetVar( "skey" ), "ln" ) ;
		$name = Util_Format_Sanatize( Util_Format_GetVar( "name" ), "ln" ) ;
		$color = Util_Format_Sanatize( Util_Format_GetVar( "color" ), "ln" ) ;

		if ( !$skey )
			$skey = Util_Format_RandomString(3);

		if ( !Marketing_put_Marketing( $dbh, $marketid, $skey, $name, $color ) )
			$error = "$name is already in use." ;
	}
	else if ( $action == "delete" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Marketing/remove.php" ) ;

		$marketid = Util_Format_Sanatize( Util_Format_GetVar( "marketid" ), "n" ) ;
		Marketing_remove_Marketing( $dbh, $marketid ) ;
	}
	$marketings = Marketing_get_AllMarketing( $dbh ) ;
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
		toggle_menu_setup( "extras" ) ;
		show_div( "marketing" ) ;
	});

	function tcolor_focus( thediv )
	{
		$( '#theform' ).find('*').each( function(){
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
		if ( confirm( "Delete this campaign?" ) )
			location.href = "marketing_click.php?ses=<?php echo $ses ?>&action=delete&marketid="+themarketid ;
	}

	function do_submit()
	{
		var name = $( "#name" ).val() ;
		var color = $( "#color" ).val() ;

		if ( name == "" )
			do_alert( 0, "Please provide the Campaign Name." ) ;
		else if ( color == "" )
			do_alert( 0, "Please select the Indication Color." ) ;
		else
			$('#theform').submit() ;
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<?php include_once( "./inc_menu.php" ) ; ?>

		<div style="margin-top: 25px;">
			<div class="op_submenu2" onClick="location.href='marketing.php?ses=<?php echo $ses ?>'">Social Media</div>
			<div class="op_submenu2" onClick="location.href='marketing_marquee.php?ses=<?php echo $ses ?>'">Chat Footer Marquee</div>
			<?php if ( is_file( "../addons/announceit/announceit.php" ) ): ?><div class="op_submenu2" onClick="location.href='../addons/announceit/announceit.php?ses=<?php echo $ses ?>'">Announce It</div><?php endif ?>
			<div class="op_submenu_focus">Campaign Tracking</div>
			<div class="op_submenu2" onClick="location.href='reports_marketing.php?ses=<?php echo $ses ?>'">Report: Campaign Clicks</div>
			<!-- <div class="op_submenu" onClick="location.href='marketing_ga.php?ses=<?php echo $ses ?>'">Google Analytics</div> -->
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			Campaign Tracking will track your marketing campaign click-through rates.  Simply append the auto generated query key to your campaign URL.  Visitors arriving from the campaign URL will be visibly noted with a color indicator on the operator traffic monitor.  <b>IMPORTANT:</b> The landing page must have the chat icon <a href="code.php?ses=<?php echo $ses ?>">HTML Code</a> to capture the query key for tracking and reporting.
		</div>

		<div style="margin-top: 25px;">
			<form>
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td width="200"><div class="td_dept_header">Name</div></td>
				<td><div class="td_dept_header">Query key to append to URL</div></td>
			</tr>
			<?php
				for ( $c = 0; $c < count( $marketings ); ++$c )
				{
					$marketing = $marketings[$c] ;
					$td1 = "td_dept_td" ;

					$edit_delete = "<span onClick=\"do_edit( $marketing[marketID], '$marketing[skey]', '$marketing[name]', '$marketing[color]' );\" style=\"cursor: pointer;\"><img src=\"../pics/btn_edit.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></span> &nbsp; <span onClick=\"do_delete($marketing[marketID])\" style=\"cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></span>" ;

					print "
						<tr>
							<td class=\"$td1\" nowrap style=\"background: #$marketing[color];\">
								<div style=\"font-weight: bold; text-shadow: none;\">$marketing[name]</div>
								<div style=\"margin-top: 5px;\">$edit_delete</div>
							</td>
							<td class=\"$td1\">
								<input type=\"text\" style=\"background: transparent; border: 1px solid transparent; font-weight: bold; color: #6E6E6E; width: 100%;\" size=\"80\" value=\"&plk=pi-$marketing[marketID]-$marketing[skey]-m\" readonly>
								<div class=\"info_box\">example: http://www.verypeachy.com/?<span class=\"txt_blue\">&plk=pi-$marketing[marketID]-$marketing[skey]-m</span></div>
							</td>
						</tr>
					" ;
				}
				if ( $c == 0 )
					print "<tr><td colspan=7 class=\"td_dept_td\">Blank results.</td></tr>" ;
			?>
			</table>
			</form>
		</div>

		<div class="edit_wrapper" style="padding: 5px; margin-top: 55px;">
			<a name="a_edit"></a><div class="edit_title">Create/Edit Marketing Click Tracking <span class="txt_red"><?php echo $error ?></span></div>
			<div style="margin-top: 10px;">
				<form method="POST" action="marketing_click.php?submit" id="theform">
				<input type="hidden" name="action" value="submit">
				<input type="hidden" name="ses" value="<?php echo $ses ?>">
				<input type="hidden" name="marketid" id="marketid" value="0">
				<input type="hidden" name="skey" id="skey" value="">
				<input type="hidden" name="color" id="color" value="">
				<table cellspacing=0 cellpadding=5 border=0>
				<tr>
					<td>Campaign Name (example: "Google PPC")<br><input type="text" name="name" id="name" size="50" maxlength="40" value="" onKeyPress="return nospecials(event)"></td>
				</tr>
				<tr>
					<td>Select Indication Color (indicator color on the traffic monitor and operator chat)<br>
						<div id="tcolor_li_DDFFEE" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #DDFFEE;" OnClick="tcolor_focus( 'DDFFEE' )"></div>
						<div id="tcolor_li_FFE07B" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #FFE07B;" OnClick="tcolor_focus( 'FFE07B' )"></div>
						<div id="tcolor_li_A4C3E3" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #A4C3E3;" OnClick="tcolor_focus( 'A4C3E3' )"></div>
						<div id="tcolor_li_FADADB" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #FADADB;" OnClick="tcolor_focus( 'FADADB' )"></div>
						<div id="tcolor_li_FABEFF" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #FABEFF;" OnClick="tcolor_focus( 'FABEFF' )"></div>
						<div id="tcolor_li_ABE3FA" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #ABE3FA;" OnClick="tcolor_focus( 'ABE3FA' )"></div>
						<div id="tcolor_li_F9FABE" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #F9FABE;" OnClick="tcolor_focus( 'F9FABE' )"></div>
						<div id="tcolor_li_BDBEF9" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #BDBEF9;" OnClick="tcolor_focus( 'BDBEF9' )"></div>
						<div id="tcolor_li_DAB195" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #DAB195;" OnClick="tcolor_focus( 'DAB195' )"></div>
						<div id="tcolor_li_C1ADD0" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #C1ADD0;" OnClick="tcolor_focus( 'C1ADD0' )"></div>
						<div id="tcolor_li_B7E3A3" style="float: left; cursor: pointer; width: 15px; height: 15px; margin-right: 3px; border: 1px solid #C2C2C2; background: #B7E3A3;" OnClick="tcolor_focus( 'B7E3A3' )"></div>

						<div style="clear: both"></div>
					</td>
				</tr>
				<tr>
					<td> <div style="padding-top: 15px;"><input type="button" value="Submit" onClick="do_submit()" class="btn"> &nbsp; &nbsp; <input type="reset" value="Reset" onClick="$( 'input#marketid' ).val(0)" class="btn"></div></td>
				</tr>
				<tr>
					<td><div style="padding-top: 10px;">* after making changes, if an operator console is open, they will need to log out and log back in for the update to take effect on the traffic monitor</div></td>
				</tr>
				</table>
				</form>
			</div>
		</div>
<?php include_once( "./inc_footer.php" ) ?>

