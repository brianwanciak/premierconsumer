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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/update_itr.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "n" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$wp = Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) ;
	$auto = Util_Format_Sanatize( Util_Format_GetVar( "auto" ), "n" ) ;
	$m = Util_Format_Sanatize( Util_Format_GetVar( "m" ), "n" ) ;
	$d = Util_Format_Sanatize( Util_Format_GetVar( "d" ), "n" ) ;
	$y = Util_Format_Sanatize( Util_Format_GetVar( "y" ), "n" ) ;

	if ( !$m )
		$m = date( "m", time() ) ;
	if ( !$d )
		$d = date( "j", time() ) ;
	if ( !$y )
		$y = date( "Y", time() ) ;

	$stat_end = mktime( 0, 0, 1, $m+1, 0, $y ) ;
	$stat_end_day = date( "j", $stat_end ) ;

	$menu = "activity" ;
	$error = "" ;

	Ops_update_itr_IdleOps( $dbh ) ;
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Operator </title>

<meta name="description" content="v.<?php echo $VERSION ?>">
<meta name="keywords" content="<?php echo md5( $KEY ) ?>">
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
	var menu ;
	var global_index ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu_op() ;
		toggle_menu_op( "<?php echo $menu ?>", '<?php echo $ses ?>' ) ;

		if ( typeof( parent.isop ) != "undefined" ) { parent.init_extra_loaded() ; }
	});

	function toggle_stats( theindex )
	{
		$('#reports').find('*').each( function(){
			var div_name = this.id ;
			if ( div_name.indexOf("div_sub_") != -1 )
				$(this).removeClass('info_box').addClass('info_clear') ;
		} );

		if ( global_index != theindex )
		{
			global_index = theindex ;
			$('#div_info').show() ;
			$('#div_sub_'+theindex).removeClass('info_clear').addClass('info_box') ;
			$('#div_clone').html( $('#stat_'+theindex).html() ) ;
		}
		else
		{
			global_index = undeefined ;
			$('#div_info').hide() ;
			$('#div_clone').empty() ;
		}
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ; ?>

		<div id="op_title" class="edit_title" style="margin-bottom: 15px;"></div>
		<div id="op_activity" style="display: none; margin: 0 auto;">
			<img src="../pics/icons/calendar.png" width="16" height="16" border="0" alt=""> Days with online activities are displayed.

			<div id="reports" style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td width="50%" valign="top">
						<div style="margin-bottom: 25px;">
							<?php
								$y_start = date( "Y", time() - ( (60*60*24*365)*5 ) ) ;
								include_once( "./inc_select_cal.php" ) ;
							?>
						</div>
						<?php
							$overall_duration = null ;
							for ( $c = 1; $c <= $stat_end_day; ++$c )
							{
								$stat_day = mktime( 0, 0, 0, $m, $c, $y ) ;
								$stat_day_expand = date( "D M j", mktime( 0, 0, 0, $m, $c, $y ) ) ;

								$stat_start = mktime( 0, 0, 0, $m, $c, $y ) ;
								$stat_end = mktime( 23, 60, 60, $m, $c, $y ) ;

								$reports = Chat_ext_get_OpStatusLog( $dbh, $opinfo["opID"], $stat_start, $stat_end ) ;

								$output = "" ;
								if ( count( $reports ) > 0 )
								{
									$prev_created = $total_duration = 0 ;
									for( $c2 = 0; $c2 < count( $reports ) ; ++$c2 )
									{
										$report = $reports[$c2] ;

										$diff = 0 ;
										$created = $report["created"] ;
										$created_expand = date( "g:i a", $created ) ;
										$status_text = ( $report["status"] ) ? "Online" : "Offline" ;

										if ( $report["status"] && !$prev_created )
											$prev_created = $created ;
										else if ( !$report["status"] && !$prev_created && !$c2 && $overall_duration )
										{
											// is online from previous day
											$created_ = mktime( 0, 0, 0, date( "m", $created ), date( "j", $created ), date( "Y", $created ) ) ;
											$created_expand_ = date( "g:i a", $created_ ) ;
											$total_duration += $created - $created_ ;

											$output .= "<div class=\"td_dept_td\"><img src=\"../pics/icons/online_green.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"\"> <span style=\"padding-left: 25px;\">$created_expand_</span></div>" ;
										}
										else if ( !$report["status"] && $prev_created )
										{
											$diff = $report["created"] - $prev_created ;
											$total_duration += $diff ;
											$prev_created = 0 ;
										}

										$class = "td_dept_td" ;
										$style = ( $report["status"] ) ? " background: #AFFF9F; " : "" ;
										$status = ( $report["status"] ) ? "<img src=\"../pics/icons/online_green.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"\">" : "<img src=\"../pics/icons/online_grey.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"\">" ;

										$output .= "<div class=\"$class\">$status <span style=\"padding-left: 25px; text-shadow: none;$style\" class=\"info_clear\">$created_expand - $status_text</span></div>" ;
									}

									if ( $prev_created )
									{
										$now = time() ;
										$diff = $now - $prev_created ;

										if ( date( "j", ( $diff + $now ) ) != date( "j", $now ) )
										{
											// when the online time goes past the end of the day
											$total_duration += mktime( 0, 0, 0, $m,date( "j", $prev_created )+1, $y ) - $prev_created ;
										}
										else
											$total_duration += $diff ;
									}

									if ( $total_duration && ( $total_duration < 60 ) )
										$total_duration = 60 ;

									$overall_duration += $total_duration ;
									$duration = Util_Format_Duration( $total_duration ) ;

									print "<div id=\"div_header_$c\" style=\"float: left; width: 45%; cursor: pointer; margin-right: 5px; margin-bottom: 5px;\" class=\"info_neutral\" onClick=\"toggle_stats($c)\"><div id=\"div_sub_$c\" class=\"info_clear\" style=\"text-shadow: none;\"><table cellspacing=0 cellpadding=0 border=0 width=\"100%\"><tr><td nowrap><b>$stat_day_expand</b></td><td style=\"font-weight: normal; padding-left: 10px;\" width=\"100%\"><img src=\"../pics/icons/online_green.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"\"> $duration</td></tr></table></div><div id=\"stat_$c\" style=\"display: none;\">$output</div></div>" ;
								}
							}
							if ( !is_null( $overall_duration ) )
								print "<div style=\"clear: both;\"></div>" ;
							else
								print "<div style=\"\">Blank results.</div>" ;

							$duration = Util_Format_Duration( $overall_duration ) ;
							$month = date( "F", $stat_start ) ;
							$year = date( "Y", $stat_start ) ;
							print "<div style=\"margin-top: 15px;\"><table cellspacing=0 cellpadding=0 border=0><tr><td nowrap><div style=\"\">Total duration <img src=\"../pics/icons/online_green.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"\"> </div></td><td style=\"font-weight: normal; padding-left: 15px;\"><span class=\"info_good\">$duration</span></td></tr></table></div>" ;
						?>
					</td>
					<td width="50%" valign="top">
						<div style="display: none;" id="div_info"><b>Note:</b> When an operator setting is updated (themes, sounds, etc) on the operator console, the console window will need to refresh to process the changes.  The refreshing of the window will trigger an online status, causing a possible <img src="../pics/icons/online_green.png" width="12" height="12" border="0" alt=""> online status right after another <img src="../pics/icons/online_green.png" width="12" height="12" border="0" alt=""> online status.</div>
						<div id="div_clone" style="max-height: 350px; overflow: auto;"></div>
					</td>
				</tr>
				</table>
			</div>
		</div>

<?php include_once( "./inc_footer.php" ) ; ?>
