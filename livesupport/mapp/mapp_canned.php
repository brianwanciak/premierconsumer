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
	include_once( "$CONF[DOCUMENT_ROOT]/API/Depts/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/get.php" ) ;
	include_once( "$CONF[DOCUMENT_ROOT]/API/Util_Mobile.php" ) ;

	$action = Util_Format_Sanatize( Util_Format_GetVar( "action" ), "ln" ) ;
	$canid = Util_Format_Sanatize( Util_Format_GetVar( "canid" ), "n" ) ;
	$flag = Util_Format_Sanatize( Util_Format_GetVar( "flag" ), "n" ) ;
	$mobile = Util_Mobile_Detect() ;
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
	}
	else if ( $action == "delete" )
	{
		include_once( "$CONF[DOCUMENT_ROOT]/API/Canned/remove.php" ) ;

		$caninfo = Canned_get_CanInfo( $dbh, $canid ) ;
		if ( $caninfo["opID"] == $opinfo["opID"] )
			Canned_remove_Canned( $dbh, $opinfo["opID"], $canid ) ;
		$action = "submit" ; $canid = 0 ;
	}

	$departments = Depts_get_OpDepts( $dbh, $opinfo["opID"] ) ;
	$cans = Canned_get_OpCanned( $dbh, $opinfo["opID"], 0 ) ;
	$cans_total = count( $cans ) ;

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
<script type="text/javascript" src="../js/setup.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/framework_cnt.js?<?php echo $VERSION ?>"></script>
<script type="text/javascript" src="../js/jquery_md5.js?<?php echo $VERSION ?>"></script>

