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
	include_once( "$CONF[DOCUMENT_ROOT]/API/Ops/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Messages/get.php" ) ;

	$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
	$page = Util_Format_Sanatize( Util_Format_GetVar( "page" ), "n" ) ;
	$index = Util_Format_Sanatize( Util_Format_GetVar( "index" ), "n" ) ;

	$departments = Depts_get_AllDepts( $dbh ) ;
	$deptinfo = Array() ;
	if ( $deptid ) { $deptinfo = Depts_get_DeptInfo( $dbh, $deptid ) ; }
	$operators = Ops_get_AllOps( $dbh ) ;

	// make hash for quick refrence
	$dept_hash = Array() ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
	}

	$messages = Messages_get_Messages( $dbh, $deptid, $page, 15 ) ;
	$total = Messages_get_TotalMessages( $dbh, $deptid ) ;
	$pages = Util_Functions_Page( $page, $index, 15, $total, "reports_chat_msg.php", "ses=$ses&deptid=$deptid" ) ;
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
	var global_messageid ;
	var global_savem = <?php echo isset( $deptinfo["deptID"] ) ? $deptinfo["savem"] : 0 ; ?> ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu() ;
		toggle_menu_setup( "rchats" ) ;
	});

	function open_message( themessageid )
	{
		var screen_width = screen.width ;
		var screen_height = screen.height ;
		var window_width = 720 ;
		var window_height = 550 ;

		global_messageid = themessageid ;

		$( '#messages' ).find('*').each( function(){
			var div_name = this.id ;
			if ( div_name.indexOf("img_") != -1 )
				$(this).css({ 'opacity': 1 }) ;
		} );

		$('#img_'+themessageid).css({ 'opacity': '0.4' }) ;

		var url = "reports_msg_view.php?ses=<?php echo $ses ?>&messageid="+themessageid+"&"+unixtime() ;
		var newwin = window.open( url, "message_"+themessageid, "scrollbars=yes,menubar=no,resizable=1,location=no,width="+window_width+",height="+window_height+",status=0" ) ;
		if ( newwin ) { newwin.focus() ; }
	}

	function delete_message()
	{
		setTimeout( function() { $('#tr_'+global_messageid).remove() ; do_alert( 1, "Delete Success" ) ; }, 500 ) ;
	}

	function switch_dept( theobject )
	{
		location.href = "reports_chat_msg.php?ses=<?php echo $ses ?>&deptid="+theobject.value+"&"+unixtime() ;
	}

	function do_savem( thevalue )
	{
		if ( global_savem != thevalue )
		{
			var json_data = new Object ;

			$.ajax({
				type: "POST",
				url: "../ajax/setup_actions.php",
				data: "ses=<?php echo $ses ?>&action=update_savem&deptid=<?php echo $deptid ?>&savem="+thevalue+"&"+unixtime(),
				success: function(data){
					global_savem = thevalue ;
					do_alert( 1, "Success" ) ;
				}
			});
		}
	}

