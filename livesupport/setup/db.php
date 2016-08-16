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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_DB.php" ) ;

	$tables = Util_DB_GetTableNames( $dbh ) ;
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
	});
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
				<div style="margin-bottom: 25px;"><img src="../pics/icons/arrow_left.png" width="16" height="15" border="0" alt=""> <a href="system.php?ses=<?php echo $ses ?>">back</a></div>
				<table cellspacing=0 cellpadding=0 border=0>
				<tr>
					<td width="120"><div class="td_dept_header">MySQL Table</div></td>
					<td width="100"><div class="td_dept_header">Rows</div></td>
					<td><div class="td_dept_header">Status</div></td>
				</tr>
				<?php
					$approx_disc_use = 0 ;
					for( $c = 0; $c < count( $tables ); ++$c )
					{
						$analyze = Util_DB_AnalyzeTable( $dbh, $tables[$c] ) ;
						$stats = Util_DB_TableStats( $dbh, $tables[$c] ) ;

						$name = $stats["Name"] ;
						$type = $analyze["Msg_type"] ;
						$status = $analyze["Msg_text"] ;

						if ( preg_match( "/^p_/", $name ) )
						{
							if ( $status = "Table is already up to date" )
								$status = "OK" ;

							$rows = $stats["Rows"] ;
							$ave_row_size = $stats["Avg_row_length"] ;
							$ave_disk = $ave_row_size * $rows ;
							$ave_size = Util_Functions_Bytes( $ave_disk ) ;
							
							$approx_disc_use += $ave_disk ;

							print "<tr><td class=\"td_dept_td_td\">$name</td><td class=\"td_dept_td_td\">$rows</td><td class=\"td_dept_td_td\">$status</td></tr>" ;
						}
					}

					$approx_disc_use = Util_Functions_Bytes( $approx_disc_use ) ;
				?>
				<tr>
					<td colspan=3><div class="info_info">Disk Space Usage: <span style="font-weight: bold;" class="info_neutral">~<?php echo $approx_disc_use ?></span></div></td>
				</tr>
				</table>
			</div>

<?php include_once( "./inc_footer.php" ) ?>