<script type="text/javascript">
<!--
	var global_canid = <?php echo $canid ?> ;
	var global_canid_delete ;
	var global_top ;
	var global_auto_canid = parent.auto_canid ;

	$(document).ready(function()
	{
		reset_mapp_div_height() ;
		$('#canned_wrapper').show() ;

		if ( global_auto_canid ) { $('#auto_can_'+global_auto_canid).prop('checked', true) ; }
		<?php if ( ( $action == "submit" ) && !$error ): ?>parent.do_alert( 1, "Success" ) ; parent.populate_cans(0) ;
		<?php elseif ( $action == "reload" ): ?>do_alert( 1, "Refresh Success" ) ;
		<?php endif ; ?>

		if ( <?php echo $canid ?> )
		{
			$('#table_<?php echo $canid ?>').addClass('info_focus') ;
			do_scroll( <?php echo $canid ?> ) ;
		}
	});

	function do_scroll( thecanid )
	{
		if ( thecanid )
		{
			var div_pos = $('#table_'+thecanid).position() ;
			var scroll_to = div_pos.top - 25 ;

			$('#canned_container').scroll() ;
			$('#canned_container').animate({
				scrollTop: scroll_to
			}, 200) ;
		}
	}

	function do_edit( thecanid, thetitle, thedeptid, themessage )
	{
		$('#table_'+thecanid).addClass('info_focus') ;
		if ( ( typeof( global_canid ) != "undefined" ) && ( global_canid != thecanid ) )
			$('#table_'+global_canid).removeClass('info_focus') ;

		if ( ( typeof( global_canid_delete ) != "undefined" ) && ( global_canid_delete != thecanid ) )
			$('#table_'+global_canid_delete).removeClass('info_focus') ;

		global_canid = thecanid ;
		global_top = $(window).scrollTop() ;

		$( "input#canid" ).val( thecanid ) ;
		$( "input#title" ).val( thetitle.replace( /&-#39;/g, "'" ) ) ;
		$( "#deptid" ).val( thedeptid ) ;
		$( "#message" ).val( themessage.replace(/<br>/g, "\r\n").replace( /&-#39;/g, "'" ) ) ;
		
		toggle_menu_info("new", 0) ;
	}

	function select_canned( thetitle )
	{
		$('#canned_container').hide() ; // fixes scroll locking bug on Android
		parent.select_canned_pre( thetitle ) ;
	}

	function do_delete( thecanid )
	{
		$('#table_'+thecanid).addClass('info_focus') ;
		if ( ( typeof( global_canid_delete ) != "undefined" ) && ( global_canid_delete != thecanid ) )
			$('#table_'+global_canid_delete).removeClass('info_focus') ;

		if ( ( typeof( global_canid ) != "undefined" ) && ( global_canid != thecanid ) )
			$('#table_'+global_canid).removeClass('info_focus') ;

		global_canid_delete = thecanid ;
		$('#div_confirm_delete').show().center() ;
	}

	function do_delete_pre( theflag )
	{
		if ( theflag )
			location.href = "mapp_canned.php?ses=<?php echo $ses ?>&action=delete&canid="+global_canid_delete ;
		else
			$('#div_confirm_delete').hide() ;
	}

	function do_cancel()
	{
		toggle_menu_info('list', 0) ;
		do_scroll( global_canid ) ;
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

	function toggle_menu_info( themenu, theclear )
	{
		var divs = Array( "list", "new" ) ;

		for ( var c = 0; c < divs.length; ++c )
		{
			$('#div_settings_'+divs[c]).hide() ;
			$('#menu_settings_'+divs[c]).removeClass('menu_traffic_info_focus').addClass('menu_traffic_info') ;
		}

		if ( theclear )
		{
			if ( typeof( global_canid ) != "undefined" )
				$('#table_'+global_canid).removeClass('info_focus') ;

			global_canid = 0 ;
			global_top = 0 ;

			$( "input#canid" ).val( 0 ) ;
			$( "input#title" ).val( "" ) ;
			$( "#deptid" ).val( 1111111111 ) ;
			$( "#message" ).val( "" ) ;
		}

		$('#div_settings_'+themenu).show() ;
		$('#menu_settings_'+themenu).removeClass('menu_traffic_info').addClass('menu_traffic_info_focus') ;

		if ( ( themenu == "list" ) && global_canid )
			do_scroll( global_canid ) ;
	}

	function select_auto_can( thecanid )
	{
		$('#table_'+thecanid).addClass('info_focus') ;
		if ( ( typeof( global_canid_delete ) != "undefined" ) && ( global_canid_delete != thecanid ) )
			$('#table_'+global_canid_delete).removeClass('info_focus') ;

		if ( ( typeof( global_canid ) != "undefined" ) && ( global_canid != thecanid ) )
			$('#table_'+global_canid).removeClass('info_focus') ;

		global_canid_delete = thecanid ; // use same variable just to process class select

		$('#div_confirm').show() ;
		$('#div_settings_list').find('*').each( function () {
			var div_name = this.id ;
			if ( div_name.indexOf( "auto_can_" ) == 0 )
				this.checked = false ;
		}) ;
		$('#auto_can_'+thecanid).prop('checked', true) ;

		$('#confirm_canid').val( thecanid ) ;
		if ( global_auto_canid != thecanid )
		{
			$('#confirm_value').val( 1 ) ;
			$('#confirm_title').html( "Automatically select and send this canned response immediately after accepting a chat?" ) ;
		}
		else
		{
			$('#confirm_value').val( 0 ) ;
			$('#confirm_title').html( "De-select this canned response?" ) ;
		}
	}

	function select_auto_can_doit( theoption )
	{
		var thecanid = parseInt( $('#confirm_canid').val() ) ;
		var thevalue = parseInt( $('#confirm_value').val() ) ;

		$('#div_confirm').hide() ;
		if ( theoption )
		{
			var unique = unixtime() ;
			var json_data = new Object ;

			$.ajax({
			type: "POST",
			url: "../ajax/chat_actions_op_cans.php",
			data: "action=auto_canned&ses=<?php echo $ses ?>&canid="+thecanid+"&value="+thevalue+"&unique="+unique,
			success: function(data){
				eval( data ) ;

				if ( json_data.status )
				{
					if ( thevalue )
						parent.auto_canid = global_auto_canid = thecanid ;
					else
					{
						parent.auto_canid = global_auto_canid = 0 ;
						$('#auto_can_'+thecanid).prop('checked', false) ;
					}
					do_alert( 1, "Success" ) ;
				}
				else
					do_alert( 0, "Error updating.  Please reload the console and try again." ) ;
			},
			error:function (xhr, ajaxOptions, thrownError){
				do_alert( 0, "Error updating.  Please reload the console and try again." ) ;
			} });
		}
		else if ( global_auto_canid )
		{
			$('#auto_can_'+thecanid).prop('checked', false) ;
			$('#auto_can_'+global_auto_canid).prop('checked', true) ;
		}
		else
			$('#auto_can_'+thecanid).prop('checked', false) ;
	}

	function do_can_jump( thecanid )
	{
		if ( thecanid )
		{
			$('#table_'+thecanid).addClass('info_focus') ;
			if ( ( typeof( global_canid ) != "undefined" ) && ( global_canid != thecanid ) )
				$('#table_'+global_canid).removeClass('info_focus') ;

			if ( ( typeof( global_canid_delete ) != "undefined" ) && ( global_canid_delete != thecanid ) )
				$('#table_'+global_canid_delete).removeClass('info_focus') ;

			global_canid = thecanid ;
			global_top = $(window).scrollTop() ;
			do_scroll( global_canid ) ;
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
			<div id="canned_container" style="overflow: auto;">

				<div style="">
					<div id="menu_settings_list" class="menu_traffic_info_focus" onClick="toggle_menu_info('list', 0)">List</div>
					<div id="menu_settings_new" class="menu_traffic_info" onClick="toggle_menu_info('new', 1)">Create/Edit</div>
					<div style="clear: both;"></div>
				</div>

				<div style="margin-top: 25px;">
					<div id="div_settings_list">
						<?php if ( $cans_total ): ?>
							<div style="margin-bottom: 15px;">Jump to:
							<select name="" onChange="do_can_jump(this.value)" onSelect="this.blur()">
							<?php
								$deptid = 0 ;
								$cans_string = "<option value=0>&nbsp;</option>" ;
								for ( $c = 0; $c < $cans_total; ++$c )
								{
									$can = $cans[$c] ;
									$title = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", $can["title"] ) ) ;
									$title_display = Util_Format_ConvertQuotes( $can["title"] ) ;

									if ( !$deptid || ( $deptid != $can["deptID"] ) )
									{
										$deptid = $can["deptID"] ;
										$dept_name = $dept_hash[$deptid] ;
										$cans_string .= "<optgroup label=\"$dept_name\">" ;
									}
									$cans_string .= "<option value=\"$can[canID]\">$title_display</option>" ;
								}
								print $cans_string ;
							?>
							</select></div>
						<?php endif ; ?>

						<?php
							for ( $c = 0; $c < $cans_total; ++$c )
							{
								$can = $cans[$c] ;
								$title = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", $can["title"] ) ) ;
								$title_display = Util_Format_ConvertQuotes( $can["title"] ) ;

								if ( isset( $dept_hash[$can["deptID"]] ) )
								{
									$message = preg_replace( "/\"/", "&quot;", preg_replace( "/'/", "&-#39;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", $can["message"] ) ) ) ;
									$message_display = preg_replace( "/\"/", "&quot;", preg_replace( "/(\r\n)|(\n)|(\r)/", "<br>", Util_Format_ConvertTags( $can["message"] ) ) ) ;
									if ( $mobile != 3 ) { $message_display = wordwrap( $message_display, 43, "<br>", true ) ; }

									$auto_can_div = "<span class='info_neutral' style=\"margin-right: 15px; text-align: center; cursor: pointer;\" class=\"chat_info_td_traffic\" onClick=\"select_auto_can($can[canID])\"><input type=\"checkbox\" id=\"auto_can_$can[canID]\" value=\"$can[canID]\"> auto select</span>" ;

									$delete_image = ( $can["opID"] == $opinfo["opID"] ) ? "<span class='info_neutral' style='margin-left: 15px; margin-right: 15px; border-bottom-left-radius: 5px 5px; -moz-border-radius-bottomleft: 5px 5px; cursor: pointer;' onClick=\"do_delete($can[canID])\">delete</span>" : "" ;
									$edit_image = ( $can["opID"] == $opinfo["opID"] ) ? "<span class='info_neutral' style='margin-left: 15px; border-bottom-left-radius: 5px 5px; -moz-border-radius-bottomleft: 5px 5px; cursor: pointer;' onClick=\"do_edit($can[canID], '$title', '$can[deptID]', '$message')\">edit</span>" : "<span class='info_neutral' style='margin-left: 15px; border-bottom-left-radius: 5px 5px; -moz-border-radius-bottomleft: 5px 5px;'><img src=\"../themes/$opinfo[theme]/lock.png\" width=\"16\" height=\"16\" border=0 title=\"created by Setup Admin\" alt=\"created by Setup Admin\"></span>" ;

									print "
										<div class=\"info_neutral\" id='table_$can[canID]' style=\"padding: 10px; margin-bottom: 1px;\">
											<div><button type=\"button\" onClick=\"select_canned('$title_display')\">select</button> <b>$title_display</b></div>
											<div style='margin-top: 5px;'>$message_display</div>

											<div style='float: right; margin-top: 5px;'>$auto_can_div $delete_image $edit_image</div><div style='clear: both;'></div>
										</div>
									" ;
								}
							}
							if ( $cans_total )
								print "<div style=\"padding: 50px;\">&nbsp;</div>" ;
							else
								print "<div class=\"info_neutral\" style=\"padding: 10px; margin-bottom: 1px;\">Blank Results.</div>" ;
						?>
					</div>

					<div id="div_settings_new" style="display: none;">
						<div style=""><img src="../themes/<?php echo $opinfo["theme"] ?>/info.png" width="14" height="14" border="0" alt=""> Canned responses created here will be available to your account and are not visible to other operators.</div>

						<form method="POST" action="mapp_canned.php?<?php echo time() ?>" id="theform">
						<input type="hidden" name="ses" value="<?php echo $ses ?>">
						<input type="hidden" name="action" value="submit">
						<input type="hidden" name="canid" id="canid" value="0">
						<div style="margin-top: 15px;">
							Reference (example: "Greeting", "Just a moment")
							<div><input type="text" name="title" id="title" class="input_text" style="width: 92%; margin-bottom: 10px;" maxlength="25"></div>
							<div>Department</div>
							<div><select name="deptid" id="deptid" style="width: 95%; margin-bottom: 10px;">
								<option value="1111111111">All Departments</option>
								<?php
									for ( $c = 0; $c < count( $departments ); ++$c )
									{
										$department = $departments[$c] ;

										print "<option value=\"$department[deptID]\">$department[name]</option>" ;
									}
								?>
							</select></div>
							<div>Canned Message</div>
							<div><textarea name="message" id="message" class="input_text" rows="4" style="min-width: 92%; margin-bottom: 10px; resize: none;" wrap="virtual"></textarea></div>

							<div><button type="button" onClick="do_submit()" class="input_button">Submit</button> &nbsp; &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="do_cancel()">cancel</span></div>
							</form>
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
			<div id="confirm_title"></div>
			<form><input type="hidden" id="confirm_canid" name="confirm_canid" value=""><input type="hidden" id="confirm_value" name="confirm_value" value=""></form>
			<div style="margin-top: 15px;"><button type="button" onClick="select_auto_can_doit(1)" class="input_button">Yes</button> &nbsp; &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="select_auto_can_doit(0)">cancel</span></div>
		</div>
	</div>
</div>

<div id="div_confirm_delete" style="display: none; position: absolute; top: 0px; left: 0px; width: 100%; height: 100%; background: url( ../pics/bg_trans_white.png ) repeat; overflow: hidden; z-index: 20;">
	<div id="div_confirm_body" class="info_info" style="position: relative; width: 350px; margin: 0 auto; top: 100px;">
		<div class="info_error" style="padding: 25px;">
			Really delete this canned response?
			<div style="margin-top: 15px;"><button type="button" onClick="do_delete_pre(1)" class="input_button">Yes</button> &nbsp; &nbsp; <span style="text-decoration: underline; cursor: pointer;" onClick="do_delete_pre(0)">cancel</span></div>
		</div>
	</div>
</div>

</body>
</html>
<?php
	if ( isset( $dbh ) && $dbh['con'] )
		database_mysql_close( $dbh ) ;
?>
