<?php
	/* (c) OSI Codes Inc. */
	/* http://www.osicodesinc.com */
	/****************************************/
	// STANDARD header for Setup
	if ( !is_file( "../../web/config.php" ) ){ HEADER("location: install.php") ; exit ; }
	include_once( "../../web/config.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Format.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Error.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/".Util_Format_Sanatize($CONF["SQLTYPE"], "ln") ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Security.php" ) ;
	$ses = Util_Format_Sanatize( Util_Format_GetVar( "ses" ), "ln" ) ;
	if ( !$admininfo = Util_Security_AuthSetup( $dbh, $ses ) ){ ErrorHandler( 608, "Invalid setup session or session has expired.", $PHPLIVE_FULLURL, 0, Array() ) ; }
	// STANDARD header end
	/****************************************/

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Vals.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/External/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

	$EMO_VERSION = "1.0" ;
	if ( is_file( "$CONF[DOCUMENT_ROOT]/addons/emoticons/VERSION.php" ) )
		include_once( "$CONF[DOCUMENT_ROOT]/addons/emoticons/VERSION.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;

	if ( $action == "update_dept_emo" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;

		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
		$value = Util_Format_Sanatize( Util_Format_GetVar( "value" ), "ln" ) ;

		$departments = Depts_get_AllDepts( $dbh ) ;
		$dept_emo = ( isset( $VALS["EMOS"] ) && $VALS["EMOS"] ) ? unserialize( $VALS["EMOS"] ) : Array() ;
		if ( $value == "on" )
		{
			if ( !$deptid )
			{
				for ( $c = 0; $c < count( $departments ); ++$c )
				{
					$department = $departments[$c] ;
					$deptid = $department["deptID"] ;
					$dept_emo[$deptid] = 1 ;
				}
			}
			else { $dept_emo[$deptid] = 1 ; }
			Util_Vals_WriteToFile( "EMOS", serialize( $dept_emo ) ) ;
		}
		else if ( $value == "off" )
		{
			if ( !$deptid )
			{
				for ( $c = 0; $c < count( $departments ); ++$c )
				{
					$department = $departments[$c] ;
					$deptid = $department["deptID"] ;
					$dept_emo[$deptid] = 0 ;
				}
			}
			else { $dept_emo[$deptid] = 0 ; }
			Util_Vals_WriteToFile( "EMOS", serialize( $dept_emo ) ) ;
		}
		$json_data = "json_data = { \"status\": 1, \"error\": \"\" };" ;

		if ( isset( $dbh ) && isset( $dbh['con'] ) )
		database_mysql_close( $dbh ) ;

		$json_data = preg_replace( "/\r\n/", "", $json_data ) ;
		$json_data = preg_replace( "/\t/", "", $json_data ) ;
		print "$json_data" ;
		exit ;
	}

	$dept_emo = ( isset( $VALS["EMOS"] ) ) ? unserialize( $VALS["EMOS"] ) : Array() ; $addon_emo = 1 ;
	$dept_emos = "" ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$deptid = $department["deptID"] ;
		if ( isset( $dept_emo[$deptid] ) )
			$dept_emos .= "dept_emos[$deptid] = '$dept_emo[$deptid]' ;" ;
		else
			$dept_emos .= "dept_emos[$deptid] = 'on' ;" ;
	}
?>
<?php include_once( "../../inc_doctype.php" ) ?>
<head>
<title> PHP Live! Support <?php echo $VERSION ?> </title>

<meta name="description" content="PHP Live! Support <?php echo $VERSION ?>">
<meta name="keywords" content="powered by: PHP Live!  www.phplivesupport.com">
<meta name="robots" content="all,index,follow">
<meta http-equiv="content-type" content="text/html; CHARSET=utf-8">
<?php include_once( "../../inc_meta_dev.php" ) ; ?>

<link rel="Stylesheet" href="../../css/setup.css?<?php echo $VERSION ?>">
<script type="text/javascript" src="../../js/global.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../../js/framework_cnt.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var dept_emos = new Object ;
	<?php echo $dept_emos ?>

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "extras" ) ;

		show_div( "emoticons" ) ;

		<?php if ( ( $action == "submit" ) && !$error ): ?>do_alert( 1, "Success" ) ;<?php endif ; ?>
	});

	function confirm_emo_onoff( thedeptid, thevalue )
	{
		var string_onoff = thedeptid+","+thevalue ;

		update_dept_emo( thedeptid, thevalue ) ;
		if ( thedeptid )
			dept_emos[thedeptid] = thevalue ;
		else
			$('#dept_emo_'+thedeptid+"_"+thevalue).prop('checked', false) ;
	}

	function update_dept_emo( thedeptid, thevalue )
	{
		var json_data = new Object ;

		$.ajax({
			type: "POST",
			url: "./emo.php",
			data: "ses=<?php echo $ses ?>&action=update_dept_emo&deptid="+thedeptid+"&value="+thevalue+"&"+unixtime(),
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					if ( !thedeptid )
					{
						if ( thevalue == "on" ) {
						<?php
							for ( $c = 0; $c < count( $departments ); ++$c )
							{
								$department = $departments[$c] ;
								print "\$('#dept_emo_$department[deptID]_on').prop('checked', true) ; " ;
							}
						?> }
						else {
						<?php
							for ( $c = 0; $c < count( $departments ); ++$c )
							{
								$department = $departments[$c] ;
								print "\$('#dept_emo_$department[deptID]_off').prop('checked', true) ; " ;
							}
						?> }
					}
					do_alert( 1, "Success" ) ;
				}
				else
					do_alert( 0, "Error [emo]. Please reload the page and try again.") ;
			}
		});
	}
