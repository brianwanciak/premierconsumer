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

	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/get.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$console = Util_Format_Sanatize( Util_Format_GetVar( "console" ), "n" ) ; $body_width = ( $console ) ? 800 : 900 ;
	$menu = Util_Format_Sanatize( Util_Format_GetVar( "menu" ), "ln" ) ;
	$wp = Util_Format_Sanatize( Util_Format_GetVar( "wp" ), "n" ) ;
	$auto = Util_Format_Sanatize( Util_Format_GetVar( "auto" ), "n" ) ;
	$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "n" ) ;
	$menu = ( $menu ) ? $menu : "go" ;

	$menu = "cans" ;
	$error = "" ;

	if ( $action == "submit" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/put.php" ) ;

		$deptid = Util_Format_Sanatize( Util_Format_GetVar( "deptid" ), "n" ) ;
		$title = Util_Format_Sanatize( Util_Format_GetVar( "title" ), "ln" ) ;
		$message = Util_Format_Sanatize( Util_Format_GetVar( "message" ), "" ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		if ( isset( $caninfo["opID"] ) )
			$opid = $caninfo["opID"] ;
		else
			$opid = $opinfo["opID"] ;

		if ( !$canid = Canned_put_Canned( $dbh, $canid, $opinfo["opID"], $deptid, $title, $message ) )
			$error = "Error processing canned message." ;
		$action = "" ; // due to scrolling fast
	}
	else if ( $action == "delete" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/remove.php" ) ;

		$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "n" ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		if ( $caninfo["opID"] == $opinfo["opID"] )
			Canned_remove_Canned( $dbh, $opinfo["opID"], $canid ) ;
		$action = "submit" ; $canid = 0 ;
	}

	$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;
	$cans = Canned_get_OpCanned( $dbh, $opinfo["opID"], 0 ) ;

	// make hash for quick refrence
	$dept_hash = Array() ;
	$dept_hash[1111111111] = "All Departments" ;
	for ( $c = 0; $c < count( $departments ); ++$c )
	{
		$department = $departments[$c] ;
		$dept_hash[$department["deptID"]] = $department["name"] ;
	}
?>
<?php include_once( "../inc_doctype.php" ) ?>
<head>
<title> Canned Responses </title>

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
	var global_canid ;
	var global_top ;

	$(document).ready(function()
	{
		$("body").css({'background': '#8DB26C'}) ;

		init_menu_op() ;
		toggle_menu_op( "<?php echo $menu ?>", '<?php echo $ses ?>' ) ;

		<?php if ( ( $action == "submit" ) && !$error ): ?>parent.do_alert( 1, "Success" ) ;<?php endif ; ?>

		if ( <?php echo $canid ?> )
		{
			var div_pos = $('#tr_'+<?php echo $canid ?>).position() ;
			var div_height = Math.round( $('#tr_'+<?php echo $canid ?>).height()/2 ) ;
			var scroll_to = div_pos.top + div_height - 200 ;

			$('html, body').animate({
				scrollTop: scroll_to
			}, 200) ;
			$('#tr_'+<?php echo $canid ?>).fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast").fadeOut("fast").fadeIn("fast") ;
		}
	});

	function do_edit( thecanid, thetitle, thedeptid, themessage )
	{
		var pos = $('#div_scrolltop').position() ;
		global_top = $(window).scrollTop() ;
		var scrollto = pos.top - 500 ;

		if ( global_canid ) { $( "#tr_"+global_canid ).css('background-color','transparent') ; }
		$( "#tr_"+thecanid ).css('background-color','#F1F1F1') ;
		$( "input#canid" ).val( thecanid ) ;
		$( "input#title" ).val( thetitle.replace( /&-#39;/g, "'" ) ) ;
		$( "#deptid" ).val( thedeptid ) ;
		$( "#message" ).val( themessage.replace(/<br>/g, "\r\n").replace( /&-#39;/g, "'" ) ) ;
		show_form(0) ;

		global_canid = thecanid ;
		$('html, body').animate({
			scrollTop: scrollto
		}, 500);
	}

	function do_delete( thiscanid )
	{
		if ( confirm( "Really delete this canned response?" ) )
			location.href = "cans.php?ses=<?php echo $ses ?>&wp=<?php echo $wp ?>&auto=<?php echo $auto ?>&console=<?php echo $console ?>&action=delete&canid="+thiscanid ;
	}

	function do_submit()
	{
		var canid = $('#canid').val() ;
		var title = $('#title').val() ;
		var deptid = $('#deptid').val() ;
		var message = $('#message').val() ;

		if ( title == "" )
			do_alert( 0, "Please provide a Reference title." ) ;
		else if ( message == "" )
			do_alert( 0, "Please provide a Message." ) ;
		else
			$('#theform').submit() ;
	}

	function cancel_edit()
	{
		$( "input#canid" ).val( 0 ) ;
		$( "input#title" ).val( "" ) ;
		$( "#deptid" ).val( "" ) ;
		$( "#message" ).val( "" ) ;

		$('#div_cans_new').hide() ;
		$('#div_cans').show() ;
		$('html, body').animate({
			scrollTop: global_top
		}, 500);
	}

	function show_form( theflag )
	{
		$(window).scrollTop(0) ;
		$('#div_cans').hide() ;
		$('#div_cans_new').show() ;

		if ( theflag )
		{
			if ( global_canid )
			{
				$( "#tr_"+global_canid ).css('background-color','transparent') ;
				global_canid = 0 ;
			}
			global_top = $(window).scrollTop() ;
		}
	}
//-->
</script>
</head>
<?php include_once( "./inc_header.php" ) ; ?>

		<div id="op_title" class="edit_title" style="margin-bottom: 15px;"></div>
		<?php if ( !count( $departments ) ): ?>
		<div id="no_dept" class="info_error"><img src="../pics/icons/warning.png" width="12" height="12" border="0" alt=""> Contact the Setup Admin to assign this account to a department.  Once assigned, <a href="./cans.php?ses=<?php echo $ses ?>&<?php echo time() ?>" style="color: #FFFFFF;">refresh</a> this page to continue.</div>
		<?php else: ?>
		<div id="div_cans">
			<div class="edit_focus" onClick="show_form(1)">Add New</div>
			<a name="a_top"></a>
			<div id="cans" style="margin-top: 25px;">
				<table cellspacing=0 cellpadding=0 border=0 width="100%" id="table_trs">
				<tr>
					<td width="18" nowrap><div class="td_dept_header">&nbsp;</div></td>
					<td width="180" nowrap><div class="td_dept_header">Title</div></td>
					<td width="180"><div class="td_dept_header">Department</div></td>
					<td><div class="td_dept_header">Message</div></td>
				</tr>
				<?php $c_ = 0 ;
					for ( $c = 0; $c < count( $cans ); ++$c )
					{
						$can = $cans[$c] ;
						$title = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", $can["title"] ) ) ;
						$title_display = preg_replace( "/\"/", "&quot;", $can["title"] ) ;
						
						 $locked = ( $can["opID"] == 1111111111 ) ? "<img src=\"../pics/icons/lock.png\" width=\"16\" height=\"16\" border=\"0\" title=\"locked - created by Setup Admin\" alt=\"locked - created by Setup Admin\"> " : "" ;

						if ( isset( $dept_hash[$can["deptID"]] ) )
						{
							++$c_ ;
							$dept_name = $dept_hash[$can["deptID"]] ;
							$message = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $can["message"] ) ) ) ;
							$message_display = preg_replace( "/\"/", "&quot;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", Util_Format_ConvertTags( $can["message"] ) ) ) ;

							$td1 = "td_dept_td" ;

							$edit_delete = ( $can["opID"] == $opinfo["opID"] ) ? "<div onClick=\"do_edit($can[canID], '$title', '$can[deptID]', '$message')\" style=\"cursor: pointer;\"><img src=\"../pics/btn_edit.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div><div onClick=\"do_delete($can[canID])\" style=\"margin-top: 5px; cursor: pointer;\"><img src=\"../pics/btn_delete.png\" width=\"64\" height=\"23\" border=\"0\" alt=\"\"></div>" : "&nbsp;" ;

							print "<tr id=\"tr_$can[canID]\"><td class=\"$td1\" nowrap>$edit_delete</td><td class=\"$td1\">$locked<b>$title_display</b></td><td class=\"$td1\">$dept_name</td><td class=\"$td1\">$message_display</td></tr>" ;
						}
					}
					if ( $c_ == 0 )
						print "<tr><td colspan=7 class=\"td_dept_td\">Blank results.</td></tr>" ;
				?>
				</table>
			</div>
		</div>

		<div id="div_cans_new" style="display: none;">
			<div style="font-size: 12px; font-weight: normal;"><img src="../pics/icons/info.png" width="14" height="14" border="0" alt=""> Canned responses created here will be available to your account and are not visible to other operators.</div>
			<div style="margin-top: 10px;">
				<form method="POST" action="cans.php?<?php echo time() ?>" id="theform">
				<table cellspacing=0 cellpadding=0 border=0 width="100%">
				<tr>
					<td valign="top" style="width: 420px; padding: 5px;">
						<input type="hidden" name="ses" value="<?php echo $ses ?>">
						<input type="hidden" name="action" value="submit">
						<input type="hidden" name="canid" id="canid" value="0">
						<div>
							Reference (example: "Welcome greeting", "Just a moment")<br>
							<input type="text" name="title" id="title" class="input_text" style="width: 98%; margin-bottom: 10px;" maxlength="25">
							Department<br>
							<select name="deptid" id="deptid" style="width: 99%; margin-bottom: 10px;">
							<option value="1111111111">All Departments</option>
							<?php
								for ( $c = 0; $c < count( $departments ); ++$c )
								{
									$department = $departments[$c] ;

									print "<option value=\"$department[deptID]\">$department[name]</option>" ;
								}
							?>
							</select>
							Canned Message<br>
							<textarea name="message" id="message" class="input_text" rows="7" style="min-width: 98%; margin-bottom: 10px;" wrap="virtual"></textarea>

							<button type="button" onClick="do_submit()" class="btn">Submit</button> &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="cancel_edit()">cancel</span>
						</div>
					</td>
					<td style="padding-left: 35px;">
						<ul>
							<li> HTML will be converted to raw code.
							<li style="margin-top: 5px;"> Dynamically populated variables:
								<ul style="margin-top: 10px;">
									<li> <b>%%visitor%%</b> = visitor's name
									<li> <b>%%operator%%</b> = your name
									<li> <b>%%op_email%%</b> = your email
								</ul>
							<li style="margin-top: 10px;"> To display an image on chat, use the <b>image:</b> prefix
								<ul style="margin-top: 10px;">
									example:
									<li style=""> <b>image:</b><i>http://www.phplivesupport.com/pics/logo_small.png</i>
								</ul>
						</ul>
					</td>
				</tr>
				</table>
				</form>
			</div>
		</div>
		<?php endif ; ?>

<?php include_once( "./inc_footer.php" ) ; ?>