//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ?>

		<div class="op_submenu_wrapper">
			<div class="op_submenu" onClick="location.href='reports_chat.php?ses=<?php echo $ses ?>'">Chat Reports</div>
			<div class="op_submenu" onClick="location.href='reports_chat_active.php?ses=<?php echo $ses ?>'">Active Chats</div>
			<div class="op_submenu" onClick="location.href='reports_chat_missed.php?ses=<?php echo $ses ?>'">Missed Chats</div>
			<div class="op_submenu_focus">Offline Messages</div>
			<div style="clear: both"></div>
		</div>

		<div style="margin-top: 25px;">
				<div class="op_submenu_focus">Offline Messages</div>
				<div class="op_submenu2" onClick="location.href='reports_chat_msg_urls.php?ses=<?php echo $ses ?>'">Message URLs</div>
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
				</td>
			</tr>
			<tr>
				<td colspan=2 style="padding-top: 15px;">
					<div class="info_neutral" style="text-shadow: none;">
						<?php
							if ( isset( $deptinfo["deptID"] ) ):
						?>
						<table cellspacing=0 cellpadding=0 border=0>
						<tr>
							<td><img src="../pics/icons/arrow_right.png" width="16" height="15" border="0" alt=""> Automatically delete department saved offline messages that were created over &nbsp; </td>
							<td>
								<div class="li_op round" style="color: #737373;"><input type="radio" id="savem_1" name="savem" value=1 onClick="do_savem(this.value)" <?php echo ( $deptinfo["savem"] == 1 ) ? "checked" : "" ?>> 1 month</div>
								<div class="li_op round" style="color: #737373;"><input type="radio" id="savem_3" name="savem" value=3 onClick="do_savem(this.value)" <?php echo ( $deptinfo["savem"] == 3 ) ? "checked" : "" ?>> 3 months</div>
								<div class="li_op round" style="color: #737373;"><input type="radio" id="savem_6" name="savem" value=6 onClick="do_savem(this.value)" <?php echo ( $deptinfo["savem"] == 6 ) ? "checked" : "" ?>> 6 months</div>
								<div class="li_op round" style="color: #737373;"><input type="radio" id="savem_12" name="savem" value=12 onClick="do_savem(this.value)" <?php echo ( $deptinfo["savem"] == 12 ) ? "checked" : "" ?>> 1 year</div>
								<div class="li_op round" style="color: #737373;"><input type="radio" id="savem_0" name="savem" value=0 onClick="do_savem(this.value)" <?php echo ( $deptinfo["savem"] == 0 ) ? "checked" : "" ?>> do not delete</div>
								<div style="clear: both;"></div>
							</td>
							<td> ago</td>
						</tr>
						</table>
						<?php else: ?>
						<img src="../pics/icons/arrow_top.png" width="15" height="16" border="0" alt=""> Select a department to set the automatic delete setting.
						<?php endif ; ?>
					</div>
					<div style="margin-top: 15px;"><img src="../pics/icons/info.png" width="12" height="12" border="0" alt=""> If the visitor has left a message, these are the saved copies of the messages.  All messages were automatically emailed to the <a href="depts.php?ses=<?php echo $ses ?>">department</a> email address.</div>
				</td>
			</tr>
			</table>
			</form>
		</div>

		<table cellspacing=0 cellpadding=0 border=0 width="100%" style="margin-top: 25px;" id="messages">
		<tr><td colspan="10"><div class="page_top_wrapper"><?php echo $pages ?></div></td></tr>
		<tr>
			<td width="20" nowrap><div class="td_dept_header">&nbsp;</div></td>
			<td width="140"><div class="td_dept_header">Created</div></td>
			<td width="80" nowrap><div class="td_dept_header">Name</div></td>
			<td width="80" nowrap><div class="td_dept_header">Email</div></td>
			<td width="80" nowrap><div class="td_dept_header">Department</div></td>
			<td width="80"><div class="td_dept_header">Footprints</div></td>
			<td><div class="td_dept_header">Subject</div></td>
		</tr>
		<?php
			for ( $c = 0; $c < count( $messages ); ++$c )
			{
				$message = $messages[$c] ;

				$visitor = $message["vname"] ;
				$department = isset( $dept_hash[$message["deptID"]] ) ? $dept_hash[$message["deptID"]] : "&nbsp;" ;
				$created_date = date( "M j, Y", $message["created"] ) ;
				$created_time = date( "g:i a", $message["created"] ) ;
				$ip = $message["ip"] ;
				$subject = $message["subject"] ;

				$chat = ( $message["chat"] ) ? "chat.png" : "space.gif" ;
				$flag_custom = ( $message["custom"] ) ? "<img src=\"../pics/icons/pin_note.png\" width=\"12\" height=\"12\" border=\"0\" alt=\"message contains custom variables\" title=\"message contains custom variables\" style=\"cursor: help;\">" : "" ;

				$btn_view = "<div onClick=\"open_message('$message[messageID]')\" style=\"cursor: pointer;\" id=\"img_$message[messageID]\"><img src=\"../pics/btn_view.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" ;

				$td1 = "td_dept_td" ;

				print "<tr id=\"tr_$message[messageID]\"><td class=\"$td1\">$btn_view</td><td class=\"$td1\" nowrap>$created_date<div style=\"font-size: 10px; margin-top: 3px;\">($created_time)</div></td><td class=\"$td1\">$visitor</td><td class=\"$td1\" nowrap><a href=\"mailto:$message[vemail]\">$message[vemail]</a></td><td class=\"$td1\">$department</td><td class=\"$td1\" nowrap>$message[footprints]</td><td class=\"$td1\"><div id=\"div_$message[messageID]\">$flag_custom $message[subject]</div></td></tr>" ;
			}
			if ( $c == 0 )
				print "<tr><td colspan=8 class=\"td_dept_td\">Blank results.</td></tr>" ;
		?>
		</table>

<?php include_once( "./inc_footer.php" ) ?>