//-->
</script>
<?php include_once( "../../setup/inc_header.php" ) ?>

		<?php include_once( "../../setup/inc_menu.php" ) ; ?>

		<div style="margin-top: 25px;">

			<div style="margin-top: 15px;">Set the emoticons feature <img src="smile.png" width="20" height="20" border="0" alt=""> for each department.  Emoticons will be visible during an active chat session for both the visitor and the operator.</div>

			<?php if ( count( $departments ) > 1 ): ?>
			<div style="margin-top: 15px;" class="info_info">
				<div class="info_good" style="float: left; width: 260px; padding: 3px; cursor: pointer;" onclick="$('#dept_emo_0_on').prop('checked', true);confirm_emo_onoff(0, 'on');"><input type="radio" name="dept_emo_0" id="dept_emo_0_on" value="on"> Enable Emoticons for ALL Departments</div>
				<div class="info_error" style="float: left; margin-left: 10px; width: 260px; padding: 3px; cursor: pointer;" onclick="$('#dept_emo_0_off').prop('checked', true);confirm_emo_onoff(0, 'off');"><input type="radio" name="dept_emo_0" id="dept_emo_0_off" value="off"> Disable Emoticons for ALL Departments</div>
				<div style="clear: both;"></div>
			</div>
			<?php endif ; ?>

			<?php if ( !count( $departments ) ): ?>
			<div style="margin-top: 15px;"><span class="info_error"><img src="../../pics/icons/warning.png" width="12" height="12" border="0" alt=""> A <a href="../../setup/depts.php?ses=<?php echo $ses ?>" style="color: #FFFFFF;">Department</a> must be created to continue.</span></div>
			<?php else: ?>
			<div style="margin-top: 5px;">
				<div class="edit_title td_dept_td">Departments</div>
				<table cellspacing=0 cellpadding=0 border=0>
				<?php
					for ( $c = 0; $c < count( $departments ); ++$c )
					{
						$department = $departments[$c] ;
						$deptid = $department["deptID"] ;
						$td1 = "td_dept_td" ;

						if ( $department["name"] != "Archive" )
						{
							$checked_on = "" ;
							if ( isset( $dept_emo[$deptid] ) && $dept_emo[$deptid] ) { $checked_on = "checked" ; }
							else if ( isset( $dept_emo[0] ) && $dept_emo[0] ) { $checked_on = "checked" ; }
							$checked_off = ( !$checked_on ) ? "checked" : "" ;

							$div_onoff = "<div style=\"margin-top: 15px;\"><div class=\"info_good\" style=\"float: left; width: 60px; padding: 3px; text-shadow: none; cursor: pointer;\" onclick=\"$('#dept_emo_$department[deptID]_on').prop('checked', true);confirm_emo_onoff($department[deptID], 'on')\"><input type=\"radio\" name=\"dept_emo_$department[deptID]\" id=\"dept_emo_$department[deptID]_on\" value=\"on\" $checked_on> On</div><div class=\"info_error\" style=\"float: left; margin-left: 10px; width: 60px; padding: 3px; text-shadow: none; cursor: pointer;\" onclick=\"$('#dept_emo_$department[deptID]_off').prop('checked', true);confirm_emo_onoff($department[deptID], 'off')\"><input type=\"radio\" name=\"dept_emo_$department[deptID]\" id=\"dept_emo_$department[deptID]_off\" value=\"off\" $checked_off> Off</div><div style=\"clear: both;\"></div></div>" ;

							print "
							<tr>
								<td class=\"$td1\" nowrap>
									<div style=\"\">$department[name]</div>
								</td>
								<td class=\"$td1\">$div_onoff</td>
							</tr>
							" ;
						}
					}
				?>
				</table>
			</div>
			<?php endif ; ?>

			<div style="padding-top: 50px; text-align: right;">Emoticons Addon v.<?php echo $EMO_VERSION ?>  <img src="../../pics/icons/disc.png" width="16" height="16" border="0" alt=""> <a href="http://www.phplivesupport.com/r.php?r=vcheck_emo&v=<?php echo base64_encode( $EMO_VERSION ) ?>&v_=<?php echo base64_encode( $VERSION ) ?>" target="new">check for new version</a></div>

		</div>

<?php include_once( "../../setup/inc_footer.php" ) ?>
