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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/get_ext.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Chat/remove_itr.php" ) ;

	Chat_remove_itr_OldRequests( $dbh ) ;

	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$deptinfo = Array() ;
	if ( $deptid )
		$deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ;
	$operators = Ops_get_AllOps( $dbh ) ;

	// make hash for quick refrence
	$operators_hash = Array() ;
	for ( $c = 0; $c < count( $operators ); ++$c )
	{
		$operator = $operators[$c] ;
		$operators_hash[$operator["opID"]] = $operator["name"] ;
	}

	$dept_hash = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
	}

	$active_requests = Chat_ext_get_AllRequests( $dbh, $deptid ) ;
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

<script type="text/javascript">
<!--
	var refresh_counter = 25 ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "rchats" ) ;

		var si_refresh = setInterval(function(){
			if ( refresh_counter <= 0 )
				location.href = "reports_chat_active.php?ses=<?php echo $ses ?>&deptid=<?php echo $deptid ?>&"+unixtime() ;
			else
			{
				$('#refresh_counter').html( pad( refresh_counter, 2 ) ) ;
				--refresh_counter ;
			}
		}, 1000) ;
	});

	function open_chat( theces )
	{
		var url = "../ops/op_trans_view.php?ses=<?php echo $ses ?>&ces="+theces+"&id=<?php echo $admininfo["adminID"] ?>&auth=setup&realtime=1&"+unixtime() ;
		newwin = window.open( url, theces, "scrollbars=yes,menubar=no,resizable=1,location=no,width=<?php echo $VARS_CHAT_WIDTH+50 ?>,height=<?php echo $VARS_CHAT_HEIGHT ?>,status=0" ) ;

		if ( newwin )
			newwin.focus() ;
	}

	function switch_dept( theobject )
	{
		location.href = "reports_chat_active.php?ses=<?php echo $ses ?>&deptid="+theobject.value+"&"+unixtime() ;
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='reports_chat.php?ses=<?php echo $ses ?>'">Chat Reports</div>
			<div class="op_submenu_focus">Active Chats</div>
			<div class="op_submenu" onClick="location.href='reports_chat_missed.php?ses=<?php echo $ses ?>'">Missed Chats</div>
			<div class="op_submenu" onClick="location.href='reports_chat_msg.php?ses=<?php echo $ses ?>'">Offline Messages</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
			<form method="POST" action="reports_chat_active.php?submit" id="form_theform" style="margin-top: 25px;">
			<select name="deptid" id="deptid" style="font-size: 16px; background: #D4FFD4; color: #009000;" OnChange="switch_dept( this )">
			<option value="0">All Departments</option>
			<?php
				$ops_assigned = 0 ;
				for ( $c = 0; $c < count( $departments ); ++$c )
				{
					$department = $departments[$c] ;
					$ops = Depts_get_DeptOps( $dbh, $department["deptID"] ) ;
					if ( count( $ops ) )
						$ops_assigned = 1 ;

					if ( $department["name"] != "Archive" )
					{
						$selected = ( $deptid == $department["deptID"] ) ? "selected" : "" ;
						print "<option value=\"$department[deptID]\" $selected>$department[name]</option>" ;
					}
				}
			?>
			</select>
			</form>

			<div style="margin-top: 15px;">View active chat sessions in real-time.  This page will automatically refresh in <span id="refresh_counter">25</span> seconds.  The <a href="ops.php?ses=<?php echo $ses ?>&jump=monitor">Operator Status Widget</a> also displays the current active chat sessions in real-time.</div>
		</div>

		<table cellspacing=0 cellpadding=0 border=0 width="100%" style="margin-top: 25px;">
		<tr>
			<td width="20" nowrap><div class="td_dept_header">&nbsp;</div></td>
			<td width="80" nowrap><div class="td_dept_header">Operator</div></td>
			<td width="80" nowrap><div class="td_dept_header">Visitor</div></td>
			<td width="80" nowrap><div class="td_dept_header">Department</div></td>
			<td width="140"><div class="td_dept_header">Created</div></td>
			<td width="140"><div class="td_dept_header">Duration</div></td>
			<td width="80"><div class="td_dept_header">IP</div></td>
			<td><div class="td_dept_header">Question</div></td>
		</tr>
		<?php
			for ( $c = 0; $c < count( $active_requests ); ++$c )
			{
				$chat = $active_requests[$c] ;

				// todo: for now only show active chats in session (accepted chats)
				if ( $chat["status"] && isset( $operators_hash[$chat["opID"]] ) )
				{
					$operator = $operators_hash[$chat["opID"]] ;
					$visitor = Util_Format_Sanatize( $chat["vname"], "v" ) ;
					$department = $dept_hash[$chat["deptID"]] ;
					$created = date( "M j (g:i a)", $chat["created"] ) ;
					$duration = time() - $chat["created"] ;
					$duration = ( ( $duration - 60 ) < 1 ) ? " 1 min" : Util_Format_Duration( $duration ) ;
					$ip = $chat["ip"] ;
					$question = nl2br( $chat["question"] ) ;

					$td1 = "td_dept_td" ;

					print "<tr id=\"tr_$chat[ces]\"><td class=\"$td1\"><img src=\"../pics/icons/chats.png\" style=\"cursor: pointer;\" onClick=\"open_chat('$chat[ces]')\" id=\"img_$chat[ces]\"></td><td class=\"$td1\" nowrap><b><div id=\"chat_$chat[ces]\">$operator</div></b></td><td class=\"$td1\" nowrap>$visitor</td><td class=\"$td1\" nowrap>$department</td><td class=\"$td1\" nowrap>$created</td><td class=\"$td1\" nowrap>$duration</td><td class=\"$td1\" nowrap>$ip</td><td class=\"$td1\">$question</td></tr>" ;
				}
			}
			if ( $c == 0 )
				print "<tr><td colspan=8 class=\"td_dept_td\">Blank results.</td></tr>" ;
		?>
		</table>

<?php include_once( "./inc_footer.php" ) ?>
