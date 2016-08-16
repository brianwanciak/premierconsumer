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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Functions.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;

	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$page = Util_Format_Sanatize( Util_Format_GetVar( "page" ), "n" ) ;
	$index = Util_Format_Sanatize( Util_Format_GetVar( "index" ), "n" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$deptinfo = Array() ;
	if ( $deptid ) { $deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ; }

	// make hash for quick refrence
	$dept_hash = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
	}

	$chats_missed = Chat_ext_get_Chats_Missed( $dbh, $deptid, $page, 15 ) ;
	$total = Chat_ext_get_Total_Chats_Missed( $dbh, $deptid ) ;
	$pages = Util_Functions_Page( $page, $index, 15, $total, "reports_chat_missed.php", "ses=$ses&deptid=$deptid" ) ;
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
		toggle_menu_setup( "rchats" ) ;
	});

	function close_message()
	{
		$('#div_message').fadeOut("fast") ;
	}

	function switch_dept( theobject )
	{
		location.href = "reports_chat_missed.php?ses=<?php echo $ses ?>&deptid="+theobject.value+"&"+unixtime() ;
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='reports_chat.php?ses=<?php echo $ses ?>'">Chat Reports</div>
			<div class="op_submenu" onClick="location.href='reports_chat_active.php?ses=<?php echo $ses ?>'">Active Chats</div>
			<div class="op_submenu_focus">Missed Chats</div>
			<div class="op_submenu" onClick="location.href='reports_chat_msg.php?ses=<?php echo $ses ?>'">Offline Messages</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			<form method="POST" action="reports_chat_active.php?submit" id="form_theform">
			<table cellspacing=0 cellpadding=0 border=0 width="100%">
			<tr>
				<td>
					<select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_dept( this )">
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
				</td>
			</tr>
			<tr>
				<td colspan=2 style="padding-top: 15px;">
					<div style=""><img src="../pics/icons/bullet_red.png" width="16" height="16" border="0" alt=""> Chat request was dropped during the routing process due to internet lag, browser closed, etc.  The request did not complete the normal routing cycle.</div>
					<div style="margin-top: 5px;"><img src="../pics/icons/bullet_orange.png" width="16" height="16" border="0" alt=""> The chat request was cancelled by the visitor during the routing cycle.  The request went to the leave a message page.</div>
					<div style="margin-top: 5px;"><img src="../pics/icons/bullet_blue.png" width="16" height="16" border="0" alt=""> Chat request completed the routing cycle to all department operators but the request was not accepted.  The request went to the leave a message page.</div>
					<div style="margin-top: 5px;"><img src="../pics/icons/email.png" width="16" height="16" border="0" alt=""> The visitor <a href="reports_chat_msg.php?ses=<?php echo $ses ?>">left a message</a>.</div>
				</td>
			</tr>
			</table>
			</form>
		</div>

		<table cellspacing=0 cellpadding=0 border=0 width="100%" style="margin-top: 25px;" id="messages">
		<tr><td colspan="10"><div class="page_top_wrapper"><?php echo $pages ?></div></td></tr>
		<tr>
			<td width="40" nowrap><div class="td_dept_header">&nbsp;</div></td>
			<td width="140"><div class="td_dept_header">Created</div></td>
			<td width="80" nowrap><div class="td_dept_header">Department</div></td>
			<td width="160" nowrap><div class="td_dept_header">Visitor Name</div></td>
			<td width="110" nowrap><div class="td_dept_header">Visitor Email</div></td>
			<td><div class="td_dept_header">Question</div></td>
		</tr>
		<?php
			for ( $c = 0; $c < count( $chats_missed ); ++$c )
			{
				$chatinfo = $chats_missed[$c] ;

				$visitor = ( $chatinfo["vname"] != "null" ) ? $chatinfo["vname"] : "&nbsp;" ;
				$email = ( $chatinfo["vemail"] != "null" ) ? "<a href=\"mailto:$chatinfo[vemail]\">$chatinfo[vemail]</a>" : "&nbsp;" ;
				$department = isset( $dept_hash[$chatinfo["deptID"]] ) ? $dept_hash[$chatinfo["deptID"]] : "&nbsp;" ;
				$created_date = date( "M j, Y", $chatinfo["created"] ) ;
				$created_time = date( "g:i a", $chatinfo["created"] ) ;
				$ip = $chatinfo["ip"] ;
				$question = ( $chatinfo["question"] != "null" ) ? $chatinfo["question"] : "&nbsp;" ;
				$bullet = "bullet_red.png" ;
				if ( ( $chatinfo["status_msg"] == 1 ) || ( $chatinfo["status_msg"] == 2 ) ) { $bullet = "bullet_blue.png" ; }
				else if ( ( $chatinfo["status_msg"] == 3 ) || ( $chatinfo["status_msg"] == 4 ) ) { $bullet = "bullet_orange.png" ; }

				$bullet_title = ( $chatinfo["status_msg"] ) ? "chat was not accepted" : "chat was dropped" ;

				$left_message = ( ( $chatinfo["status_msg"] == 2 ) || ( $chatinfo["status_msg"] == 4 ) ) ? "<div><img src=\"../pics/icons/email.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"left a message\" title=\"left a message\"></div>" : "" ;

				$td1 = "td_dept_td" ;

				print "<tr id=\"tr_$chatinfo[ces]\"><td class=\"$td1\"><img src=\"../pics/icons/$bullet\" width=\"16\" height=\"16\" border=\"0\" alt=\"$bullet_title\" title=\"$bullet_title\">$left_message</td><td class=\"$td1\" nowrap>$created_date<div style=\"font-size: 10px; margin-top: 3px;\">($created_time)</div></td><td class=\"$td1\">$department</td><td class=\"$td1\">$visitor</td><td class=\"$td1\" nowrap>$email</td><td class=\"$td1\"><div id=\"div_$chatinfo[ces]\">$question</div></td></tr>" ;
			}
			if ( $c == 0 )
				print "<tr><td colspan=5 class=\"td_dept_td\">Blank results.</td></tr>" ;
		?>
		</table>

<?php include_once( "./inc_footer.php" ) ?>
